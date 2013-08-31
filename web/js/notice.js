var notice = {
    
    typesContainer  : null,
    typeContainers  : null,
    selector        : null,
    save            : null,
    form            : null,
    valid           : true,
    
    init : function () {

        this.typesContainer = $('#types-container');

        this.typeContainers = $('.notice-type');

        this.selector = $('#fenchy_noticebundle_noticetype_type');
        
        $('#fenchy_noticebundle_noticetype_'+this.selector.val()).show();
        if (typeof option !== 'undefined' && option !== null
                                          && option.name == 'direction') {
            $('#fenchy_noticebundle_noticetype_'+this.selector.val()+'_value_'+option.id).hide();
        }
        
        this.selector.change(this.change);
        
        this.save = $('#notice-save');
        
        this.form = $('#notice-form');
        
        this.save.click(function () {
            notice.valid = true;
            clearAllWatermarks(notice.form);
            if(notice.isValid() || notice.valid) {
                notice.form.submit();
            }
        })
    },
    
    isValid : function () {
        
        var $elements = this.form.find('input, textarea, select');
        $elements.each(function () {
            if($(this).attr('required') == 'required' && $(this).val() == '') {
                
                notice.valid = false;
            }
        });
    },
    
    change : function () {
        
        notice.typeContainers.hide();
        $('#fenchy_noticebundle_noticetype_'+notice.selector.val()).show();
        if (typeof option !== 'undefined' && option !== null
                                          && option.name == 'direction') {
            $('#fenchy_noticebundle_noticetype_'+notice.selector.val()+'_value_'+option.id).hide();
        }
    }
     
}

function getBeforeDate(daysBefore)
{
    var todayDate = new Date();
    var beforeDate = new Date();
    
    beforeDate.setTime(todayDate.getTime() - (daysBefore * 24 * 60 * 60 * 1000));  
    
    return beforeDate;    
}

function getAfterDate(daysAfter)
{
    var todayDate = new Date();
    var afterDate = new Date();
    
    afterDate.setTime(todayDate.getTime() + (daysAfter * 24 * 60 * 60 * 1000));  
    
    return afterDate;    
}
