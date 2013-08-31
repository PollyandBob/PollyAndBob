/**
 * @author jtokarski
 */


    window.fbAsyncInit = function() {
        FB.init({"appId":facebookAppId,"xfbml":true,"oauth":true,"status":false,"cookie":true,"logging":true});
        onFbInit();
    };
    
    (function() {
        var e = document.createElement('script');
        e.src = document.location.protocol + "\/\/connect.facebook.net\/en_US\/all.js";
        e.async = true;
        document.getElementById('fb-root').appendChild(e);
    }());
   


function onFbInit() {

$('#connect-facebook').click(function(){
                FB.login(function(response) {
                    if (response.authResponse) {
                        $.ajax({
                            type: "POST",
                            url: fenchy_connect_facebook,
                            data: { token: response.authResponse.accessToken, 
                            	    facebookId: response.authResponse.userID,
                            	    noticeUrl: window.location.href }
                        }).done(function( data ) {
                            if (null != data.url) {
                                window.location.replace( data.url );
                            } else {
                                alert( 'y');
                            }
                        }).fail(function( data ) {
                            if (null != data.responseText) {
                                alert( data.responseText );
                            } else {
                                alert( 'n');
                            }
                        });
                    }
                }, {
                    scope: 'email, user_birthday, user_location'
                });
                return false;
            });


}

