		function selectedOptions(obj)
        {
			
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
			
            for (var i in checkTexts) {
			  e = checkTexts[i];
			  if(e!='offerevents' && e!='offerothers' && e!='offergroups')
			  {
				  html +="<a href='#' class='blue_button' >"+e+"</a>";  
			  }
			   
			};
			$(".selectoptions").siblings(".reviewdetial").slideUp('slow', function() {
            });
            $(".optionsdetail").html(html);
            
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
			if(starttime!='' && endtime !='')
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
			
            $.ajax({
                url: '/app_dev.php/listings',
                type: 'post',
                data: { ids: checkValues, sortby: sortbys, aroundyou: distance, when: whendate, now: nowtime,keyword: keywords },
                beforeSend : function() {
					$(".ajax_loader_info").show();
					$(".feedsdetail").hide();
				},
                success:function(data){
                	//alert(data);
                	newHtml = $(data).find(".feedsdetail").html();
                	var count = $(data).find(".around").html();
                	if(count[0]=='0')
                	{
                		$(".feedsdetail").html('<h4>Newsfeeds not found</h4>');
                	}
                	else
                	{
                		$(".feedsdetail").html(newHtml);
                		if(sortbys=='distance')
                        {
                        	//alert('test');
                        	var sortedList = $(data).find('.newsfeeds').toArray().sort(function(lhs, rhs){ 
                                return parseInt($(lhs).children("span.votes").text(),10) - parseInt($(rhs).children("span.votes").text(),10); 
                             });
                            $(".feedsdetail").html(sortedList);
                            
                        }
                		
                	}
                	$(".around").html(count);
                },
                complete : function() {
					$(".ajax_loader_info").hide();
					$(".feedsdetail").show();
				}
            });		 	
        }

//        function sortByOptions(obj)
//        {
//        	var checkValues = $('input[name=selectoptions]:checked').map(function()
//                    {
//                        return $(this).val();
//                    }).get();
//            
//        	var checkValue = obj.id;
//            var newHtml= '';
//            
//            $(".loginwindow3").hide();
//			$('#'+obj.id).css('color','#51b9d4');
//			$('#'+obj.id).parent().siblings().children().css('color','#000000');
//			
//            $.ajax({
//                url: '/app_dev.php/listings',
//                type: 'post',
//                data: { ids:checkValues, sortby: checkValue },
//                beforeSend : function() {
//					$(".ajax_loader_info").show();
//					$(".feedsdetail").hide();
//				},
//                success:function(data){
//                	newHtml = $(data).find(".feedsdetail").html();
//                	var count = $(data).find(".around").html();
//                	$(".feedsdetail").html(newHtml);
//                	$(".around").html(count);
//                },
//                complete : function() {
//					$(".ajax_loader_info").hide();
//					$(".feedsdetail").show();
//				}
//            });		 	
//        }

        function clearOptions()
        {
            var checkValues = $('input[name=selectoptions]:checked').map(function()
                    {
                        return $(this).val();
                    }).get();
            var checkTexts = $('input[name=selectoptions]:checked').map(function()
                    {
                		return $(this).attr('id');
                    }).get();
            $('input[name=selectoptions]:checked').prop('checked', false);
            
            $(".optionsdetail").html('');
            var checkValues = "";
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
		
		function addNeighbor(neighborid,obj)
		{
			//alert(neighborid);
			var url = "/app_dev.php/user/addneighbors";

			$.ajax({
				url : url,
				type : "post",
				data : {
					'neighborId' : neighborid,
					},
				beforeSend : function() {
					
				},
				success : function(response) {
					$('#dialog30 .title').text(obj.id);
	    		    $('.neighbor_success').click();
	    		    $(obj).hide();
	    		    $('.remove_n').show();
				},
				error : function() {
					alert('Something went wrong!');
				},
				complete : function() {
					
				}
			});
		}
		
		function removeNeighbor(neighborid,obj)
		{
			//alert(neighborid);
			var url = "/app_dev.php/user/addneighbors";
				
			$.ajax({
				url : url,
				type : "post",
				data : {
					'neighborId' : neighborid,
					'remove' : obj.id,
					},
				beforeSend : function() {
					
				},
				success : function(response) {
					$('#dialog30 .title').text(obj.id);
	    		    $('.neighbor_success').click();
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
		
