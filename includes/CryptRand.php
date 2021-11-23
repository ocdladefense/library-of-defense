<?php
/**
 * A cryptographic random generator class used for generating secret keys
 *
 * This is based in part on Drupal code as well as what we used in our own code
 * prior to introduction of this class.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @author Daniel Friesen
 * @file
 */

class MWCryptRand {

	/**
	 * Minimum number of iterations we want to make in our drift calculations.
	 */
	const MIN_ITERATIONS = 1000;

	/**
	 * Number of milliseconds we want to spend generating each separate byte
	 * of the final generated bytes.
	 * This is used in combination with the hash length to determine the duration
	 * we should spend doing drift calculations.
	 */
	const MSEC_PER_BYTE = 0.5;

	/**
	 * Singleton instance for public use
	 */
	protected static $singleton = null;

	/**
	 * The hash algorithm being used
	 */
	protected $algo = null;

	/**
	 * The number of bytes outputted by the hash algorithm
	 */
	protected $hashLength = null;

	/**
	 * A boolean indicating whether the previous random generation was done using
	 * cryptographically strong random number generator or not.
	 */
	protected $strong = null;

	/**
	 * Initialize an initial random state based off of whatever we can find
	 */
	protected function initialRandomState() {
		// $_SERVER contains a variety of unstable user and system specific information
		// It'll vary a little with each page, and vary even more with separate users
		// It'll also vary slightly across different machines
		$state = serialize( $_SERVER );

		// To try vary the system information of the state a bit more
		// by including the system's hostname into the state
		$state .= wfHostname();

		// Try to gather a little entropy from the different php rand sources
		$state .= rand() . uniqid( mt_rand(), true );

		// Include some information about the filesystem's current state in the random state
		$files = array();

		// We know this file is here so grab some info about ourself
		$files[] = __FILE__;

		// We must also have a parent folder, and with the usual file structure, a grandparent
		$files[] = __DIR__;
		$files[] = dirname( __DIR__ );

		// The config file is likely the most often edited file we know should be around
		// so include its stat info into the state.
		// The constant with its location will almost always be defined, as WebStart.php defines
		// MW_CONFIG_FILE to $IP/LocalSettings.php unless being configured with MW_CONFIG_CALLBACK (eg. the installer)
		if ( defined( 'MW_CONFIG_FILE' ) ) {
			$files[] = MW_CONFIG_FILE;
		}

		foreach ( $files as $file ) {
			wfSuppressWarnings();
			$stat = stat( $file );
			wfRestoreWarnings();
			if ( $stat ) {
				// stat() duplicates data into numeric and string keys so kill off all the numeric ones
				foreach ( $stat as $k => $v ) {
					if ( is_numeric( $k ) ) {
						unset( $k );
					}
				}
				// The absolute filename itself will differ from install to install so don't leave it out
				$state .= realpath( $file );
				$state .= implode( '', $stat );
			} else {
				// The fact that the file isn't there is worth at least a
				// minuscule amount of entropy.
				$state .= '0';
			}
		}

		// Try and make this a little more unstable by including the varying process
		// id of the php process we are running inside of if we are able to access it
		if ( function_exists( 'getmypid' ) ) {
			$state .= getmypid();
		}

		// If available try to increase the instability of the data by throwing in
		// the precise amount of memory that we happen to be using at the moment.
		if ( function_exists( 'memory_get_usage' ) ) {
			$state .= memory_get_usage( true );
		}

		// It's mostly worthless but throw the wiki's id into the data for a little more variance
		$state .= wfWikiID();

		// If we have a secret key or proxy key set then throw it into the state as well
		global $wgSecretKey, $wgProxyKey;
		if ( $wgSecretKey ) {
			$state .= $wgSecretKey;
		} elseif ( $wgProxyKey ) {
			$state .= $wgProxyKey;
		}

		return $state;
	}

