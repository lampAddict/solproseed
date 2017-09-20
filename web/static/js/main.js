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
});
