function ns_remove_adresse($) {
    
    $.fn.ready();
	'use strict';


	/**
	 * Remove All from Saved for Later
	 */
	$('.alternative_adresse_remove').on('click', function(event) {
        event.preventDefault();
                        
        var $this = $(this),
            object_id = $this.attr('data-id');

		$.ajax({
			type: 'post',
			url: remove_adresse_ajax_url,
			data: {
				'object_id': object_id,
				'action': 'ns_remove_adresse'
            },
            dataType: 'JSON',
			success: function(data) {
                $('.ticket-retrun-adresse-table').empty();
				$('.ticket-retrun-adresse-table').html(data);
				if ( $this.attr('data-id') == $('#alternative_adresse_edit_save').attr('data-id') ) {
					$(".organization").val('');
					$(".adresse").val('');
					$(".ville").val('');
					$(".province").val('');
					$(".code_postal").val('');
					$(".pays").val('');
					$('.ticket-retrun-adresse-edit-form input[name="alternative_adresse_edit_save"]').css('display', 'none');
				}
                ns_edit_adresse($);
				ns_remove_adresse($);
				ns_update_adresse($);
			},
			error: function(error) {
				console.log(error);
			}
		});
	});
}

jQuery(document).ready(function($) {
	ns_remove_adresse($);
});