	/**
	 * Randomly hash data while mixing in clock drift data for randomness
	 *
	 * @param $data string The data to randomly hash.
	 * @return String The hashed bytes
	 * @author Tim Starling
	 */
	protected function driftHash( $data ) {
		// Minimum number of iterations (to avoid slow operations causing the loop to gather little entropy)
		$minIterations = self::MIN_ITERATIONS;
		// Duration of time to spend doing calculations (in seconds)
		$duration = ( self::MSEC_PER_BYTE / 1000 ) * $this->hashLength();
		// Create a buffer to use to trigger memory operations
		$bufLength = 10000000;
		$buffer = str_repeat( ' ', $bufLength );
		$bufPos = 0;

		// Iterate for $duration seconds or at least $minIerations number of iterations
		$iterations = 0;
		$startTime = microtime( true );
		$currentTime = $startTime;
		while ( $iterations < $minIterations || $currentTime - $startTime < $duration ) {
			// Trigger some memory writing to trigger some bus activity
			// This may create variance in the time between iterations
			$bufPos = ( $bufPos + 13 ) % $bufLength;
			$buffer[$bufPos] = ' ';
			// Add the drift between this iteration and the last in as entropy
			$nextTime = microtime( true );
			$delta = (int)( ( $nextTime - $currentTime ) * 1000000 );
			$data .= $delta;
			// Every 100 iterations hash the data and entropy
			if ( $iterations % 100 === 0 ) {
				$data = sha1( $data );
			}
			$currentTime = $nextTime;
			$iterations++;
		}
		$timeTaken = $currentTime - $startTime;
		$data = $this->hash( $data );

		wfDebug( __METHOD__ . ": Clock drift calculation " .
			"(time-taken=" . ( $timeTaken * 1000 ) . "ms, " .
			"iterations=$iterations, " .
			"time-per-iteration=" . ( $timeTaken / $iterations * 1e6 ) . "us)\n" );
		return $data;
	}

	/**
	 * Return a rolling random state initially build using data from unstable sources
	 * @return string A new weak random state
	 */
	protected function randomState() {
		static $state = null;
		if ( is_null( $state ) ) {
			// Initialize the state with whatever unstable data we can find
			// It's important that this data is hashed right afterwards to prevent
			// it from being leaked into the output stream
			$state = $this->hash( $this->initialRandomState() );
		}
		// Generate a new random state based on the initial random state or previous
		// random state by combining it with clock drift
		$state = $this->driftHash( $state );
		return $state;
	}

	/**
	 * Decide on the best acceptable hash algorithm we have available for hash()
	 * @throws MWException
	 * @return String A hash algorithm
	 */
	protected function hashAlgo() {
		if ( !is_null( $this->algo ) ) {
			return $this->algo;
		}

		$algos = hash_algos();
		$preference = array( 'whirlpool', 'sha256', 'sha1', 'md5' );

		foreach ( $preference as $algorithm ) {
			if ( in_array( $algorithm, $algos ) ) {
				$this->algo = $algorithm;
				wfDebug( __METHOD__ . ": Using the {$this->algo} hash algorithm.\n" );
				return $this->algo;
			}
		}

		// We only reach here if no acceptable hash is found in the list, this should
		// be a technical impossibility since most of php's hash list is fixed and
		// some of the ones we list are available as their own native functions
		// But since we already require at least 5.2 and hash() was default in
		// 5.1.2 we don't bother falling back to methods like sha1 and md5.
		throw new MWException( "Could not find an acceptable hashing function in hash_algos()" );
	}

	/**
	 * Return the byte-length output of the hash algorithm we are
	 * using in self::hash and self::hmac.
	 *
	 * @return int Number of bytes the hash outputs
	 */
	protected function hashLength() {
		if ( is_null( $this->hashLength ) ) {
			$this->hashLength = strlen( $this->hash( '' ) );
		}
		return $this->hashLength;
	}

	/**
	 * Generate an acceptably unstable one-way-hash of some text
	 * making use of the best hash algorithm that we have available.
	 *
	 * @param $data string
	 * @return String A raw hash of the data
	 */
	protected function hash( $data ) {
		return hash( $this->hashAlgo(), $data, true );
	}

	/**
	 * Generate an acceptably unstable one-way-hmac of some text
	 * making use of the best hash algorithm that we have available.
	 *
	 * @param $data string
	 * @param $key string
	 * @return String A raw hash of the data
	 */
	protected function hmac( $data, $key ) {
		return hash_hmac( $this->hashAlgo(), $data, $key, true );
	}

	/**
	 * @see self::wasStrong()
	 */
	public function realWasStrong() {
		if ( is_null( $this->strong ) ) {
			throw new MWException( __METHOD__ . ' called before generation of random data' );
		}
		return $this->strong;
	}

