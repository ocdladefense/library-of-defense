<?php
/**
 * Lod - Development theme for Lod
 * improvements.
 *
 * @todo document
 * @file
 * @ingroup Skins
 */



/**
 * SkinTemplate class for Vector skin
 * @ingroup Skins
 */
class SkinOcdla extends SkinTemplate {

	var $skinname = 'ocdla',
	$stylename = 'ocdla',
	$template = 'OcdlaTemplate';
	public $useHeadElement = true;

	/**
	 * Initializes output page and sets up skin-specific parameters
	 * @param $out OutputPage object to initialize
	 */
	public function initPage( OutputPage $out ) {
		global $wgLocalStylePath, $wgRequest, $LodUseHeadElement;
		$this->useHeadElemnt = $LodUseHeadElement;

		$tpl = $this->setupTemplate( $this->template, 'skins' );


		parent::initPage($out);

		// $tpl->set( 'title', $out->getPageTitle() );
		$tpl->set( 'title', 'My Title');
			$attrs = " lang=\"$escUserlang\" dir=\"$escUserdir\"";
			$tpl->set( 'userlangattributes', $attrs );

		// Append CSS which includes IE only behavior fixes for hover support -
		// this is better than including this in a CSS fille since it doesn't
		// wait for the CSS file to load before fetching the HTC file.
		$min = $wgRequest->getFuzzyBool( 'debug' ) ? '' : '.min';
		$out->addHeadItem( 'csshover',
			'<!--[if lt IE 7]><style type="text/css">body{behavior:url("' .
				htmlspecialchars( $wgLocalStylePath ) .
				"/{$this->stylename}/csshover{$min}.htc\")}</style><![endif]-->"
		);
		$out->addMeta('viewport','width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no');
		$out->addMeta( 'http:X-UA-Compatible', 'IE=Edge' );
		$out->addMeta("apple-mobile-web-app-capable","yes");
		$out->addMeta('apple-mobile-web-app-title','OCDLA');
		
		/**
		 * See also:
		 * http://stackoverflow.com/questions/19071414/change-the-color-of-the-ios-7-status-bar-in-safari-and-chrome
		 */
		$out->addMeta("apple-mobile-web-app-status-bar-style","black-translucent");
		/**
		 * Additional tags:
		 * <!-- Specify a launch screen image -->
				<link rel="apple-touch-startup-image" href="/launch.png">
				<!-- Change Status Bar appearance -->
				<meta name="apple-mobile-web-app-status-bar-style" content="black">
		*/

		$out->addLink(array('rel'=>'apple-touch-icon','href'=>"/apple-touch-icon-iphone.png"));
		$out->addLink(array('rel'=>'apple-touch-startup-image','href'=>"/apple-touch-launch.png"));
		$out->addStyle('/skins/ocdla/css/manifest-overrides.css', 'all');
		
		/*
		<link rel="apple-touch-icon" href="touch-icon-iphone.png">
		<link rel="apple-touch-icon" sizes="152x152" href="touch-icon-ipad.png">
		<link rel="apple-touch-icon" sizes="180x180" href="touch-icon-iphone-retina.png">
		<link rel="apple-touch-icon" sizes="167x167" href="touch-icon-ipad-retina.png">
		*/
		/**
		 * http://www.mediawiki.org/wiki/Manual:$wgRequest
		 */
		// $request = $out->getRequest();
		$out->addModuleStyles( 'skins.ocdla' );
		// $out->addModuleScripts( 'skins.lod' );
		$out->addModules( 'skins.ocdla.js' );
		
		if(in_array(strtolower($out->getPageTitle()),array('search results','search'))) {
			$out->addModules( 'skins.ocdla.js' );
		}

		$Resource_Loader = $out->getResourceLoader();
		/*
	protected function filterModules( $modules, $position = null, $type = ResourceLoaderModule::TYPE_COMBINED ){
		$resourceLoader = $this->getResourceLoader();
		$filteredModules = array();
		foreach( $modules as $val ){
			$module = $resourceLoader->getModule( $val );
			if( $module instanceof ResourceLoaderModule
				&& $module->getOrigin() <= $this->getAllowedModules( $type )
				&& ( is_null( $position ) || $module->getPosition() == $position ) )
			{
				$filteredModules[] = $val;
			}
		}
		return $filteredModules;
	}
		*/
		
		/**
		 * Additional script files
		 */
		// $out->addScriptFile('/path/to/additional/script/filename.ext');

		/**
		 * Additional styles
		 */
		$out->addStyle('/skins/lod/IE90Fixes.css?v=0.5', 'all','lt IE 9');
		// print $out->getScript();exit;
		
	}

