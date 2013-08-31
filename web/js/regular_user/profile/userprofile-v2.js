$(document).ready(function() {
    
    $('#logout').click(function() {
        location.href = this.href;
    });
    
    var menuDisappearTime = 500;
    
    /*top user panel*/
    $("#user-account-login-menu").click(function(event) {

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
    
    function handleHash(hash) {
        
        var activate = '';
        
        if(hash == '#user-listings') {
            activate = 'tabs-2';
        } else if(hash == '#user-reviews') {
            activate = 'tabs-1';
        } else if(hash == '#user-wrote-reviews') {
            activate = 'tabs-3';
        }
        
        if(typeof tabsJQ != 'undefined') {
            tabsJQ.tabs("select", activate);
        } else if(activate != '') {
            activateTabsJQ = activate;
        }
        
    }
    
    $('a[href="#user-listings"],a[href="#user-reviews"],a[href="#user-wrote-reviews"]').click(function(){
        if(typeof profileHome != 'undefined') {
            handleHash($(this).attr('href'));
        } else {
            window.location = $(this).attr('rel') + $(this).attr('href');
        }
        return false;
    });
    
    if(window.location.hash) {
        handleHash(window.location.hash);
    }
    
    
});






