define([
    'jquery',
    'mage/utils/wrapper',
    'mage/validation',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Checkout/js/model/cart/totals-processor/default',
    'Magento_Checkout/js/model/cart/cache'
], function ($, wrapper, validation, quote, rateRegistry,getTotalsAction,defaultTotal,cartCache) {
    'use strict';
    $(document).ready(function () {
        
        setTimeout(function () {
            refreshRegion();
            $('[name="shippingAddress.identification_customer"]').children('div').children('input').attr('maxlength', '12')         
        },4000);


        $(document).on('change', '[name="city2"]', function () {
            //$(this).trigger("change");
            var postalCode = $(this).find(":selected").data('postal');
            $('input[name="postcode"]').val(postalCode).prop('readonly', true).trigger("change");
            $('[name="city"]').val($(this).val()).trigger("change");

            var address = quote.shippingAddress();
            address.postcode = postalCode;
            address.trigger_reload = new Date().getTime();
            rateRegistry.set(address.getKey(), null);
            rateRegistry.set(address.getCacheKey(), null);
            quote.shippingAddress(address);
        });

        /*$(document).on('click', '#checkout-payment-method-load input[name="payment[method]"]', function () {

            $('body').trigger('processStart');
            $.ajax({
                url: BASE_URL + 'aventi_flete/index/index',
                type: "post",
                dataType: "json",
                data: {payment: $(this).val()},
                cache: false
            })
                .done(function (json) {
                    defaultTotal.estimateTotals();
                    var deferred = $.Deferred();
                    getTotalsAction([], deferred);
                    $('body').trigger('processStop');
                    var address = quote.shippingAddress();                    
                    address.trigger_reload = new Date().getTime();
                    rateRegistry.set(address.getKey(), null);
                    rateRegistry.set(address.getCacheKey(), null);
                    quote.shippingAddress(address);
                })

        });*/






        $(document).on('change', '[name="region_id"]', function () {
            changeCity(this);
        });

        $(document).on('mouseenter focus', '[name="city"]', function () {
            var _this = $(this);
            if (_this.is('input')) {
                $('[name="city2"]').remove();
                _this.after('<select name="city2" required><option value="">Seccione una ciudad</option></select>').attr('type', 'hidden');
                if (_this.val() != '') {
                    var region = $('[name="city"]').closest('form').find('select[name="region_id"]').val()
                    if (region != '') {
                        $('body').trigger('processStart');
                        var cities = $('[name="city2"]');

                        cities.find('option')
                            .remove()
                            .end()
                            .append('<option value="">Seccione una ciudad</option>')

                        $.ajax({
                            url: BASE_URL + 'citydropdown/index/index',
                            type: "post",
                            dataType: "json",
                            data: {region_id: region},
                            cache: false
                        })
                            .done(function (json) {
                                $.each(json, function (i, attribute) {
                                    cities.append(
                                        "<option data-postal='" +
                                           attribute.postalCode +
                                                    "' value='" +
                                                 attribute.name +
                                                            "' "+
                                        ((attribute.name == _this.val()) ?'selected': '')
                                        +" >" + attribute.name + "</option>");
                                });
                                $('body').trigger('processStop');
                            })
                    }
                }
            }
        });

    });

    function changeCity(region,selected) {
        selected = typeof selected !== 'undefined' ?  selected : 0;
        var cityObject = $('[name="city"]');
        cityObject.val('').trigger("change");
        if ($(region).val() != '') {
            $('body').trigger('processStart');
            $.ajax({
                url: BASE_URL + 'citydropdown',
                type: "post",
                dataType: "json",
                data: {region_id: $(region).val()},
                cache: false
            })
                .done(function (json) {
                    var count = Object.keys(json).length;
                    if (count > 0) {

                        if ($('[name="city2"]').length == 0) {
                            cityObject.after('<select name="city2" required><option value="">Seccione una ciudad</option></select>');
                            cityObject.attr('type', 'hidden');
                        }

                        var cities = $('[name="city2"]');

                        cities.find('option')
                            .remove()
                            .end()
                            .append('<option value="">Seccione una ciudad</option>')

                        $.each(json, function (i, attribute) {
                            cities.append("<option data-postal='" + attribute.postalCode + "' value='" + attribute.name + "'>" + attribute.name + "</option>");
                        });
                        if(selected != 0){
                            cities.find('option[data-postal="'+selected+'"]').attr("selected",true);
                            cities.trigger('change');
                        }
                    }
                    $('body').trigger('processStop');
                })
                .fail(function () {
                    alert("error");
                });
        }

    }

    function refreshRegion(){
        if ($('[name="region_id"]').val() != '' && $('input[name="postcode"]') != '') {            
            changeCity($('[name="region_id"]'), $('input[name="postcode"]').val());            
        }
    }

    $(window).bind('hashchange', function() {            
        setTimeout(function(){            
            refreshRegion();       
        }, 3000);                                           
    });

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction) {

            return originalAction();
        });
    }
});