	/**
	 * Load skin and user CSS files in the correct order
	 * fixes bug 22916
	 * @param $out OutputPage object
	 */
	 // parent is includes/SkinTemplate.php
	 // commented out 2013-03-16 to prevent legacy stylesheet's media queries from 
	 // interfering with mobile CSS
	function setupSkinUserCss( OutputPage $out )
	{
		parent::setupSkinUserCss( $out );
	}
}

/**
 * QuickTemplate class for Lod skin
 * @ingroup Skins
 */
class OcdlaTemplate extends BaseTemplate {

	/* Members */

	/**
	 * @var Skin Cached skin object
	 */
	var $skin;
	
	private $useOfflineManifest = true;

	/* Functions */

	/**
	 * Outputs the entire contents of the (X)HTML page
	 */
	public function execute() {
		global $wgLang, $wgStylePath, $wgVectorUseIconWatch, $wgRequest, $wgUser,
		$wgAuthOcdla_HostRedirect, $wgUseCustomContactForms;

		$this->skin = $this->data['skin'];
		
		$this->skinImagePath = $wgStylePath . '/ocdla/images';

		// Build additional attributes for navigation urls
		//$nav = $this->skin->buildNavigationUrls();
		$nav = $this->data['content_navigation'];

		if ( $wgVectorUseIconWatch ) {
			$mode = $this->skin->getTitle()->userIsWatching() ? 'unwatch' : 'watch';
			if ( isset( $nav['actions'][$mode] ) ) {
				$nav['views'][$mode] = $nav['actions'][$mode];
				$nav['views'][$mode]['class'] = rtrim( 'icon ' . $nav['views'][$mode]['class'], ' ' );
				$nav['views'][$mode]['primary'] = true;
				unset( $nav['actions'][$mode] );
			}
		}

		$xmlID = '';
		foreach ( $nav as $section => $links ) {
			foreach ( $links as $key => $link ) {
				if ( $section == 'views' && !( isset( $link['primary'] ) && $link['primary'] ) ) {
					$link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
				}

				$xmlID = isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID;
				$nav[$section][$key]['attributes'] =
					' id="' . Sanitizer::escapeId( $xmlID ) . '"';
				if ( $link['class'] ) {
					$nav[$section][$key]['attributes'] .=
						' class="' . htmlspecialchars( $link['class'] ) . '"';
					unset( $nav[$section][$key]['class'] );
				}
				if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ) {
					$nav[$section][$key]['key'] =
						Linker::tooltip( $xmlID );
				} else {
					$nav[$section][$key]['key'] =
						Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
				}
			}
		}
		$this->data['namespace_urls'] = $nav['namespaces'];
		$this->data['view_urls'] = $nav['views'];
		$this->data['action_urls'] = $nav['actions'];
		$this->data['variant_urls'] = $nav['variants'];

		// Reverse horizontally rendered navigation elements
		if ( $wgLang->isRTL() ) {
			$this->data['view_urls'] =
				array_reverse( $this->data['view_urls'] );
			$this->data['namespace_urls'] =
				array_reverse( $this->data['namespace_urls'] );
			$this->data['personal_urls'] =
				array_reverse( $this->data['personal_urls'] );
		}
		// Output HTML Page
		if($this->useOfflineManifest && $this->skin->getTitle() == 'OcdlaWebApp') {
			$head = str_replace('lang="en"','lang="en" manifest="cache.manifest"',$this->data['headelement']);
			$head = str_replace('&*','',$head);
			$head = str_replace('&amp;*','',$head);
			echo $head;
		}
		else {
			$this->html( 'headelement' );
		}
	?>
		<input type="checkbox" id="drawer-toggle" name="drawer-toggle" />
		<label for="drawer-toggle" id="drawer-toggle-label"></label>

		<header>OCDLA</header>
		<nav id="drawer">
			<div class="drawer-heading">
				<a data-doc-id="search-form" href="#">Search</a>
			</div>
			
