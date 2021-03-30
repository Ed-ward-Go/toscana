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
            var source = $(this).find(":selected").data('source');
            var name = $(this).find(":selected").data('name');
            $("#locationpopup-postcode").val(source);
            $("#locationpopup-name").val(name);
            $(".toast__container").css('display', 'table-cell');
            setTimeout(function() {
                $(".toast__container").css('display', 'none');
            }, 5000);
        });

        /*$(document).on('click', '.location-popup-button', function(){
            document.querySelector('.location-popup-button > button').disabled = true;
            openModalLocation(false);
        });*/

        $(document).on('click', '.dont-change', function(){
            document.querySelector('.location-popup-button > button').disabled = false;
            var isDefault = localStorage.getItem('isDefault');
            if(isDefault !== undefined){
                localStorage.removeItem('isDefault');
                isDefault = JSON.parse(isDefault);
                delete isDefault.default;
                setLocation('locationpopup/index/savelocation', isDefault);
            }
        });

        $(document).on("click","#close-minicart",function(e){
            $('.minicart-wrapper .action.showcart').click();
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
                //openModal(json);
                $("#pop-location-title__name").text(json.name);
            }
            else if(validate && json != null){
                if(json.hasOwnProperty('default')){
                    //openModal();
                    localStorage.setItem('isDefault', JSON.stringify(json));
                }
                $(".location-popup-button > button > strong > span").text(json.name);
                $("#pop-location-title__name").text(json.name);
            }
        })
        .fail(function (e) {
            console.log("ERROR ");
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
                        $("#locationpopup-city").val(json.id);
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
        $(".location-popup-button > p > strong > span").text(data.city);
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
            document.querySelector('.location-popup-button > button').disabled = false;
        })
        .fail(function (e) {
            console.log("error to set location ", e);
        });
    }


    function displayDeleteds(items){

        var append = '<ul>';

        items.forEach(element => {

            append += '<li>' + element.product + '</li>';

        });

        append += '</ul>';
        $(".toast__message").text('').append(append);
        $(".toast__type").text('Algunos productos no están disponibles en '+ $(".location-popup-button p").text());
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
