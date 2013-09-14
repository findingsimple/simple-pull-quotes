jQuery(document).ready(function($) {

	$( 'span.pullquote' ).each( function() {

		var $quote = $(this);

		$quote.addClass( 'pulledquote' ).after( $quote.html() );

		$quote.prepend('<span class="quote-open ss-quote"></span>'); /* useful for adding curly quotes */

		$quote.append('<span class="quote-close"></span>'); /* useful for adding curly quotes */

		if ( $quote.data( 'wrap' ) ) {

			$quote.wrap( '<p>' );

			$quote = $quote.parent().addClass( $(this).attr( 'class' ) );

			$quote.attr( 'style', $(this).attr( 'style' ) );

			$(this).attr( 'style', '' );

			$(this).attr( 'class', '' );

		}

		if ( $(this).data( 'back' ) ) {

			var $back = parseInt( $(this).data( 'back' ) );

			var $parent = $quote.parents( 'p' );

			while( $back > 0 ) {

				$parent = $parent.prev( 'p' );

				--$back;

			}

			$parent.before( $quote.clone() );

			$quote.remove();

		} else {

			$quote.parents('p').css( 'position', 'relative' );

		}

	});

});