			<ul id="drawer-contents">
				<div class="drawer-heading">Books</div>
				<div class="ocdlaTemplate" id="ns-template"><li class="book book-ns">{{nsName}}</li></div>
				<div class="ocdlaTemplate" id="page-template"><li><a data-doc-id="{{page_id}}" href="#">{{prettyTitle}}</a></li></div>
			</ul>
			
			<div id="drawer-seminars">
				<div class="drawer-heading">My Seminars</div>
				<ul id="seminars-content">
					<li class="book book-ns">Mission Possible: Defending Sex Cases</li>
				</ul>
			</div>
			
			<div id="drawer-pdfs">
				<div class="drawer-heading">My Downloads</div>
				<ul id="downloads-content">
					<li class="book book-ns">Felony Sentencing in Oregon</li>
					<li class="book book-ns">DUII Trial Notebook</li>
				</ul>
			</div>
		</nav>
		
    <div id="wrapper">
    <div id="masthead">
      	<a href='/' class="masthead_link">&nbsp;</a>
      	<div class="ocdla_link header_link">
      		<a href="//www.ocdla.org" title="Go to the OCDLA homepage">
      			<img src="<?php print $this->skinImagePath; ?>/ocdla_link.gif" alt="Go to the OCDLA website" />
      		</a>
      		<br />
      		<a href="//www.ocdla.org" title="Go to the OCDLA homepage">OCDLA HOME</a>
      	</div>
      	<div class="getinvolved_link header_link">
      		<a href="/Get_Involved" title="Get Involved">
      			<img src="<?php print $this->skinImagePath; ?>/get_involved.gif" alt="Get Inolved with the Library of Defense" />
      		</a>
      		<br />
      		<a href="/Get_Involved" title="Get Involved">GET INVOLVED</a>
      	</div>
      	<div class="masthead_filler">&nbsp;</div>
    </div>
    <div id="submast">
	    <ul class="submastlinks">
	      <li><a href="/">Main Entrance</a></li>
	  		<li><a href="/Blog:Main">Blog</a></li>
	  		<li><a href="/Blog:Case_Reviews">Case Reviews</a></li>
	  		<li><a href="/Public:Subscriptions">OCDLA Books Online</a></li>
	  		<li><a href="/Resources">Resources</a></li>
	  		<li><a href="/User:Ryan">Ryan Scott</a></li>
	  		<li><a href="/How_To_Edit">Edit This Site!</a></li>
	  		<?php if($wgUseCustomContactForms): ?>
					<li><a href="/Special:Contact_Form?type=suggest">Make a Suggestion</a></li>
					<li><a href="/Special:Contact_Form?type=issue">Report a Problem</a></li>
	  		<?php endif; ?>
	  		<?php if($wgUser->mId == 0) { ?>
	  			<!--<li><a href="/Special:UserLogin&returnto=Welcome_to_The_Library">Log In</a></li>
	  			-->
	  			<li id="header-login">
	  				<?php
	  					$requestServer = $wgAuthOcdla_HostRedirect;	
	  					$requestUrl = $wgRequest->getRequestURL();			
							$retURL = urlencode($requestServer.$requestUrl);
	  				?>
	  				<a href="https://auth.ocdla.org/login?retURL=<?php print $retURL ?>">
	  					Log In 
	  				</a>
	  			</li>
	  		<?php } ?>
	  	</ul>
  	</div>
    <div id="wiki-wrapper">
		<div id="mw-page-base" class="noprint"></div>
		<div id="mw-head-base" class="noprint"></div>
		<!-- header -->
		<!-- moved from below -->
		<div id="mw-head" class="noprint">
			<a href="/">
				<img src="<?php print $this->skinImagePath; ?>/book.png" style="margin:24px 5px" alt="A Book from the Library of Defense" />
			</a>
			<?php /*$this->renderNavigation( 'PERSONAL' );*/ ?>
			<div id="p-personal"></div>
			<div id="left-navigation">
				<?php $this->renderNavigation( array( 'NAMESPACES', 'VARIANTS', 'SEARCH' ) ); ?>
			</div>
			<div id="right-navigation">
				<?php $this->renderNavigation( array( 'VIEWS', 'ACTIONS') ); ?>
			</div>
		</div>
		<!-- /header -->
		<!-- panel -->

