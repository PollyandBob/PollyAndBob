var gallery_show = {
    
    container: null,
    
    
    
    init: function ($data) {

        this.container = $('#gallery-show');
        this.container.agile_carousel({
                carousel_data: this.parseData($data),
                carousel_outer_height: 430,
                carousel_height: 330,
                slide_height: 330,
                carousel_outer_width: 720,
                slide_width: 720,
                transition_type: "slide",
                transition_time: 600,
                timer: 3000,
                continuous_scrolling: true,
                control_set_2: "content_buttons",
                change_on_hover: "content_buttons"
            });
        this.adjustImages();
//       
    },
    
    parseData: function ($data) {
        
        var result = [];
        
        for(var index = 0; index < $data.imgs.length; index++) {
            $element = {};
            $element.content = '<div class="slide_inner">'+
                        '<a class="photo_link" href="'+$data.imgs[index].original_source+'" rel="prettyPhoto['+$data.id+']">'+
                            '<img class="photo" src="'+$data.imgs[index].big_source+'"/>'+
                        '</a><a class="captiono" href="#">'+
                        '</a></div>';
            $element.content_button = '<div class="thumb">'+
                                    '<img src="'+$data.imgs[index].small_source+'"/></div>';
            result.push($element);
        }
        
        return result;
    },
    
    adjustImages: function () {
        var images = this.container.find('.photo');
        var ratio = 1;
        for (var imgI = 0; imgI < images.length; imgI++) {
            ratio = images[imgI].width/images[imgI].height;
            if (ratio > 1.3) $(images[imgI]).addClass('landscape');
            else if (ratio < 0.77) $(images[imgI]).addClass('portrait');
        }
    }
}