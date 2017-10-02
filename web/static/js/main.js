function showMenuItems() {
    var x = document.getElementById("menu");
    if (x.className === "topnav") {
        x.className += " responsive";
    } else {
        x.className = "topnav";
    }
}

$( document ).ready(function(){
    //user's page, set role routine
    $('.btn.setUserRole').click(function(e){
        $.ajax({
            method: 'POST',
            url: 'setUserRole',
            data: {
                uid: $(e.currentTarget).attr('user-data')
                ,addRole: $(e.currentTarget).parent().parent().find('#addRole').val()
                ,removeRole: $(e.currentTarget).parent().parent().find('#removeRole').val()
            }
        })
        .done(function( response ){
            if( response.result ){
                location.reload();
            }
        })
        .fail(function( response ){
            console.log('FAIL to set user role');
            console.log(response.msg);
        });
    });
    //.. unset role routine
    $('.btn.setUserBlock').click(function(e){
        $this = $(e.currentTarget);
        $.ajax({
            method: 'POST',
            url: 'setUserBlock',
            data: {
                uid: $this.attr('user-data')
            }
        })
        .done(function( response ){
            if( response.result ){
                $this.text($this.text() == 'Разблокировать' ? 'Заблокировать' : 'Разблокировать');
            }
        })
        .fail(function( response ){
            console.log('FAILED to deal with user block');
            console.log(response.msg);
        });
    });

    //admin's main page
    $.each($('#appbundle_seeddata input'), function(indx, elt){
        $(elt).change(function(){
            var revenue = 0,
                oilPrice = parseInt($('#appbundle_seeddata_oil_price').val()) || 0,
                oilYield = parseFloat($('#appbundle_seeddata_oil_yield').val()) || 0,
                oilMealPrice = parseInt($('#appbundle_seeddata_oilmeal_price').val()) || 0,
                oilMealYield = parseFloat($('#appbundle_seeddata_oilmeal_yield').val()) || 0,
                usdrub = parseInt($('#appbundle_seeddata_usdrub').val()) || 0
            ;

            $('#appbundle_seeddata_revenue').val( (((oilPrice - 15)*usdrub - 2000)*oilYield + (oilMealPrice*usdrub - 2000)*oilMealYield)/100 );
        });
    });

    //manager's main page
    $('#appbundle_deal_comment').attr('style','height:120px');

    var updateSeedLogisticPrice = function(){
        var
            deliveryPrice = parseInt($('#appbundle_deal_delivery_price').val()) || 0,
            shipmentPrice = parseInt($('#appbundle_deal_shipment_price').val()) || 0,
            storagePrice = parseInt($('#appbundle_deal_storage_price').val()) || 0
        ;
        $('#appbundle_deal_logistic_price').val( (deliveryPrice + shipmentPrice + storagePrice*3) );
        $('#appbundle_deal_logistic_price').change();
    };

    $('#appbundle_deal_delivery_price').change(updateSeedLogisticPrice);
    $('#appbundle_deal_shipment_price').change(updateSeedLogisticPrice);
    $('#appbundle_deal_storage_price').change(updateSeedLogisticPrice);

    $('#appbundle_deal_logistic_price').change(function(){
        var
            purchasePrice = parseInt($('#appbundle_deal_seed_price').val()) || 0,
            logisticPrice = parseInt($('#appbundle_deal_logistic_price').val()) || 0
        ;
        $('#appbundle_deal_seed_purchase_price').val( parseFloat(purchasePrice + logisticPrice).toFixed(2) );
        $('#appbundle_deal_seed_purchase_price').change();
    });

    var $oilContent = $('#appbundle_deal_oil_content');
    $oilContent.change(function(){
        var oilContent = parseInt($oilContent.val())/100 || 0,
            purchasePrice = parseInt($('#appbundle_deal_seed_price').val()) || 0,
            logisticPrice = parseInt($('#appbundle_deal_logistic_price').val()) || 0,
            oilProcessingCost = parseInt($('#oilProcessingCost').val()) || 0,
            price = 0
        ;

        if( oilContent > 0.48 ){
            price = (oilContent - 0.48)*1.5*purchasePrice + purchasePrice;
        }
        else{
            if( oilContent > 0.46 ){
                price = purchasePrice;
            }
            else{
                if( oilContent >= 0.43 ){
                    price = purchasePrice - ( 0.46 - oilContent )*2*purchasePrice;
                }
                else{
                    price = purchasePrice - ( 0.06 + ( 0.43 - oilContent )*3 )*purchasePrice;
                }
            }
        }

        $('#appbundle_deal_seed_purchase_price_oil').val( parseFloat((price + logisticPrice)*1.02 + oilProcessingCost).toFixed(2) );
        $('#appbundle_deal_seed_purchase_price_oil').change();
    });

    $('#appbundle_deal_seed_purchase_price_oil').change(function(){
        var omegaNumeratorOil       = parseInt($('#omegaNumeratorOil').val()) || 0,
            omegaNumeratorOilMeal   = parseInt($('#omegaNumeratorOilMeal').val()) || 0,
            minOmega                = parseFloat($('#appbundle_deal_min_alpha_coefficient').val()),
            alpha                   =  $('#appbundle_deal_alpha_coefficient').val() || 0,
            oilContent              = parseInt($oilContent.val()) || 0,
            baseReward              = parseInt($('#baseReward').val()) || 0,
            omega                   = 0,
            oilYield                = 0,
            oilMealYield            = 0
        ;
        //calculate omega coefficient
        oilYield        = oilContent*0.91 - 1.2;
        oilMealYield    = 81.5 - oilYield;
        omega = ((omegaNumeratorOil * oilYield + omegaNumeratorOilMeal * oilMealYield) / 100) / parseInt($(this).val());

        $('#appbundle_deal_omega_coefficient').val( parseFloat( omega ).toFixed(2) );
        $('#appbundle_deal_min_alpha_coefficient_excess').val( parseFloat( alpha - minOmega ).toFixed(2) );
        $('#appbundle_deal_alpha_bonus').val( (parseFloat( alpha - minOmega ).toFixed(2))*baseReward );
    });

    $('#appbundle_deal_seed_purchase_price').change(function(){
        var alphaNumerator = parseInt($('#alphaNumerator').val()) || 0;
        //calculate alpha coefficient
        $('#appbundle_deal_alpha_coefficient').val( parseFloat(alphaNumerator / (parseInt($(this).val())*1.02)).toFixed(2) );
    });

    $('#appbundle_deal_seed_price').change(function(){
        $('#appbundle_deal_logistic_price').change();
    });

    //invoke form params for deal view
    $('#appbundle_deal_delivery_price').change();
    $oilContent.change();

    //report's page
    //date range picker
    $('input[class="daterange"]').daterangepicker({
        locale: {
            format: 'DD.MM.YYYY',
            "firstDay": 1,
            "daysOfWeek": [
                "Вс",
                "Пн",
                "Вт",
                "Ср",
                "Чт",
                "Пт",
                "Сб"

            ],
            "monthNames": [
                "Январь",
                "Февраль",
                "Март",
                "Апрель",
                "Май",
                "Июнь",
                "Июль",
                "Август",
                "Сентябрь",
                "Октябрь",
                "Ноябрь",
                "Декабрь"
            ],
            "applyLabel": "Выбрать период",
            "cancelLabel": "Отмена",
        },
    });
    //prepare report button click handler
    $('.prepareReport').click(function(e){
        var
             datePeriod = $('#datePeriod').val()
            ,uids = ''
            ,url = 'prepare'
        ;

        $.each( $('.selectUser'), function(i,elt){
            if( $(elt).is(':checked') )uids += $(elt).attr('data-user') + ',';
        });

        params = {
            uids: uids.substring(0, uids.length-1),
            period: datePeriod
        };

        $.ajax({
            method: 'POST',
            url: url,
            data: params
        })
        .done(function( response, status, request ){
            var disp = request.getResponseHeader('Content-Disposition');
            if(
                   disp
                && disp.search('attachment') != -1
            ){
                var form = $('<form method="POST" action="' + url + '">');
                $.each(params, function(k, v) {
                    form.append($('<input type="hidden" name="' + k + '" value="' + v + '">'));
                });
                $('body').append(form);
                form.submit();
            }
        })
        .fail(function( response ){
            console.log('FAILED to prepare report');
            console.log(response);
        });
    });

});
