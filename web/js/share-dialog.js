/**
 * @author jtokarski
 */

var cancel = "Cancel";

$(document).ready(function() {
  
	
  $('#nc-dialog').dialog({
  	autoOpen: false,
  	modal: true,
  	resizable: false,
  	width: 360,
  	height: 240
  });	

  $('#share-dialog-cancel').button().click(function() {
  	$('#nc-dialog').dialog("close");
  });

  $('.icon.share').click(function() {
    $.getScript('/js/share-connect-facebook.js',function() {
        $('#nc-dialog').css('visibility','visible');
     	$('#nc-dialog').dialog( "open" );
    });

  	return false;
  });
});