	/**
	 * @see self::generate()
	 */
	public function realGenerate( $bytes, $forceStrong = false ) {
		wfProfileIn( __METHOD__ );

		wfDebug( __METHOD__ . ": Generating cryptographic random bytes for " . wfGetAllCallers( 5 ) . "\n" );

		$bytes = floor( $bytes );
		static $buffer = '';
		if ( is_null( $this->strong ) ) {
			// Set strength to false initially until we know what source data is coming from
			$this->strong = true;
		}

		if ( strlen( $buffer ) < $bytes ) {
			// If available make use of mcrypt_create_iv URANDOM source to generate randomness
			// On unix-like systems this reads from /dev/urandom but does it without any buffering
			// and bypasses openbasedir restrictions, so it's preferable to reading directly
			// On Windows starting in PHP 5.3.0 Windows' native CryptGenRandom is used to generate
			// entropy so this is also preferable to just trying to read urandom because it may work
			// on Windows systems as well.
			if ( function_exists( 'mcrypt_create_iv' ) ) {
				wfProfileIn( __METHOD__ . '-mcrypt' );
				$rem = $bytes - strlen( $buffer );
				$iv = mcrypt_create_iv( $rem, MCRYPT_DEV_URANDOM );
				if ( $iv === false ) {
					wfDebug( __METHOD__ . ": mcrypt_create_iv returned false.\n" );
				} else {
					$buffer .= $iv;
					wfDebug( __METHOD__ . ": mcrypt_create_iv generated " . strlen( $iv ) . " bytes of randomness.\n" );
				}
				wfProfileOut( __METHOD__ . '-mcrypt' );
			}
		}

		if ( strlen( $buffer ) < $bytes ) {
			// If available make use of openssl's random_pseudo_bytes method to attempt to generate randomness.
			// However don't do this on Windows with PHP < 5.3.4 due to a bug:
			// http://stackoverflow.com/questions/1940168/openssl-random-pseudo-bytes-is-slow-php
			// http://git.php.net/?p=php-src.git;a=commitdiff;h=cd62a70863c261b07f6dadedad9464f7e213cad5
			if ( function_exists( 'openssl_random_pseudo_bytes' )
				&& ( !wfIsWindows() || version_compare( PHP_VERSION, '5.3.4', '>=' ) )
			) {
				wfProfileIn( __METHOD__ . '-openssl' );
				$rem = $bytes - strlen( $buffer );
				$openssl_bytes = openssl_random_pseudo_bytes( $rem, $openssl_strong );
				if ( $openssl_bytes === false ) {
					wfDebug( __METHOD__ . ": openssl_random_pseudo_bytes returned false.\n" );
				} else {
					$buffer .= $openssl_bytes;
					wfDebug( __METHOD__ . ": openssl_random_pseudo_bytes generated " . strlen( $openssl_bytes ) . " bytes of " . ( $openssl_strong ? "strong" : "weak" ) . " randomness.\n" );
				}
				if ( strlen( $buffer ) >= $bytes ) {
					// openssl tells us if the random source was strong, if some of our data was generated
					// using it use it's say on whether the randomness is strong
					$this->strong = !!$openssl_strong;
				}
				wfProfileOut( __METHOD__ . '-openssl' );
			}
		}

		// Only read from urandom if we can control the buffer size or were passed forceStrong
		if ( strlen( $buffer ) < $bytes && ( function_exists( 'stream_set_read_buffer' ) || $forceStrong ) ) {
			wfProfileIn( __METHOD__ . '-fopen-urandom' );
			$rem = $bytes - strlen( $buffer );
			if ( !function_exists( 'stream_set_read_buffer' ) && $forceStrong ) {
				wfDebug( __METHOD__ . ": Was forced to read from /dev/urandom without control over the buffer size.\n" );
			}
			// /dev/urandom is generally considered the best possible commonly
			// available random source, and is available on most *nix systems.
			wfSuppressWarnings();
			$urandom = fopen( "/dev/urandom", "rb" );
			wfRestoreWarnings();

			// Attempt to read all our random data from urandom
			// php's fread always does buffered reads based on the stream's chunk_size
			// so in reality it will usually read more than the amount of data we're
			// asked for and not storing that risks depleting the system's random pool.
			// If stream_set_read_buffer is available set the chunk_size to the amount
			// of data we need. Otherwise read 8k, php's default chunk_size.
			if ( $urandom ) {
				// php's default chunk_size is 8k
				$chunk_size = 1024 * 8;
				if ( function_exists( 'stream_set_read_buffer' ) ) {
					// If possible set the chunk_size to the amount of data we need
					stream_set_read_buffer( $urandom, $rem );
					$chunk_size = $rem;
				}
				$random_bytes = fread( $urandom, max( $chunk_size, $rem ) );
				$buffer .= $random_bytes;
				fclose( $urandom );
				wfDebug( __METHOD__ . ": /dev/urandom generated " . strlen( $random_bytes ) . " bytes of randomness.\n" );
				if ( strlen( $buffer ) >= $bytes ) {
					// urandom is always strong, set to true if all our data was generated using it
					$this->strong = true;
				}
			} else {
				wfDebug( __METHOD__ . ": /dev/urandom could not be opened.\n" );
			}
			wfProfileOut( __METHOD__ . '-fopen-urandom' );
		}

		// If we cannot use or generate enough data from a secure source
		// use this loop to generate a good set of pseudo random data.
		// This works by initializing a random state using a pile of unstable data
		// and continually shoving it through a hash along with a variable salt.
		// We hash the random state with more salt to avoid the state from leaking
		// out and being used to predict the /randomness/ that follows.
		if ( strlen( $buffer ) < $bytes ) {
			wfDebug( __METHOD__ . ": Falling back to using a pseudo random state to generate randomness.\n" ); 
		}
		while ( strlen( $buffer ) < $bytes ) {
			wfProfileIn( __METHOD__ . '-fallback' );
			$buffer .= $this->hmac( $this->randomState(), mt_rand() );
			// This code is never really cryptographically strong, if we use it
			// at all, then set strong to false.
			$this->strong = false;
			wfProfileOut( __METHOD__ . '-fallback' );
		}

		// Once the buffer has been filled up with enough random data to fulfill
		// the request shift off enough data to handle the request and leave the
		// unused portion left inside the buffer for the next request for random data
		$generated = substr( $buffer, 0, $bytes );
		$buffer = substr( $buffer, $bytes );

		wfDebug( __METHOD__ . ": " . strlen( $buffer ) . " bytes of randomness leftover in the buffer.\n" );

		wfProfileOut( __METHOD__ );
		return $generated;
	}

