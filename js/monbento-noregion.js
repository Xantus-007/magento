;

if(typeof jQuery != "undefined")
{
    jQuery(function($) {
            $('#country, #billing\\:country_id, #shipping\\:country_id').bind('change', function() {
                    if ($(this).val() in {'AU':'', 'CA':'', 'DE':'', 'GB':'', 'IE':'', 'US':''}) {
                            $(this).closest('li.fields').prev().children('div:odd').show();
                    }
                    else {
                            $(this).closest('li.fields').prev().children('div:odd').hide();
                    }
            });

            $('#country, #billing\\:country_id, #shipping\\:country_id').trigger('change');
    });
}