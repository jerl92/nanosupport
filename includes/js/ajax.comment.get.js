function ns_get_comment($postids) {
    
    $.fn.ready();
	'use strict';


	/**
	 * Remove All from Saved for Later
	 */
					
	var $this = $(this),
	ns_label = $('.ns-label'),
			object_id = null;

	$.ajax({
		type: 'post',
		url: get_comment_ajax_url,
		data: {
			'object_id': $postids,
			'action': 'ns_get_comment'
		},
		dataType: 'JSON',
		success: function(data) {
			$('.ticket-responses-widget').append(data);
			if( ns_label.length > 0 ) {
				$('.ns-label').each(function() {
						adaptColor($(this));
				});
			}
		},
		error: function(error) {
			console.log(error);
		}
	})
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