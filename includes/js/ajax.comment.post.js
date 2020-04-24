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
				$('.ticket-responses-widget').append(data);
				if( ns_label.length > 0 ) {
					$('.ns-label').each(function() {
							adaptColor($(this));
					});
				}
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

function adaptColor(selector) {
	var rgb = $(selector).css("background-color");

	if (rgb.match(/^rgb/)) {
	var a = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/),
			r = a[1],
			g = a[2],
			b = a[3];
	}
	var hsp = Math.sqrt(
	0.299 * (r * r) +
	0.587 * (g * g) +
	0.114 * (b * b)
	);
	if ( r == 0 && g == 0 && b == 0) { 
			return null;
	} else {
			var hsp = Math.sqrt(
			0.299 * (r * r) +
			0.587 * (g * g) +
			0.114 * (b * b)
			);
			if (hsp > 127.5) {
					$(selector).css('color', 'black');
			} else {
					$(selector).css('color', 'white');
			}
	}
};

jQuery(document).ready(function($) {
	ns_get_post_comment($);
});