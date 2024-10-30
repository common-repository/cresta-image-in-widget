jQuery.noConflict();
jQuery(document).ready(function($){
	$('body').on('mouseover', 'input.choose_border', function(e) {
		e.preventDefault();
		var theREL = $(this).attr('rel');
		$("input.choose_border[rel="+theREL+"]").on("keyup mouseup", function() {
			$('img.custom_media_image_user#'+theREL).css('border-radius', $(this).val()+'px');
		});
	});
	var media_uploader;
	if ( media_uploader ) {
      media_uploader.open();
      return;
    }
	function open_media_uploader_image(theID) {
		media_uploader = wp.media({
			title: CrestaGetTheText.get_image_title,
			button: {
				text: CrestaGetTheText.button_title
			},
			multiple: false
		});
		media_uploader.on("select", function(){
			var json = media_uploader.state().get("selection").first().toJSON();
			var image_url = json.url;
			var image_id = json.id;
			$('.custom_media_url_image#'+theID).val(image_url);
			$('.custom_media_id').val(image_id);
			$('.custom_media_image_user#'+theID).attr('src',image_url).css('display','block');
		});
		media_uploader.open();
	}
	$('body').on('click', '#upload-btn', function(e) {
		e.preventDefault();
		var theID = $(this).attr('rel');
		open_media_uploader_image(theID);
	});
});