requirejs([
    'jquery'
], function($){
    
    $(document).ready(function(){
        $("#location_config_postcode").prop("readonly", true);
        setTimeout(function(){
            $('#location_config_region').trigger('change');    
        }, 1000);
        $('#location_config_country').change(function(){
            var param = {
                country: $(this).val(),
                form_key: window.FORM_KEY
            };
            requestToLoad( 'locationpopup/index/index', param, 1);
            
        });

        $('#location_config_region').change(function(){
            var param = {
                region_id: $(this).val(),
                form_key: window.FORM_KEY
            };
            requestToLoad( 'citydropdown', param, 2);
        });

        $('#location_config_city').change(function(){            
            var postalCode = $(this).find(":selected").data('postcode');
            $("#location_config_postcode").val(postalCode);
        });

    });

    function requestToLoad(url, param, type){
        $.ajax({
            url: window.customUrl + url + '?isAjax=true',
            type: "post",
            dataType: "json",
            data: param,
            cache: false
        })
        .done(function (json) {
            var count = Object.keys(json).length;
            console.log(json)
            var option = '';
            var label = '';
            if (count > 0) {               

                if(type == 1){
                    option = $('#location_config_region');
                    label = 'Seleccione una región';
                }else if(type == 2){
                    option = $('#location_config_city');
                    label = 'Seleccione una ciudad';
                }

                option.find('option')
                    .remove()
                    .end()
                    .append('<option value="">'+label+'</option>');

                $.each(json, function (i, attribute) {
                    if(type == 1){
                        option.append("<option value='" + attribute.label + "'>" + attribute.label + "</option>");
                    }else if(type == 2){
                        option.append("<option data-postcode='"+ attribute.postalCode +"' value='" + attribute.name + "'>" + attribute.name + "</option>");
                    }                    
                });  
                if(window.defaultPostCode != 0){
                    option.find('option[data-postcode="'+window.defaultPostCode+'"]').attr("selected",true);
                    option.trigger('change');
                }             
            }            
        })
        .fail(function (e) {
            
            alert("error");
        });
    }

});