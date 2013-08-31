$(document).ready(function(){
    
    $('tr td:not(td:nth-child(1))', '#messages-list').click(function(){
        
        var tr = $(this).closest('tr');
        var table_subject = $(tr).find('.table-subject');
        var a = $(table_subject).find('a');
        
        if($(a).is('a')) {
            var href = $(a).attr('href');
            if(href != '' && href != '#') {
                window.location.replace(href);
            }
        }

        return false;
        
    });
    
    
    $('tr td:nth-child(1)', '#messages-list').click(function(e){
        
        if($(e.target).is('td')) {
            var input = $(this).find('input[type="checkbox"]');
            
            if($(input).is(':checked')) {
                $(this).find('input[type="checkbox"]').removeAttr('checked');
            } else {
                $(this).find('input[type="checkbox"]').attr('checked', 'checked');
            }
            
            
        }
        
    });
    
    $('input.check-all', '#messages-list').click(function(){
        
        if($(this).is(':checked')) {
            $('input[type="checkbox"]', '#messages-list tbody').attr('checked', 'checked');
        } else {
            $('input[type="checkbox"]', '#messages-list tbody').removeAttr('checked');
        }
        
        return true;
        
    });
    
    $('#messages-delete').click(function(){
        
        if(confirm('Are you sure ?')) {
            
            $('#messages-list-form').submit();
            
        }
        
        return false;
    });
    
});