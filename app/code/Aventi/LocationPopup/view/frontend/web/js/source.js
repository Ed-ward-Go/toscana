require([
    'jquery',
    'mage/url',
    'mage/translate'
], function ($,url,$t) {
    const getLocationUrl = 'locationpopup/index/getsourceinformation';

    $(document).on('ready', function(){
        setTimeout(function(){
            loadSources();
        }, 8000);
    });

    function loadSources() {
        var _self = this;
        $.ajax({
            url: BASE_URL + getLocationUrl,
            type: "get",
            dataType: "json",
            cache: false
        }).done(function (json) {
            if(json){
                let sourceInterval = setInterval(function () {
                    if($('.source-shipping-information').length){
                        $('.ship-to').css('display', 'none');
                        var html =
                            '<div class="offices">'+
                                '<div>'+
                                    '<div class="title">'+
                                        '<h5>' + $.mage.__('Source selected')+
                                        '</h5>' +
                                        '<p>' +$.mage.__('Your order will be ready to be picked up in:') +
                                        '</p>'+
                                        '<h4>'+
                                            '<span class="title">'+ json.name +'</span>'
                                        '</h4>' +
                                    '</div>'+
                                '</div>'+
                                '<div>'+
                                    '<div class="address">'+
                                        '<span class="address">'+ json.address +'</span>'+
                                        '<span class="address">'+ json.city +'</span>'+
                                    '</div>'+
                                '</div>'+
                            '</div>';
                        $('.source-shipping-information').html(html);
                        clearInterval(sourceInterval);
                    }
                }, 2000)
            }
        })
        .fail(function (e) {
            console.log("ERROR Source: ", e)
        });
    };


});
