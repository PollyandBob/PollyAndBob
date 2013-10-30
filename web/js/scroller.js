var scroller = {
    
    index   : 1,
    url     : '',
    outern  : null,
    inner   : null,
    endMsg  : 'No more data to load',
    loader  : null,
    indexInUrl : false,
    on      : true,
    ajaxCall: 0,
    noLinks : false,
    
    loadContent : function() {

        if(scroller.on && Math.abs(scroller.outern.offset().top + scroller.outern.height() - (scroller.inner.offset().top + scroller.inner.height())) < 1 && scroller.ajaxCall == 0) {
            scroller.loader.show();
            if(scroller.indexInUrl) {
                var url = scroller.url.split('/');
                url.pop();
                scroller.url = url.join('/')+'/';
            }
            scroller.ajaxCall ++;
            $.ajax({
                url: scroller.url+(++scroller.index),
                success: scroller.onSuccess
            });

        }
    },
    
    onSuccess : function(html) {
        
        scroller.ajaxCall --;
        scroller.loader.hide();
        if(html) {
            scroller.inner.append(html);
            if(scroller.noLinks) {
                clearLinks();
            }
        } else {

            scroller.loader.remove();
            scroller.outern.append('<center>'+scroller.endMsg+'</center>');
            scroller.on = false;
        }
    },
    
    init : function (options) {
        
        if(typeof(options) != 'undefined') for(i in options) {
            if(typeof(this[i]) != 'undefined') this[i] = options[i];
        }
        
        if(null == this.loader) {
            
            this.outern.append('<div id="my-loadMoreAjaxLoader" style="display:none;"><center><img src="/images/ajax-loader.gif" /></center></div>')
            this.loader = $('#my-loadMoreAjaxLoader');
        }
        
        this.outern.scroll(this.loadContent);
    }
}