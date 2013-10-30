var loginMenuTO;
var menuDisappearTime = 470;

$(document).ready(function() {
   
     /* lang */
    $("#lang-menu").click(function(event) {

        if ($('#lang-menu-wrapper').css('display') == 'none') {
            $('#lang-menu-wrapper').show().position({
                my: "right bottom",
                at: "right bottom",
                of: $("#lang-menu"),
                collision: "none"
            });

            $(this).addClass("active");
        }
        else {
            $('#lang-menu-wrapper').hide();
            $(this).removeClass("active");
        }


        event.stopPropagation();
        event.preventDefault();


    }).mouseleave(function() {
        loginMenuTO = window.setTimeout(function() {
            $('#lang-menu-wrapper').hide();
            $("#lang-menu").removeClass("active");

        }, menuDisappearTime);

    }).mouseenter(function() {
        if ($('#lang-menu-wrapper').css('display') != 'none') {
            window.clearTimeout(loginMenuTO);
        }

    });

    $('#lang-menu-wrapper').click(function(event) {

    }).mouseleave(function() {
        loginMenuTO = window.setTimeout(function() {
            $('#lang-menu-wrapper').hide();
            $("#lang-menu").removeClass("active");
        }, menuDisappearTime);

    }).mouseenter(function() {
        window.clearTimeout(loginMenuTO);
    });

})