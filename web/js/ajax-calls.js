		function selectedOptions(obj)
        {
			var cookies = 0;
			// Select all
			var selectAll = 0;
			if(obj.id=='option_button_all')
			{
				 selectAll = 1;
				 $("input[name='selectoptions']").each(function () {
		                $(this).attr("checked", true);
		         });
			}
			$('#search').val('');
			// Your Selected options Function
        	var checkValues = $('input[name=selectoptions]:checked').map(function()
                    {
                        return $(this).val();
                    }).get();
            var checkTexts = $('input[name=selectoptions]:checked').map(function()
                    {
                		return $(this).attr('id');
                    }).get();
                        
            var html="";
			var newHtml= '';
			
			if(obj.id!='option_button_all' && obj.id!='option_button')
			{
				cookies = 1;
				checkValues = '';
				if($('#'+obj.id).hasClass('selected'))
				{
					  if(obj.id==6)
					  {
						  $('#12').removeClass("selected");
					  }
					  if(obj.id==7)
					  {
						  $('#13').removeClass("selected");
					  }
					  if(obj.id==2)
					  {
						  $('#8').removeClass("selected");
					  }
					  $('#'+obj.id).removeClass("selected");
				} 
				else
				{
					  if(obj.id==6)
					  {
						  $('#12').addClass("selected");
					  }
					  if(obj.id==7)
					  {
						  $('#13').addClass("selected");
					  }
					  if(obj.id==2)
					  {
						  $('#8').addClass("selected");
					  }
					$('#'+obj.id).addClass("selected");
				}
				var count = 0;
				var allRedSelected = "blue";
				var checkButtons = $('.optionsdetail > .selected').map(function()
	                    {
							count++;
	                		return $(this).attr('id');
	                	}).get();
				checkValues = checkButtons;
				if(count==13)
				{
					allRedSelected = "red";
				}
			}
			else
			{
				
				for (var i in checkTexts) {
					  e = checkTexts[i];
					  v = checkValues[i];  
					  if(e!='offerevents' && e!='offerothers' && e!='offergroups')
					  {
						  html +="<a href='javascript:void(0)' id='"+v+"' class='blue_button' onclick='selectedOptions(this)' >"+e+"</a>";  
					  }
					  else
					  {
						  html +="<a href='#' style='display:none;' id='"+v+"' class='blue_button' onclick='selectedOptions(this)' >"+e+"</a>";
					  }	  
					   
				};
				$(".optionsdetail").html(html);
				
			}
			//alert(checkValues);
			
			$(".selectoptions").siblings(".reviewdetial").slideUp('slow', function() {
            });
            //
            
            // Search box function 
			var searchstr = $(obj).parent().find('#search').attr('name');
			var keywords = '';
			if(searchstr=='keyword')
			{
				keywords = $(obj).parent().find('#search').val();
			}
			else
			{
				keywords = $('#search').val();
			}
            
            // Sort By Function 
            var sortbys="";
            $(".loginwindow3").hide();
            if(obj.id=='date' || obj.id=='distance' || obj.id=='relevance'){
            	$('.loginwindow3 #'+obj.id).css('color','#51b9d4');
            	$('.loginwindow3 #'+obj.id).parent().siblings().children().css('color','#333333');
            }
            
            $(".loginwindow3 ul li").each(function(){
                var current = $(this).find('a');
                if(current.attr('style')=="color: rgb(81, 185, 212);")
                {
                	sortbys = current.attr('id');
                }
            });
            var checkValue = obj.id;
            
            if(!sortbys)
            {
            	sortbys = checkValue;
            }
                        
			
			// Around You function 
			var distance = $('#filter-distance-slider .ui-slider-handle').find('span .tooltip-value').text();
			var text = $('#filter-distance-slider .ui-slider-handle').find('span .tooltip-value').text();
			distance = distance.slice(0, -2) * 1000;
			
			if(text=='no limit')
			{	
				distance = '';
			}
			
			// WHEN between two dates Function
			var starttime = $('#slider-range a.ui-slider-handle:first').attr('id');
			starttime = starttime.slice(0,-3);
			var endtime = $('#slider-range a.ui-slider-handle:last').attr('id');
			endtime = endtime.slice(0,-3);
			var whendate ='';
			//alert(starttime);
			if(starttime!='' && endtime !='' && selectAll==0 && allRedSelected=="blue")
			{
				whendate = starttime+'to'+endtime;
			}
			
			//NOW function for search
			var nowtime = '';
			var classnow =  $(obj).attr('class');
			if(classnow=='nowtime')
			{
				nowtime = obj.id;
			}
			var neighbors = 0;
			var searchQ = '';
			var invar = $('.optionsdetail > a.selected').attr('id');
			//alert(invar);
			if(invar==1)
			{
				 neighbors = 1;
				 searchQ = '/search?find=neighbors';
			}
			
            $.ajax({
                url: '/app_dev.php/listings'+searchQ,
                type: 'post',
                data: { ids: checkValues, sortby: sortbys, aroundyou: distance, when: whendate, now: nowtime,keyword: keywords, cookies: cookies,findNeighbor: neighbors },
                beforeSend : function() {
					$(".ajax_loader_info").show();
					$('.no_find_newsfeeds').hide();
					$(".feedsdetail").hide();
				},
                success:function(data){
                	//alert(data);
                	newHtml = $(data).find(".feedsdetail").html();
                	var count = $(data).find(".around").html();
                	if(count[0]=='0' && neighbors==0)
                	{
                		$(".feedsdetail").html('');
                		$('.no_find_newsfeeds').show();
                	}
                	else
                	{
                		$('.no_find_newsfeeds').hide();
                		$(".feedsdetail").html(newHtml);
                		if(sortbys=='distance')
                        {
                        	//alert('test');
                        	var sortedList = $(data).find('.newsfeeds').toArray().sort(function(lhs, rhs){ 
                                return parseInt($(lhs).children("span.votes").text(),10) - parseInt($(rhs).children("span.votes").text(),10); 
                             });
                            $(".feedsdetail").html(sortedList);
                            
                        }
                		if(count[0]=='0' && neighbors==1)
                    	{
                    		$(".no_find_neighbor").show();
                    	}
                		
                	}
                	
                	$(".around").html(count);
                	if(sortbys!='date' && sortbys!='relevance')
                	{	
	                	$('#container > div.newsfeeds').sort(function(a,b) {
	       			     return $(a).attr('id') > $(b).attr('id') ? 1 : -1;
	                	}).appendTo('#container');
                	}
                	else if(sortbys=='date')
                	{
                		$('#container > div.newsfeeds').sort(function(a,b) {
   	       			     return $(a).attr('style') < $(b).attr('style') ? 1 : -1;
   	                	}).appendTo('#container');
                	}
                	
                },
                complete : function() {
					$(".ajax_loader_info").hide();
					$(".feedsdetail").show();
				}
            });
            
        }


        function clearOptions()
        {
        	$('.selectoptions').css('pointer-events', 'auto');
        	$('#slider-range').css('pointer-events', 'auto'); 
        	$('.loginwindow3 #date').css('pointer-events', 'auto');
        	$('.loginwindow3 #relevance').css('pointer-events', 'auto'); 
        	
        	
        	var checkValues = $('input[name=selectoptions]:checked').map(function()
                    {
                        return $(this).val();
                    }).get();
            var checkTexts = $('input[name=selectoptions]:checked').map(function()
                    {
                		return $(this).attr('id');
                    }).get();
            $('input[name=selectoptions]:checked').prop('checked', false);
            
            var selectedRed = "";
            var checkButtons = $('.optionsdetail > .selected').map(function()
                    {
                		return $(this).attr('id');
                    }).get();
            for (var i in checkButtons) {
				
            	if(checkButtons[i]!="")
            	{
            		selectedRed = 1;
            	}
			}
			
            if(selectedRed==1)
            {
            	$(".optionsdetail a").attr('class', 'blue_button');
            }
            else
            {
            	$(".optionsdetail").html('');
            }
            
            if(checkValues=='')
            {
            	checkValues = $('input[name=selectoptions]').map(function()
                        	  {
                            		return $(this).val();
                        	  }).get();
            }
            
            var newHtml= '';
            $.ajax({
                url: '/app_dev.php/listings',
                type: 'post',
                data: { ids: checkValues },
                beforeSend : function() {
					$(".ajax_loader_info").show();
					$(".feedsdetail").hide();
				},
                success:function(data){
						//alert(data);	
                	newHtml = $(data).find(".feedsdetail").html();
                	var count = $(data).find(".around").html();
                	$(".feedsdetail").html(newHtml);
                	$(".around").html(count);
                	
                	$('#container > div.newsfeeds').sort(function(a,b) {
          			     return $(a).attr('id') > $(b).attr('id') ? 1 : -1;
                   	}).appendTo('#container');
                   	
                	
                },
                complete : function() {
					$(".ajax_loader_info").hide();
					$(".feedsdetail").show();
				}
            });		 	
        }

		function timeStart()
		{
			 var time = new Date();
		     var time = time.getTime();
		     return time;  
		}

		function timeEnd()
		{
			 var today = new Date();
			 var theDate = new Date(today.getTime() + (30 * 86400000));
			 myDate = theDate.getTime();
		     return myDate;       
		}
		
		function addNeighbor(neighborid, obj, neighbourRequestId)
		{			
			var url = "/user/addneighbors";

			$.ajax({
				url : url,
				type : "post",
				data : {
					'neighborId' : neighborid,
					'neighbourRequestId': neighbourRequestId,
					'rejectN': obj.id
					},
				beforeSend : function() {
					
				},
				success : function(response) {		
					if(response == 'rejected')
					{
						$('#neighbor_reject').click();
						window.location.reload();
					}
					else
					{
						$('#neighbor_success').click();	    		    
						$('.reject_n').hide();
						$('.added_n').hide();
						$('#redtitleN'+neighbourRequestId).html('accepted');
					}
				},
				error : function() {
					alert('Something went wrong!');
				},
				complete : function() {
					
				}
			});
		}
		
		function removeNeighbor(neighborid, obj, neighbourRequestId)
		{
			//alert(neighborid);
			var url = "/user/addneighbors";
				
			$.ajax({
				url : url,
				type : "post",
				data : {
					'neighborId' : neighborid,
					'remove' : obj.id,
					'neighbourRequestId': neighbourRequestId,
					},
				beforeSend : function() {
					
				},
				success : function(response) {					
	    		    $('#neighbor_remove').click();
	    		    $(obj).hide();
	    		    $('.added_n').show();
				},
				error : function() {
					alert('Something went wrong!');
				},
				complete : function() {
					
				}
			});
		}
		
		function registerFormsubmit(obj)
		{			 
			 var form = obj.id;
			 var $url = $('.'+form).attr('action');
		     var $data = $('.'+form).serialize();
		     $.ajax({
		        type: "POST",
		        url: $url,
		        data: $data,
		        beforeSend: function()
		        {
		        	$(".ajax_loader_info").show();
		        	$("#dialog10").css('opacity','0.8');
		        },
		        success : function(response) {
		         var errors  = $(response).find('.form-error').html();
		    	 $('.'+form).find('.form-error').html(errors);
		    	 if(errors)
		    	 {
		    		 $(".ajax_loader_info").hide();
		    		 $("#dialog10").css('opacity','1');
		    		 $('.register-form .form-error ul:first').find('li:last').hide();
		    	 }
		    	 else
		    	 {		    		 
		    		 window.location.reload();
		    	 }
		        },
		        complete : function() {
		        	 $(".ajax_loader_info").hide();
		    		 $("#dialog10").css('opacity','1');
				}
		     
		     });
		    	 
		}