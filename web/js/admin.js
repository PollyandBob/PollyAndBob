$(document).ready(function() {
    $('.has-subrow').click(function () {
        var $subrow = $(this).next();
        if ($subrow.attr('class').indexOf('displayNone') === -1) {
            $subrow.addClass('displayNone');
        } else {
            $subrow.removeClass('displayNone');
        }
    });
    
    $('a.confirm').click(function(){
        if(confirm('Are you sure?')) {
            return true;
        }
        
        return false;
    });
    $('.filter-dp').datepicker({'dateFormat' : 'yy-mm-dd'});
});