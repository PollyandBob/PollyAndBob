//jqAutocomplete -- main binding (should contain additional options to pass to autocomplete)
//jqAutocompleteSource -- the array to populate with choices (needs to be an observableArray)
//jqAutocompleteQuery -- function to return choices (if you need to return via AJAX)
//jqAutocompleteValue -- where to write the selected value


ko.bindingHandlers.jqAutocomplete = {
    init: function(element, valueAccessor, allBindingsAccessor, viewModel) {
        var options = valueAccessor() || {},
            allBindings = allBindingsAccessor(),
            unwrap = ko.utils.unwrapObservable,
            modelValue = allBindings.jqAutocompleteValue,
            source = allBindings.jqAutocompleteSource,
            query = allBindings.jqAutocompleteQuery,
            appendTo = allBindings.jqAutocompleteAppendTo;
            

        options.focus = function() {
            // Prevent value inserted on focus // wtf ?
            return false;
        }

        //When an item is selected from suggestions
        // write proper value to the model
        options.select = function(event, ui) {
            var currentInputValue = $(element).val();
            var terms = split( currentInputValue );
            // Remove the current input ( on which current suggestion is based )
            terms.pop();
            // Add the item selected from suggestions;
            terms.push( ui.item.value );
            // Add placeholder, to assure that separator is added on the end of new 
            // input value
            terms.push( '' );
            var newInputValue = terms.join('  ');
            modelValue(ui.item ? newInputValue : null);
            // Return FALSE so that jQueryUI does not overwrite our value update
            return false;
        };


        options.change = function(event, ui) {
            var allBindings = allBindingsAccessor(),
            modelValue = allBindings.jqAutocompleteValue;
            var currentInputValue = $(element).val();
            modelValue(currentInputValue);
            // TODO: 
            //  2. Dont need to refresh suggestions - Done automatically by jQueryUI Autocomplete (verified)
        }

        // Two below "lambda functions" manage the "multiphrase" autocomplete
        // /\s{2}/  - the phrase separator in autocomplete will be two spaces
        function split( val ) { return val.split( /\s{2}/ ); };
        function getLastPhrase( term ) { return split( term ).pop(); };


        options.source = function(request, response) {
            // request.term stores the text value currently typed by user to 
            // the search input.
               
            // Don't query for the whole expression typed into search,
            // but only for the part considered to be last phrase.
            var lastPhrase = getLastPhrase( request.term );
            query.call(this, lastPhrase, response);
        }
        
        options.minLength = 3;
        options.delay = 600;
        
        options.appendTo = appendTo;

        //initialize autocomplete
        $(element).autocomplete(options);
    },
    update: function(element, valueAccessor, allBindingsAccessor, viewModel) {
       var allBindings = allBindingsAccessor(),
           unwrap = ko.utils.unwrapObservable,
           modelValue = unwrap(allBindings.jqAutocompleteValue) || '';
        //Update input value based on a model change
        // so that working of Knockout's 'value' binding is reproduced
        // in our binding.
        //$(element).val(modelValue);
    }
};