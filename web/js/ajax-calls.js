	function selectedOptions(obj)
        {
			var cookies = 0;
			// Select all
                        $('.feedsdetail').css("height","800px");
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
				if(count==14)
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
						  html +="<a href='javascript:void(0)' id='"+v+"' class='blue_button' onclick='selectedOptions(this)' >"+e+"<span id='span"+v+"'>(0)</span></a>";  
					  }
					  else
					  {
						  html +="<a href='#' style='display:none;' id='"+v+"' class='blue_button' onclick='selectedOptions(this)' >"+e+"<span id='span"+v+"'>(0)</span></a>";
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
                        var currentTime = 0;
                        var endDate = 0;
                        var startDate = 0;
                        if(classnow=='nowtime')
			{
				nowtime = obj.id;
                                //alert();
                                endDate = $('#hiddentime').val();
                                startDate = $('#hiddentime2').val();
                                currentTime = $('#hiddentime3').val();
                                $("#slider-range").slider("option", "values", [startDate,currentTime]);
                                var endTimed = new Date();
                                var emonth2 = endTimed.getMonth() + 1;
                                var eday2 = endTimed.getDate();
                                //alert(eday2);
                                var eyear2 = endTimed.getFullYear();
                                if (emonth2 < 10) emonth2 = '0' + emonth2;
                                if (eday2 < 10) eday2 = '0' + eday2;
                                
                                $("#slider-range > a.ui-slider-handle:last" ).attr('id',currentTime);
                                $("#slider-range > a.ui-slider-handle:last" ).html( "<span class='inner-tooltip'><strong class='tooltip-value'>"+eday2 + "." + emonth2 + "." + eyear2+"</strong></span>" );
                                //$("#slider-range").slider('values',startDate,endDate);
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
            var save =1;            
            if(checkValues==false)
            {
                var checkButtons2 = $('.optionsdetail > .blue_button').map(function()
                    {
                		return $(this).attr('id');
                    }).get();
                    checkValues = checkButtons2;
                    //alert(checkValues);
                    save=0;
            }
            if(count==0)
            {
                save=0;    
            }
            $.ajax({
                url: '/listings'+searchQ,
                type: 'post',
                data: { ids: checkValues, sortby: sortbys, aroundyou: distance, when: whendate, now: nowtime,keyword: keywords, cookies: cookies,findNeighbor: neighbors, save:save},
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
                	{       //alert("wrong");
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
                	if(sortbys!='date' && sortbys!='relevance' && sortbys!='distance')
                	{	
	                	$('#container > div.newsfeeds').sort(function(a,b) {
   	       			     return parseInt($(a).attr('style')) < parseInt($(b).attr('style')) ? 1 : -1;
   	                	}).appendTo('#container');
                	}
                        else if(sortbys=='distance')
                	{	
	                	$('#container > div.newsfeeds').sort(function(a,b) {
	       			     return parseInt($(a).attr('id')) > parseInt($(b).attr('id')) ? 1 : -1;
	                	}).appendTo('#container');
                	}
                	else if(sortbys=='date')
                	{
                		$('#container > div.newsfeeds').sort(function(a,b) {
   	       			     return parseInt($(a).attr('style')) < parseInt($(b).attr('style')) ? 1 : -1;
   	                	}).appendTo('#container');
                	}
                        if($('.feedsdetail').html()=="")
                        {
                            $('.feedsdetail').css("height","auto");
                        }
                        //Newsfeed: show amount of listings per option in option tool 
                        var aFirst = checkValues.toString().split(',');
                        var id = 0;
                        var count=0;
                        $('.after-ajax-none').html('');
                        $( "div.optionsdetail a" ).each(function() {
                            id = $( this ).attr( "id" );
                            count= $('div.searchOptionsCalc'+id).length;
                            for (var i = 0; i < aFirst.length; i++) {
                            if (aFirst[i] == id) {
                                //alert('jay!');
                                $('#span'+id).text("("+count+")");
                            }
                            }
                            
                         });
                	
                },
                complete : function() {
					$(".ajax_loader_info").hide();
					$(".feedsdetail").show();
                                        var n = 0;
                                         $(".newsfeeds").each(function(){
                                                n = n+1;
                                          });
                                          $(".around").text(n+' options around you');
				}
            });
            
        }


        function clearOptions()
        {
                $('.selectoptions').css('pointer-events', 'auto');
                $('#slider-range').css('pointer-events', 'auto'); 
                $('.loginwindow3 #date').css('pointer-events', 'auto');
                $('.loginwindow3 #relevance').css('pointer-events', 'auto'); 
                $('.feedsdetail').css("height","800px");
                $('input[name=selectoptions]:checked').prop('checked', false);
                var checkValues = $('input[name=selectoptions]:checked').map(function()
                {
                    return $(this).val();
                }).get();
                var checkTexts = $('input[name=selectoptions]:checked').map(function()
                {
                    return $(this).attr('id');
                }).get();
                

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

                var newHtml= '';
                $.ajax({
                    url: '/listings',
                    type: 'post',
                    data: {
                        ids: checkValues,
                        when: whendate
                    },
                    beforeSend : function() {
                        $(".ajax_loader_info").show();
                        $(".feedsdetail").hide();
                    },
                    success:function(data){
                        //alert(data);	
                        $('.after-ajax-none').html('');
                        newHtml = $(data).find(".feedsdetail").html();
                        var count = $(data).find(".around").html();
                        $(".feedsdetail").html(newHtml);
                        $(".around").html(count);

                        $('#container > div.newsfeeds').sort(function(a,b) {
                            return parseInt($(a).attr('style')) < parseInt($(b).attr('style')) ? 1 : -1;
                        }).appendTo('#container');
                        if($('.feedsdetail').html()=="")
                        {
                            $('.feedsdetail').css("height","auto");
                        }
                        //Newsfeed: show amount of listings per option in option tool 
                        var aFirst = checkValues.toString().split(',');
                        var id = 0;
                        var count=0;
                        $( "div.optionsdetail a" ).each(function() {
                            id = $( this ).attr( "id" );
                            count= $('div.searchOptionsCalc'+id).length;
                            for (var i = 0; i < aFirst.length; i++) {
                                if (aFirst[i] == id) {
                                    //alert('jay!');
                                    $('#span'+id).text("("+count+")");
                                }
                            }

                        });


                    },
                    complete : function() {
                        $(".ajax_loader_info").hide();
                        $(".feedsdetail").show();
                        var n = 0;
                        $(".newsfeeds").each(function(){
                            n = n+1;
                        });
                        $(".around").text(n+' options around you');
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
                        
                        $('.feedsdetail #right'+neighbourRequestId).css('background','none repeat scroll 0 0 #FFFFFF');
                        $('#rightImg'+neighbourRequestId+' img').attr('src','/images/bgright_rightpart.png');
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
						$(obj).parent().find('.reject_n').hide();
						$(obj).parent().find('.added_n').hide();
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
		
                function joinClosedGroup(neighborid, obj, neighbourRequestId,groupId)
		{			
			var url = "/user/joinclosedgroup";

                        $('.feedsdetail #right'+neighbourRequestId).css('background','none repeat scroll 0 0 #FFFFFF');
                        $('#rightImg'+neighbourRequestId+' img').attr('src','/images/bgright_rightpart.png');
                        
			$.ajax({
				url : url,
				type : "post",
				data : {
					'neighborId' : neighborid,
					'neighbourRequestId': neighbourRequestId,
					'rejectN': obj.id,
                                        'groupId': groupId
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
						$(obj).parent().find('.reject_n').hide();
						$(obj).parent().find('.added_n').hide();
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
		    		 //$('.register-form .form-error ul:first').find('li:last').hide();
                                 $('.register-form .form-error li').each(function()
                                 {
                                    var current = $(this);                                    
                                    if(current.text()=='This value is already used.')
                                        $(this).hide();
                                    if(current.text()=='Message cannot be blank, please enter proper message.')
                                        $(this).hide();
                                    
                                 });
		    	 }
		    	 else
		    	 {	
                             
                                var requester = GetURLParameter('requester');
                                 
                                 $.ajax({
                                        type: "POST",
                                        url: '/user/addactivitypoint',
                                        data: {
                                            'requester': requester
                                        },
                                        beforeSend : function() {					
                                        },
                                        success : function(response) { 
                                            //alert('done:');
                                        },
                                        error : function() {                                                
                                        },
                                        complete : function() {	
                                            window.location.reload();
                                        }
                                 });
		    		
		    	 }
		        },
		        complete : function() {
		        	 $(".ajax_loader_info").hide();
		    		 $("#dialog10").css('opacity','1');
			}
		     
		     });
		    	 
		}
                function GetURLParameter(sParam)
                {
                    var sPageURL = decodeURIComponent(window.location.search.substring(1));
                   
                    var sURLVariables = sPageURL.split('&');
                    for (var i = 0; i < sURLVariables.length; i++)
                    {
                        var sParameterName = sURLVariables[i].split('=');
                        if (sParameterName[0] == sParam)
                        {
                            return sURLVariables[i].substr(10);
                        }
                    }
                }
                function tellAFriendsFormsubmit(obj)
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
                               
                                var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
                                var notvalid = 0;
                                $('input[name="friends_email1[]"]').each(function() {
                                    if (!testEmail.test($(this).val()) && $(this).val()!='')
                                    {
                                      notvalid = 1;
                                    }
                                });
                                if(notvalid==1)
                                {
                                    $(".ajax_loader_info").hide();
                                    $(".errors").show();
                                    return false;
                                }
		        },
		        success : function(response) {
		         var errors  = $(response).find('.form-error').html();
		    	 $('.'+form).find('.form-error').html(errors);
                         var success = $(response).find('#successDialog2').text();
                         if(success==1)
                         {
                            $(".errors").hide();
                            $(".ajax_loader_info").hide();
                            $('#successDialog2').click();
                         }
                                
		    	 
		        },
		        complete : function() {
                                 $(".ajax_loader_info").hide();
		        	 $('input[name="friends_email1[]"]').val('');
			}
		     
		     });
		    	 
		}