	/**
	 * @see self::generateHex()
	 */
	public function realGenerateHex( $chars, $forceStrong = false ) {
		// hex strings are 2x the length of raw binary so we divide the length in half
		// odd numbers will result in a .5 that leads the generate() being 1 character
		// short, so we use ceil() to ensure that we always have enough bytes
		$bytes = ceil( $chars / 2 );
		// Generate the data and then convert it to a hex string
		$hex = bin2hex( $this->generate( $bytes, $forceStrong ) );
		// A bit of paranoia here, the caller asked for a specific length of string
		// here, and it's possible (eg when given an odd number) that we may actually
		// have at least 1 char more than they asked for. Just in case they made this
		// call intending to insert it into a database that does truncation we don't
		// want to give them too much and end up with their database and their live
		// code having two different values because part of what we gave them is truncated
		// hence, we strip out any run of characters longer than what we were asked for.
		return substr( $hex, 0, $chars );
	}

	/** Publicly exposed static methods **/

	/**
	 * Return a singleton instance of MWCryptRand
	 * @return MWCryptRand
	 */
	protected static function singleton() {
		if ( is_null( self::$singleton ) ) {
			self::$singleton = new self;
		}
		return self::$singleton;
	}

	/**
	 * Return a boolean indicating whether or not the source used for cryptographic
	 * random bytes generation in the previously run generate* call
	 * was cryptographically strong.
	 *
	 * @return bool Returns true if the source was strong, false if not.
	 */
	public static function wasStrong() {
		return self::singleton()->realWasStrong();
	}

	/**
	 * Generate a run of (ideally) cryptographically random data and return
	 * it in raw binary form.
	 * You can use MWCryptRand::wasStrong() if you wish to know if the source used
	 * was cryptographically strong.
	 *
	 * @param $bytes int the number of bytes of random data to generate
	 * @param $forceStrong bool Pass true if you want generate to prefer cryptographically
	 *                          strong sources of entropy even if reading from them may steal
	 *                          more entropy from the system than optimal.
	 * @return String Raw binary random data
	 */
	public static function generate( $bytes, $forceStrong = false ) {
		return self::singleton()->realGenerate( $bytes, $forceStrong );
	}

	/**
	 * Generate a run of (ideally) cryptographically random data and return
	 * it in hexadecimal string format.
	 * You can use MWCryptRand::wasStrong() if you wish to know if the source used
	 * was cryptographically strong.
	 *
	 * @param $chars int the number of hex chars of random data to generate
	 * @param $forceStrong bool Pass true if you want generate to prefer cryptographically
	 *                          strong sources of entropy even if reading from them may steal
	 *                          more entropy from the system than optimal.
	 * @return String Hexadecimal random data
	 */
	public static function generateHex( $chars, $forceStrong = false ) {
		return self::singleton()->realGenerateHex( $chars, $forceStrong );
	}

}
