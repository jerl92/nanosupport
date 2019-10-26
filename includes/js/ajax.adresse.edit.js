function ns_edit_adresse($) {
    
    $.fn.ready();
	'use strict';


	/**
	 * Remove All from Saved for Later
	 */
	$('.alternative_adresse_edit').on('click', function(event) {
        event.preventDefault();
                        
        var $this = $(this),
            object_id = {id:$this.attr('data-id')};

		$.ajax({
			type: 'post',
			url: edit_adresse_ajax_url,
			data: {
				'object_id': object_id,
				'action': 'ns_edit_adresse'
            },
            dataType: 'JSON',
			success: function(data) {
                $('.ticket-retrun-adresse-edit-form input[name="organization"]').val(data['organization']);
                $('.ticket-retrun-adresse-edit-form input[name="adresse"]').val(data['adresse']);
                $('.ticket-retrun-adresse-edit-form input[name="ville"]').val(data['ville']);
                $('.ticket-retrun-adresse-edit-form input[name="province"]').val(data['province']);
                $('.ticket-retrun-adresse-edit-form input[name="code_postal"]').val(data['code_postal']);
                $('.ticket-retrun-adresse-edit-form input[name="pays"]').val(data['pays']);
                $('.ticket-retrun-adresse-edit-form input[name="alternative_adresse_edit_save"]').css('display', 'block');
                $('.ticket-retrun-adresse-edit-form input[name="alternative_adresse_edit_save"]').attr('data-id', object_id['id']);
                ns_edit_adresse($);
                ns_remove_adresse($);
                $('.alternative_adresse_remove').each(function() {
                    $(this).attr('disabled', true);
                });
			},
			error: function(error) {
				console.log(error);
			}
		});
    });
}

jQuery(document).ready(function($) {
	ns_edit_adresse($);
});

function ns_edit_adresse_save($) {
    
    $.fn.ready();
	'use strict';

    $('.alternative_adresse_edit_save').on('click', function(event) {
        event.preventDefault();
                        
        var $this = $(this),
            alternative_adresse = {id:$this.attr('data-id'), organization:$(".organization").val(), adresse:$(".adresse").val(), ville:$(".ville").val(), province:$(".province").val(), code_postal:$(".code_postal").val(), pays:$(".pays").val()},
            object_id = alternative_adresse;

        $.ajax({
            type: 'post',
            url: edit_adresse_ajax_url,
            data: {
                'object_id': object_id,
                'action': 'ns_edit_adresse'
            },
            dataType: 'JSON',
            success: function(data) {
                $('.ticket-retrun-adresse-edit-form input[name="alternative_adresse_edit_save"]').css('display', 'none');
                $('.ticket-retrun-adresse-table').empty();
                $('.ticket-retrun-adresse-table').html(data);
                $(".organization").val('');
                $(".adresse").val('');
                $(".ville").val('');
                $(".province").val('');
                $(".code_postal").val('');
                $(".pays").val('');
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
	ns_edit_adresse_save($);
});