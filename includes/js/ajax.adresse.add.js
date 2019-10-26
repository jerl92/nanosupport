function ns_add_adresse($) {
    
    $.fn.ready();
	'use strict';


	/**
	 * Remove All from Saved for Later
	 */
	$('.alternative_adresse_submit').on('click', function(event) {
        event.preventDefault();
                        
        var $this = $(this),
                alternative_adresse = {organization:$(".organization").val(), adresse:$(".adresse").val(), ville:$(".ville").val(), province:$(".province").val(), code_postal:$(".code_postal").val(), pays:$(".pays").val()},
				object_id = alternative_adresse;

		$.ajax({
			type: 'post',
			url: add_adresse_ajax_url,
			data: {
				'object_id': object_id,
				'action': 'ns_add_adresse'
            },
            dataType: 'JSON',
			success: function(data) {
				$('.ticket-retrun-adresse-edit-form input[name="alternative_adresse_edit_save"]').css('display', 'none');
				$('.ticket-retrun-adresse-table').append(data);
                ns_edit_adresse($);
				ns_remove_adresse($);
				ns_update_adresse($);
				$(".organization").val('');
				$(".adresse").val('');
				$(".ville").val('');
				$(".province").val('');
				$(".code_postal").val('');
				$(".pays").val('');
				$('.alternative_adresse_remove').each(function() {
					$(this).attr('disabled', false);
				});
			},
			error: function(error) {
				console.log(error);
			}
        })
	});
}

jQuery(document).ready(function($) {
	ns_add_adresse($);
});