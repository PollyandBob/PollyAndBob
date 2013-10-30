
jQuery(document).ready(function() {	
								
/*
	var id = $('a[name=modal1]').attr('href');
	
		//Get the screen height and width
		var maskHeight = $(window).height();
		var maskWidth = $(window).width();
	
		//Set heigth and width to mask to fill up the whole screen
		$('#mask').css({'width':maskWidth,'height':maskHeight});
		
		//transition effect		
		$('#mask').fadeIn(1000);	
		$('#mask').fadeTo("slow",0.8);	
	
		//Get the window height and width
		var winH = $(window).height();
		var winW = $(window).width();
              
		//Set the popup window to center
		$(id).css('top',  winH/2-$(id).height()/2);
		$(id).css('left', winW/2-$(id).width()/2);
	
		//transition effect
		$(id).fadeIn(2000); 
	
*/		
		
	//select all the a tag with name equal to modal
	jQuery('a[name=modal]').click(function(e) {
		//Cancel the link behavior
		e.preventDefault();
		
		//Get the A tag
		var id = jQuery(this).attr('href');
	
		//Get the screen height and width
		var maskHeight = jQuery(window).height();
		var maskWidth = jQuery(window).width();
	
		//Set heigth and width to mask to fill up the whole screen
		jQuery('#mask').css({'width':maskWidth,'height':maskHeight});
		
		//transition effect		
		jQuery('#mask').fadeIn(1000);	
		jQuery('#mask').fadeTo("slow",0.8);	
	
		//Get the window height and width
		var winH = jQuery(window).height();
		var winW = jQuery(window).width();
		//Set the popup window to center
		if(id == '#dialog2'){
			jQuery(id).css('top', winH/2-jQuery(id).height()/2);
			jQuery(id).css('left', winW/2-jQuery(id).width()/2);
			
			}else{
				
			
		jQuery(id).css('top',  winH/2-jQuery(id).height()/2);
		jQuery(id).css('left', winW/2-jQuery(id).width()/2);
	}
		//transition effect
		jQuery(id).fadeIn(2000); 
	
	});
	
	//if close button is clicked
	jQuery('.window .close').click(function (e) {
		//Cancel the link behavior
		e.preventDefault();
		
		jQuery('#mask').fadeOut(500);
		jQuery('.window').fadeOut(500);
	});	
	jQuery('.window .close1').click(function (e) {
		//Cancel the link behavior
		e.preventDefault();
		
		jQuery('#dialog6').fadeOut(500);
		jQuery('#dialog11').fadeOut(500);
		jQuery('#dialog12').fadeOut(500);
		jQuery('#dialog13').fadeOut(500);
		jQuery('#dialog14').fadeOut(500);
		jQuery('#dialog15').fadeOut(500);
		jQuery('#dialog16').fadeOut(500);
		jQuery('#dialog17').fadeOut(500);
		jQuery('#dialog18').fadeOut(500);
		jQuery('#dialog19').fadeOut(500);
		jQuery('#dialog20').fadeOut(500);
		jQuery('#dialog21').fadeOut(500);
		jQuery('#dialog22').fadeOut(500);
		jQuery('#dialog23').fadeOut(500);
	});	
    
    jQuery('.make_listing').click(function (e) {
		//Cancel the link behavior
		e.preventDefault();
		jQuery('#dialog5').hide();
		jQuery('#dialog6').hide();
		jQuery('#dialog11').hide();
		jQuery('#dialog12').hide();
		jQuery('#dialog13').hide();
		jQuery('#dialog14').hide();
		jQuery('#dialog15').hide();
		jQuery('#dialog16').hide();
		jQuery('#dialog17').hide();
		jQuery('#dialog18').hide();
		jQuery('#dialog19').hide();
		jQuery('#dialog20').hide();
		jQuery('#dialog21').hide();
		jQuery('#dialog22').hide();
		jQuery('#dialog23').hide();
	});	
	jQuery('.window .close2').click(function (e) {
		//Cancel the link behavior
		e.preventDefault();
		
		jQuery('#dialog7').fadeOut(500);
	});	
	
	//if mask is clicked
	jQuery('#mask').click(function () {
	//	jQuery(this).hide();
	//	jQuery('.window').hide();
	});	
	
	jQuery('.reviews').click(function(event) {
		//event.preventDefault();
		jQuery(this).siblings(".reviewdetail").slideToggle('fast', function() {
        });                 
     });
	jQuery('.reviewopen').click(function(event) {
    	 //event.preventDefault();
		jQuery(this).parent('.reviewdetail').slideToggle('fast', function() {
     	});
     });
	
	jQuery('.comments').click(function(event) {
		//event.preventDefault();
		jQuery(this).siblings(".commentdetail").slideToggle('fast', function() {
        });                 
     });
	jQuery('.commentopen').click(function(event) {
    	 //event.preventDefault();
		jQuery(this).parent('.commentdetail').slideToggle('fast', function() {
     	});
     });
	
	jQuery('.selectoptions').click(function(event) {
		//event.preventDefault();
		jQuery(this).siblings(".reviewdetial").slideToggle('fast', function() {
        });                 
     });
	jQuery('.selectoptionsopen').click(function(event) {
    	 //event.preventDefault();
		jQuery(this).parent('.reviewdetial').slideToggle('fast', function() {
     	});
     });
});
