function fenchyDropdown($element) {
    var self = this;
    
    // elements
    self.$selector = $element;
    self.$original = self.$selector.find('select:first-child');
    self.oOptions = self.$original.find('option');
    self.optContainer = self.$selector.find('.drop-down');
    self.title = self.$selector.find('.selector-title');
    self.selectedValue = self.$selector.find('.select-value');
    self.replacement = self.$selector.find('.replacement');
    self.hide = 'style="display:none;"';
    
    // functions
    self.copyOptions = function () {
        var $htmlOptions = '';
        var hide = self.hide;
        for(var optI = 0; optI < self.oOptions.length; optI++) {
            
            if(self.oOptions[optI].selected) {
                self.selectedValue.html($(self.oOptions[optI]).html());
                hide = '';
            }
            else {
                hide = self.hide;
            }
            $htmlOptions += '<div><i class="icon-ok" '+hide+'></i><span rel="'+self.oOptions[optI].value+'">'+$(self.oOptions[optI]).html()+'</span></div>';
            
        }
        self.optContainer.html($htmlOptions);
    };
    
    self.setTitle = function () {
        var label = self.$selector.find('.dd-label label');
        if(label.length) {
            self.title.html(label.html());
        } else {
            self.title.html(self.$selector.find('.dd-label').html());
        }
    };
    
    self.showOptions = function () {
        self.optContainer.show();
    };
    
    self.hideOptions = function (e) {
        self.optContainer.hide();
    };
    
    self.chooseOption = function (element) {
        element.stopPropagation();
        $(this).parent().find('.icon-ok').hide();
        $(this).find('.icon-ok').show();
        var selected = $(this).find('span');
        self.$original.val(selected.attr('rel'));
        self.selectedValue.html(selected.html());
        self.hideOptions();
    };
    // MAIN
    self.optContainer.hide();
    self.setTitle();
    self.title.html = self.$selector.find('.dd-label').html();
    self.copyOptions();
    self.replacement.click(self.showOptions);
    self.optContainer.find('div').click(self.chooseOption);
    
    
    
    
}

