( function( $ ) {
	const colors = {
			twitter: [ 29, 202, 255 ],
			spotify: [ 0, 0, 0 ],
			rocco: [ 144, 144, 144 ]
		},
		scrollDuration = 1000,
		scrollAnimationType = 'swing';
	let $scrollPort,
		$window,
		currentBackgroundColor,
		currentCardIndex,
		dammedButton,
		dammedDate,
		maxScrollTop,
		scrollProgress,
		resizeTimer,
		scrollTimer,
		windowHeight,
		windowWidth,
		cards = [];

	function handleResize() {
		windowHeight = $window.height();
		windowWidth = $window.width();
		maxScrollTop = windowHeight * ( cards.length - 1 );
		const dateDisplayWidth = dammedDate.width(),
			dateDisplayLeft = ( ( windowWidth - dateDisplayWidth ) / 2 ),
			dammedButtonWidth = dammedButton.width(),
			dammedButtonLeft = ( windowWidth - dammedButtonWidth ) / 2;
		dammedDate.css( 'left', dateDisplayLeft );
		dammedButton.css( 'left', dammedButtonLeft );

		$( '.dammed-card' ).each( function() {
			$( this ).css( 'height', windowHeight );
		} );
		$( '.dammed-content' ).each( function() {
			const contentTop = ( windowHeight - $( this ).height() ) / 2;
			$( this ).css( 'padding-top', contentTop );
		} );
	}
	function updateScrollPositions() {
		currentCardIndex = Math.floor( $scrollPort.scrollTop() / windowHeight );
		scrollProgress = ( windowHeight - ( ( windowHeight * ( currentCardIndex + 1 ) ) - $scrollPort.scrollTop() ) ) / windowHeight;
	}

	function handleScroll() {
		updateScrollPositions();
		calculateBackgroundColor();
		$( 'body' ).css( 'backgroundColor', 'rgb(' + currentBackgroundColor.r + ', ' + currentBackgroundColor.g + ', ' + currentBackgroundColor.b + ')' );
		if ( $scrollPort.scrollTop() >= maxScrollTop - 100 ) {
			dammedButton.fadeOut();
		} else {
			dammedButton.fadeIn();
		}
	}
	function calculateBackgroundColor() {
		let r, g, b;
		if ( currentCardIndex === cards.length - 1 ) {
			r = colors[ cards[ currentCardIndex ] ][ 0 ];
			g = colors[ cards[ currentCardIndex ] ][ 1 ];
			b = colors[ cards[ currentCardIndex ] ][ 2 ];
		} else {
			r = colors[ cards[ currentCardIndex ] ][ 0 ] - ( ( colors[ cards[ currentCardIndex ] ][ 0 ] - colors[ cards[ currentCardIndex + 1 ] ][ 0 ] ) * scrollProgress );
			g = colors[ cards[ currentCardIndex ] ][ 1 ] - ( ( colors[ cards[ currentCardIndex ] ][ 1 ] - colors[ cards[ currentCardIndex + 1 ] ][ 1 ] ) * scrollProgress );
			b = colors[ cards[ currentCardIndex ] ][ 2 ] - ( ( colors[ cards[ currentCardIndex ] ][ 2 ] - colors[ cards[ currentCardIndex + 1 ] ][ 2 ] ) * scrollProgress );
		}
		currentBackgroundColor = { r, g, b };
	}

	function scrollNext() {
		updateScrollPositions();
		$scrollPort.animate( { scrollTop: windowHeight * ( currentCardIndex + 1 ) }, scrollDuration, scrollAnimationType );
	}

	function setCards() {
		$( '.dammed-card' ).each( function() {
			cards.push( $( this ).data( 'type' ) );
		} );
	}

	function updateClock() {
		const date = new Date(),
			options = {
				hour12: false,
				year: 'numeric',
				month: '2-digit',
				day: '2-digit',
				hour: '2-digit',
				minute: '2-digit',
				second: '2-digit'
			};
		$( '#date' ).text( date.toLocaleDateString( 'uk-UK', options ) );
	}

	$( document ).ready( function() {
		setCards();
		$scrollPort = $( 'html, body' );
		$window = $( window );
		dammedDate = $( '#dammed-date' );
		dammedButton = $( '#dammed-button' );
		$window
			.on( 'resize', function() {
				clearTimeout( resizeTimer );
				resizeTimer = setTimeout( handleResize, 500 );
			} )
			.on( 'scroll', function( ) {
				clearTimeout( scrollTimer );
				resizeTimer = setTimeout( handleScroll, 250 );
			} );
		dammedButton
			.on( 'click', function() {
				scrollNext();
			} );
		handleResize();
		handleScroll();
		setTimeout( handleResize, 2000 );
		setInterval( updateClock, 1000 );
	} );
} )( jQuery );
