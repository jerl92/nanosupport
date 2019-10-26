function ns_update_adresse($) {
    
    $.fn.ready();
	'use strict';

	/**
	 * Remove All from Saved for Later
     */                  

    var $this = $(this);

    $.ajax({
        type: 'post',
        url: update_adresse_ajax_url,
        data: {
            'action': 'ns_update_adresse'
        },
        dataType: 'JSON',
        success: function(data) {
            $('.ns-ticket-retrun-adresse-table').empty();
            $('.ns-ticket-retrun-adresse-table').html(data);
            $('input[type=radio]').each(function(e){
                $('input[type=radio]').on('click', function (e) {
                    $( "#clear_alternative_adresse" ).removeClass( "disabled" );
                });
            });
            $( "#clear_alternative_adresse" ).on('click', function (e) {
                $( "#clear_alternative_adresse" ).addClass( "disabled" );
            });
            $('.alternative_adresse_remove').each(function() {
                $(this).attr('disabled', false);
            });
        },
        error: function(error) {
            console.log(error);
        }
    })
}