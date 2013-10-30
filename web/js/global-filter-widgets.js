var sortbyMenuTO;

$(document).ready(function() {
    
    $("section.content .select-button .replacement").click(function(event) {
        
        if ( $(".select-button .replacement .drop-down").first().css('display') == 'none' ) { 
            $(".select-button .replacement .drop-down").first().show();
            $("section.content .select-button .replacement #sortby-dropdown-icon")
                .removeClass("icon-caret-down").addClass("icon-caret-up");
            event.stopPropagation();
            $('html').click(function() {
                $(".select-button .replacement .drop-down").hide();
                $("section.content .select-button .replacement #sortby-dropdown-icon")
                    .removeClass("icon-caret-up").addClass("icon-caret-down");
            });
        }
        else
            $(".select-button .replacement .drop-down").hide();
    });
    
    
    $(".select-button .replacement .drop-down").mouseenter(function() {
        window.clearTimeout( sortbyMenuTO );
    }).mouseleave(function() {
        sortbyMenuTO = setTimeout(function() {
            $(".select-button .replacement .drop-down").hide();
            $("section.content .select-button .replacement #sortby-dropdown-icon")
                .removeClass("icon-caret-up").addClass("icon-caret-down");            
        }, menuDisappearTime);
    });      
    
    $('#globalFilter .group > header .with-arrow i').click(function(){
        $(this).closest('header').toggleClass('noactive');
        $(this).closest('.group').find(".wrapper").slideToggle(500, function(){
            filterScrollActive();
        });                
    });
    
    $('#globalFilter .group .checkbox-wrapper .with-arrow i').live('click', function(){
        var suvCat = $(this).closest('.checkbox-wrapper').parent('li').children('ul');        
        if(suvCat.find('li').length){
            $(this).closest('.checkbox-wrapper').toggleClass('noactive');
            $(this).closest('.checkbox-wrapper').parent('li').children('ul').slideToggle(400, function(){
                filterScrollActive();
            });                
        }        
    });
         
    $(window).scroll(function() {
        if($('#globalFilter .sidebar > .wrapper').length){
            var filSidRoute = $('#globalFilter .sidebar > .wrapper');
            filterScroll(filSidRoute);
        }  
    });
        
});

function filterScrollActive(){
        var filSidRoute = $('#globalFilter .sidebar > .wrapper');
        var filterPos = filSidRoute.position();
        var filterNewHeight = filSidRoute.outerHeight();
        var pageWrapperHeight = $('#globalFilter').innerHeight();

        if((filterPos.top+filterNewHeight) > pageWrapperHeight){
            filSidRoute.stop(true,false).animate({top : pageWrapperHeight-filterNewHeight}, 400);
        }
}
function filterScroll(filSidRoute){
            
            var filterHeight = filSidRoute.outerHeight();
            var pageWrapperHeight = $('#globalFilter').innerHeight();         

            if(filterHeight < pageWrapperHeight){
                windowScroll = $(window).scrollTop();                
                if((windowScroll+filterHeight) < pageWrapperHeight){
                    filSidRoute.stop(true,false).animate({top : windowScroll}, 0);
                }
                else {
                    filSidRoute.stop(true,false).animate({top : (pageWrapperHeight-filterHeight)}, 0);
                }
            }
}

$(window).load(function() {                
                     	
});