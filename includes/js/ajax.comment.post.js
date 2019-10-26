function ns_get_post_comment($) {
    
    $.fn.ready();
	'use strict';


	/**
	 * Remove All from Saved for Later
	 */
	if ($('.ticket-responses-widget')) {
                        
		var $this = $(this),
		ns_label = $('.ns-label'),
		object_id = null;

		$.ajax({
			type: 'post',
			url: get_post_comment_ajax_url,
			data: {
				'object_id': object_id,
				'action': 'ns_get_post_comment'
            },
            dataType: 'JSON',
			success: function(data) {
				$( '.ticket-responses-widget' ).empty();		
				$.each( data, function( index, value ) {
					sleep(25);
					ns_get_comment(value);
					sleep(25);
				});
				setTimeout(function(){ ns_get_post_comment($); }, 30000);
			},
			error: function(error) {
				console.log(error);
			}
        })
	}
}

function sleep(milliseconds) {
	var start = new Date().getTime();
	for (var i = 0; i < 1e7; i++) {
	  if ((new Date().getTime() - start) > milliseconds){
		break;
	  }
	}
}

jQuery(document).ready(function($) {
	ns_get_post_comment($);
});