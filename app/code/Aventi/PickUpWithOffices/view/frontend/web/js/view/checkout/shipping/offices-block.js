
define([
    'jquery',
    'uiComponent',
    'ko'
    
], function ($,Component,ko) {
    'use strict';

    var listProducts = ko.observableArray([]);
    
    
    return Component.extend({
        defaults: {
            template: 'Aventi_PickUpWithOffices/checkout/shipping/offices-block'
        },
        loadOffices: function(){
            var self =  this;
            var _data = [];
            $.ajax({
                url: BASE_URL+'pickupwithoffices',
                type: "post",
                dataType: "json",
                cache: true
            })
            .done(function(json){
                $.each( json.data, function( key, p ) {
                    if(self.exist(p.category) == 0) {
                        listProducts.push(p);
                    }
                });
            })
            .fail(function() {
                alert( "error" );
            });

        },
        getOffices: function () {
            this.loadOffices();
            return listProducts;
        },
        exist:function (city) {
            var total = 0;
            $.each(listProducts(), function( index, value ) {
                if(city == value.category){
                    total = 1;
                    return 1;
                }
            });
            return total;
        }
    });
});