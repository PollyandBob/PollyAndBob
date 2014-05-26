/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function(){  
      //alert($('li.selected').attr('class'));    
      $('.crop_me4').jWindowCrop({
              
            targetWidth: 970, //Width of facebook cover division
            targetHeight: 326, //Height of cover division
            loadingText: '',
            onChange: function(result) {
                    var strcropX = $(this).css('left');
                    var strcropY = $(this).css('top');
                    strcropX = strcropX.replace(/px/g, '');
                    strcropX = strcropX.replace(/-/g, '');
                    strcropY = strcropY.replace(/px/g, '');
                    strcropY = strcropY.replace(/-/g, '');	
                    /*$('#form_cropX').val(strcropX);
                    $('#form_cropY').val(strcropY);*/
                    /*console.log("separation from left- "+result.cropX);
                    console.log("separation from left- "+result.cropY);
                    console.log("width- "+result.cropW-455);
                    console.log("Height- "+result.cropH-123);*/
            }
    });
		 
    $('.placeholder_listing').find('.jwc_controls').empty();     
    });
