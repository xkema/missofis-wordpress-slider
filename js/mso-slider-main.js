// slider initiator
jQuery( document ).ready( function() {

	//
	jQuery( '.rslides' ).responsiveSlides( {

		//
		auto: true,             // Boolean: Animate automatically, true or false
		speed: 500,            // Integer: Speed of the transition, in milliseconds
		timeout: 5000,          // Integer: Time between slide transitions, in milliseconds
		pager: true,           // Boolean: Show pager, true or false
		nav: true,             // Boolean: Show navigation, true or false
		random: false,          // Boolean: Randomize the order of the slides, true or false
		pause: true,           // Boolean: Pause on hover, true or false
		pauseControls: true,    // Boolean: Pause when hovering controls, true or false
		prevText: "&lsaquo;",   // String: Text for the "previous" button
		nextText: "&rsaquo;",       // String: Text for the "next" button
		maxwidth: "",           // Integer: Max-width of the slideshow, in pixels
		navContainer: "",       // Selector: Where controls should be appended to, default is after the 'ul'
		manualControls: "",     // Selector: Declare custom pager navigation
		namespace: "rslides",   // String: Change the default namespace used
		before: function(){},   // Function: Before callback
		after: function(){}     // Function: After callback


	} );

	//
	jQuery( '.rslides, .rslides_nav' ).on( 'mouseenter', function() {

		// transitions controlled by css
		// jQuery( '.rslides_nav.prev' ).css( { 'opacity':1, 'left':14 } );
		// jQuery( '.rslides_nav.next' ).css( { 'opacity':1, 'right':14 } );
		jQuery( '.rslides_nav' ).css( { 'opacity':1 } );
		

	} );

	//
	jQuery( '.rslides, .rslides_nav' ).on( 'mouseleave', function() {
		
		// transitions controlled by css
		// jQuery( '.rslides_nav.prev' ).css( { 'opacity':0, 'left':-36 } );
		// jQuery( '.rslides_nav.next' ).css( { 'opacity':0, 'right':-36 } );
		jQuery( '.rslides_nav' ).css( { 'opacity':0 } );

	} );


	//
	// jQuery( '.rslides_nav:visible' ).css( { 'opacity':0 } );


} ); // endof document.load