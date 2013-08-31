var loginMenuTO;
var menuDisappearTime = 470;

$(document).ready(function() {

    

    $('#_submit').click(function() {
        $('#login-wrapper form').submit();
    });
    $('#logout').click(function() {
        location.href = this.href;
    });
    $('html').click(function() {
        $("#login-wrapper").hide();
        
        $("#user-account-login > li").removeClass("active");
        
    });

    $("#user-account-login #user-account-login-login").click(function(event) {



        if ($('#login-wrapper').css('display') == 'none') {
            $('#login-wrapper').show().position({
                my: "right top",
                at: "right bottom",
                of: $("#user-account-login-login a"),
                collision: "none"
            });

            $(this).addClass("active");
        }
        else {
            $('#login-wrapper').hide();
            $(this).removeClass("active");
        }


        event.stopPropagation();
        event.preventDefault();


    }).mouseleave(function() {
        loginMenuTO = window.setTimeout(function() {
            $('#login-wrapper').hide();
            $("#user-account-login > li").removeClass("active");

        }, menuDisappearTime);

    }).mouseenter(function() {
        if ($('#login-wrapper').css('display') != 'none') {
            window.clearTimeout(loginMenuTO);
        }

    });

    $('#login-wrapper').click(function(event) {
        event.stopPropagation();
        event.preventDefault();
    }).mouseleave(function() {
        loginMenuTO = window.setTimeout(function() {
            $('#login-wrapper').hide();
            $("#user-account-login > li").removeClass("active");
        }, menuDisappearTime);

    }).mouseenter(function() {
        window.clearTimeout(loginMenuTO);
    });




    $.blockUI.defaults.css = {};


    $("#user-account-login #user-account-login-signup").click(function(event) {

        $.blockUI({
            message: $('#signup-wrapper'),
            onOverlayClick: function() {
                $.unblockUI();
            },
            showOverlay: true,
            css: {
                border: "none",
                cursor: "default",
                width: "100%",
                textAlign: "center",
                backgroundColor: "transparent",
                top: "130px"
            },
            overlayCSS: {
                backgroundColor: '#fff',
                opacity: 0.8,
                cursor: 'default'
            }
        });

        $("div.blockMsg").css('height', '0px').css('overflow', 'visible');
        event.preventDefault();
    });



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


    /*top user panel*/
    $("#user-account-login-menu").click(function(event) {

        console.log(event.target);
        console.log($(event.target).closest('#user-account-login-menu-wrapper'));

        if (!$(event.target).is('#user-account-login-menu-wrapper') && !$(event.target).closest('#user-account-login-menu-wrapper').is('ul')) {

            if ($('#user-account-login-menu-wrapper').css('display') == 'none') {
                $('#user-account-login-menu-wrapper').show().position({
                    my: "left top",
                    at: "left bottom",
                    of: $("#user-account-login-menu"),
                    collision: "none",
                    offset: "1 0"
                });

                $(this).addClass("active");
            }
            else {
                $('#user-account-login-menu-wrapper').hide();
                $(this).removeClass("active");
            }


            event.stopPropagation();
            event.preventDefault();

        }
    }).mouseleave(function() {
        loginMenuTO = window.setTimeout(function() {
            $('#user-account-login-menu-wrapper').hide();
            $("#user-account-login-menu").removeClass("active");

        }, menuDisappearTime);

    }).mouseenter(function() {
        if ($('#user-account-login-menu-wrapper').css('display') != 'none') {
            window.clearTimeout(loginMenuTO);
        }

    });

})