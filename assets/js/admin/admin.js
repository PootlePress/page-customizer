/**
 * Created by Shramee on April 02, 2015.
 */
(function ($) {

    $(document).ready(function () {
		//Hiding background customizing options if .ppc-field.background-image .image-upload-path is blank
		$bg_url = $('.ppc-field.background-image .image-upload-path');
		$bg_options = $('.ppc-field.background-repeat, .ppc-field.background-position, .ppc-field.background-attachment');

		if($bg_url.val() == ''){
			$bg_options.hide(0);
		}

		$bg_url.change(function(){
			if($bg_url.val() == ''){
				$bg_options.hide(0);
			}else{$bg_options.show(0);}
		})


        //wpColorPicker
		$('.ppc-field .color-picker-hex').wpColorPicker();
		
		// Uploading Fields aka media selection
		var file_frame;
		$('.ppc-field .upload-button').live('click', function( event ){
			event.preventDefault();

			$textField = $(this).siblings('input');

			// If the media frame already exists, reopen it.
			if ( file_frame ) {
			  file_frame.open();
			  return;
			}

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
			  title: $( this ).data( 'uploader_title' ),
			  button: {
				text: $( this ).data( 'uploader_button_text' ),
			  },
			  multiple: false  // Set to true to allow multiple files to be selected
			});

			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {
			  // We set multiple to false so only get one image from the uploader
			  attachment = file_frame.state().get('selection').first().toJSON();

			  // Do something with attachment.id and/or attachment.url here
			  $textField.val(attachment.url)
			  $textField.change();

			});

			// Finally, open the modal
			file_frame.open();
		});

    });

})(jQuery);