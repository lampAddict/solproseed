function showMenuItems() {
    var x = document.getElementById("menu");
    if (x.className === "topnav") {
        x.className += " responsive";
    } else {
        x.className = "topnav";
    }
}

$( document ).ready(function(){
    //users page
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
});
