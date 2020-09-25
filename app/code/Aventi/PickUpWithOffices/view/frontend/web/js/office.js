require([
    'jquery',
    'mage/url',
    'mage/translate'
], function ($,url,$t) {

    $(document).on('click','.js-click',function (){
        $('.js-click').removeClass('active');
        var self =  $(this);
        self.addClass('active');
        office_id = self.find('input').val();
        $('body').trigger('processStart');
        $.ajax({
            url: BASE_URL+'pickupwithoffices/index/SelectSave',
            type: "post",
            dataType: "json",
            data: { office_id: office_id },
            cache: false
        })
        .done(function(json){
            if(json > 0){
               var html = '<h5>'+$.mage.__('office selected')+'</h5>' +
                          '<ul>'+
                             '<li>'+ self.find('.title').text() +'</li>'+
                             '<li>'+ self.find('span.address').text() +'</li>'+
                          '</ul>';
                $('.pick-block-shipping-information').html(html);
            }
            $('body').trigger('processStop');
        });
    });

    $(document).on('click', '#checkout-step-shipping_method .row', function (){
        var type = $(this).find('input.radio').val();
        if(type == 'pickup_pickup'){
            $('#list-offices').slideDown('slow');
        }else{
            $('.pick-block-shipping-information').html('');
            $('.js-click').removeClass('active');
            $('#list-offices').slideUp('slow');
            $('body').trigger('processStart');
            $.ajax({
                url: BASE_URL+'pickupwithoffices/index/SelectSave',
                type: "post",
                dataType: "json",
                data: { office_id: 0},
                cache: false
            })
                .done(function(json){
                    $('body').trigger('processStop');
            })
        }
    });



   /*
   $(document).on('submit','.methods-shipping',function (e){
        var shipping = $('.checkout-shipping-method input[class="radio"]:checked').val() || '';
        if($('.js-click.active').length <= 0 && shipping == 'pickup_pickup'){
            alert('Por favor seleccione una de nuestras oficinas');
            $('#list-offices').slideDown('slow');
            e.preventDefault();
            return false;
        }
    })*/

});