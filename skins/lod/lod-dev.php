<?php
	function outputPage( OutputPage $out=null ) {
		global $wgContLang;
		global $wgScript, $wgStylePath;
		global $wgMimeType, $wgJsMimeType;
		global $wgXhtmlDefaultNamespace, $wgXhtmlNamespaces, $wgHtml5Version;
		global $wgDisableCounters, $wgSitename, $wgLogo, $wgHideInterlanguageLinks;
		global $wgMaxCredits, $wgShowCreditsIfMax;
		global $wgPageShowWatchingUsers;
		global $wgArticlePath, $wgScriptPath, $wgServer;

		wfProfileIn( __METHOD__ );
		Profiler::instance()->setTemplated( true );

		$oldContext = null;
		if ( $out !== null ) {
			// @todo Add wfDeprecated in 1.20
			$oldContext = $this->getContext();
			$this->setContext( $out->getContext() );
		}

		$out = $this->getOutput();
		$request = $this->getRequest();
		$user = $this->getUser();
		$title = $this->getTitle();

		wfProfileIn( __METHOD__ . '-init' );
		$this->initPage( $out );

		$tpl = $this->setupTemplate( $this->template, 'skins' );
		wfProfileOut( __METHOD__ . '-init' );

		wfProfileIn( __METHOD__ . '-stuff' );
		$this->thispage = $title->getPrefixedDBkey();
		$this->titletxt = $title->getPrefixedText();
		$this->userpage = $user->getUserPage()->getPrefixedText();
		$query = array();
		if ( !$request->wasPosted() ) {
			$query = $request->getValues();
			unset( $query['title'] );
			unset( $query['returnto'] );
			unset( $query['returntoquery'] );
		}
		$this->thisquery = wfArrayToCGI( $query );
		$this->loggedin = $user->isLoggedIn();
		$this->username = $user->getName();

		if ( $this->loggedin || $this->showIPinHeader() ) {
			$this->userpageUrlDetails = self::makeUrlDetails( $this->userpage );
		} else {
			# This won't be used in the standard skins, but we define it to preserve the interface
			# To save time, we check for existence
			$this->userpageUrlDetails = self::makeKnownUrlDetails( $this->userpage );
		}

		wfProfileOut( __METHOD__ . '-stuff' );

		wfProfileIn( __METHOD__ . '-stuff-head' );
		if ( !$this->useHeadElement ) {
			$tpl->set( 'pagecss', false );
			$tpl->set( 'usercss', false );

			$tpl->set( 'userjs', false );
			$tpl->set( 'userjsprev', false );

			$tpl->set( 'jsvarurl', false );

			$tpl->setRef( 'xhtmldefaultnamespace', $wgXhtmlDefaultNamespace );
			$tpl->set( 'xhtmlnamespaces', $wgXhtmlNamespaces );
			$tpl->set( 'html5version', $wgHtml5Version );
			$tpl->set( 'headlinks', $out->getHeadLinks() );
			$tpl->set( 'csslinks', $out->buildCssLinks() );
			$tpl->set( 'pageclass', $this->getPageClasses( $title ) );
			$tpl->set( 'skinnameclass', ( 'skin-' . Sanitizer::escapeClass( $this->getSkinName() ) ) );
		}
		wfProfileOut( __METHOD__ . '-stuff-head' );

		wfProfileIn( __METHOD__ . '-stuff2' );
		$tpl->set( 'title', $out->getPageTitle() );
		$tpl->set( 'pagetitle', $out->getHTMLTitle() );
		$tpl->set( 'displaytitle', $out->mPageLinkTitle );

		$tpl->setRef( 'thispage', $this->thispage );
		$tpl->setRef( 'titleprefixeddbkey', $this->thispage );
		$tpl->set( 'titletext', $title->getText() );
		$tpl->set( 'articleid', $title->getArticleID() );

		$tpl->set( 'isarticle', $out->isArticle() );

		$subpagestr = $this->subPageSubtitle();
		if ( $subpagestr !== '' ) {
			$subpagestr = '<span class="subpages">' . $subpagestr . '</span>';
		}
		$tpl->set( 'subtitle',  $subpagestr . $out->getSubtitle() );

		$undelete = $this->getUndeleteLink();
		if ( $undelete === '' ) {
			$tpl->set( 'undelete', '' );
		} else {
			$tpl->set( 'undelete', '<span class="subpages">' . $undelete . '</span>' );
		}

		$tpl->set( 'catlinks', $this->getCategories() );
		if( $out->isSyndicated() ) {
			$feeds = array();
			foreach( $out->getSyndicationLinks() as $format => $link ) {
				$feeds[$format] = array(
					'text' => $this->msg( "feed-$format" )->text(),
					'href' => $link
				);
			}
			$tpl->setRef( 'feeds', $feeds );
		} else {
			$tpl->set( 'feeds', false );
		}

		$tpl->setRef( 'mimetype', $wgMimeType );
		$tpl->setRef( 'jsmimetype', $wgJsMimeType );
		// $tpl->set( 'charset', 'UTF-8' );
		$tpl->set( 'charset', 'utf-8' );
		$tpl->setRef( 'wgScript', $wgScript );
		$tpl->setRef( 'skinname', $this->skinname );
		$tpl->set( 'skinclass', get_class( $this ) );
		$tpl->setRef( 'skin', $this );
		$tpl->setRef( 'stylename', $this->stylename );
		$tpl->set( 'printable', $out->isPrintable() );
		$tpl->set( 'handheld', $request->getBool( 'handheld' ) );
		$tpl->setRef( 'loggedin', $this->loggedin );
		$tpl->set( 'notspecialpage', !$title->isSpecialPage() );
		/* XXX currently unused, might get useful later
		$tpl->set( 'editable', ( !$title->isSpecialPage() ) );
		$tpl->set( 'exists', $title->getArticleID() != 0 );
		$tpl->set( 'watch', $user->isWatched( $title ) ? 'unwatch' : 'watch' );
		$tpl->set( 'protect', count( $title->isProtected() ) ? 'unprotect' : 'protect' );
		$tpl->set( 'helppage', $this->msg( 'helppage' )->text() );
		*/
		$tpl->set( 'searchaction', $this->escapeSearchLink() );
		$tpl->set( 'searchtitle', SpecialPage::getTitleFor( 'Search' )->getPrefixedDBKey() );
		$tpl->set( 'search', trim( $request->getVal( 'search' ) ) );
		$tpl->setRef( 'stylepath', $wgStylePath );
		$tpl->setRef( 'articlepath', $wgArticlePath );
		$tpl->setRef( 'scriptpath', $wgScriptPath );
		$tpl->setRef( 'serverurl', $wgServer );
		$tpl->setRef( 'logopath', $wgLogo );
		$tpl->setRef( 'sitename', $wgSitename );

		$userLang = $this->getLanguage();
		$userLangCode = $userLang->getHtmlCode();
		$userLangDir  = $userLang->getDir();

		$tpl->set( 'lang', $userLangCode );
		$tpl->set( 'dir', $userLangDir );
		$tpl->set( 'rtl', $userLang->isRTL() );

		$tpl->set( 'capitalizeallnouns', $userLang->capitalizeAllNouns() ? ' capitalize-all-nouns' : '' );
		$tpl->set( 'showjumplinks', $user->getOption( 'showjumplinks' ) );
		$tpl->set( 'username', $this->loggedin ? $this->username : null );
		$tpl->setRef( 'userpage', $this->userpage );
		$tpl->setRef( 'userpageurl', $this->userpageUrlDetails['href'] );
		$tpl->set( 'userlang', $userLangCode );

		// Users can have their language set differently than the
		// content of the wiki. For these users, tell the web browser
		// that interface elements are in a different language.
		$tpl->set( 'userlangattributes', '' );
		$tpl->set( 'specialpageattributes', '' ); # obsolete

		if ( $userLangCode !== $wgContLang->getHtmlCode() || $userLangDir !== $wgContLang->getDir() ) {
			$escUserlang = htmlspecialchars( $userLangCode );
			$escUserdir = htmlspecialchars( $userLangDir );
			// Attributes must be in double quotes because htmlspecialchars() doesn't
			// escape single quotes
			$attrs = " lang=\"$escUserlang\" dir=\"$escUserdir\"";
			$tpl->set( 'userlangattributes', $attrs );
		}

		wfProfileOut( __METHOD__ . '-stuff2' );

		wfProfileIn( __METHOD__ . '-stuff3' );
		$tpl->set( 'newtalk', $this->getNewtalks() );
		$tpl->set( 'logo', $this->logoText() );

		$tpl->set( 'copyright', false );
		$tpl->set( 'viewcount', false );
		$tpl->set( 'lastmod', false );
		$tpl->set( 'credits', false );
		$tpl->set( 'numberofwatchingusers', false );
		if ( $out->isArticle() && $title->exists() ) {
			if ( $this->isRevisionCurrent() ) {
				if ( !$wgDisableCounters ) {
					$viewcount = $this->getWikiPage()->getCount();
					if ( $viewcount ) {
						$tpl->set( 'viewcount', $this->msg( 'viewcount' )->numParams( $viewcount )->parse() );
					}
				}

				if ( $wgPageShowWatchingUsers ) {
					$dbr = wfGetDB( DB_SLAVE );
					$num = $dbr->selectField( 'watchlist', 'COUNT(*)',
						array( 'wl_title' => $title->getDBkey(), 'wl_namespace' => $title->getNamespace() ),
						__METHOD__
					);
					if ( $num > 0 ) {
						$tpl->set( 'numberofwatchingusers',
							$this->msg( 'number_of_watching_users_pageview' )->numParams( $num )->parse()
						);
					}
				}

				if ( $wgMaxCredits != 0 ) {
					$tpl->set( 'credits', Action::factory( 'credits', $this->getWikiPage(),
						$this->getContext() )->getCredits( $wgMaxCredits, $wgShowCreditsIfMax ) );
				} else {
					$tpl->set( 'lastmod', $this->lastModified() );
				}
			}
			$tpl->set( 'copyright', $this->getCopyright() );
		}
		wfProfileOut( __METHOD__ . '-stuff3' );

		wfProfileIn( __METHOD__ . '-stuff4' );
		$tpl->set( 'copyrightico', $this->getCopyrightIcon() );
		$tpl->set( 'poweredbyico', $this->getPoweredBy() );
		$tpl->set( 'disclaimer', $this->disclaimerLink() );
		$tpl->set( 'privacy', $this->privacyLink() );
		$tpl->set( 'about', $this->aboutLink() );

		$tpl->set( 'footerlinks', array(
			'info' => array(
				'lastmod',
				'viewcount',
				'numberofwatchingusers',
				'credits',
				'copyright',
			),
			'places' => array(
				'privacy',
				'about',
				'disclaimer',
			),
		) );

		global $wgFooterIcons;
		$tpl->set( 'footericons', $wgFooterIcons );
		foreach ( $tpl->data['footericons'] as $footerIconsKey => &$footerIconsBlock ) {
			if ( count( $footerIconsBlock ) > 0 ) {
				foreach ( $footerIconsBlock as &$footerIcon ) {
					if ( isset( $footerIcon['src'] ) ) {
						if ( !isset( $footerIcon['width'] ) ) {
							$footerIcon['width'] = 88;
						}
						if ( !isset( $footerIcon['height'] ) ) {
							$footerIcon['height'] = 31;
						}
					}
				}
			} else {
				unset( $tpl->data['footericons'][$footerIconsKey] );
			}
		}

		$tpl->set( 'sitenotice', $this->getSiteNotice() );
		$tpl->set( 'bottomscripts', $this->bottomScripts() );
		$tpl->set( 'printfooter', $this->printSource() );

		# An ID that includes the actual body text; without categories, contentSub, ...
		$realBodyAttribs = array( 'id' => 'mw-content-text' );

		# Add a mw-content-ltr/rtl class to be able to style based on text direction
		# when the content is different from the UI language, i.e.:
		# not for special pages or file pages AND only when viewing AND if the page exists
		# (or is in MW namespace, because that has default content)
		if ( !in_array( $title->getNamespace(), array( NS_SPECIAL, NS_FILE ) ) &&
			in_array( $request->getVal( 'action', 'view' ), array( 'view', 'historysubmit' ) ) &&
			( $title->exists() || $title->getNamespace() == NS_MEDIAWIKI ) ) {
			$pageLang = $title->getPageViewLanguage();
			$realBodyAttribs['lang'] = $pageLang->getHtmlCode();
			$realBodyAttribs['dir'] = $pageLang->getDir();
			$realBodyAttribs['class'] = 'mw-content-'.$pageLang->getDir();
		}

		$out->mBodytext = Html::rawElement( 'div', $realBodyAttribs, $out->mBodytext );
		$tpl->setRef( 'bodytext', $out->mBodytext );

		# Language links
		$language_urls = array();

		if ( !$wgHideInterlanguageLinks ) {
			foreach( $out->getLanguageLinks() as $l ) {
				$tmp = explode( ':', $l, 2 );
				$class = 'interwiki-' . $tmp[0];
				unset( $tmp );
				$nt = Title::newFromText( $l );
				if ( $nt ) {
					$ilLangName = Language::fetchLanguageName( $nt->getInterwiki() );
					if ( strval( $ilLangName ) === '' ) {
						$ilLangName = $l;
					} else {
						$ilLangName = $this->getLanguage()->ucfirst( $ilLangName );
					}
					$language_urls[] = array(
						'href' => $nt->getFullURL(),
						'text' => $ilLangName,
						'title' => $nt->getText(),
						'class' => $class,
						'lang' => $nt->getInterwiki(),
						'hreflang' => $nt->getInterwiki(),
					);
				}
			}
		}
		if ( count( $language_urls ) ) {
			$tpl->setRef( 'language_urls', $language_urls );
		} else {
			$tpl->set( 'language_urls', false );
		}
		wfProfileOut( __METHOD__ . '-stuff4' );

		wfProfileIn( __METHOD__ . '-stuff5' );
		# Personal toolbar
		$tpl->set( 'personal_urls', $this->buildPersonalUrls() );
		$content_navigation = $this->buildContentNavigationUrls();
		$content_actions = $this->buildContentActionUrls( $content_navigation );
		$tpl->setRef( 'content_navigation', $content_navigation );
		$tpl->setRef( 'content_actions', $content_actions );

		$tpl->set( 'sidebar', $this->buildSidebar() );
		$tpl->set( 'nav_urls', $this->buildNavUrls() );

		// Set the head scripts near the end, in case the above actions resulted in added scripts
		if ( $this->useHeadElement ) {
			$tpl->set( 'headelement', $out->headElement( $this ) );
		} else {
			$tpl->set( 'headscripts', $out->getHeadScripts() . $out->getHeadItems() );
		}

		$tpl->set( 'debug', '' );
		$tpl->set( 'debughtml', $this->generateDebugHTML() );
		$tpl->set( 'reporttime', wfReportTime() );

		// original version by hansm
		if( !wfRunHooks( 'SkinTemplateOutputPageBeforeExec', array( &$this, &$tpl ) ) ) {
			wfDebug( __METHOD__ . ": Hook SkinTemplateOutputPageBeforeExec broke outputPage execution!\n" );
		}

		// Set the bodytext to another key so that skins can just output it on it's own
		// and output printfooter and debughtml separately
		$tpl->set( 'bodycontent', $tpl->data['bodytext'] );

		// Append printfooter and debughtml onto bodytext so that skins that were already
		// using bodytext before they were split out don't suddenly start not outputting information
		$tpl->data['bodytext'] .= Html::rawElement( 'div', array( 'class' => 'printfooter' ), "\n{$tpl->data['printfooter']}" ) . "\n";
		$tpl->data['bodytext'] .= $tpl->data['debughtml'];

		// allow extensions adding stuff after the page content.
		// See Skin::afterContentHook() for further documentation.
		$tpl->set( 'dataAfterContent', $this->afterContentHook() );
		wfProfileOut( __METHOD__ . '-stuff5' );

		// execute template
		wfProfileIn( __METHOD__ . '-execute' );
		$res = $tpl->execute();
		wfProfileOut( __METHOD__ . '-execute' );

		// result may be an error
		$this->printOrError( $res );

		if ( $oldContext ) {
			$this->setContext( $oldContext );
		}
		wfProfileOut( __METHOD__ );
	}