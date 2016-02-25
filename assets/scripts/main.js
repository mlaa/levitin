/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 * ======================================================================== */

( function( $ ) {

	// Use this variable to set up the common and page specific functions. If you
	// rename this variable, you will also need to rename the namespace below.
	var CPWPST = {
		// All pages
		'common': {
			init: function() {
				// JavaScript to be fired on all pages
			},
			finalize: function() {
				// JavaScript to be fired on all pages, after page specific JS is fired
			}
		},
		// Home page
		'home': {
			init: function() {
				// JavaScript to be fired on the home page
			},
			finalize: function() {
				// JavaScript to be fired on the home page, after the init JS
			}
		},
		// About us page, note the change from about-us to about_us.
		'about_us': {
			init: function() {
				// JavaScript to be fired on the about us page
			}
		}
	};

	// The routing fires all common scripts, followed by the page specific scripts.
	// Add additional events for more control over timing e.g. a finalize event
	var UTIL = {
		fire: function( func, funcname, args ) {
			var fire;
			var namespace = CPWPST;
			funcname = ( funcname === undefined ) ? 'init' : funcname;
			fire = func !== '';
			fire = fire && namespace[func];
			fire = fire && typeof namespace[func][funcname] === 'function';

			if ( fire ) {
				namespace[func][funcname](args);
			}
		},
		loadEvents: function() {
			// Fire common init JS
			UTIL.fire( 'common' );

			// Fire page-specific init JS, and then finalize JS
			$.each( document.body.className.replace( /-/g, '_' ).split( /\s+/ ), function( i, classnm ) {
				UTIL.fire( classnm );
				UTIL.fire( classnm, 'finalize' );
			});

			// Fire common finalize JS
			UTIL.fire( 'common', 'finalize' );
		}
	};

	// Load Events
	$( document ).ready( UTIL.loadEvents );

	// profile editing
	$( document ).ready( function() {
		// header fields (hidden in main form, duplicated to be accessible by user in the header)
		var init_social_fields = ( function() {
			var social_field_change_handler = function ( e ) {
				var input = $( this ).find( 'input' );
				$( '#profile-edit-form input[name=' + input.attr( 'name' ) + ']' ).val( input.val() );
			};
			$( '#profile-edit-form' ).find( '.field-twitter, .field-facebook, .field-linkedin, .field-orcid' ).each( function () {
				var clone = $( this ).clone();

				// only keep label & input, no permissions radios (or anything else)
				clone
					.find( ':not( label[for^=field], input[id^=field] )' )
					.remove();

				// remove id to prevent conflict
				clone
					.find( 'input' )
					.removeAttr( 'id' );

				// remove corresponding view-only div
				$( '#item-header-content #item-main' )
					.find( '.' + clone.attr( 'class' ) )
					.remove();

				// move to header
				clone
					.appendTo( '#item-header-content #item-main' )
					.change( social_field_change_handler );
			} );
		} )();

		var init_visibility_controls = ( function() {
			$( '#profile-edit-form .editable' ).each( function() {
				var div = $( this );

				// add visibility controls
				div.append( '<a href="#" class="visibility">hide</a>' );

				// bind visibility controls
				div.find( '.visibility' ).click( function() {
					var a = $( this );

					if ( a.html() === 'hide' ) {
						a.html( 'show' );
						div.addClass( 'collapsed' );
						div.find( '.adminsonly input' ).attr( 'checked', true );
						div.find( '.public input' ).attr( 'checked', false );
					} else {
						a.html( 'hide' );
						div.removeClass( 'collapsed' );
						div.find( '.adminsonly input' ).attr( 'checked', false );
						div.find( '.public input' ).attr( 'checked', true );
					}

					return false;
				} );

				if ( div.find( '.adminsonly input' ).is( ':checked' ) ) {
					div.find( '.visibility' ).triggerHandler( 'click' );
				}
			} );
		} )();

		// cancel button to send user back to view mode
		var init_cancel_button = ( function() {
			$( '#profile-edit-form #cancel' ).click( function( e ) {
				e.preventDefault();
				window.location = $( '#public' ).attr( 'href' );
			} );
		} )();

		// highlight changed fields to encourage user to save changes
		var init_change_highlighting = ( function() {
			$( '#profile-edit-form select, #profile-edit-form input, #profile-edit-form textarea, #item-main input' ).change( function() {
				if ( $( this ).is( 'select' ) ) {
					$( this ).siblings( '.select2' ).find( '.select2-selection' ).addClass( 'changed' );
				} else {
					$( this ).addClass( 'changed' );
				}
			} );
		} )();
	} );

} )( jQuery ); // Fully reference jQuery after this point.
