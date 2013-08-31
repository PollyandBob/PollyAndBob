var createListing = {
    step1 : {
        init: function () {
            $('#type-choices li').click(function(e){
                $('#type-choices li.selected').removeClass('selected');
                $(this).addClass('selected');
            });
        }
    },
    step2 : {
        init: function () {
            var $end = $('#fenchy_noticebundle_noticetype_end_date');
            var $start = $('#fenchy_noticebundle_noticetype_start_date');
            var $dd = $('#review-type-selector');
            $end.datetimepicker({
                dateFormat: 'dd.mm.yy', 
                stepMinute: 5, 
                minuteMax: 55
            });
            $start.datetimepicker({
                dateFormat: 'dd.mm.yy', 
                stepMinute: 5, 
                minuteMax: 55,
                onClose: function () {
                    if($end.val() == '') $end.val(this.value);
                }
            });
            
            if($dd.length) new fenchyDropdown($dd);
        }
    }
}