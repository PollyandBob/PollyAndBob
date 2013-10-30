

var gallery_edit = {

    crop : {
        // The variable jcrop_api will hold a reference to the
        // Jcrop API once Jcrop is instantiated.
        jcrop_api : null,
        xsize : 1,
        ysize : 1,
        boundx : null,
        boundy : null,
        $submit : null,
        $cancel : null,
        imgName : null,
        //PunkAveFileUploader instance
        punkAve : null,
        // last cropping data (x, y, w, h)
        data : null,
        
        initJcrop : function () {

            // Invoke Jcrop in typical fashion
            $('#target').Jcrop({
                onChange: gallery_edit.crop.updatePreview,
                onSelect: gallery_edit.crop.updatePreview,
                aspectRatio: gallery_edit.crop.xsize / gallery_edit.crop.ysize
            },function(){
                // Use the API to get the real image size
//                var bounds = this.getBounds();
//                gallery_edit.crop.boundx = bounds[0];
//                gallery_edit.crop.boundy = bounds[1];
//                // Store the API in the jcrop_api variable
                gallery_edit.crop.jcrop_api = this;
            });

        },
        
        init : function (data) {
            gallery_edit.crop.url = data.url;
            gallery_edit.crop.punkAve = data.punkAve;
            gallery_edit.crop.$submit = $('#crop-submit');
            gallery_edit.crop.$cancel = $('#crop-cancel');
            gallery_edit.crop.$cancel.click(function () {$.fancybox.close();});
            gallery_edit.crop.$submit.click(gallery_edit.crop.save);
            gallery_edit.crop.initJcrop();
        },
        
        takeImageToCrop : function () {
            if(null === gallery_edit.crop.jcrop_api) {
                gallery_edit.crop.initJcrop();
            }
            var origUrl = $(this).parent().find('input[name="original"]').val();
            gallery_edit.crop.jcrop_api.setImage(origUrl);
            gallery_edit.crop.imgName = $(this).attr('rel');
            gallery_edit.crop.data = null;
            var $original = new Image();
            $original.src = origUrl;
            $original.onload = function () {
                var opt = {
                    href: '#crop-area',
                    autoDimensions : false,
                    autoScale : false,
                    width : $original.naturalWidth + 10,
                    height : $original.naturalHeight + 50
                };
                $.fancybox(opt);
            }
        },
                
        refreshCroppedImage : function (data) {
    
            $.fancybox.close();
            if(!data.status) {
                alert('Could not crop image.');
                return;
            }
            var newDate = new Date();
            var unique = newDate.getTime()
            gallery_edit.crop.punkAve.replaceImage({
                'thumbnail_url': data.small+'?a='+unique,
                'medium_url' : data.big+'?a='+unique,
                'url': data.original,
                'name': data.name
            });
        },
        
        updatePreview : function (c) {
            
            if (parseInt(c.w) > 0) {

                gallery_edit.crop.data = c;
            }
        },
                
        save : function () {
            if (null === gallery_edit.crop.data) return;
            
            gallery_edit.crop.data.name = gallery_edit.crop.imgName;
            
            $.ajax({
                type     : 'post',
                dataType : 'json',
                data     : gallery_edit.crop.data,
                url      : gallery_edit.crop.url,
                success  : gallery_edit.crop.refreshCroppedImage
            });
        }

    }
};