require([
    'jquery',
    'mage/url'
], function ($, url) {

    $(document).on('change', '[name="identification_customer"]', function () {

        var payload = {
                param: $(this).val()

        };

        $.ajax({
            url: BASE_URL + 'rest/V1/aventi-sap/identification',
            global: false,
            contentType: 'application/json',
            type: 'PUT',
            async: false,
            data: JSON.stringify(payload),
            cache: false
         }).done(function (json) {

        });

    });

});


