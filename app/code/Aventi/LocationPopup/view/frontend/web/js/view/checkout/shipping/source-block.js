
define([
    'jquery',
    'uiComponent',
    'ko'

], function ($,Component,ko) {
    'use strict';

    let source = ko.observableArray([]);
    let globalSelf = '';
    const getLocationUrl = 'locationpopup/index/getsourceinformation';

    return Component.extend({
        defaults: {
            template: 'Aventi_LocationPopup/checkout/shipping/sources-block'
        },
        initialize: function () {
            globalSelf = this;
            this._super();
            this.loadSources();
        },
        loadSources: function(){
            var _self = this;

            $.ajax({
                url: BASE_URL + getLocationUrl,
                type: "get",
                dataType: "json",
                cache: false
            }).done(function (json) {
                source.removeAll();
                if(json){
                    source.push({
                        'id' : json.id,
                        'name' : json.name,
                        'address' : json.address,
                        'city' : json.city
                    });
                    source.valueHasMutated();
                }
            })
            .fail(function (e) {
                console.log("ERROR OFFICE: ", e)
            });
        },
        getSourceName: function () {
            return this.returnIndexValue('name');
        },
        getAddress: function () {
            return this.returnIndexValue('address');
        },
        getCity: function () {
            return this.returnIndexValue('city');
        },
        returnIndexValue: function (indexArray){
            let returnElement = '';
            $.each(source(), function (index, value) {
                returnElement = value[indexArray];
            });
            return returnElement;
        }
    });
});
