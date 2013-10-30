function messageLoader(element, url) {
    
    var self = this;
    self.rowTemplate = _.template($('#msg-row').html());
    self.$table = element;
    self.url = url;

    
    self.getNew = function () {
        var checks = self.$table.find('.message-check');
        var mIds = [];
        for(var checkI = 0; checkI < checks.length; checkI++) if($(checks[checkI]).attr('rel')) mIds.push($(checks[checkI]).attr('rel'));
        $.ajax({
            type: 'post',
            url: self.url,
            dataType: 'json',
            data: {
                ids: mIds
            },
            success: function (data) {
                self.append(data);
            },
            error: function(d) {

            }
        });
    };
    
    self.append = function(data) {
        for(var rowI in data) {
            self.$table.prepend(self.rowTemplate(data[rowI]));
        }
    };
    
    self.interval = setInterval(self.getNew, 2000);
        
}
