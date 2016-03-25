/**
 * Created by shramee on 22/9/15.
 */
jQuery(document).ready(function( $ ){
	if ( pageCustomizerFixedBackground ) {
		$( '#page-customizer-bg-fixed' )
			.prependTo( $('body') )
			.removeClass('ppc-no-show').show().get(0).play();
	}
});