require([
    'jquery',
    'mage/url',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-rate-registry'
], function ($, url, quote, rateRegistry) {

    $(document).on('change', '[name="region_id"]', function () {
        changeCity(this);
    });

    $(document).on('change', '[name="city2"]', function () {
        //$(this).trigger("change");
        var postalCode = $(this).find(":selected").data('postal');
        $('input[name="postcode"]').val(postalCode).prop('readonly', true).trigger("change");
        $('[name="city"]').val($(this).val()).trigger("change");
        var address = quote.shippingAddress();

        address.trigger_reload = new Date().getTime();

        rateRegistry.set(address.getKey(), null);
        rateRegistry.set(address.getCacheKey(), null);

        quote.shippingAddress(address);
    });


    function changeCity(region) {
        var cityObject = $('[name="city"]');
        cityObject.val('').trigger("change");
        if ($(region).val() != '') {
            $('body').trigger('processStart');
            $.ajax({
                url: BASE_URL + 'citydropdown/index/index',
                type: "post",
                dataType: "json",
                data: {region_id: $(region).val()},
                cache: false
            })
                .done(function (json) {
                    var count = Object.keys(json).length;                    
                    if (count > 0) {

                        if ($('[name="city2"]').length == 0  && cityObject.is('input') ) {
                            cityObject.after('<select name="city2" required><option value="">Seccione una ciudad</option></select>');
                            cityObject.attr('type', 'hidden');
                            var cities = $('[name="city2"]');
                        }
                        else if ($('[name="city2"]').length > 0){
                            var cities = $('[name="city2"]');
                        }
                        else{
                            var cities = cityObject;
                        }



                        cities.find('option')
                            .remove()
                            .end()
                            .append('<option value="">Seccione una ciudad</option>')

                        $.each(json, function (i, attribute) {
                            cities.append("<option data-postal='" + attribute.postalCode + "' value='" + attribute.name + "'>" + attribute.name + "</option>");
                        });
                    }
                    $('body').trigger('processStop');
                })
                .fail(function () {
                    alert("error");
                });
        }
    }

});