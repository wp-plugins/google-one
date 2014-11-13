(function($) {
	$(document).ready( function() {
		$( '#gglplsn_settings_form input' ).bind( "change click select", function() {
			if ( $( this ).attr( 'type' ) != 'submit' ) {
				$( '.updated.fade' ).css( 'display', 'none' );
				$( '#gglplsn_settings_notice' ).css( 'display', 'block' );
			};
		});
		$( '#gglplsn_settings_form select' ).bind( "change", function() {
			$( '.updated.fade' ).css( 'display', 'none' );
			$( '#gglplsn_settings_notice' ).css( 'display', 'block' );
		});
	});
})(jQuery);