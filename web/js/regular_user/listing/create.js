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
            var $start_time = $('#fenchy_noticebundle_noticetype_start_time');
            var $end_time = $('#fenchy_noticebundle_noticetype_end_time');
            var $dd = $('#review-type-selector');
            $end.datepicker({
                dateFormat: 'dd.mm.yy', 
                stepMinute: 5, 
                minuteMax: 55
            });
            $start_time.timepicker({
                //dateFormat: 'dd.mm.yy', 
                stepMinute: 5, 
                minuteMax: 55
            });
            $end_time.timepicker({
                //dateFormat: 'dd.mm.yy', 
                stepMinute: 5, 
                minuteMax: 55
            });
            $start.datepicker({
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