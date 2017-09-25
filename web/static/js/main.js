function showMenuItems() {
    var x = document.getElementById("menu");
    if (x.className === "topnav") {
        x.className += " responsive";
    } else {
        x.className = "topnav";
    }
}

$( document ).ready(function(){
    //users page set role routine
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

    //admin main page
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

    //manager main page
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
        $('#appbundle_deal_seed_purchase_price').val( (purchasePrice + logisticPrice)*1.02 );
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

        //(ЕСЛИ(B9>0.48,(B9-0.48)*1.5*B2+B2,ЕСЛИ(B9>0.46,B2,ЕСЛИ(B9>=0.43,B2-(0.46-B9)*2*B2,B2-(0.06+(0.43-B9)*3)*B2)))+B3)*1.02+B14
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

        $('#appbundle_deal_seed_purchase_price_oil').val( parseFloat((price + logisticPrice)*1.02 + oilProcessingCost).toFixed(3) );
        $('#appbundle_deal_seed_purchase_price_oil').change();
    });

    $('#appbundle_deal_seed_purchase_price_oil').change(function(){
        var omegaNumerator = parseInt($('#omegaNumerator').val()) || 0,
            minOmega = parseFloat($('#appbundle_deal_min_omega_coefficient').val());
        omega = 0
        ;
        //calculate omega coefficient
        omega = omegaNumerator / parseInt($(this).val());

        $('#appbundle_deal_omega_coefficient').val( omega );
        $('#appbundle_deal_min_omega_coefficient_excess').val( parseFloat( omega - minOmega ).toFixed(3) );
        $('#appbundle_deal_omega_bonus').val( (parseFloat( omega - minOmega ).toFixed(3))*500 );
    });

    $('#appbundle_deal_seed_purchase_price').change(function(){
        var alphaNumerator = parseInt($('#alphaNumerator').val()) || 0;
        //calculate alpha coefficient
        $('#appbundle_deal_alpha_coefficient').val( parseFloat(alphaNumerator / parseInt($(this).val())).toFixed(3) );
    });

    $('#appbundle_deal_seed_price').change(function(){
        $('#appbundle_deal_logistic_price').change();
    });

    //invoke form params
    $('#appbundle_deal_delivery_price').change();
    $oilContent.change();
});