			<div id="mw-panel" class="noprint">
			<h3 class="mw-customtoggle-sections">
				Library Collections
			</h3>
			<div id="mw-customcollapsible-sections" class="noprint mw-collapsible">
				<?php $this->renderPortals( $this->data['sidebar'] ); ?>
			</div>
			</div>
		<!-- /panel -->
		<!-- content -->
		<div id="content">
			<a id="top"></a>
			<div id="mw-js-message" style="display:none;"<?php $this->html( 'userlangattributes' ) ?>></div>
			<?php if ( $this->data['sitenotice'] ): ?>
			<!-- sitenotice -->
			<div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div>
			<!-- /sitenotice -->
			<?php endif; ?>
			<!-- firstHeading -->
			<h1 id="firstHeading" class="firstHeading"><?php $this->html( 'title' ) ?></h1>
			<!-- /firstHeading -->
			<!-- bodyContent -->
			<div id="bodyContent">
				<?php if ( $this->data['isarticle'] ): ?>
				<!-- tagline -->
				<div id="siteSub"><?php $this->msg( 'tagline' ) ?></div>
				<!-- /tagline -->
				<?php endif; ?>
				<!-- subtitle -->
				<div id="contentSub"<?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
				<!-- /subtitle -->
				<?php if ( $this->data['undelete'] ): ?>
				<!-- undelete -->
				<div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
				<!-- /undelete -->
				<?php endif; ?>
				<?php if( $this->data['newtalk'] ): ?>
				<!-- newtalk -->
				<div class="usermessage"><?php $this->html( 'newtalk' )  ?></div>
				<!-- /newtalk -->
				<?php endif; ?>
				<?php if ( $this->data['showjumplinks'] ): ?>
				<!-- jumpto -->
				<div id="jump-to-nav">
					<?php $this->msg( 'jumpto' ) ?> <a href="#mw-head"><?php $this->msg( 'jumptonavigation' ) ?></a>,
					<a href="#p-search"><?php $this->msg( 'jumptosearch' ) ?></a>
				</div>
				<!-- /jumpto -->
				<?php endif; ?>
				<!-- bodycontent -->
				<?php $this->html( 'bodycontent' ) ?>
				<!-- /bodycontent -->
				<?php if ( $this->data['printfooter'] ): ?>
				<!-- printfooter -->
				<div class="printfooter">
				<?php $this->html( 'printfooter' ); ?>
				</div>
				<!-- /printfooter -->
				<?php endif; ?>
				<?php if ( $this->data['catlinks'] ): ?>
				<!-- catlinks -->
				<?php $this->html( 'catlinks' ); ?>
				<!-- /catlinks -->
				<?php endif; ?>
				<?php if ( $this->data['dataAfterContent'] ): ?>
				<!-- dataAfterContent -->
				<?php $this->html( 'dataAfterContent' ); ?>
				<!-- /dataAfterContent -->
				<?php endif; ?>
				<div class="visualClear"></div>
				<!-- debughtml -->
				<?php $this->html( 'debughtml' ); ?>
				<!-- /debughtml -->
			</div>
			<!-- /bodyContent -->
		</div>
		<!-- /content -->
		<!-- header -->
		<!-- moved above to appropriate region -->
		<?php
		/*<div id="mw-head" class="noprint">
			<a href="/">
				<img src="/images/book.png" style="margin:24px 5px" alt="A Book from the Library of Defense" />
			</a>
			<?php $this->renderNavigation( 'PERSONAL' ); ?>
			<div id="left-navigation">
				<?php $this->renderNavigation( array( 'NAMESPACES', 'VARIANTS', 'SEARCH' ) ); ?>
			</div>
			<div id="right-navigation">
				<?php $this->renderNavigation( array( 'VIEWS', 'ACTIONS') ); ?>
			</div>
		</div>
		*/
		?>
		<!-- /header -->
		<!-- panel -->
		<!--	<div id="mw-panel" class="noprint">
				<?php /*$this->renderPortals( $this->data['sidebar'] );*/ ?>
			</div>
		-->
		<!-- /panel -->
		<!-- footer -->
		<div id="footer"<?php $this->html( 'userlangattributes' ) ?>>
			<?php if( count( $this->getFooterLinks() ) > 0 ) {
				foreach( $this->getFooterLinks() as $category => $links ): ?>
				<ul id="footer-<?php echo $category ?>">
					<?php foreach( $links as $link ): ?>
						<li id="footer-<?php echo $category ?>-<?php echo $link ?>"><?php $this->html( $link ) ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endforeach; 
				} ?>
			<?php $footericons = $this->getFooterIcons("icononly");
			if ( count( $footericons ) > 0 ): ?>
				<ul id="footer-icons" class="noprint">
<?php			foreach ( $footericons as $blockName => $footerIcons ): ?>
					<li id="footer-<?php echo htmlspecialchars( $blockName ); ?>ico">
<?php				foreach ( $footerIcons as $icon ): ?>
						<?php echo $this->skin->makeFooterIcon( $icon ); ?>

<?php				endforeach; ?>
					</li>
<?php			endforeach; ?>
				</ul>
			<?php endif; ?>
			<div style="clear:both"></div>
		</div>
		<!-- /footer -->
		<!-- fixalpha -->
		<script type="<?php $this->text( 'jsmimetype' ) ?>"> if ( window.isMSIE55 ) fixalpha(); </script>
		<!-- /fixalpha -->
		<?php $this->printTrail(); ?>

