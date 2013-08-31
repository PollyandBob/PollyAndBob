
ko.bindingHandlers.jqSlider = {
    init: function(element, valueAccessor, allBindingsAccessor, viewModel) {
        var options = valueAccessor() || {};
        var allBindings = allBindingsAccessor();
        var modelValue = allBindings.jqSliderValue;


        options.slide = function( event, ui ) {
            modelValue(ui.value);
        }
        
        options.stop = function(event, ui) {
            if(typeof globalFilterViewModel != 'undefined') {
                globalFilterViewModel.reloadDoSearch();
            }
        }


        options.range = 'min';
        $(element).slider(options);
    },
    update: function(element, valueAccessor, allBindingsAccessor, viewModel) {
       var allBindings = allBindingsAccessor();
       var unwrap = ko.utils.unwrapObservable;
       var modelValue = unwrap(allBindings.jqSliderValue) || '';
       //console.log('modelValue ----'+modelValue);
       $(element).slider("value", modelValue);
    }
}
