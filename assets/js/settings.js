/**
 * WP Plugin Template settings js.
 *
 * @package
 */

/* global jQuery */
jQuery( document ).ready( function ( $ ) {
	/***** Colour picker *****/

	$( '.colorpicker' ).hide();
	$( '.colorpicker' ).each( function () {
		$( this ).farbtastic(
			$( this ).closest( '.color-picker' ).find( '.color' )
		);
	} );

	$( '.color' ).click( function () {
		$( this ).closest( '.color-picker' ).find( '.colorpicker' ).fadeIn();
	} );

	$( document ).mousedown( function () {
		$( '.colorpicker' ).each( function () {
			const display = $( this ).css( 'display' );
			if ( display === 'block' ) {
				$( this ).fadeOut();
			}
		} );
	} );

	/***** Uploading images *****/

	let fileFrame;

	jQuery.fn.uploadMediaFile = function ( button, previewMedia ) {
		// If the media frame already exists, reopen it.
		if ( fileFrame ) {
			fileFrame.open();
			return;
		}

		// Create the media frame.
		fileFrame = wp.media.frames.fileFrame = wp.media( {
			title: jQuery( this ).data( 'uploader_title' ),
			button: {
				text: jQuery( this ).data( 'uploader_button_text' ),
			},
			multiple: false,
		} );

		// When an image is selected, run a callback.
		const buttonId = button.attr( 'id' );
		const fieldId = buttonId.replace( '_button', '' );
		const previewId = buttonId.replace( '_button', '_preview' );

		fileFrame.on( 'select', function () {
			const attachment = fileFrame
				.state()
				.get( 'selection' )
				.first()
				.toJSON();
			jQuery( '#' + fieldId ).val( attachment.id );
			if ( previewMedia ) {
				jQuery( '#' + previewId ).attr(
					'src',
					attachment.sizes.thumbnail.url
				);
			}
			fileFrame = false;
		} );

		// Finally, open the modal.
		fileFrame.open();
	};

	jQuery( '.image_upload_button' ).click( function () {
		jQuery.fn.uploadMediaFile( jQuery( this ), true );
	} );

	jQuery( '.image_delete_button' ).click( function () {
		jQuery( this ).closest( 'td' ).find( '.image_data_field' ).val( '' );
		jQuery( this ).closest( 'td' ).find( '.image_preview' ).remove();
		return false;
	} );
} );
