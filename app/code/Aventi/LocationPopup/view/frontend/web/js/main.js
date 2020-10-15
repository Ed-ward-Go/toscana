require(
[
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Customer/js/customer-data'
],
function( $, modal, customerData ) {    
    $(window).load(function () {
        openModalLocation(true);                    
    });

    $(document).ready(function(){

        var deleted = JSON.parse(localStorage.getItem('itemsDeleted'));
        
        if(deleted !== null){

            displayDeleteds(deleted);

        }

        $('.toast__close').click(function(e){
            e.preventDefault();
            var parent = $(this).parent('.toast');
            parent.fadeOut("slow");            
        });

        $(document).on('submit', '#locationpopup-form', function(e){
            e.preventDefault();
            var form = $(this).serializeArray();            
            var data = {};
            $(form ).each(function(index, obj){
                data[obj.name] = obj.value;
            });                     
            setLocation('locationpopup/index/savelocation', data);                
        });
    
        $(document).on('change', '#locationpopup-city', function(e){
            var postalCode = $(this).find(":selected").data('postcode');
            $("#locationpopup-postcode").val(postalCode);
            $(".toast__container").css('display', 'table-cell');                     
            setTimeout(function() {
                $(".toast__container").css('display', 'none');
            }, 5000);
        });

        $(document).on('change', '#locationpopup-region', function(e){
            var region_id = $(this).val();

            loadCitiesByRegion(region_id);
            
        });

        $(document).on('click', '.location-switcher__container', function(){
            openModalLocation(false);
        });

        $(document).on('click', '.dont-change', function(){                     
            var isDefault = localStorage.getItem('isDefault');                
            if(isDefault !== undefined){
                localStorage.removeItem('isDefault'); 
                isDefault = JSON.parse(isDefault);               
                delete isDefault.default;                  
                setLocation('locationpopup/index/savelocation', isDefault);                
            }        
        });

    });    

    function openModalLocation(validate){
        $.ajax({
            url: BASE_URL + 'locationpopup/index/getlocation',
            type: "get",
            dataType: "json",            
            cache: false
        })
        .done(function (json) {
            
            //var count = Object.keys(json).length;            
            if(!validate && json != null){
                openModal(json);
                $("#pop-location-title__name").text(json.city);
            }
            else if(validate && json != null){
                if(json.hasOwnProperty('default')){
                    openModal();
                    localStorage.setItem('isDefault', JSON.stringify(json));
                }                
                $(".location-switcher__container > p").text(json.city);
                $("#pop-location-title__name").text(json.city);
            }               
        })
        .fail(function (e) {            
            alert("error");
        });
        
    }

    function openModal(json = null) {
        if ($('#location-popup').length) {
            var options = {
                type: 'popup',
                modalClass: 'modal_location_popup',
                responsive: true,
                innerScroll: true,
                title: '',
                buttons: [],
                opened: function($Event) {
                    $('.modal-header button.action-close', $Event.srcElement).hide();
                }
            };
            var locationPopup = modal(options, $('#location-popup'));            
            setTimeout(function(){                 
                $('#location-popup').trigger('openModal'); 
                var interval = setInterval(function(){
                                        
                    if(json != null){
                        $("#locationpopup-region").val(json.region);
                        loadCitiesByRegion(json.region, json.postcode);                                                
                    }

                    if($("#locationpopup-city").find('option')){
                        $("#locationpopup-city").trigger('change');  
                        clearInterval(interval);
                    }                    
                }, 500);                        
                //$(".modal_location_popup [data-role='closeBtn']").css('display', 'none');
            }, 1000);                    
        }
    }

    function setLocation(url, data){
        $(".location-switcher__container > p").text(data.city);
        $("#pop-location-title__name").text(data.city);
        $('body').trigger('processStart');
        $.ajax({
            url: BASE_URL + url,
            type: "post",
            dataType: "json",
            data: {data : data},
            cache: false
        })
        .done(function (json) {
            $('#location-popup').trigger('closeModal');            
            if(json && typeof json == 'object'){
                if(Object.keys(json).length > 0){
                    localStorage.setItem('itemsDeleted', JSON.stringify(json));
                }                
            }             
            window.location.reload(true);
        })
        .fail(function (e) {            
            alert("error");
        });
    }  
    
    function loadCitiesByRegion(region_id, selected = null){
        $.ajax({
            url: BASE_URL + 'locationpopup/index/getcitiesbyregion',
            type: "get",                     
            data: {
                region: region_id
            },
            cache: false
        })
        .done(function (json) {
            var options = '';
            json.forEach(element => {
                var checked = '';
                if(selected != null){
                    if(element.postalCode == selected){
                        checked = 'selected'
                    }
                }
                options += '<option data-postcode="'+ element.postalCode +'" value="'+ element.name +'" '+ checked +' >'+ element.name + '</option>';
            });
            $("#locationpopup-city").find('option').remove().end().append(options);
        })
        .fail(function (e) {            
            alert("error");
        });

    }

    function displayDeleteds(items){

        var append = '<ul>'; 

        items.forEach(element => {
            
            append += '<li>' + element.product + '</li>';

        });

        append += '</ul>';
        $(".toast__message").text('').append(append);
        $(".toast__type").text('Algunos productos no están disponibles en '+ $(".location-switcher__container p").text());
        $(".toast__container").css('display', 'table-cell');   
        setTimeout(function() {
            $(".toast__container").css('display', 'none');
        }, 10000);
        var sections = ['cart'];
        customerData.invalidate(sections);
        customerData.reload(sections, true);
        localStorage.removeItem('itemsDeleted');
    }
});