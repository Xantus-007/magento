/* global PENCILOCALIZE */

(function($) {
	"use strict";
	var PENCI = PENCI || {};

	/* General functions
	 ---------------------------------------------------------------*/
	PENCI.general = function () {
		// Top search
		$( '#top-search a.search-click' ).on( 'click', function () {
			$( '.show-search' ).fadeToggle();
			$( '.show-search input.search-input' ).focus();
		} );

		// Go to top
		$( '.go-to-top, .penci-go-to-top-floating' ).on( 'click', function () {
			$( 'html, body' ).animate( { scrollTop: 0 }, 700 );
			return false;
		} );

		// Lazyload
		$('.penci-lazy').Lazy({
			effect: 'fadeIn',
			effectTime: 300,
			scrollDirection: 'both'
		});

		// Go to top button
		var $goto_button = $( '.penci-go-to-top-floating' );
		if ( $goto_button.length ) {
			$(document).scroll(function() {
				var y = $(this).scrollTop();
				if ( y > 300) {
					$goto_button.addClass('show-up');
				} else {
					$goto_button.removeClass('show-up');
				}
			} );
		}
		
		// Call back fitvid when click load more button on buddypress
		$( 'body.buddypress .activity .load-more a' ).on( 'click', function () {
			$(document).ajaxStop(function() {
			  $( ".container" ).fitVids();
			});
		} );
	}
	
	/* Cookie Law
	 ---------------------------------------------------------------*/
	PENCI.cookie = function () {
		var wrapCookie = '.penci-wrap-gprd-law',
			$wrapCookie = $( wrapCookie ),
			classAction = 'penci-wrap-gprd-law-close',
			penciCookieName = 'penci_law_footer_new';

		if( ! $wrapCookie.length ){
			return false;
		}

		var penciCookie = {
			set: function ( name, value ) {
				var date = new Date();
				date.setTime( date.getTime() + (31536000000) );
				var expires = "; expires=" + date.toGMTString();
				document.cookie = name + "=" + value + expires + "; path=/";
			},
			read: function ( name ) {
				var namePre = name + "=";
				var cookieSplit = document.cookie.split( ';' );
				for ( var i = 0; i < cookieSplit.length; i ++ ) {
					var cookie = cookieSplit[i];
					while ( cookie.charAt( 0 ) == ' ' ) {
						cookie = cookie.substring( 1, cookie.length );
					}
					if ( cookie.indexOf( namePre ) === 0 ) {
						return cookie.substring( namePre.length, cookie.length );
					}
				}
				return null;
			},
			erase: function ( name ) {
				this.set( name, "", - 1 );
			},
			exists: function ( name ) {
				return (
					this.read( name ) !== null
				);
			}
		};

		$wrapCookie.removeClass( 'penci-close-all' );
		if (! penciCookie.exists(penciCookieName) || ( penciCookie.exists(penciCookieName) && 1 == penciCookie.read(penciCookieName) ) ) {
			$wrapCookie.removeClass( classAction );
		}else {
			$wrapCookie.addClass( classAction );
		}

		$( '.penci-gprd-accept, .penci-gdrd-show' ).on( 'click', function ( e ) {
			e.preventDefault();

			var $this = $(this),
				$parent_law = $this.closest( wrapCookie );

			$parent_law.toggleClass(classAction);

			if ( $parent_law.hasClass( classAction ) ) {
				penciCookie.set( penciCookieName, '2' );
			}else {
				penciCookie.set( penciCookieName, '1' );
			}

			return false;
		} );
	}

	/* Sticky main navigation
	 ---------------------------------------------------------------*/
	PENCI.main_sticky = function () {
		if ( $().sticky && ! $( "nav#navigation" ).hasClass( 'penci-disable-sticky-nav' ) ) {
			var spaceTop = 0;
			if ( $( 'body' ).hasClass( 'admin-bar' ) ) {
				spaceTop = 32;
			}
			$( "nav#navigation" ).each( function () {
				$( this ).sticky( { topSpacing: spaceTop } );
			} );
		} // sticky
	}

	/* Fix margin headline
	 ----------------------------------------------------------------*/
	PENCI.fixheadline = function () {
		var $headline_title = $( '.penci-headline .headline-title' );
		if ( $headline_title.length ) {
			var headline_w = $headline_title.outerWidth() + 70;
			$('.penci-headline-posts' ).css( 'margin-left', headline_w + 'px' );
			$('.rtl .penci-headline-posts' ).css( 'margin-left', 0 + 'px' );
			$('.rtl .penci-headline-posts' ).css( 'margin-right', headline_w + 'px' );
		}
	}

	/* Homepage Featured Slider
	 ---------------------------------------------------------------*/
	PENCI.featured_slider = function () {
		if ( $().owlCarousel ) {
			$( '.featured-area .penci-owl-featured-area' ).each( function () {
				var $this = $( this ),
					$style = $this.data( 'style' ),
					$auto = false,
					$autotime = $this.data( 'autotime' ),
					$speed = $this.data( 'speed' ),
					$loop = $this.data('loop'),
					$item = 1,
					$nav = true,
					$dots = false,
					$rtl = false,
					$items_desktop = 1,
					$items_tablet = 1,
					$items_tabsmall = 1;

				if( $style === 'style-2' ) {
					$item = 2;
				} else if( $style === 'style-28' ) {
					$loop = true;
				}

				if( $('html').attr('dir') === 'rtl' ) {
					$rtl = true;
				}
				if ( $this.attr('data-auto') === 'true' ) {
					$auto = true;
				}
				if ( $this.attr('data-nav') === 'false' ) {
					$nav = false;
				}
				if ( $this.attr('data-dots') === 'true' ) {
					$dots = true;
				}
				if ( $this.attr('data-item') ) {
					$item = parseInt( $this.data('item') );
				}
				if ( $this.attr('data-desktop') ) {
					$items_desktop = parseInt( $this.data('desktop') );
				}
				if ( $this.attr('data-tablet') ) {
					$items_tablet = parseInt( $this.data('tablet') );
				}
				if ( $this.attr('data-tabsmall') ) {
					$items_tabsmall = parseInt( $this.data('tabsmall') );
				}

				var owl_args = {
					rtl               : $rtl,
					loop              : $loop,
					margin            : 0,
					items             : $item,
					navSpeed          : $speed,
					dotsSpeed         : $speed,
					nav               : $nav,
					slideBy           : $item,
					mouseDrag         : false,
					lazyLoad          : true,
					dots              : $dots,
					navText           : ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
					autoplay          : $auto,
					autoplayTimeout   : $autotime,
					autoplayHoverPause: true,
					autoplaySpeed     : $speed,
					responsive        : {
						0   : {
							items: 1
						},
						480 : {
							items  : $items_tabsmall,
							slideBy: $items_tabsmall
						},
						768 : {
							items  : $items_tablet,
							slideBy: $items_tablet
						},
						1170: {
							items  : $items_desktop,
							slideBy: $items_desktop
						}
					}
				}

				if( $style === 'style-2' ) {
					owl_args['center'] = true;
					owl_args['margin'] = 10;
					owl_args['autoWidth'] = true;
				} else if( $style === 'style-28' ) {
					owl_args['margin'] = 4;
					owl_args['items'] = 6;
					owl_args['autoWidth'] = true;
				} else if( $style === 'style-38' ) {
					owl_args['center'] = true;
					owl_args['margin'] = 5;
					owl_args['autoWidth'] = true;
				}

				$this.imagesLoaded( function() { $this.owlCarousel( owl_args ); } );
				
				$this.on('initialize.owl.carousel', function(event) {
					$this.closest('.featured-area').addClass('penci-featured-loaded');
				});

				if( $style === 'style-2' || $style === 'style-38' || $style === 'style-5' || $style === 'style-28' || $style === 'style-29' ) {
					$this.on( 'changed.owl.carousel', function ( event ) {
						$this.find( '.penci-lazy' ).Lazy( {
							effect: 'fadeIn',
							effectTime: 300,
							scrollDirection: 'both'
						} );
					} );
				}
			} );
		}	// if owlcarousel
	}

	/* Owl Slider General
	 ---------------------------------------------------------------*/
	PENCI.owl_slider = function () {
		if ( $().owlCarousel ) {
			$( '.penci-owl-carousel-slider' ).each( function () {
				var $this = $( this ),
					$auto = true,
					$dots = false,
					$nav = true,
					$loop = true,
					$rtl = false,
					$dataauto = $this.data( 'auto' ),
					$items_desktop = 1,
					$items_tablet = 1,
					$items_tabsmall = 1,
					$speed = 600,
					$item = 1,
					$autotime = 5000,
					$datalazy = false;

				if( $('html').attr('dir') === 'rtl' ) {
					$rtl = true;
				}
				if ( $this.attr('data-dots') ) {
					$dots = true;
				}
				if ( $this.attr('data-loop') ) {
					$loop = false;
				}
				if ( $this.attr('data-nav') ) {
					$nav = false;
				}
				if ( $this.attr('data-desktop') ) {
					$items_desktop = parseInt( $this.data('desktop') );
				}
				if ( $this.attr('data-tablet') ) {
					$items_tablet = parseInt( $this.data('tablet') );
				}
				if ( $this.attr('data-tabsmall') ) {
					$items_tabsmall = parseInt( $this.data('tabsmall') );
				}
				if ( $this.attr('data-speed') ) {
					$speed = parseInt( $this.data('speed') );
				}
				if ( $this.attr('data-autotime') ) {
					$autotime = parseInt( $this.data('autotime') );
				}
				if ( $this.attr('data-item') ) {
					$item = parseInt( $this.data('item') );
				}
				if ( $this.attr('data-lazy') ) {
					$datalazy = true;
				}

				var owl_args = {
					loop              : $loop,
					rtl               : $rtl,
					margin            : 0,
					items             : $item,
					slideBy           : $item,
					lazyLoad          : $datalazy,
					navSpeed          : $speed,
					dotsSpeed         : $speed,
					nav               : $nav,
					dots              : $dots,
					navText           : ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
					autoplay          : $dataauto,
					autoplayTimeout   : $autotime,
					autoHeight        : true,
					autoplayHoverPause: true,
					autoplaySpeed     : $speed,
					responsive        : {
						0   : {
							items  : 1,
							slideBy: 1
						},
						480 : {
							items  : $items_tabsmall,
							slideBy: $items_tabsmall
						},
						768 : {
							items  : $items_tablet,
							slideBy: $items_tablet
						},
						1170: {
							items  : $items_desktop,
							slideBy: $items_desktop
						}
					}
				};

				if ( $this.hasClass( 'penci-headline-posts' ) ) {
					owl_args['animateOut'] = 'slideOutUp';
					owl_args['animateIn'] = 'slideInUp';
				}

				$this.imagesLoaded( function() { $this.owlCarousel( owl_args ); } );

				$this.on('changed.owl.carousel', function(event) {
					$this.find( '.penci-lazy' ).Lazy( {
						effect: 'fadeIn',
						effectTime: 300,
						scrollDirection: 'both'
					} );
				});
			} );
		}	// if owlcarousel
	}

	/* Fitvids
	 ---------------------------------------------------------------*/
	PENCI.fitvids = function() {
		// Target your .container, .wrapper, .post, etc.
		$( ".container" ).fitVids();
	}

	/* Sticky sidebar
	 ----------------------------------------------------------------*/
	PENCI.sticky_sidebar = function() {
		if ( $().theiaStickySidebar ) {
			var top_margin = 90;
			if( $('body' ).hasClass('admin-bar') && $('body' ).hasClass('penci-vernav-enable') ){
				top_margin = 62;
			} else if( ! $('body' ).hasClass('admin-bar') && $('body' ).hasClass('penci-vernav-enable') ) {
				top_margin = 30;
			} else if( $('body' ).hasClass('admin-bar') && ! $('body' ).hasClass('penci-vernav-enable') ){
				top_margin = 122;
			}
			
			$('#main.penci-main-sticky-sidebar, #sidebar.penci-sticky-sidebar' ).theiaStickySidebar({
				// settings
				additionalMarginTop: top_margin
			});
		} // if sticky
	}

	/* Mega menu
	 ----------------------------------------------------------------*/
	PENCI.mega_menu = function () {
		$( '#navigation .penci-mega-child-categories a' ).mouseenter( function () {
			if ( !$( this ).hasClass( 'cat-active' ) ) {
				var $this = $( this ),
					$row_active = $this.data( 'id' ),
					$parentA = $this.parent().children( 'a' ),
					$parent = $this.closest( '.penci-megamenu' ),
					$rows = $this.closest( '.penci-megamenu' ).find( '.penci-mega-latest-posts' ).children( '.penci-mega-row' );
				$parentA.removeClass( 'cat-active' );
				$this.addClass( 'cat-active' );
				$rows.hide();
				$parent.find( '.' + $row_active ).fadeIn( '300' ).css( 'display', 'inline-block' );
			}
		} );
	}

	/* Mobile menu responsive
	 ----------------------------------------------------------------*/
	PENCI.mobile_menu = function () {
		// Add indicator
		$( '#sidebar-nav .menu li.menu-item-has-children > a' ).append( '<u class="indicator"><i class="fa fa-angle-down"></i></u>' );
		$( '#sidebar-nav .penci-mega-child-categories' ).closest('li.penci-mega-menu' ).children('a').append( '<u class="indicator"><i class="fa fa-angle-down"></i></u>' );

		// Toggle menu when click show/hide menu
		$( '#navigation .button-menu-mobile' ).on( 'click', function () {
			$( 'body' ).addClass( 'open-sidebar-nav' );
		} );

		// indicator click
		$( '#sidebar-nav .menu li a .indicator' ).on( 'click', function ( e ) {
			var $this = $( this );
			e.preventDefault();
			$this.children().toggleClass( 'fa-angle-up' );
			$this.parent().next().slideToggle( 'fast' );
		} );

		// Close sidebar nav
		$( '#close-sidebar-nav' ).on( 'click', function () {
			$( 'body' ).removeClass( 'open-sidebar-nav' );
		} );
	}
	
	PENCI.toggleMenuHumburger = function () {
		var $menuhumburger = $( '.penci-menu-hbg' );
		if ( $menuhumburger.length ) {
			var $body = $( 'body' ),
			$button = $( '.penci-vernav-toggle,.penci-menuhbg-toggle,#penci-close-hbg,.penci-menu-hbg-overlay' ),
			sidebarClass = 'penci-menuhbg-open';

			// Add indicator
			$( '.penci-menu-hbg .menu li.menu-item-has-children > a' ).append( '<u class="indicator"><i class="fa fa-angle-down"></i></u>' );
			$( '.penci-menu-hbg .penci-mega-child-categories' ).closest('li.penci-mega-menu' ).children('a').append( '<u class="indicator"><i class="fa fa-angle-down"></i></u>' );

			// indicator click
			$( '.penci-menu-hbg .menu li a .indicator' ).on( 'click', function ( e ) {
				var $this = $( this );
				e.preventDefault();
				$this.children().toggleClass( 'fa-angle-up' );
				$this.parent().next().slideToggle( 'fast' );
			} );

			// Click to show mobile menu
			$button.on( 'click', function ( e ) {
				e.preventDefault();

				if ( $body.hasClass( sidebarClass ) ) {
					$body.removeClass( sidebarClass );
					$button.removeClass( 'active' );

					return;
				}
				e.stopPropagation(); // Do not trigger click event on '.site' below
				$body.addClass( sidebarClass );
				$button.addClass( 'active' );
			} );
			
			// Scroll menu hamburger and callback lazyload
			$menuhumburger.on('scroll', function() {
				$('.penci-menu-hbg .penci-lazy').Lazy({
					effect: 'fadeIn',
					effectTime: 300,
					scrollDirection: 'both'
				});
			} );
		}
	}

	/* Light box
	 ----------------------------------------------------------------*/
	PENCI.lightbox = function () {
		if ( $().magnificPopup) {
			$( 'a[data-rel^="penci-gallery-image-content"], .penci-enable-lightbox .gallery-item a' ).magnificPopup( {
				type               : 'image',
				closeOnContentClick: true,
				closeBtnInside     : false,
				fixedContentPos    : true,
				image              : {
					verticalFit: true
				},
				gallery : {
					enabled: true
				},
				zoom               : {
					enabled : true,
					duration: 300
				}
			} );

			$( 'a[data-rel^="penci-gallery-bground-content"]' ).magnificPopup( {
				type               : 'image',
				closeOnContentClick: true,
				closeBtnInside     : false,
				fixedContentPos    : true,
				image              : {
					verticalFit: true
				},
				gallery : {
					enabled: true
				}
			} );


			// Enable lightbox videos
			$('.penci-other-layouts-lighbox').magnificPopup( {
				type: 'iframe',
				mainClass: 'mfp-fade',
				fixedContentPos    : true,
				closeBtnInside     : false,
				closeOnContentClick: true
			} );
		} // if magnificPopup exists
	}

	/* Masonry layout
	 ----------------------------------------------------------------*/
	PENCI.masonry = function() {
		$(window).load(function() {
			var $masonry_container = $( '.penci-masonry' );
			if ( $masonry_container.length ) {
				$masonry_container.each( function () {
					var $this = $( this );
					// initialize isotope
					$this.isotope( {
						itemSelector      : '.item-masonry',
						transitionDuration: '.55s',
						layoutMode        : 'masonry'
					} );
				} );
			}
		});
	}

	/* Video Background
	 ----------------------------------------------------------------*/
	PENCI.video_background = function() {
		var $penci_videobg = $( '#penci-featured-video-bg' );
		if ( $().mb_YTPlayer && $penci_videobg.length ) {
			$(window ).load( function() {
				$("#penci-featured-video-bg").mb_YTPlayer();
				setTimeout(function(){
					$('.featured-area').addClass( 'loaded-wait' ).append('<div class="overlay-video-click"></div>');
					$('.overlay-video-click').on( 'click', function () {
						var $this = $(this);
						if( !$this.hasClass( 'pause-video' ) ) {
							$this.addClass('pause-video');
							jQuery('#penci-featured-video-bg').pauseYTP();
						} else {
							$this.removeClass('pause-video');
							jQuery('#penci-featured-video-bg').playYTP();
						}
					});
				}, 4000);
			} );
		}
	}

	/* Portfolio
	 ----------------------------------------------------------------*/
	PENCI.portfolio = function () {
		var $penci_portfolio = $( '.penci-portfolio' );
		if ( $().isotope && $penci_portfolio.length ) {
			$( '.penci-portfolio' ).each( function () {
				var $this = $( this ),
					unique_id = $( this ).attr( 'id' ),
					DataFilter = null;

				if( typeof(portfolioDataJs) != "undefined" && portfolioDataJs !== null) {
					for ( var e in portfolioDataJs ) {

						if ( portfolioDataJs[e].instanceId == unique_id ) {
							var DataFilter = portfolioDataJs[e];
						}
					}
				}

				$( window ).load( function () {
					$this.isotope( {
						itemSelector    : '.portfolio-item',
						animationEngine : 'best-available',
						animationOptions: {
							duration: 250,
							queue   : false
						}
					} ); // isotope

					$this.addClass( 'loaded' );

					$('.portfolio-item .inner-item-portfolio').each( function () {
						var $this = $( this );
						$this.one( 'inview', function ( event, isInView, visiblePartX, visiblePartY ) {
							$this.addClass( 'animated' );
						} ); // inview
					} ); // each

					var location = window.location.hash.toString();
					if ( location.length ) {
						location = location.replace( '#', '' );
						location.match( /:/ );
						var Mlocation = location.match( /^([^:]+)/ )[1];
						location = location.replace( Mlocation + ":", "" );

						if ( location.length > 1 ) {

							var $termActive = $afilter.filter( '[data-term="' + location + '"]' ),
							    portfolioItem = $this.find( '.portfolio-item' ),
								$buttonLoadMore = $this.parent().find( '.penci-pagenavi-shortcode' );

							if ( $termActive.length ) {

								liFilter.removeClass( 'active' );
								$termActive.parent().addClass( 'active' );
								$this.isotope( { filter: '.penci-' + location } );

								var dataTerm = $termActive.data( "term" ),
									p = {};

								DataFilter.currentTerm = dataTerm;
								$.each( DataFilter.countByTerms, function ( t, e ) {
									p[t] = 0
								} );

								portfolioItem.each( function ( t, e ) {
									$.each( ($( e ).data( "terms" ) + "").split( " " ), function ( t, e ) {
										p[e] ++;
									} )
								} );

								var show_button = 'number' == typeof p[dataTerm] && p[dataTerm] == DataFilter.countByTerms[dataTerm];
								if ( $buttonLoadMore.length ){
									if ( portfolioItem.length !== DataFilter.count && ! show_button ) {
										$buttonLoadMore.show();
									}else{
										$buttonLoadMore.hide();
									}
								}
							}
						}
					}
				} ); // load

				// Filter items when filter link is clicked
				var $filter = $this.parent().find( '.penci-portfolio-filter' ),
					$afilter = $filter.find( 'a' ),
					liFilter = $filter.find( 'li' );

				liFilter.on( 'click', function () {

					var self = $( this ),
						term = self.find( 'a' ).data( "term" ),
						selector = self.find( "a" ).attr( 'data-filter' ),
						$e_dataTerm = $filter.find( 'a' ).filter( '[data-term="' + term + '"]' ),
						portfolioItem = $this.find( '.portfolio-item' ),
						$buttonLoadMore = $this.parent().find( '.penci-pagenavi-shortcode' ),
						scrollTop = $( window ).scrollTop();

					liFilter.removeClass( 'active' );
					self.addClass( 'active' );

					$this.parent().find( '.penci-ajax-more-button' ).attr( 'data-cat', term );

					$this.isotope( { filter: selector } );
					console.log(term);
					if ( $e_dataTerm.length ) {
						window.location.hash = "*" == term ? "" : term;

						$( window ).scrollTop( scrollTop );
					}

					var p = {};
					DataFilter.currentTerm = term;
					$.each( DataFilter.countByTerms, function ( t, e ) {
						p[t] = 0
					} );

					portfolioItem.each( function ( t, e ) {
						$.each( ($( e ).data( "terms" ) + "").split( " " ), function ( t, e ) {
							p[e] ++;
						} )
					} );

					var show_button = 'number' == typeof p[term] && p[term] == DataFilter.countByTerms[term];
					if ( $buttonLoadMore.length ){
						if ( portfolioItem.length !== DataFilter.count && ! show_button ) {
							$buttonLoadMore.show();
						}else{
							$buttonLoadMore.hide();
						}
					}

					return false;
				} );

				PENCI.portfolioLoadMore.loadMore( $this, DataFilter );
				PENCI.portfolioLoadMore.infinityScroll( DataFilter );

			} ); // each .penci-portfolio

		}	// end if isotope & portfolio


		var $btnLoadMore = $( '.penci-plf-loadmore' );
		if ( ! $().isotope || ! $btnLoadMore.length ) {
			return false;
		}
	}

	PENCI.portfolioLoadMore = {
		btnLoadMore : $( '.penci-plf-loadmore' ),
		loadMore: function( $pfl_wapper, DataFilter ){
			var self = this;
			$( 'body' ).on( 'click', '.penci-ajax-more-button', function ( event ) {
				self.actionLoadMore( $( this ),$pfl_wapper, DataFilter );
			} );
		},
		infinityScroll: function( DataFilter ){
			var self = this,
				$handle = $( '.penci-plf-loadmore' ),
				$button_load = $handle.find( '.penci-ajax-more-button' );

			if ( $handle.hasClass( 'penci-infinite-scroll' ) ) {
				$( window ).on( 'scroll', function () {

					var hT = $button_load.offset().top,
						hH = $button_load.outerHeight(),
						wH = $( window ).height(),
						wS = $( this ).scrollTop();

					if ( ( wS > ( hT + hH - wH ) ) && $button_load.length ) {
						var $pfl_wapper = $button_load.closest( '.penci-portfolio' );
						self.actionLoadMore( $button_load,$pfl_wapper, DataFilter );
					}
				} ).scroll();
			}
		},
		actionLoadMore: function ( $button_load, $pfl_wapper, DataFilter ) {
			if ( $button_load.hasClass( 'loading-portfolios' ) ) {
				return false;
			}

			$button_load.addClass( 'loading-portfolios' );

			var mesNoMore = $button_load.data( 'mes_no_more' ),
				mes = $button_load.data( 'mes' );

			DataFilter.pflShowIds = [];

			$button_load.closest('.wrapper-penci-portfolio').find( '.portfolio-item' ).each( function ( t, e ) {
				DataFilter.pflShowIds.push( $( e ).data( 'pflid' ) );
			} );

			var data = {
				action: 'penci_pfl_more_post_ajax',
				datafilter: DataFilter,
				nonce: ajax_var_more.nonce
			};
			$.post( ajax_var_more.url, data, function ( response ) {
				if ( ! response.data.items ) {
					$button_load.find( '.ajax-more-text' ).html( mesNoMore );
					$button_load.removeClass( 'loading-portfolios' );

					$button_load.closest( '.wrapper-penci-portfolio' ).find( '.penci-portfolio-filter li.active' ).addClass( 'loadmore-finish' );

					setTimeout( function () {
						$button_load.parent().parent().hide();
						$button_load.find( '.ajax-more-text' ).html( mes );
					}, 1200 );

					return false;
				}

				var $wrap_content = $button_load.closest( '.wrapper-penci-portfolio' ).find( '.penci-portfolio' ),
					$data = $( response.data.items );

				$wrap_content.find( '.inner-portfolio-posts' ).append( $data );
				$wrap_content.isotope( 'appended', $data ).imagesLoaded( function () {
					$wrap_content.isotope( 'layout' );
				} );

				$('.penci-lazy').Lazy({
					effect: 'fadeIn',
					effectTime: 300,
					scrollDirection: 'both'
				});

				$( ".container" ).fitVids();
				
				$( 'a[data-rel^="penci-gallery-image-content"]' ).magnificPopup( {
					type               : 'image',
					closeOnContentClick: true,
					closeBtnInside     : false,
					fixedContentPos    : true,
					image              : {
						verticalFit: true
					},
					gallery : {
						enabled: true
					},
					zoom               : {
						enabled : true,
						duration: 300
					}
				} );

				$wrap_content.addClass( 'loaded' );

				$('.portfolio-item .inner-item-portfolio').each( function () {
					var $this = $( this );
					$this.one( 'inview', function ( event, isInView, visiblePartX, visiblePartY ) {
						$this.addClass( 'animated' );
					} ); // inview
				} ); // each

				$button_load.removeClass( 'loading-portfolios' );
			} );

			$.ajax( {
				type: 'POST',
				dataType: 'html',
				url: ajax_var_more.url,
				data: 'datafilter=' + DataFilter + '&action=penci_pfl_more_post_ajax&nonce=' + ajax_var_more.nonce,
				success: function ( data ) {


				},
				error: function ( jqXHR, textStatus, errorThrown ) {
				}

			} );

		}
	}

	/* Gallery
	 ----------------------------------------------------------------*/
	PENCI.gallery = function () {
		var $justified_gallery = $( '.penci-post-gallery-container.justified' );
		var $masonry_gallery = $( '.penci-post-gallery-container.masonry' );
		if ( $().justifiedGallery && $justified_gallery.length ) {
			$( '.penci-post-gallery-container.justified' ).each( function () {
				var $this = $( this );
				$this.justifiedGallery( {
					rowHeight: $this.data( 'height' ),
					lastRow  : 'nojustify',
					margins  : $this.data( 'margin' ),
					randomize: false
				} );
			} ); // each .penci-post-gallery-container
		}

		if ( $().isotope && $masonry_gallery.length ) {

			$('.penci-post-gallery-container.masonry .item-gallery-masonry').each(function () {
				var $this = $(this);
				if ($this.attr('title')) {
					var $title = $this.attr('title');
					$this.children().append('<div class="caption">' + $title + '</div>');
				}
			});
		}

		$(window).load(function() {
			if ( $masonry_gallery.length ) {
				$masonry_gallery.each( function () {
					var $this = $( this );
					// initialize isotope
					$this.isotope( {
						itemSelector      : '.item-gallery-masonry',
						transitionDuration: '.55s',
						layoutMode        : 'masonry'
					} );

					$this.addClass( 'loaded' );

					$('.penci-post-gallery-container.masonry .item-gallery-masonry').each( function () {
						var $this = $( this );
						$this.one( 'inview', function ( event, isInView, visiblePartX, visiblePartY ) {
							$this.addClass( 'animated' );
						} ); // inview
					} ); // each

				} );
			}
		});
	},
	
	/* Jarallax
	 ----------------------------------------------------------------*/
	PENCI.Jarallax = function () {
		if ( ! $.fn.jarallax || ! $( '.penci-jarallax' ).length ) {
			return false;
		}
		$( '.penci-jarallax' ).each( function () {
			var $this = $( this ),
				$jarallaxArgs = {};

			console.log($this.data('video-src'));
			$this.imagesLoaded( { background: true }, function () {
				jarallax( $this, $jarallaxArgs );
			} );


		} );
	},
	
	/* Related Popup
	 ----------------------------------------------------------------*/
	PENCI.RelatedPopup = function () {
		if ( $( '.penci-rlt-popup' ).length ) {
			var rltpopup = $('.penci-rlt-popup'),
				rltclose = $('.penci-rlt-popup .penci-close-rltpopup');
			$(window).load(function() {
				$('.penci-flag-rlt-popup').bind('inview', function(event, isInView, visiblePartX, visiblePartY) {
					if ( ! rltpopup.hasClass( 'rltpopup-notshow-again' ) && isInView ) {
						rltpopup.addClass('rltpopup-show-up');
						
						rltclose.click(function(e) {
							e.preventDefault();
							rltpopup.removeClass('rltpopup-show-up').addClass('rltpopup-notshow-again');
						});
					}
				});
				rltclose.click(function(e) {
					e.preventDefault();
					rltpopup.removeClass('rltpopup-show-up').addClass('rltpopup-notshow-again');
				});
			});
		}
	}


	/* Init functions
	 ---------------------------------------------------------------*/
	$(document).ready(function() {
		PENCI.general();
		PENCI.cookie();
		PENCI.main_sticky();
		PENCI.fixheadline();
		PENCI.featured_slider();
		PENCI.owl_slider();
		PENCI.fitvids();
		PENCI.sticky_sidebar();
		PENCI.mega_menu();
		PENCI.mobile_menu();
		PENCI.toggleMenuHumburger();
		PENCI.lightbox();
		PENCI.masonry();
		PENCI.video_background();
		PENCI.portfolio();
		PENCI.gallery();
		PENCI.Jarallax();
		PENCI.RelatedPopup();
		$(window ).resize( function(){ PENCI.sticky_sidebar(); } );
	});

})(jQuery);	// EOF