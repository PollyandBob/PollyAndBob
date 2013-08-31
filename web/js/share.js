

$(document).ready(function() {
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
});


function onFbInit() {

    $('.icon.share').click(function() {
    	FB.ui({
          method: 'feed',
         link: shareExcerpt.link,
          picture: shareExcerpt.picture,
          name: shareExcerpt.name,
          description: shareExcerpt.description,
        }, 
            function(response) {
                console.log('publishStory UI response: ', response);
            });
    });
   
};




