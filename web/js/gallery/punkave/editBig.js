var gallery_show = {
    
    container: null,
    
    
    
    init: function ($data) {

        this.container = $('#gallery-show');
        this.container.agile_carousel({
                carousel_data: this.parseData($data),
                carousel_outer_height: 430,
                carousel_height: 330,
                slide_height: 330,
                carousel_outer_width: 710,
                slide_width: 710,
                transition_type: "slide",
                transition_time: 600,
                timer: 3000,
                continuous_scrolling: true,
                control_set_2: "content_buttons",
                change_on_hover: "content_buttons"
            });
    },
    
    parseData: function ($data) {
        
        var result = [];
        
        for(var index = 0; index < $data.length; index++) {
            $element = {};
            $element.content = '<div class="slide_inner">'+
                        '<a class="photo_link" href="#">'+
                            '<img class="photo" src="'+$data[index].big_source+'"/>'+
                        '</a><a class="captiono" href="#">'+
                        '</a></div>';
            $element.content_button = '<div class="thumb">'+
                                    '<img src="'+$data[index].small_source+'"/></div>';
            result.push($element);
        }
        
        return result;
    }
}