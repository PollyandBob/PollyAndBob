$(document).ready(function(){
    
    function attachRegisterForm(selector) {
    
    $(selector).click(function(){
        
        $(this).attr('disabled', 'disabled');

        var form = $(this).closest('form');

        var data = $(form).serialize();
        var action = $(form).attr('action');

        var width = $('#signup-wrapper').width();
        var height = $('#signup-wrapper').height();

        var layer = $('<div />').append($('<img />').attr('src', '/images/ajaxgif-big.gif').attr('alt', '').css('margin', '0 auto').css('margin-top', '100px')).css('z-index', '10').css('position', 'absolute').css('background', 'rgba(255, 255, 255, 0.7)').css('width', width).css('height', height).css('padding', '0').css('top', '0').css('left', '0').css('text-align', 'center').appendTo('#signup-wrapper');

        $.post(action, data, function(res){
            if(typeof res.registered != 'undefined' && res.registered == 1) {
                window.location.href = res.redirect;
            } else {
                $('#signup-wrapper').html(res);
                $(layer).remove();
                attachRegisterForm($('button.submit-form','form.register-form'));
            }
            
        });
        
        return false;
        
    });
    
    }
    
    attachRegisterForm($('button.submit-form','form.register-form'));
    
});