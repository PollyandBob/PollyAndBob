var loginMenuTO;
var menuDisappearTime = 470;

$(document).ready(function() {

    $('#_submit').click(function() {
        $('#login-wrapper form').submit();
    });
    
    $('html').click(function() {
        $("#login-wrapper").hide();        
        $("#user-account-login-login").removeClass("active");        
    });

    $("#user-account-login-login > a").click(function(event) {
    	
        if ($('#login-wrapper').css('display') == 'none') {
            $('#login-wrapper').show().position({
                my: "right top",
                at: "right bottom",
                of: $("#user-account-login-login a"),
                collision: "none"
            });

            $(this).parent('li').addClass("active");
        }
        else {
            $('#login-wrapper').hide();
            $(this).parent('li').removeClass("active");
        }

        event.stopPropagation();
        event.preventDefault();

    }).mouseleave(function() {
        if(!$('#user-login-pass').is(':focus') && !$('#user-login-email').is(':focus')) {
            loginMenuTO = window.setTimeout(function() {
                $('#login-wrapper').hide();
                $("#user-account-login-login").removeClass("active");

            }, menuDisappearTime);
        }

    }).mouseenter(function() {
        if ($('#login-wrapper').css('display') != 'none') {
            window.clearTimeout(loginMenuTO);
        }

    });

    $('#login-wrapper').click(function(event) {
        event.stopPropagation();
        event.preventDefault();
    }).mouseleave(function() {
        if(!$('#user-login-pass').is(':focus') && !$('#user-login-email').is(':focus')) {
            loginMenuTO = window.setTimeout(function() {
                $('#login-wrapper').hide();
                $("#user-account-login-login").removeClass("active");
            }, menuDisappearTime);
        }

    }).mouseenter(function() {
        window.clearTimeout(loginMenuTO);
    });


    $.blockUI.defaults.css = {};

    $("#user-account-login-signup, .sign-up-btn").click(function(event) {

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
   

})