  </div>
  </div>
	</body>
</html>
<?php
	}

	/**
	 * Render a series of portals
	 *
	 * @param $portals array
	 */
	private function renderPortals( $portals ) {
		// Force the rendering of the following portals
		if ( !isset( $portals['SEARCH'] ) ) {
			$portals['SEARCH'] = true;
		}
		if ( !isset( $portals['TOOLBOX'] ) ) {
			$portals['TOOLBOX'] = true;
		}
		if ( !isset( $portals['LANGUAGES'] ) ) {
			$portals['LANGUAGES'] = true;
		}
		// Render portals
		foreach ( $portals as $name => $content ) {
			if ( $content === false )
				continue;

			echo "\n<!-- {$name} -->\n";
			switch( $name ) {
				case 'SEARCH':
					break;
				case 'TOOLBOX':
					$this->renderPortal( 'tb', $this->getToolbox(), 'toolbox', 'SkinTemplateToolboxEnd' );
					break;
				case 'LANGUAGES':
					if ( $this->data['language_urls'] ) {
						$this->renderPortal( 'lang', $this->data['language_urls'], 'otherlanguages' );
					}
					break;
				default:
					$this->renderPortal( $name, $content );
				break;
			}
			echo "\n<!-- /{$name} -->\n";
		}

	}

	private function renderPortal( $name, $content, $msg = null, $hook = null ) {
		if ( !isset( $msg ) ) {
			$msg = $name;
		}
		?>
<div class="portal" id='<?php echo Sanitizer::escapeId( "p-$name" ) ?>'<?php echo Linker::tooltip( 'p-' . $name ) ?>>
	<h5<?php $this->html( 'userlangattributes' ) ?>><?php $msgObj = wfMessage( $msg ); echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $msg ); ?></h5>
	<div class="body">
<?php
		if ( is_array( $content ) && count( $content ) > 0 ): ?>
		<ul>
<?php
			foreach( $content as $key => $val ): ?>
			<?php echo $this->makeListItem( $key, $val ); ?>

<?php
			endforeach;
			if ( isset( $hook ) ) {
				wfRunHooks( $hook, array( &$this, true ) );
			}
			?>
		</ul>
<?php
		else: ?>
		<?php echo $content; /* Allow raw HTML block to be defined by extensions */ ?>
<?php
		endif; ?>
	</div>
