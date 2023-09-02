jQuery( document ).ready( function() {
	jQuery( '.message .difp-form' ).on( 'click', '.difp-button', function( e ) {
		e.preventDefault();
		var element = this;
		jQuery( element ).prop( 'disabled', true );
		jQuery( element ).parent().parent().next( '.difp-ajax-response' ).html( '' );
		jQuery( element ).next( '.difp-ajax-img' ).show();
		var data = jQuery( element.form ).serialize().replace( /&token=[^&;]*/, '&token=' + difp_shortcode_newmessage.token ) + '&difp_action=shortcode-newmessage';
		jQuery.post( difp_shortcode_newmessage.ajaxurl, data, function( response ) {
			jQuery( element ).parent().parent().next( '.difp-ajax-response' ).html( response['info'] );
			if( 'success' == response['difp_return'] ) {
				jQuery( element.form ).hide();
			}
		}, 'json')
		.fail( function() {
			jQuery( element ).parent().parent().next( '.difp-ajax-response' ).html( difp_shortcode_newmessage.refresh_text );
		})
		.complete( function() {
			jQuery( element ).next( '.difp-ajax-img' ).hide();
			jQuery( element ).prop( 'disabled', false );
		});
	});
});
