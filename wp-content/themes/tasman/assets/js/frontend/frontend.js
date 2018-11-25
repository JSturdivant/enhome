/* global Tasmanl10n */
( function( $ ) {

	var tasman = tasman || {};

	tasman.init = function() {

		tasman.$body 	= $( document.body );
		tasman.$window = $( window );
		tasman.$html 	= $( 'html' );
		tasman.$footerWidgets = $( '.footer-widgets' );

		this.inlineSVG();
		this.fitVids();
		this.responsiveTable();
		this.smoothScroll();
		this.stickit();
		this.subMenuToggle();
		this.gallery();
		this.masonry();
		this.returnToTop();
		this.bind();

	};

	tasman.supportsInlineSVG = function() {

		var div = document.createElement( 'div' );
		div.innerHTML = '<svg/>';
		return 'http://www.w3.org/2000/svg' === ( 'undefined' !== typeof SVGRect && div.firstChild && div.firstChild.namespaceURI );

	};

	tasman.inlineSVG = function() {

		if ( true === tasman.supportsInlineSVG() ) {
			document.documentElement.className = document.documentElement.className.replace( /(\s*)no-svg(\s*)/, '$1svg$2' );
		}

	};

	tasman.fitVids = function() {

		$( '#page' ).fitVids({
			customSelector: 'iframe[src^="https://videopress.com"]'
		});

	};

	tasman.responsiveTable = function() {
		$( 'table' ).wrap( '<div class="table-responsive"></div>' );
	};

	tasman.smoothScroll = function() {

		var $smoothScroll = $( 'a[href*="#content"], a[href*="#site-navigation"], a[href*="#secondary"], a[href*="#page"]' );

		$smoothScroll.on( 'click', function(event) {
	        // On-page links
	        if (
	            location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') &&
	            location.hostname === this.hostname
	        ) {
	            // Figure out element to scroll to
	            var target = $(this.hash);
	            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
	            // Does a scroll target exist?
	            if (target.length) {
	                // Only prevent default if animation is actually gonna happen
	                event.preventDefault();
	                $('html, body').animate({
	                    scrollTop: target.offset().top
	                }, 500, function() {
	                    // Callback after animation
	                    // Must change focus!
	                    var $target = $(target);
	                    $target.focus();
	                    if ($target.is(':focus')) { // Checking if the target was focused
	                        return false;
	                    } else {
	                        $target.attr( 'tabindex', '-1' ); // Adding tabindex for elements not focusable
	                        $target.focus(); // Set focus again
	                    }
	                });
	            }
	        }
		});

	};

	tasman.stickit = function() {

		var $mainNav = $( '.main-navigation' );

		$mainNav.stickit({
			screenMinWidth: 782,
			zIndex: 5
		});

	};

	tasman.subMenuToggle = function() {

		var $subMenu = $( '.main-navigation .sub-menu' );

		$subMenu.before( '<button class="sub-menu-toggle" role="button" aria-expanded="false">' + Tasmanl10n.expandMenu + Tasmanl10n.collapseMenu + Tasmanl10n.subNav + '</button>' );
		$( '.sub-menu-toggle' ).on( 'click', function( e ) {

			e.preventDefault();

			var $this = $( this );
			$this.attr( 'aria-expanded', function( index, value ) {
				return 'false' === value ? 'true' : 'false';
			});

			// Add class to toggled menu
			$this.toggleClass( 'toggled' );
			$this.next( '.sub-menu' ).slideToggle( 0 );

		});

	};

	tasman.gallery = function() {

		var $entryGallery = $( '.entry-gallery' );

		$entryGallery.each( function() {

			var galleryID = $(this).attr('id');

			$( '#'+ galleryID ).justifiedGallery({
				rowHeight : 150,
				margins : 5,
				lastRow: 'justify'
			});

			$( '#'+ galleryID ).magnificPopup({
				delegate: 'a',
				type: 'image',
				closeOnContentClick: false,
				closeBtnInside: false,
				mainClass: 'mfp-with-zoom mfp-img-mobile',
				image: {
					verticalFit: true,
					titleSrc: function(item) {
						return item.el.attr('title') + ' &middot; <a class="image-source-link" href="'+item.el.attr('data-source')+'" target="_blank">'+ Tasmanl10n.imageSrc +'</a>';
					}
				},
				gallery: {
					enabled: true
				},
				zoom: {
					enabled: true,
					duration: 300, // don't foget to change the duration also in CSS
					opener: function(element) {
						return element.find('img');
					}
				}

			});
		});

	};

	tasman.masonry = function() {

		tasman.$footerWidgets.masonry({
			itemSelector: '.widget',
			columnWidth: '.widget'
		});

		$( window ).load(function(){
	        tasman.$footerWidgets.masonry( 'reloadItems' );
	        tasman.$footerWidgets.masonry( 'layout' );
		});

	};

	tasman.returnToTop = function() {

		var $returnTop = $( '.return-to-top' );

		$(window).scroll(function () {
		    if ($(this).scrollTop() > 720) {
		        $returnTop.removeClass('off').addClass('on');
		    }
		    else {
		        $returnTop.removeClass('on').addClass('off');
		    }
		});

	};

	tasman.bind = function() {

		tasman.$body.on( 'post-load', function () {
			tasman.fitVids();
			tasman.gallery();
		});

		tasman.$window.load(function(){
	        tasman.$footerWidgets.masonry( 'reloadItems' );
	        tasman.$footerWidgets.masonry( 'layout' );
		});

		tasman.$body.on( 'wp-custom-header-video-loaded', function() {
			tasman.$body.addClass( 'has-header-video' );
		});

	};

	/** Initialize tasman.init() */
	$( function() {

		tasman.init();

	    if ( 'undefined' === typeof wp || ! wp.customize || ! wp.customize.selectiveRefresh ) {
	        return;
	    }

		wp.customize.selectiveRefresh.bind( 'sidebar-updated', function( sidebarPartial ) {
			if ( 'sidebar-1' === sidebarPartial.sidebarId ) {
	        	tasman.$footerWidgets.masonry( 'reloadItems' );
	        	tasman.$footerWidgets.masonry( 'layout' );
			}
		});

	});


} )( jQuery );
