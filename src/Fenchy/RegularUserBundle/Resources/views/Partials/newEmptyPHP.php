 <script type="text/javascript"> 
	 $(document).ready(function() {	
	     $("#offerprice").keydown(function(event) {
	         if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || (event.keyCode == 65 && event.ctrlKey === true) || (event.keyCode >= 35 && event.keyCode <= 39)) {
	            	return;
	         } else {
	             if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && ((event.keyCode < 96 || event.keyCode > 105)  && event.keyCode != 190 )) {
	                 event.preventDefault();
	             }
	         }
	     });
	     $("#pieces").keydown(function(event) {
	         if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || (event.keyCode == 65 && event.ctrlKey === true) || (event.keyCode >= 35 && event.keyCode <= 39)) {
	            	return;
	         } else {
	             if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
	                 event.preventDefault();
	             }
	         }
	     });
             nice = $("#dialog42 #requestDiv").niceScroll();
	 }); 
	 $(function(){
		 $('#startdate').datepicker({
	         dateFormat: 'dd.mm.yy', 
	         stepMinute: 5, 
	         minuteMax: 55
	     });
		 
		 $('#enddate').datepicker({
	         dateFormat: 'dd.mm.yy', 
	         stepMinute: 5, 
	         minuteMax: 55
	     });	 
	 
	 	$('#starttime').timepicker({          
        	stepMinute: 5, 
         	minuteMax: 55
     	});
	 	$('#endtime').timepicker({          
         	stepMinute: 5, 
         	minuteMax: 55
     	});
	 });
		function sendRequest()
		{
			var id = '{{ notice.id }}';
			var request_text = $('#requestdata').val();
			var swap_msg = $('#swapmsg').val();
                        var proposed_location = $('#proposelocation').val();
			var item = $('#pieces').val();
                        var item1 = $('#pieces1').val();
			var price = $('#price').val();				
			var total = $('#total').val();
			var currency = $('#cur option:selected').text();
			var free = $('#freebox').is(':checked');
			var offerprice = $('#offerprice').val();
			var noticetype = '{{ notice.type }}';
			var start_date = $('#startdate').val();
			var start_time = $('#starttime').val();
			var end_date = $('#enddate').val();
			var end_time = $('#endtime').val();
			
			//alert(request_text);
			//alert(item);
			var itemAvailableDisplay = $('#itemAvailable').css('display');
                        //var TakeFreeDisplay = $('#TakeFree').css('display');
			var display = $('#proposeprice').css('display');
			var flag = true;
			if( itemAvailableDisplay != 'none' || (noticetype == "goods" && display != "none"))
			{				
				var item = parseInt($('#pieces').val(), 10);				
				var ava_item = parseInt($('#hiddentext').val(), 10);
							
				if(item <= 0 || !item)
				{
					alert('SYSTEM IS ASKING TO ENTER MORE THAN ONE ');
					flag = false;
				}
			}	
			
			
			if(display != 'none')
			{
                            
				if($('#freebox').is(':checked') && ( $('#offerprice').val()!="" && $('#offerprice').val()!="0" ) )
				{
					alert('GIVE PRICE OR SELECT THE FREE');
					flag = false;
				}
				if(!$('#freebox').is(':checked') && ( $('#offerprice').val() =="" || $('#offerprice').val()=="0") )
				{
					alert('GIVE PRICE OR SELECT THE FREE');
					flag = false;
				}
				if(!$('#freebox').is(':checked'))
				{
					if(currency == 'CURRENCY')
					{
						alert('SELECT CURRENCY');
						flag = false;
					}
				}
				if(noticetype ==  'service' || noticetype == "goods")
				{
                                    if(!$('#freebox').is(':checked'))
                                    {
					if(!$('#default_settings').is(':checked'))
					{
						alert('CHECK DEFAULT PAYMENT SETTINGS');
						flag = false;
					}
                                    }
				}
			}
			$('#requestdata').focus(function()
			{
				$('#requestdata').val('');
			});
			{#if($('#requestdata').val() == "" || $('#requestdata').val() == '*REQUIRED')
			{
				$('#requestdata').val('*REQUIRED');
				flag = false;
			}#}
                                //alert(flag);
                                //flag = false;
			if(flag)
			{
				var url = "{{path('fenchy_notice_send_reueqst')}}";
				$.ajax({
					url : url,
					type : "post",
					data : {
						'noticeId': id,					
						'request_text': request_text,
						'item': item,
						'price': price,
						'total': total,
						'currency': currency,
						'free': free,
						'offerprice': offerprice,
						'swap_msg': swap_msg,
                                                'proposed_location': proposed_location,
						'start_date': start_date,
						'start_time': start_time,
						'end_date': end_date,
						'end_time': end_time
						},
					beforeSend : function() {
						
					},
					success : function(response) {
						//alert('Your request send');
						$('#RequestSend').click();
						$('#dialog42').hide();
					},
					error : function() {
						alert('Something went wrong!');
					},
					complete : function() {
						$('#requestdata').val('');
						$('#pieces').val('1');
						var price = "{{ notice.price }}";
						$('#price').val(price);
						$('#total').val('0');
						$('#cur option:selected').text('CURRENCY');
						$('#freebox').prop('checked', false);
						//$('#freebox').attr('checked' : false);
						$('#default_settings').prop('checked' , false);
						$('#offerprice').val('');
						$('#swapmsg').val('');
                                                $('#proposelocation').val('');                                                
						$('#startdate').val('');
						$('#starttime').val('');
						$('#enddate').val('');
						$('#endtime').val('');	
						//setTimeout(function(){ location.reload(); }, 100);
					}
				}); 
			}				
		}
		$(function(){
			$('#default_settings').click(function(){
				var payment = '{{ payment }}';
               	if(!payment)
               	{
                   	$('#dialog42').hide();
	            	$('#SetPayments').click();
	                $('#default_settings').prop( "checked", false );
               	}
			});
		});
		$(function(){
			  $('#freebox').click(function(){
					var item = parseInt($('#pieces').val(), 10);
					var price1 = '{{ notice.price }}'; 
                                        //alert(item);
				  	if($('#freebox').is(':checked'))
				  	{
					  	$('#price').val(0);	
					  	$('#offerprice').val(0);
						$('#total').val(0);	
				  	}
				  	else
				  	{
                                            if(price1>0)
				  		$('#price').val(price1);
				  		$('#total').val(item * parseFloat($('#price').val()));  
				  	}
			  });
		});
                $(function(){
                    $('#cur').change(function(){
                        currency = $('#cur option:selected').text();
                        noticeCur = '{{ notice.currency }}';
                        if(noticeCur == 'Dollar')                        
                            original = '$';
                        else
                            original = '€';
                        if(currency == 'EURO')                        
                            $('#totalpricebox span').html('€');
                        else if(currency == 'DOLLAR')
                            $('#totalpricebox span').html('$');
                        else
                            $('#totalpricebox span').html(original);
                    });
                });
		$('#freebox').is(':checked');
		$(function(){
			  $('.zoomthis').click(function(event){

				var piece = 1;
			    var price1 = '{{ notice.price }}';
			    var piece = '{{notice.pieces}}';
                            var spot = '{{ notice.spot }}';
                            var unlimited = '{{ notice.unlimited }}';
                            var free = '{{ notice.free }}';
                            
                            if(free)
                            {
                                $('#totalpricebox').hide();
                            }
                            
			    $('#proposeprice').hide();
			    $('#proposeTime').hide();
			    $('#Swap').hide();
                            $('#ProposeLocation').hide();
			    //$('#TakeFree').hide();
                            
			    var id = event.target.id;
			    
			    if(id== 'swap_it' || id== 'to_swap')
			    {
				    $('#Swap').show();
			    }
                            if(id == 'Propose_location')
			    {
				    $('#ProposeLocation').show();
			    }
			    if(id=='propose_time_price')
			    {
			    	$('#proposeTime').show();
			    }
			    if(id=='propose_time_price' || id=='propose_price' || id=='to_sell' || id=='to_lend')
			    {
				    $('#proposeprice').show();
			    }
			    if(id=='propose_time_price' || id=='propose_price' || id=='buy' || id=='buy_a_spot' || id=='to_sell' || id=='to_lend')
			    {
                                $('#totalpricebox').show();
                                if(price1>0)
                                    $('#price').val(price1);
                                else
                                    $('#price').val(0);
					$('#total').val(1 * parseFloat($('#price').val(), 10));  
                                var width = parseInt($('#totalpricebox input').val().length) * 8 + 'px';                                            
                                $('#totalpricebox input').css('width', width);
			    }
			    else
			    {
			    	//$('#pieces').val(0);
			    	$('#price').val(0);
			    	$('#total').val(0);
                                $('#totalpricebox').hide();
			    }
                            if(id=='take_free' || id=='to_give')
                            {
                                 //$('#itemAvailable').hide();
                                 //$('#TakeFree').show();
                            }
                            else
                            {
                                if(spot>1 || piece >1 || unlimited=='true')
                                    $('#itemAvailable').show();
                                 //$('#TakeFree').hide();
                            }
			  });
			});
		$(function(){
			  $('#pieces').blur(function(){
				var item = parseInt($('#pieces').val(), 10);
				var price = parseFloat($('#price').val(), 10);
				var ava_item = parseInt($('#hiddentext').val(), 10);
                                //alert(item +'=>'+price +'=>'+ava_item);
                                
				var type = "{{ notice.type }}";
                                var unlimited = "{{ notice.unlimited }}";
								
					if(item > ava_item && !unlimited)
					{
						$('#pieces').val(ava_item);
						if(type=="goods")
							alert("ONLY " + ava_item + " REQUIRED");
						else
							alert("ONLY " + ava_item + " AVAILABLE");
						$('#total').val(parseInt($('#pieces').val(), 10) * price);
					}					
					else
						$('#total').val(parseInt($('#pieces').val(), 10) *price);
                                  var width = parseInt($('#totalpricebox input').val().length) * 8 + 'px';                                            
                                  $('#totalpricebox input').css('width', width);
												   
			  });
			});		
		$(function(){
			  $('#offerprice').blur(function(){
				var item = parseInt($('#pieces').val(), 10);
				var price = $('#offerprice').val();
				var price1 = '{{ notice.price }}'; 
				if(price=="")
					$('#price').val(price1);
				else
					$('#price').val(price);	
				$('#total').val(item * parseFloat($('#price').val(), 10));
			  });
			});
		function sendMsg()
		{
			var flag = true;
			var title = $('#MsgTitle').val();
			var content = $('#MsgContent').val();			
			
			if(title == "" || content == "")
			{
				flag = false;
				$('#MsgTitle').attr('placeholder', 'required*');
				$('#MsgContent').attr('placeholder', 'required*');
			}
                        if(title.trim() == "" || content.trim() == "" )
                        {
                                flag = false;
				$('#MsgTitle').val('');
				$('#MsgContent').val('');
                                $('#MsgTitle').attr('placeholder', 'required*');
				$('#MsgContent').attr('placeholder', 'required*');
                        }
			var receiver = "{{ notice.user.id }}";
                        
                        {% if notice.usergroup %}
                            var groupid = "{{ notice.usergroup.id }}";
                        {% else %}
                            var groupid ='';
                        {% endif %}
			if(flag)
			{
				var url = "{{path('fenchy_regular_user_sendmsgform')}}";
				$.ajax({
					url : url,
					type : "post",
					data : {
						'receiverId': receiver,				
						'title': title,
						'content': content,
                                                'groupId': groupid
						},
					beforeSend : function() {
						
					},
					success : function(response) {
						$('#dialog44').hide();
						$('#successDialog').click();
					},
					error : function() {
						alert('Something went wrong!');
					},
					complete : function() {
						$('#MsgTitle').val('');
						$('#MsgContent').val('');					
					}
				}); 
			}		
		}
</script>
{# empty Twig template #}
<style>
.rightparts .profile .person img {
    height: 152px;
    width: 152px;
}
.bgbottom {
    bottom: -20px;
    margin-left: -14px;
    margin-top: 0;
    position: relative;
    right: 0;
}
</style>
<div class="rightparts">
	<div class="right" >
  	  	<div class="options">
  	  	{% if notice.type != "neighbours" and notice.type != "groups" and notice.type != "offergroups" %}
	    	{% set price = notice.price %}
	    	{% if (price|length) > 0 %}
	    		<h4>{{ price }} {% if notice.currency !="currency" %} {{ notice.currency |trans }}{% else %} {{ 'euro'|trans }} {% endif %} 
	    		{% if notice.type == 'offerevents' %}
    				{{ 'notice.perspot' | trans }}
    			{% elseif notice.type == 'offerservice' %}
    				{{ 'notice.perhour' | trans }}
    			{% elseif notice.type == 'offergoods' %}
    				{{ 'notice.perpiece' | trans }}
    			{% endif %}
    			</h4>
    		{% elseif notice.type.priceAvailable %}
    			<h4> Free </h4>    			
    		{% endif %}
    	{% endif %}
    		<div class="optionsdetail">
                {% for value in notice.values %}
                	{% set options = value.valueAsString | split('^') %}
                	{% for option in options %}
						{% if option %}
							{% if app.user.id is defined and app.user.id != displayUser.id and (option != "need" and option != "offer")%}
								{% if notice.completed or notice.closed or oneRequest %}
									<a  id="{{ option }}" href="#dialog43" name="modal" id="vzoom_1944" class="zoomthis blue-button">{% if oneRequest and option=='take_part' %}  {% if oneRequest.status == 'accepted' %} {{ 'notice.you_take_part'|trans }} {% else %} {{ 'notice.you_requested'|trans }}{% endif %} {% else %}{{ option|trans }}{%  if option == "contact_me" %}&nbsp;{{ displayUser.name }}!{% endif %}{% endif %}</a>
								{% else %}
                                                                        <a  id="{{ option }}" href="#dialog42" name="modal" id="vzoom_1944" class="zoomthis blue-button" id="{{ option }}">{% if oneRequest and option=='take_part' %}  {% if oneRequest.status == 'accepted' %} {{ 'notice.you_take_part'|trans }} {% else %} {{ 'notice.you_requested'|trans }} {% endif %} {% else %}{{ option|trans }}{%  if option == "contact_me" %}&nbsp;{{ displayUser.name }}!{% endif %}{% endif %}</a>
                                                                {% endif %}
                                                        {% elseif (option != "need" and option != "offer")%}
                                                            <a  id="{{ option }}" href="#dialog43" name="modal" id="vzoom_1944" class="zoomthis blue-button" id="{{ option }}">
                                                                    {% if oneRequest and option=='take_part' %}  {% if oneRequest.status == 'accepted' %} You take part {% else %} {{ 'notice.you_requested'|trans }} {% endif %} {% else %}{{ option|trans }}{%  if option == "contact_me" %}&nbsp;{{ displayUser.name }}!{% endif %}{% endif %}</a>
                                                        {% endif %}
                                                {% endif %}
    				{% endfor %}
    			{% endfor %}
            </div>
            <div class="clearfix"></div>
            {% if notice.type != "neighbours" and notice.type != "groups" and notice.type != "offergroups" %}
            
            <h2>
            {% if notice.unlimited %}
	    		{{ 'listing.create.unlimited'|trans }}&nbsp;{{ 'notice.spot'|trans }} {{ 'request.left'|trans }}
	    	{% endif %}
	    	{% if notice.onePiece or notice.pieces == 1%}
	    		1 &nbsp;{{ 'listing.create.piece'|trans }} {{ 'request.left'|trans }}
	    	{% endif %}
	    	{% if notice.pieces > 1%}
	    		{{ notice.pieces }} &nbsp;{{ 'listing.create.pieces'|trans }} {% if notice.type=='goods' %} required {% else %} {{ 'request.left'|trans }} {% endif %}
	    	{% endif %}
	    	{% if notice.spot > 0 %}
	    		{{ notice.spot }}&nbsp;{% if notice.spot == 1 %} {{ 'listing.create.spots'|trans }} {% else %}{{ 'listing.create.spot'|trans }} {% endif %} {{ 'request.left'|trans }}
	    	{% endif %}
           <br>
	    		{% if notice.startDate %}
	    			{{ 'regularuser.message.date'|trans }} {{ notice.startDate|date('d.m.Y')}}
	    				
	    		{% endif %}
	    		{% if notice.endDate %}
	    			{% if date(notice.endDate) != date(notice.startDate) %}
	    				{{ 'regularuser.message.to'|trans }} {{ notice.endDate|date('d.m.Y')}}<br>
	    			{% endif %}
	    		{% endif %}<!--<br>-->
	    		{% if notice.startTime %}
					{{ 'regularuser.message.from'|trans }} {{ notice.startTime|date('h:i A') }}
	    		{% endif %}
	    		{% if notice.endTime %}
	    			{{ 'regularuser.message.to'|trans }} {{ notice.endTime|date('h:i A') }}
	    		{% endif %}
	    		
	    	</h2>
	    	
	    		{% if not notice.startDate %}
	    			<h2>{{ 'listing.create.date_arrange'|trans }}</h2>
	    		{% endif %}
	    	
	    	
	    	
	    		{% if not notice.startTime %}
	    			<h2>{{ 'listing.create.start_time_arrange'|trans }}</h2>
	    		{% endif %}
	    	
    		{% if notice.locationArrange == 'true' %}
    			<h2>{{ 'listing.create.location_arrange'|trans }}</h2>
    		{% elseif notice.type. locationChangeAvailable %}
    			<h2>{{ notice.location }}</h2>
    		{% endif %}
    	{% endif %}
                        <div class="bgbottom"><img src="{{ asset('images/bgbottom_leftpart.png')}}" alt=""></div>
    	</div>
    	<div class="options">
                {% if notice.usergroup %}
                    <h4>{{ notice.usergroup.groupname }}</h4>
                {% else %}
                    <h4>{{ displayUser.name }}</h4>
                {% endif %}
    		<div class="profile">        	
        		<div class="person">
        		{% if app.user %}
                            {% if notice.usergroup %}
                            <a href="{{ path('fenchy_regular_user_user_groupprofile_groupinfo',{ 'groupId' : notice.usergroup.id}) }}">
                                    <img src=" {% if not notice.usergroup.webPath %} {{ absolute(asset('images/default_profile_picture.png')) }} {% else %} {{ absolute(asset(notice.usergroup.webPath)) }}{% endif %}" alt=""/>
                            </a>
                            {% else %}
                                <a href="{{ path('fenchy_regular_user_user_otherprofile_aboutotherchoice',{'userId': displayUser.id}) }}">
                                    {% render "FenchyNoticeBundle:GlobalFilter:generateCode" with { 'userId': displayUser.id } %}
                                </a>
                                <div class="neighbor {{ managerType[2] }}">
                                    <p>{{ managerType[1] }}</p>
                                    <span>{{ displayUser.activity }}</span>
                                </div>  
                            {% endif %}
                        {% else %}
                            {% if notice.usergroup %}
                                <a href="javascript:void(0);">
                                        <img src="{% if not notice.usergroup.webPath %} {{ absolute(asset('images/default_profile_picture.png')) }} {% else %} {{ absolute(asset(notice.usergroup.webPath)) }}{% endif %}" alt=""/>
                                </a>
                            {% else %}
                                <a href="javascript:void(0);">
                                        {% render "FenchyNoticeBundle:GlobalFilter:generateCode" with { 'userId': displayUser.id } %}
                                </a>
                                <div class="neighbor {{ managerType[2] }}">
                                    <p>{{ managerType[1] }}</p>
                                    <span>{{ displayUser.activity }}</span>
                                </div>   
                            {% endif %}
                        {% endif %}	
            			            
            	</div>
            </div>
            <h2>{{displayUser.regularuser.aboutMe|truncate(200)}}</h2>
            {% if app.user.id is defined and app.user.id != displayUser.id %}
              	<div class="optionsdetail">              	
              	<a href="#dialog44" class="zoomthis blue_button" name="modal" id="vzoom_1944" style="margin-left: 70px;">                                            
               		{{ 'btn.send_message' | trans }}
            	</a></div>
            {% endif %}  
                        <div class="bgbottom"><img src="{{ asset('images/bgbottom_leftpart.png')}}" alt=""></div>
		</div>
                {% if notice.usergroup %}
                    {% render "FenchyNoticeBundle:Widgets:userGroupListings" with {'notice_id' : notice.id} %}
                {% else %}
                    {% render "FenchyNoticeBundle:Widgets:userListings" with {'notice_id' : notice.id} %}
                {% endif %}
		{% render "FenchyNoticeBundle:Widgets:similarListings" with {'notice_id' : notice.id} %}
		
    </div>
</div>
<section id="boxes2">
    <div id="dialog42" class="window" >
	   	<div class="big_popup">
	      	<div class="big_popupclose"><a href="" class="close"></a></div>
	      	
			<form action="" method="post">
		    <div id="requestDiv">
                        <h3 style="text-align:center;">{{ 'request.msg1'|trans }}</h3>
		    	<h2 style="text-align:center; padding: 20px 12px 5px;">{{ 'request.msg2'|trans }} {{ displayUser.name }}</h2>	    	
    			
    			{% if (notice.pieces >=1 or notice.spot >=1 or notice.unlimited or notice.onePiece or notice.type == 'offerservice' or notice.type == 'service') and notice.type != "goods" %}
    			<div id="itemAvailable" {% if notice.pieces ==1 or notice.spot ==1 or notice.type == 'offerservice' or notice.type == 'service' or notice.onePiece %}style="display: none" {% endif %}>
    			     			
	    			{% if notice.type == "offerservice" or notice.type == "service" %}
	    				<div class="box1"><input type="text" id="pieces" placeholder="{{ 'listing.create.hour'|trans|upper }}" value="1" readonly="readonly"></input> {% if (notice.price >0) and (not notice.free) %}{% endif %}
                                            <input type="hidden" id="price" value="{{ notice.price }}" readonly="readonly" ></input> {% if (notice.price >0) or (not notice.free) %}{% endif %}</div>
                                <div class="box1" id="totalpricebox" style="padding: 5px 0px !important;"> TOTAL PRICE: <input type="text" id="total" placeholder="{{ 'listing.create.total'|trans|upper }}" readonly="true" style="{% if (notice.price <=0) or (not notice.price) or (notice.free) %} display:inline-block; {% else %} display: block; {% endif %}"></input><span>{% if notice.currency=='Euro' %}€{% elseif notice.currency=='Dollar' %}${%endif%}</span></div>
    				{% elseif notice.type == "offergoods" %}
    					<div class="box1">{{ notice.pieces }} {{ 'listing.create.pieces'|trans|upper }} {{ 'request.left'|trans|upper }} {{ 'request.how_many'|trans|upper }}<br><br>
    					<input type="hidden" id="hiddentext" value="{% if notice.onePiece %}1{% else %}{{ notice.pieces }}{% endif %}"/>
    					<input type="text" id="pieces" placeholder="{{ 'listing.create.pieces'|trans|upper }}" value="{% if notice.onePiece %}1{% endif %}"></input>  {% if (notice.price >0) and (not notice.free) %} {% endif %}
                                        <input type="hidden" id="price" value="{{ notice.price }}" readonly="readonly"></input> {% if (notice.price >0) and (not notice.free) %} {% endif %}</div>
                                            <div class="box1" id="totalpricebox" style="padding: 5px 0px !important;"> TOTAL PRICE: <input type="text" id="total" placeholder="{{ 'listing.create.total'|trans|upper }}" readonly="true" style="{% if (notice.price <=0) or (not notice.price) or (notice.free) %} display:none; {% else %} display: inline-block; {% endif %}"></input><span>{% if notice.currency=='Euro' %}€{% elseif notice.currency=='Dollar' %}${% endif %}</span></div>
    				{% elseif notice.type == "offerevents" %}
    				<div class="box1">{% if notice.unlimited %}{{ 'listing.create.unlimited'|trans }} {% else %}{{ notice.spot }}{% endif %} {{ 'listing.create.spot'|trans|upper }} {{ 'request.left'|trans|upper }} {{ 'request.how_many'|trans|upper }}<br><br>
    					<input type="hidden" id="hiddentext" value="{{ notice.spot }}"/>
					<input type="text" id="pieces" placeholder="{{ 'listing.create.spot'|trans|upper }}"></input>
                                        <input type="hidden" id="price" value="{{ notice.price }}" readonly="readonly"></input> </div>
                                <div class="box1" id="totalpricebox" style="padding: 5px 0px !important;"> TOTAL PRICE: <input type="text" id="total" placeholder="{{ 'listing.create.total'|trans|upper }}" readonly="true" style="{% if (notice.price <=0) or (not notice.price) or (notice.free) %} display:none; {% else %} display: inline-block; {% endif %}"></input><span>{% if notice.currency=='Euro' %}€{% elseif notice.currency=='Dollar' %}${% endif %}</span></div>
    				{% endif %}    				
    				
                        
    			</div>
    			{% endif %}
    			
                        {% if (notice.pieces >1 or notice.spot >1 or notice.unlimited or notice.onePiece) %}
    			{#<div id="TakeFree" {% if notice.pieces ==1 or notice.spot ==1 or notice.onePiece %}style="display: none" {% endif %}>
    			<p>     			
	    			
    				{% if notice.type == "offergoods" or   notice.type == "goods" %}
                                        {% if notice.type == "goods" %}
                                            {% if notice.pieces > 1 %} {{ notice.pieces }} {{ 'listing.create.pieces'|trans|upper }} required. {{ 'request.how_many_pieces'|trans }}<br><br> {% endif %}
                                        {% else %}
                                            {{ notice.pieces }} {{ 'listing.create.pieces'|trans|upper }} {{ 'request.left'|trans|upper }} {{ 'request.how_many'|trans|upper }}<br><br>
                                        {% endif %}
    					<input type="hidden" id="hiddentext1" value="{% if notice.onePiece %}1{% else %}{{ notice.pieces }}{% endif %}"/>
    					<input type="text" id="pieces1" placeholder="{{ 'listing.create.pieces'|trans|upper }}" value="{% if notice.onePiece %}1{% endif %}"></input>    					
    				{% elseif notice.type == "offerevents" %}
    				{% if notice.unlimited %}{{ 'listing.create.unlimited'|trans }} {% else %}{{ notice.spot }}{% endif %} {{ 'listing.create.spot'|trans|upper }} {{ 'request.left'|trans|upper }} {{ 'request.how_many'|trans|upper }}<br><br>
                                    <input type="hidden" id="hiddentext1" value="{{ notice.spot }}"/>
                                    <input type="text" id="pieces1" placeholder="{{ 'listing.create.spot'|trans|upper }}"></input> 
    				{% endif %}    				
    				
    			</p>
    			</div>#}
    			{% endif %}
                        
    			<div id="proposeTime">
    				<div class="box1"> {{ 'request.time_propose'|trans }}<br><br>
					{{ 'notice.start'|trans|upper }} :
    					<input type="text" id="startdate" placeholder="{{ 'listing.create.start_date'|trans }}"/>
    					<input type="text" id="starttime" placeholder="{{ 'listing.create.start_time'|trans }}"/><br>
                                        &nbsp;{{ 'notice.end'|trans|upper }} : &nbsp;&nbsp; 
    					<input type="text" id="enddate" placeholder="{{ 'listing.create.end_date'|trans }}"/>
    					<input type="text" id="endtime" placeholder="{{ 'listing.create.end_time'|trans }}"/>
                                </div>
    			</div>
    			<div id="proposeprice" class="proposeprice2">
    			
    				{% if notice.type == "goods" %}
    					{% if notice.pieces > 1 %} <div class="box1"> <li> {{ notice.pieces }} {{ 'listing.create.pieces'|trans|upper }} required. {{ 'request.how_many_pieces'|trans }}</li><br>
    					
                                            <input type="hidden" id="hiddentext" value="{% if notice.onePiece %}1{% else %}{{ notice.pieces }}{% endif %}"/> 
                                            <input type="text" id="pieces" placeholder="{{ 'listing.create.pieces'|trans|upper }}" value="{{ notice.pieces }}"></input> 
                                            <input type="hidden" id="price" value="{{ notice.price }}" readonly="readonly"></input>
                                        </div>
                                        <div class="box1" id="totalpricebox" style="padding: 5px 0px !important;">
                                            TOTAL PRICE: <input type="text" id="total" placeholder="{{ 'listing.create.total'|trans|upper }}" readonly="true"></input>
                                            <span>{% if notice.currency=='Euro' %}€{% elseif notice.currency=='Dollar' %}${% endif %}</span>
                                        </div>
                                        {% endif %} 
    				{% endif %}
                                        
    			<div class="box1">
    				
                                <li>{{ 'request.price_propose'|trans }}</li>
                                <li style="margin-top: 5px; margin-left: -65px">    				
    				{% if notice.type == "offerservice" or notice.type == "service" %}
    					<input id="offerprice" type="text" placeholder="{{ 'listing.create.price_hour'|trans|upper }}" />
    				{% elseif notice.type == "offergoods"  or notice.type == 'goods'%}
    					<input id="offerprice" type="text" placeholder="{{ 'listing.create.price_piece'|trans|upper }}"/>
    				{% elseif notice.type == "offerevents" %}
    					<input id="offerprice" type="text" placeholder="{{ 'listing.create.price_spot'|trans|upper }}" style="margin-left: -80px;"/>
    				{% endif %}
    				
    				<select id="cur">
    					<option>CURRENCY</option>
    					<option>EURO</option>
    					<option>DOLLAR</option>
    					
    				</select>
                                   <div class="checkbox">
                                       <input class="checkbox" type="checkbox" value="free" id="freebox"><label style="font-size:13px;">FREE</label>
                                   </div>
                                </li>
                                <br>
                                {% if notice.type == "goods" %}
    					{% if notice.pieces == 1 or notice.onePiece %} 
                                           
                                                <input type="hidden" id="hiddentext" value="{% if notice.onePiece %}1{% else %}{{ notice.pieces }}{% endif %}"> </input> 
                                                <input type="hidden" id="pieces" placeholder="{{ 'listing.create.pieces'|trans|upper }}" value="1"></input> 
                                                <input type="hidden" id="price" value="{{ notice.price }}" readonly="readonly"></input> 
                                                <input type="hidden" id="total" placeholder="{{ 'listing.create.total'|trans|upper }}" readonly="true"></input>
                                          
                                        {% endif %} 
    				{% endif %}
    				
    				{% if notice.type == "service" or notice.type== "goods" %}
                                    <div class="checkbox" style="margin-top: -5px; width: 100%; text-align: center; margin-left: 0px; padding: 0px;">
                                        <input type="checkbox" value="payment_settings" id="default_settings">&nbsp;</input> 
                                        <label style="font-size:13px; color: #000; letter-spacing: 1px"> {{ 'listing.create.default_setting'|trans }}</label>
                                    </div>    				
                                    <a href="{{ path('fenchy_regular_user_settings_payment') }}" class="blue_button">{{ 'listing.create.learn_more'|trans }} </a>
    				
    				{% endif %}
                        </div>
    			</div>
    			<div id="Swap">
    				<div class="box1" style="border: 0px none;  margin-top: -10px;"> <br>    					
                                        <textarea id="swapmsg" rows="3" cols="48" style="width: 380px; padding: 10px;" placeholder="{{ 'request.swap_msg'|trans|upper }}" required="required"></textarea>
                                </div>
    			</div>
                        <div id="ProposeLocation">
    				<div class="box1" style="border: 0px none;  margin-top: -10px;"> 
                                    <textarea id="proposelocation" rows="3" cols="48" style="width: 380px;; padding: 10px;" placeholder="{{ 'request.location_msg'|trans|upper }}" required="required"></textarea>
                                </div>
    			</div>
    			<div class="box1" style="border: 0px none;  margin-top: -10px;">
                            <textarea id="requestdata" rows="3" cols="48" style="width: 380px; padding: 10px;" placeholder="{{ 'request.message_to_user'|trans|upper }} {{ displayUser.name }}"></textarea>
                        </div>
                        <div class="bottom-buttons">
			    <input class="blue-btn" type="button" id="continue" value="{{ 'request.send'|trans }}" onclick="sendRequest();" style="float: right; margin-right: 65px; padding: 1px 6px 0; height: auto;"/>
                            <a class="blue-btn" style="padding: 2px 6px 0; height: auto; float: left;" href="{{path('fenchy_notice_indexv2')}}">
			    	{{ 'request.return_to_listing'|trans }}
			  	</a>
				
			</div>
                            </div>
                    </form>
		</div>
		<img class="popup_bottom" src="/images/popup_bottom.png" alt="" style="width: 100%;" />
	</div>
	<div id="mask"></div>
</section>
<section id="boxes2">
	<div id="dialog43" class="window">
	   	<div class="big_popup">
	      	<div class="big_popupclose"><a href="" class="close"></a></div>
	      	{% if app.user.id is defined and app.user.id != displayUser.id %}
	      		{% if notice.completed %}
	      			<div id="requestDiv">
		    			<p>{{'request.sorry_msg'|trans }}
				      		{% if notice.type == "offerevents" %}
				      				{{ 'notice.spot'|trans }}
				      		{% else %}
				      				{{ 'notice.piece'|trans }}
				      		{% endif %}
                                            {% if notice.type == 'goods' %}
                                                wanted
                                            {% endif %}
	      				</p>  			
   					</div>
	      		{% elseif notice.closed %}
	      			<div id="requestDiv">
	      				<p>
	      					{{ 'request.listing_closed1'|trans }}
	      				</p>
	      			</div>
                        {% elseif oneRequest %}
                                <div id="requestDiv">
                                    {% if oneRequest.status =='accepted' %}
	      				<p>
	      					{{ 'request.your_take_part_msg'|trans }}
	      				</p>
                                    {% else %}
                                        <p>
	      					{{ 'request.your_requested_msg'|trans }}
	      				</p>
                                    {% endif %}
	      			</div>
	      		{% endif %}
	      	{% endif %}
			{% if app.user.id is defined and app.user.id == displayUser.id%}
		    	<div id="requestDiv">
		    		<p>{{ 'request.your_list'|trans }}</p>  			
   				</div>
   			{% endif %}
   			{% if app.user.id is not defined %}
   				<div id="requestDiv">
		    		<p>{{ 'request.login_require'|trans }}</p>  			
   				</div>
   			{% endif %}			
		</div>
		<img class="popup_bottom" src="/images/popup_bottom.png" alt="" style="top: 69px; width: 100%;">
	</div>
	<div id="mask"></div>
</section>
<a id="successDialog" href="#dialog45" class="zoomthis blue_button" name="modal" id="vzoom_1944" style="display: none;">
<section id="boxes2">
	<div id="dialog44" class="window">
	   	<div class="big_popup">
	      	<div class="big_popupclose"><a href="" class="close"></a></div>
				<p>{{ 'regularuser.write_msg_to'|trans }} {{ notice.user.name }} about {{ notice.title }}</p>
					<input type="text" id="MsgTitle" placeholder="{{ 'message.title'|trans }}"/>
                                        <div class="clearfix"></div>
					<textarea id="MsgContent" placeholder="{{ 'message.content'|trans }}"></textarea>
				<input class="blue_button" style="margin-bottom: 10px; margin-left: -71px; margin-top: 130px;" type="button" id="continue" value="{{ 'btn.send'|trans }}" onclick="sendMsg();"/>
		</div>
		<img class="popup_bottom" src="/images/popup_bottom.png" alt="" style="width:100%">
	</div>
	<div id="mask"></div>
</section>

<section id="boxes2">
	<div id="dialog45" class="window">
	   	<div class="big_popup">
	      	<div class="big_popupclose"><a href="" class="close"></a></div>
			<div id="messageForm">
				<p>{{ 'regularuser.msg_send'|trans }}</p>
			</div>
		</div>
		<img class="popup_bottom" src="/images/popup_bottom.png" alt="" style="width: 100%; bottom: -9px;">
	</div>
	<div id="mask"></div>
</section>

<a id="RequestSend" href="#dialog49" class="zoomthis blue_button" name="modal" id="vzoom_1944" style="display: none;">
<section id="boxes2">
	<div id="dialog49" class="window">
	   	<div class="big_popup">
	      	<div class="big_popupclose"><a href="" class="close"></a></div>
			<div id="messageForm">
				<p>{{ 'request.msg1'|trans }}</p>
			</div>
		</div>
		<img class="popup_bottom" src="/images/popup_bottom.png" alt="" style="width: 100%; bottom: -9px;">
	</div>
	<div id="mask"></div>
</section>

<a id="SetPayments" href="#dialog451" class="zoomthis blue_button" name="modal" id="vzoom_1944" style="display: none;">
    <section id="boxes2">
		<div id="dialog451" class="window">
		<div id="dialog43">
	   	<div class="big_popup">
	      	<div class="big_popupclose"><a href="" class="close"></a></div>
			<div id="messageForm">
				<p>{{ 'regularuser.payment_settings'|trans }}</p>
			</div>
			<a href="{{ path('fenchy_regular_user_settings_payment') }}" class="blue_button right" style="margin-right:20px;">{{ 'regularuser.setpayment'|trans }}</a>
		</div>
		<img class="popup_bottom" src="/images/popup_bottom.png" alt="" style="width: 100%; bottom: -9px;">
		</div>
		</div>
		<div id="mask"></div>
	</section>
	
<script type="text/javascript">
    $(document).ready(function () {
        $('#static-calendar').datepicker({
            beforeShowDay : function (date) {

                var listingStarts = new Date('{{ notice.startDate|date('Y-m-d') }}');
                listingStarts.setHours(0);
                listingStarts.setMinutes(0);
                listingStarts.setSeconds(0);
                listingStarts.setMilliseconds(0);
                var listingEnds = new Date('{{ notice.endDate|date('Y-m-d') }}');
                listingEnds.setHours(23);
                listingEnds.setMinutes(59);
                listingEnds.setSeconds(59);
                listingEnds.setMilliseconds(99);
                if (date - listingStarts >= 0 && listingEnds - date > 0) {
                    return [true, 'highlight'];
                }

                return [true, ''];
             },
             defaultDate : ''
        });
        
    });
</script>