</div>
<?php
	}

	/**
	 * Render one or more navigations elements by name, automatically reversed
	 * when UI is in RTL mode
	 */
	private function renderNavigation( $elements ) {
		global $wgVectorUseSimpleSearch, $wgVectorShowVariantName, $wgUser, $wgLang;

		// If only one element was given, wrap it in an array, allowing more
		// flexible arguments
		if ( !is_array( $elements ) ) {
			$elements = array( $elements );
		// If there's a series of elements, reverse them when in RTL mode
		} elseif ( $wgLang->isRTL() ) {
			$elements = array_reverse( $elements );
		}
		// Render elements
		foreach ( $elements as $name => $element ) {
			echo "\n<!-- {$name} -->\n";
			switch ( $element ) {
				case 'NAMESPACES':
?>
<div id="p-namespaces" class="vectorTabs<?php if ( count( $this->data['namespace_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
	<h5><?php $this->msg( 'namespaces' ) ?></h5>
	<ul<?php $this->html( 'userlangattributes' ) ?>>
		<?php foreach ( $this->data['namespace_urls'] as $link ): ?>
			<li <?php echo $link['attributes'] ?>><span><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></span></li>
		<?php endforeach; ?>
	</ul>
</div>
<?php
				break;
				case 'VARIANTS':
?>
<div id="p-variants" class="vectorMenu<?php if ( count( $this->data['variant_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
	<?php if ( $wgVectorShowVariantName ): ?>
		<h4>
		<?php foreach ( $this->data['variant_urls'] as $link ): ?>
			<?php if ( stripos( $link['attributes'], 'selected' ) !== false ): ?>
				<?php echo htmlspecialchars( $link['text'] ) ?>
			<?php endif; ?>
		<?php endforeach; ?>
		</h4>
	<?php endif; ?>
	<h5><span><?php $this->msg( 'variants' ) ?></span><a href="#"></a></h5>
	<div class="menu">
		<ul<?php $this->html( 'userlangattributes' ) ?>>
			<?php foreach ( $this->data['variant_urls'] as $link ): ?>
				<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php
				break;
				case 'VIEWS':
?>
<div id="p-views" class="vectorTabs<?php if ( count( $this->data['view_urls'] ) == 0 ) { echo ' emptyPortlet'; } ?>">
	<h5><?php $this->msg('views') ?></h5>
	<ul<?php $this->html('userlangattributes') ?>>
		<?php foreach ( $this->data['view_urls'] as $link ): ?>
			<li<?php echo $link['attributes'] ?>><span><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php
				// $link['text'] can be undefined - bug 27764
				if ( array_key_exists( 'text', $link ) ) {
					echo array_key_exists( 'img', $link ) ?  '<img src="' . $link['img'] . '" alt="' . $link['text'] . '" />' : htmlspecialchars( $link['text'] );
				}
				?></a></span></li>
		<?php endforeach; ?>
	</ul>
</div>
<?php
				break;
				case 'ACTIONS':
?>
<div id="p-cactions" class="vectorMenu<?php if ( count( $this->data['action_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
	<h5><span><?php $this->msg( 'actions' ) ?></span><a href="#"></a></h5>
	<div class="menu">
		<ul<?php $this->html( 'userlangattributes' ) ?>>
			<?php foreach ( $this->data['action_urls'] as $link ): ?>
				<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php
				break;
				case 'PERSONAL':
?>
<div id="p-personal" class="<?php if ( count( $this->data['personal_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
	<h5><?php $this->msg( 'personaltools' ) ?></h5>
	<ul<?php $this->html( 'userlangattributes' ) ?>>
<?php			foreach( $this->getPersonalTools() as $key => $item ) { ?>
		<?php echo $this->makeListItem( $key, $item ); ?>

<?php			} ?>
	</ul>
</div>
<?php
				break;
				case 'SEARCH':
?>
<div id="p-search">
	<h5<?php $this->html( 'userlangattributes' ) ?>><label for="searchInput"><?php $this->msg( 'search' ) ?></label></h5>
	<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
		<input type='hidden' name="title" value="<?php $this->text( 'searchtitle' ) ?>"/>
		<?php if ( true ): ?>
		<div id="simpleSearch">
			<?php if ( $this->data['rtl'] ): ?>
			<?php echo $this->makeSearchButton( 'image', array( 'id' => 'searchButton', 'src' => $this->skin->getSkinStylePath( 'images/search-rtl.png' ) ) ); ?>
			<?php endif; ?>
			<?php echo $this->makeSearchInput( array( 'id' => 'searchInput', 'type' => 'text' ) ); ?>
			<?php if ( !$this->data['rtl'] ): ?>
			<?php echo $this->makeSearchButton( 'image', array( 'id' => 'searchButton', 'src' => $this->skin->getSkinStylePath( 'images/search-ltr.png' ) ) ); ?>
			<?php endif; ?>
		</div>
		<?php else: ?>
		<?php echo $this->makeSearchInput( array( 'id' => 'searchInput' ) ); ?>
		<?php echo $this->makeSearchButton( 'go', array( 'id' => 'searchGoButton', 'class' => 'searchButton' ) ); ?>
		<?php echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton' ) ); ?>
		<?php endif; ?>
	</form>
</div>
<?php

				break;
			}
			echo "\n<!-- /{$name} -->\n";
		}
	}
}