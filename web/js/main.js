$(document).ready(function() {
    $(':input').focus(function(){
            $(this).css("color","black")
        });
    
    $('.ask_again').change(function(){
            address = $(this).val();
                location.href=address;
    });
    
    //File uploader
    var wrapper = $('<div/>').css({height:0,width:0,'overflow':'hidden'});
    var fileInput = $('.hide-me').wrap(wrapper);

    fileInput.change(function(){
        $this = $(this);
        $('#reg_upload_button').text($this.val());
    })

    $('#reg_upload_button').click(function(){
        fileInput.click();
    }).show();
    
    $('input[watermark], textarea[watermark]').each(function () {
        
        var $this = $(this);
        
        if($this.val() == '') {
            
            this.style.setProperty('color', '#d5d5d5');
            $this.val($this.attr('watermark'));
            
        } else if ($this.val() == $this.attr('watermark')) {
            
            this.style.setProperty('color', '#d5d5d5');
        }
    });
    
    $('input[watermark], textarea[watermark]').focus(function () {
        
        var $this = $(this);
        var mark = $this.attr('watermark');
        
        if(mark == $this.val()) {
            $this.val('');
        }
        
        this.style.setProperty('color', 'black');
    });
    
    $('input[watermark], textarea[watermark]').blur(function () {
        
        var $this = $(this);
        var mark = $this.attr('watermark');
        
        if('' == $this.val()) {
            
            $this.val(mark);
            this.style.setProperty('color', '#d5d5d5');
            
        } else {
            
            this.style.removeProperty('color');
        }
    });
    
     $('form input[type="submit"]').click(function () {
         
         var watermarks;
         if($(this).parents('form').length) {
             
             watermarks = $(this).parents('form').find('[watermark]');
             
         } else {
             
             watermarks = $('[watermark]');
         }
         
         watermarks.each(function () {
             
             if($(this).val() == $(this).attr('watermark')) {
             
                 $(this).val('');
             }
         });
     });
	 /*
     var x1 = $(document).height();
     var x2 = $('#content .rounded').offset().top
     var z = x1 - x2 - 125;
     $('#content .rounded').height(z+'px');
     $('#content .rounded .scrollable').height((z - 100)+'px');
	 */
        
    $('.add-sticker').fancybox({type:'iframe'});
    $('input[type="date"]').datepicker();
    $('input[type="datetime"]').datepicker();
});
var refreshGallery = function () {
    $('#gallery-frame').attr('src', $('#gallery-frame').attr('src'));
}

var clearAllWatermarks = function ($form) {
    
    var watermarks;
    if(typeof($form) == 'object') {
        watermarks = $form.find('[watermark]');
    } else {
        watermarks = $('[watermarks]');
    }
         
    watermarks.each(function () {

        if($(this).val() == $(this).attr('watermark')) {

            $(this).val('');
        }
    });
}

var clearLinks = function ($newUrl) {
    
    if(!$newUrl) $newUrl = '#';
    var $a = $('#menubar a:not(.public, .cleared), #sitebody a:not(.public, .cleared)');
    $a.attr('href', $newUrl);
    $a.addClass('cleared');
    
    return $a;
}

var reload = function () {
    
    location.reload(true);
}