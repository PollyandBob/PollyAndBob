
function resize_triangle(number)
{
//    var optionHeight = $('#tc_id_'+number).height(),
//    tHeight = optionHeight / 2,
//    tHeightP = (optionHeight + 2)/2;
//    
//    tHeight = Math.round(tHeight);
//    tHeightP = Math.round(tHeightP);
//
//    $('#tr_id_'+number).css("border-top", tHeightP + "px solid transparent");
//    $('#tr_id_'+number).css("border-bottom", tHeightP + "px solid transparent");
//
//    $('#tri_id_'+number).css("border-top", tHeight + "px solid transparent");
//    $('#tri_id_'+number).css("border-bottom", tHeight + "px solid transparent");
//    $('#tri_id_'+number).css("top", "-" + tHeight + "px");
    
}

$(window).load(function () {
    
    var $notices = $('.triangle_main_block');
    var innerHeight, outernHeight, optionHeight;
    var out, ins;
    var $notice;
    
    for(var arrIndex = 0; arrIndex < $notices.length; arrIndex++) {
        $notice = $($notices[arrIndex]);
        optionHeight = $notice.find('.triangle_content').height();
        innerHeight = optionHeight / 2;
        outernHeight = (optionHeight + 2) / 2;

        if($notice.find('.triangle_right').length > 0) {
            
            out = $notice.find('.triangle_right');
            ins = $notice.find('.triangle_right_inside')
            out.css("border-top", outernHeight + "px solid transparent");
            out.css("border-bottom", outernHeight + "px solid transparent");
            ins.css("border-top", innerHeight + "px solid transparent");
            ins.css("border-bottom", innerHeight + "px solid transparent");
            ins.css("top", "-" + innerHeight + "px");
            
        } else if ($notice.find('.triangle_left').length > 0) {
            
            out = $notice.find('.triangle_left');
            ins = $notice.find('.triangle_left_inside')
            out.css("border-top", outernHeight + "px solid transparent");
            out.css("border-bottom", outernHeight + "px solid transparent");
            ins.css("border-top", innerHeight + "px solid transparent");
            ins.css("border-bottom", innerHeight + "px solid transparent");
            ins.css("top", "-" + innerHeight + "px");
        }

    }
});