define(
    [
        'ko',
        'uiComponent',
        'jquery'
    ],
    function (
        ko,
        Component,
        $
    ) {
        var credit = ko.observableArray([]);
        var objectee = '';
        var globalSelf = '';
        const getCreditUrl = 'credit/index/index';
        return Component.extend({
            initialize: function () {
                globalSelf = this;
                this._super();
                this.getCredit();
            },
            item: function (_available, _canPay) {
                var _self = this;
                _self.available = ko.observable(_available);
                _self.canPay = ko.observable(_canPay);
            },
            getCredit(){
                var _self = this;

                $.ajax({
                    url: BASE_URL + getCreditUrl,
                    type: "get",
                    dataType: "json",
                    cache: false
                }).done(function (json) {
                    credit.removeAll();
                    if(json){
                       credit.push({
                           'available' : json.available,
                           'canPay' : json.canPay,
                           'formatPrice' : json.formatPrice
                       });
                       credit.valueHasMutated();
                    }
                })
                .fail(function (e) {
                    alert("error");
                });
            },
            getAvailableCredit: function () {
                return this.returnIndexValue('formatPrice');
            },
            canPay: function () {
                return this.returnIndexValue('canPay');
            },
            returnIndexValue: function (indexArray){
                let returnElement = '';
                $.each(credit(), function (index, value) {
                    returnElement = value[indexArray];
                });
                console.log(returnElement);
                return returnElement;
            }
        });
    }
);
