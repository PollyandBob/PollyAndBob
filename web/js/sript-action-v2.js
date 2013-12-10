$(document).ready(function() {

    if($("section.content .inner-content #reviews-tabs").length){
        $("section.content .inner-content #reviews-tabs").tabs();
    }
    
    if($(".main-wrapper").length && ($('.main-wrapper').outerHeight() < $('#page-wrapper').height())){
        $('body').css('background', '#F4F4F4');
    }
    
    $('.login-with-twitter', '#login-wrapper').click(function(){
        
        var href = $(this).attr('href');
        window.location.replace(href);
        return false;
        
    });
    

    $('.forgot_pass', '#login-wrapper').click(function(){
        
        var href = $(this).attr('href');
        window.location.replace(href);
        return false;
        
    });
    
    if($('.flash-message-overlayer').is('div')) {
        setTimeout(function(){$('.flash-message-overlayer').fadeOut(400);}, 3000);
    }
        
});