
ko.bindingHandlers.jqDatepicker = {
    init: function(element, valueAccessor, allBindingsAccessor, viewModel) {
        var options = valueAccessor() || {};
        var allBindings = allBindingsAccessor();
        var modelValue = allBindings.jqDatepickerValue;
        var afterPick = allBindings.jqDatepickerOnPick;
        
        
        
        options.dateFormat = "yy-mm-dd";        
        options.defaultDate = null;
        
        options.onSelect = function(date) {
            modelValue(date);
            afterPick();
        };
        
        $(element).datepicker(options);
    },
    update: function(element, valueAccessor, allBindingsAccessor, viewModel) {
       var allBindings = allBindingsAccessor();
       var unwrap = ko.utils.unwrapObservable;
       var modelValue = unwrap(allBindings.jqDatepickerValue) || '';
       if ( !modelValue ) {
           $(element).datepicker( "setDate", null);
           $('.ui-datepicker .ui-state-active', element).removeClass('ui-state-active');
       }
       else 
           $(element).datepicker( "setDate", modelValue );
    }
}