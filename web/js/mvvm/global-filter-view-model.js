function GlobalFilterViewModel( 
    gcoder, 
    baseViewUri,
    routes,
    pagination,
    initCategories, 
    initPostDate, 
    distanceDefaults, 
    translations,
    filterLastUrl ) 
{
    var self = this;
    
    //
    // View state and helper observables and non-observables;
    //
    self.baseViewUri = baseViewUri;
    self.pagination = pagination;
    self.gGeocoder = gcoder; 
    self.stateReadFromUri = false;
    self.distanceDefaults = distanceDefaults;
    self.translations = translations;
    self.routes = routes;
    self.timeoutTimer = null;
    self.number = 0;
    self.filterLastUrl = filterLastUrl;
    
    self.moreNoticesBeingLoaded = false;
    
    /*  Array helper functions */
    self.inArrayElement = function( needle, haystack, elementKey ) {
        for ( var key in haystack ) {
            if ( haystack[key][elementKey] == needle )
                return true;
        }
        return false;
    }
    
    self.getArrayElementByIdProp = function ( idProp, haystack, idPropName) {
        for ( var key in haystack ) {
            if ( haystack[key][idPropName] == idProp )
                return haystack[key];
        }
        return undefined;
    };
    
    /* END: Array helper functions */
    
    self.putStateSupport = ko.computed( function() {
        if (('replaceState' in window.history) && window.history['replaceState'] !== null) {
            if ( document.location.hash.length > 0 )
                 window.history.replaceState({},'Fenchy',
                     '/' + self.baseViewUri + '/' + document.location.hash.slice(1) );
            $(window).bind('popstate', function( event ){
                self.curFilterUriUpdate.valueHasMutated();
                self.retrieveFilterContent( true, false );
            });      
            return true;
        }
        else {
            // If URL is not hash'ed - we have to replace it and
            // unfortunately - reload to page
            var rx = new RegExp( baseViewUri+'/?([^#]*)', 'i' );
            var match = rx.exec(document.location.href);
            if ( match && typeof match[1]=='string'  &&  match[1].length>0 ) {
                document.location.href =  document.location.protocol + '//' +
                    document.location.host + baseViewUri + '#' + match[1];
            }
            $(window).bind('hashchange', function(){
                self.curFilterUriUpdate.valueHasMutated();
                self.retrieveFilterContent( true, false );                
            });
            return false;
        }
    });
    
    // Name of template of categories selector is made observable just in order
    // to be able to prompt Knockout to re-render categories bar.
    self.categoriesTemplate = ko.observable('categories-template');
    self.listMode = ko.observable('list');
    self.listingTemplate = ko.computed( function() {
        return self.listMode() + '-template';
    });
        
    self.notices = ko.observableArray([]);
    self.noticesCount = ko.observable(0);
    
    // View Model Class field, used to store whole data returned by AJAX request.
    // After request all observables and fields are populated copying from it
    //  and it's not used any further.
    self.filterContentResult = {};
    
    //
    //  END: View state and helper observables;
    //
    
    // tu np. zdefiniować WSZYSTKIE observable określające kryteria
    self.fcGFormattedAddressVal = ko.observable()
    self.fcGFormattedAddress = ko.computed({
        read: self.fcGFormattedAddressVal,
        write: function(value) {
            self.fcGLat(null);
            self.fcGLng(null);
            self.fcGFormattedAddressVal(value);
        }
    });
    self.fcGLat = ko.observable(null);
    self.fcGLng = ko.observable(null);
    
    self.fcPhrase = ko.observable();
    // self.fcCategories - Not exactly observable, but will store observables
    for ( var key0 in initCategories ) {
        for( var key1 in initCategories[key0]['subcategories'] ) {
            for( var key2 in initCategories[key0]['subcategories'][key1]['subcategories'] ) {
                initCategories[key0]['subcategories'][key1]['subcategories'][key2]['checked'] = ko.observable(false);
            }
            initCategories[key0]['subcategories'][key1]['subcategoriesObs'] =
                initCategories[key0]['subcategories'][key1]['subcategories'];
            delete initCategories[key0]['subcategories'][key1]['subcategories'];
        }
        
        initCategories[key0]['subcategoriesObs'] =
            initCategories[key0]['subcategories'];
        delete  initCategories[key0]['subcategories'];
        
        initCategories[key0].checkedLRV = ko.observable( false );
        initCategories[key0].checked =  ko.computed({
            'read': function() {
                var all_unchecked = true;
                for( var key1 in this.subcategoriesObs ) {
                    for( var key2 in this.subcategoriesObs[key1]['subcategoriesObs'] ) {
                        if ( this.subcategoriesObs[key1]['subcategoriesObs'][key2]['checked']() ) {
                            all_unchecked = false;
                        }
                    }
                }
                
                if ( !all_unchecked ) {
                    this['checkedLRV'](true);
                    return true;
                }
                else
                    return this['checkedLRV']();
            },
            'write': function( value ) {
                if ( this['checkedLRV']() && value == false ) {
                    for( var key1 in this.subcategoriesObs ) {
                        for( var key2 in this.subcategoriesObs[key1]['subcategoriesObs'] ) {
                            this.subcategoriesObs[key1]['subcategoriesObs'][key2]['checked']( false );
                        }
                    }
                    this['checkedLRV'](false);
                } else if ( value == true ) {
                    this['checkedLRV'](true);
                }                
            },
            'owner': initCategories[key0],
        });
    }
    
    self.reloadDoSearchConfirm = function(num){
        self.doSearch( false );
    };
    
    self.reloadDoSearch = function(){
        clearTimeout(self.timeoutTimer);
        self.timeoutTimer = setTimeout(function(){ self.reloadDoSearchConfirm() }, 1000);
    };
    
    
    
    self.fcCategories = initCategories;
    self.categoryCheck = function( category ) {
        category.checked(!category.checked());
        self.boxCategoryUpdate.valueHasMutated();
        self.reloadDoSearch();
    };
    
    self.defaultDistance = self.distanceDefaults.distanceSliderDefault;
    self.fcDistance = ko.observable(self.defaultDistance);
    self.fcDistanceText = ko.computed(function() {
        var fcDistance = self.fcDistance();
        if ( fcDistance < self.distanceDefaults.distanceSliderMax ) {
            return (fcDistance / 1000 ).toString() + ' km';
        } else {
            return self.translations.no_limit;
        }
    });
    
    //TODO: PostDates
    self.defaultPostDate = null;
    self.fcPostDate = ko.observable( null );
    self.availablePostDates = ko.observableArray(initPostDate);
    
    self.fcPostDateBar = ko.computed({ 
        read: function() {
            var curPostDate = self.fcPostDate();
                        
            var curPostDateMoment = moment(curPostDate, 'YYYY-MM-DD' );
            if ( curPostDateMoment && curPostDateMoment.isValid() ) {
                var nowMoment = moment();
                if ( curPostDateMoment.isSame( nowMoment, 'day') )
                    return 'today';
                else if ( curPostDateMoment.isSame( nowMoment.subtract('days', 1), 'day') )
                    return 'yesterday';
                else
                    return null;
            } else if ( self.inArrayElement(curPostDate, self.availablePostDates(), 'id') ) {
                return curPostDate;
            } else {
                return null;
            }
        },
        write: function(value) {
            if ( self.inArrayElement(value, self.availablePostDates(), 'id') ) {
                if ( value == 'today' ) {
                    self.fcPostDate( moment().format('YYYY-MM-DD') );
                } else if ( value == 'yesterday' ) {
                    self.fcPostDate( moment().subtract('days',1).format('YYYY-MM-DD') );
                } else {
                    self.fcPostDate( value );
                }
            }
        }
    });
    self.selectDateBar = function( postDate ) {
        self.fcPostDateBar( postDate.id );
        //self.doSearch( false );
        self.reloadDoSearch();
        //self.fcPostDatePick('2012-12-06');
    }
    
    self.fcPostDatePick = ko.computed( {
        read: function() {
            var fcPostDate = self.fcPostDate();
            fcPostDateMoment = moment(fcPostDate, 'YYYY-MM-DD');
            if ( fcPostDateMoment && fcPostDateMoment.isValid() )
                return fcPostDate;
            else
                return null;            
        },
        write: function(value) {
            if ( moment(value, 'YYYY-MM-DD').isValid() )
                self.fcPostDate(value);
        }
    } );
    self.selectDatePick = function() {
        //self.doSearch( false );
        self.reloadDoSearch();
    }
    
    
    
    // END - PostDates
    
    
    
    //TODO: SortBy
    self.defaultSortBy = 'time';
    self.fcSortBy = ko.observable(self.defaultSortBy);
    self.availableSorts = ko.observableArray([
        { 'optValue': 'rel', 'optText':self.translations.sort_rel },        
        { 'optValue': 'dist', 'optText':self.translations.sort_dist },
        { 'optValue': 'time', 'optText':self.translations.sort_time }
    ]);
    self.displayFcSortBy = ko.computed({
        read: function() {
            var sortItem = self.getArrayElementByIdProp( self.fcSortBy(), self.availableSorts(), 'optValue');
            if ( sortItem )
                return sortItem.optText;
            else
                return '';
        }
    });
    self.selectSort = function ( sortBy ) {
        self.fcSortBy( sortBy.optValue );
    }
    
    //TODO: Range
    self.defaultFcRangeLow = 0;
    self.defaultFcRangeHigh = 10;
    self.fcRangeLow = ko.observable(self.defaultFcRangeLow);
    self.fcRangeHigh = ko.observable(self.defaultFcRangeHigh);
    self.noticesLoadMore = ko.observable( false );
    
    self.fcUserId = ko.observable( null );
    
    // - koniec observabli kryteriów
    

    // 'Criteria Box observables'
    
    self.boxCategoryUpdate = ko.observable();
    self.boxCategory = ko.computed({
        read: function() {
            self.boxCategoryUpdate();
            var cats = self.fcCategories;
            for ( var key0 in cats ) {
                if ( cats[key0]['checked']() )
                    return true;
                for ( var key1 in cats[key0]['subcategoriesObs'] ) {
                    for ( var key2 in cats[key0]['subcategoriesObs'][key1]['subcategoriesObs'] ) {
                        if ( cats[key0]['subcategoriesObs'][key1]['subcategoriesObs'][key2]['checked']() )
                            return true;
                    }
                }
            }
            return false;
        }
    });
    self.xBoxCategory = function() {
        var cats = self.fcCategories;
        for ( var key0 in cats ) {
            cats[key0]['checked'](false);
            for ( var key1 in cats[key0]['subcategoriesObs'] ) {
                for ( var key2 in cats[key0]['subcategoriesObs'][key1]['subcategoriesObs'] ) {
                    cats[key0]['subcategoriesObs'][key1]['subcategoriesObs'][key2]['checked'](false);
                }
            }
        }
        self.boxCategoryUpdate.valueHasMutated();
        //self.doSearch( false );
        self.reloadDoSearch();
    };
    
    self.boxDistance = ko.computed({
        read: function() {
//            if ( self.fcDistance() != self.defaultDistance )
            if ( self.fcDistance() <= 
                (self.distanceDefaults.distanceSliderMax - self.distanceDefaults.distanceSliderSnap) )
                return true;
            else
                false;
        }
    });
    self.xBoxDistance = function() {
        self.fcDistance(self.distanceDefaults.distanceSliderMax);
        //self.doSearch( false );
        self.reloadDoSearch();
    };
    
    
    self.boxPostDate = ko.computed({
        read: function() {
            if ( self.fcPostDate() != self.defaultPostDate )
                return true;
            else
                return false;
        }
    });
    self.xBoxPostDate = function() {
        self.fcPostDate(self.defaultPostDate);
        //self.doSearch( false );
        self.reloadDoSearch();
    };
    
    
    self.boxWhere = ko.computed({
        read: function() {
            if ( self.fcGLat() && self.fcGLng() )
                return true;
            else
                return false;
        }
    });
    self.xBoxWhere = function() {
        self.fcGFormattedAddress('');
        //self.doSearch( false );
        self.reloadDoSearch();
    };
    
    
    self.boxWhat = ko.computed({
        read: function() {
            if ( self.fcPhrase() && self.fcPhrase().length > 0 )
                return true;
            else
                return false;
        }
    });
    self.xBoxWhat = function() {
        self.fcPhrase('');
        //self.doSearch( false );
        self.reloadDoSearch();
    };
    
    self.boxUserId = ko.computed({
        read: function() {
            if ( parseInt(self.fcUserId()) > 0 )
                return true;
            else
                return false;
        }
    });
    self.xBoxUserId = function() {
        self.fcUserId( null );
        //self.doSearch( false );
        self.reloadDoSearch();
    }
    
    
    self.xBoxAll = function() {
        var cats = self.fcCategories;
        for ( var key0 in cats ) {
            cats[key0]['checked'](false);
            for ( var key1 in cats[key0]['subcategoriesObs'] ) {
                for ( var key2 in cats[key0]['subcategoriesObs'][key1]['subcategoriesObs'] ) {
                    cats[key0]['subcategoriesObs'][key1]['subcategoriesObs'][key2]['checked'](false);
                }
            }
        }
        self.boxCategoryUpdate.valueHasMutated();
        self.fcRangeLow(self.defaultFcRangeLow);
        self.fcRangeHigh(self.defaultFcRangeHigh);
        self.fcSortBy(self.defaultSortBy);
        self.fcDistance(self.distanceDefaults.distanceSliderMax);
        self.fcPostDate(self.defaultPostDate);
        self.fcGFormattedAddress('');
        self.fcPhrase('');
        //self.doSearch( false );
        self.reloadDoSearch();
    }
    
    
    /*
     *
        filter.categories = tempCat0;
        filter.range = { low: self.fcRangeLow(), high: self.fcRangeHigh()};
     *
     **/
    
    
    
    // END - 'Criteria Box observables'    
    
    self.curFilterUriUpdate = ko.observable();
    self.curFilterUri = ko.computed({
        read: function() {
            self.curFilterUriUpdate();
            if (self.putStateSupport()) {
                var rx = new RegExp( self.baseViewUri.slice(1)+'/?(.*)', 'i');
                var match = rx.exec(document.location.href);
                if ( match && typeof match[1]=='string'  &&  match[1].length>0 ) 
                    return match[1];
                else
                    return '';
            }
            else
                return document.location.hash.slice(1);
        },
        write: function(value) {
            // value - musn't contain trailing slash
            if (self.putStateSupport())
                window.history.pushState({},'Fenchy',self.baseViewUri+'/'+value);
            else
                document.location.hash = '#'+value;
        }
    });
    
    

       
    // This observable holds the object that will be JSON.strigify'ed, 
    // and sent in request body after "Search" button is pressed. 
    self.toGoFilterCriteria = ko.computed( { read: function() {
        var filter = {};
        
        var tempCat0 = [];
        var tempFcCat0 = self.fcCategories;  // table of categories
        for( var key0 in tempFcCat0 ) {
            var i = tempCat0.push({}); // create temporary category
            tempCat0[i-1]['checked'] = tempFcCat0[key0]['checked']();
            tempCat0[i-1]['id'] = tempFcCat0[key0]['id'];
            tempCat0[i-1]['subcategories'] = [];
            var tempCat1 = tempCat0[i-1]['subcategories'];
            var tempFcCat1 = tempFcCat0[key0]['subcategoriesObs']; // table of properties
                        
            for ( var key1 in tempFcCat1 ) {
                var i = tempCat1.push({});   // create temporary property
                tempCat1[i-1]['checked'] = false;  // no check-box for property
                tempCat1[i-1]['id'] = tempFcCat1[key1]['id'];  // no check-box for property
                tempCat1[i-1]['subcategories'] = [];
                var tempCat2 = tempCat1[i-1]['subcategories']; // temp TABLE of values
                var tempFcCat2 = tempFcCat1[key1]['subcategoriesObs']; //table of values
                for ( var key2 in tempFcCat2 ) {
                    var i = tempCat2.push({});
                    tempCat2[i-1]['checked'] = tempFcCat2[key2]['checked']();
                    tempCat2[i-1]['id'] = tempFcCat2[key2]['id'];
                    //??? tempCat2(i)['checked'] = tempFcCat2[key2] ['checked']  ();
                }
            }
        }
        filter.categories = tempCat0;
        filter.range = { low: self.fcRangeLow(), high: self.fcRangeHigh()};
        filter.sortBy = self.fcSortBy();
        filter.distance = self.fcDistance();
        filter.postDate = self.fcPostDate();
        filter.what = self.fcPhrase();
        filter.location = {
            where: self.fcGFormattedAddress(),
            lat: self.fcGLat(),
            lng: self.fcGLng()
        };
        filter.userId = self.fcUserId();
        filter.number = self.number;
        
        return filter;
    },
    //deferEvaluation: true
    });//.extend({ throttle: 13});
        
    


    // This observable holds the URI value that should appear, after "Search" button is
    // pressed. New URI will be recalculated on every filter criteria update.
    self.toGoFilterUri = ko.computed( {
    read: function() {
            var uriComponents = new Array();
            var uriFromCriteria = '';
            
            var fcGLat = self.fcGLat();
            var fcGLng = self.fcGLng();
            // ...
            
            if ( fcGLat && fcGLng ) {
                uriComponents.push('lt='+self.fcGLat());
                uriComponents.push('lg='+self.fcGLng());
            }
            
            var categoriesComponent = '';
            var tempFcCat0 = self.fcCategories;  // table of categories
            for( var key0 in tempFcCat0 ) {
                var id0 = tempFcCat0[key0]['id'];
                if ( tempFcCat0[key0]['checked']() ) {
                   if ( categoriesComponent != '' )
                       categoriesComponent = categoriesComponent + '_';
                   categoriesComponent = categoriesComponent + id0;
                }
                var subcatComponent = '';

                var tempFcCat1 = tempFcCat0[key0]['subcategoriesObs']; // table of properties
                for ( var key1 in tempFcCat1 ) {
                    var id1 = tempFcCat1[key1]['id'];
                    var tempFcCat2 = tempFcCat1[key1]['subcategoriesObs']; //table of values
                    for ( var key2 in tempFcCat2 ) {
                        if ( tempFcCat2[key2]['checked']() ) {
                            var id2 = tempFcCat2[key2]['id'];
                            if ( subcatComponent != '' ) 
                                subcatComponent = subcatComponent + '&';
                            subcatComponent = subcatComponent + id1 + '-'+id2;
                        };
                    }
                }
                if ( subcatComponent != '' ) {
                    if ( categoriesComponent != '' )
                        categoriesComponent = categoriesComponent + '_';
                    categoriesComponent = categoriesComponent + 
                        id0 + '-' + subcatComponent;
                }
            }
            if ( categoriesComponent != '' ) {
                uriComponents.push('cat='+categoriesComponent);
            }
            
            var fcDistance = self.fcDistance();
            if ( fcDistance >= self.distanceDefaults.distanceSliderMin 
                && fcDistance != self.distanceDefaults.distanceSliderDefault 
                && fcDistance <= self.distanceDefaults.distanceSliderMax) {
                if ( fcDistance == self.distanceDefaults.distanceSliderMax )
                    uriComponents.push('dst=nl');
                else
                    uriComponents.push('dst='+fcDistance.toString());
            }
            
            var fcSortBy = self.fcSortBy();
            if ( fcSortBy && fcSortBy != self.defaultSortBy ) {
                uriComponents.push('sb='+fcSortBy.toString());
            }
 
            var fcPostDate = self.fcPostDate();
            if ( fcPostDate && fcPostDate != self.defaultPostDate) {
                uriComponents.push('pd='+fcPostDate.toString());
            }
 
            var fcPhrase = self.fcPhrase();
            if ( fcPhrase && fcPhrase.length > 0) {
                uriComponents.push('phr='+window.encodeURIComponent(fcPhrase));
            }
            
            var fcUserId = self.fcUserId();
            if ( parseInt(fcUserId) > 0 ) {
                uriComponents.push('uid='+fcUserId.toString() );
            }


            
            // Read function, used only to trigger URI update on dependent
            // observables change.
            // TODO: Make dependant on ALL the filter criteria observables,
            // so that when one criteria chcanges, new filter is being applied
            uriFromCriteria = uriComponents.join('/');
            
            
            return uriFromCriteria;

            //// Hold back from it. This will be moved "Search button pressed"
            //// event handler.
            //if ( self.stateReadFromUri )
            //    self.curFilterUri(uriFromCriteria);
    },
    //deferEvaluation: true
    });//.extend({ throttle: 13 });

    

    
   
    self.listModeList = ko.computed(function() {
        if ( self.listMode()  == 'list' )
            return true;
    });
    
    self.listModeTiles = ko.computed(function() {
        if ( self.listMode()  == 'tiles' )
            return true;
    });
    
    self.switchListModeList = function() {
        self.listMode('list');  
    };
    
    self.switchListModeTiles = function() {
        self.listMode('tiles');
    };
    
    

    // For jQuery UI Autocomplete binding
    self.fenSuggestionsArr = ko.observableArray([]);
//    self.fenSearchSelection = ko.observable();
    self.fenRetrieveSuggestions = function(searchTerm, sourceArray) {
        
        var request = jQuery.ajax({
                url: self.routes.fenchy_what_autocomplete_suggestions,
                type: "POST",
                data: {phrase: searchTerm},
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                // those two below are not sufficient, but let them be
                //dataType: "json",
                converters: { 'application json': jQuery.parseJSON   },   
        });

        request.done(function( data, textStatus, jqXHR ) {
            sourceArray(data);
        });

        request.fail(function( jqXHR, textStatus, errorThrown ) {
        });
    };
    // END - For jQuery UI Autocomplete binding

    /* ajaxloader */
    self.createAjaxLoader = function() {
        var loader = $('<div />').attr('class', 'loadergif').append($('<div />').attr('class', 'loadergif-inner-box').append($('<img />').attr('src', '/images/ajaxgif-big3.gif').attr('alt', 'loading')));
        return loader;
    }

    self.showAjaxLoader = function() {
        var loader = self.createAjaxLoader();
        var header = $('header.command-bar', 'section.content');
        if(!$(header).next().is('.loadergif')) {
            $(header).after(loader);
        }
        return loader;
    }

    self.retrieveFilterContent = function( initial, loadMore ) {
        var reqCriteriaData = self.toGoFilterCriteria();
        if ( initial ) {
           var initialUri = self.curFilterUri();
           if( initialUri == '') {
               self.parseUri(self.filterLastUrl);
               self.curFilterUri(self.filterLastUrl);
           } else
               self.parseUri(initialUri);
        } else {
            reqCriteriaData.toGoUrl = self.toGoFilterUri();        
        };
        self.fcGLat.valueHasMutated();
        
        var filterAjaxLoader = self.showAjaxLoader();
        var request = jQuery.ajax({
            url: self.routes.fenchy_filter_content,
            type: "POST",
            data: JSON.stringify(reqCriteriaData),
            contentType: 'application/json; charset=UTF-8',
            dataType: 'json',
            async: true,
            converters: { 'application json': jQuery.parseJSON }
        });
        request.done(function( data, textStatus, jqXHR ) {
            self.filterContentResult = data;
            self.afterBothRetrieveFilterContent( loadMore );
            if ( !initial ) {
                self.curFilterUri(self.toGoFilterUri());
            }
            $('#scrollable').tinyscrollbar_update();
            self.number++;
            $(filterAjaxLoader).remove();
        });
        request.fail(function( jqXHR, textStatus, errorThrown ) { }); 
    };

    
    self.doSearch = function( loadMore ) {
        
        if ( ( self.fcGLat == null || self.fcLng == null ) && 
            self.fcGFormattedAddress() && self.fcGFormattedAddress().length > 0 ) {
            self.gGeocoder.geocode( {'address': self.fcGFormattedAddress() }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK ) {
                    self.fcGFormattedAddress(results[0].formatted_address);
                    self.fcGLat(results[0].geometry.location.lat());
                    self.fcGLng(results[0].geometry.location.lng());
                }
                else {
                    self.fcGFormattedAddress('');
                    self.fcGLat(null);
                    self.fcGLng(null);  
                }
            });
        };
        if ( !loadMore ) {
            self.fcRangeLow(self.defaultFcRangeLow);
            self.fcRangeHigh(self.defaultFcRangeHigh);
        }
        self.retrieveFilterContent( false, loadMore );
    };
    
    // Parses passed URI and saves it's component values to corresponding
    // observables. This will automatically trigger address update and
    // filter results reload
    self.parseUri = function(value) {
        var valueValid = false;
        var match;
           
        // parse - will be true when the URL has been manually
        // entered by user.
        // TODO: parse given URI to filter criteria.
        // TODO: apply filter to current view;
        var rx_lat = new RegExp('lt=(-?[0-9]{1,3}\.[0-9]*)','i');
        match = rx_lat.exec(value);
        if ( match && typeof match[1]=='string'  &&  match[1].length>0 ) {
            self.fcGLat(match[1]);
        }
        else 
            self.fcGLat(null);
        var rx_lng = new RegExp('lg=(-?[0-9]{1,3}\.[0-9]*)','i');
        match = rx_lng.exec(value);
        if ( match && typeof match[1]=='string'  &&  match[1].length>0 ) {
            self.fcGLng(match[1]);
        }
        else
            self.fcGLng(null);
        
        var rx_cat = new RegExp('cat=([^/]+)');
        match = rx_cat.exec(value);
        var checks = [];
        if ( match && typeof match[1]=='string'  &&  match[1].length>0 ) {
            var categoryParts = match[1].split('_');
            var rx_scat = new RegExp('([a-z0-9]+)-(.+)');
            var rx_mcat = new RegExp('([a-z0-9]+)');
            for ( var key0 in categoryParts ) {
                var match_scat = rx_scat.exec(categoryParts[key0]);
                var match_mcat = rx_mcat.exec(categoryParts[key0]);
                if ( match_scat && typeof match_scat[1]=='string' && match_scat[1].length>0
                                && typeof match_scat[2]=='string' && match_scat[2].length>0) {
                    var catId = match_scat[1];
                    var subcategoryParts = match_scat[2].split('&');
                    for ( var key1 in subcategoryParts ) {
                        var splitSubcat = subcategoryParts[key1].split('-');
                        if ( splitSubcat.length == 2 ) {
                            checks.push({
                                cat: catId,
                                scat: splitSubcat[0],
                                val: splitSubcat[1]
                            });
                        }
                    }
                } else if ( match_mcat && typeof match_mcat[1]=='string' && match_mcat[1].length>0 ) {
                    var catId = match_mcat[1];
                    checks.push({
                        cat: catId,
                        scat: null,
                        val: null
                    });
                }
            }
        };
        var tempFcCat0 = self.fcCategories;  // table of categories
        for( var key0 in tempFcCat0 ) {
            var id0 = tempFcCat0[key0]['id'];
            for ( var i in checks ) {
                if ( checks[i]['cat']==id0 && checks[i]['scat']==null && checks[i]['val']==null )
                    tempFcCat0[key0]['checked'](true);
            };
            var tempFcCat1 = tempFcCat0[key0]['subcategoriesObs']; // table of properties
            for ( var key1 in tempFcCat1 ) {
                var id1 = tempFcCat1[key1]['id'];
                var tempFcCat2 = tempFcCat1[key1]['subcategoriesObs']; //table of values
                for ( var key2 in tempFcCat2 ) {
                    var id2 = tempFcCat2[key2]['id'];
                    for ( var i in checks ) {
                        if ( checks[i]['cat']==id0 && checks[i]['scat']==id1 && checks[i]['val']==id2 ) {
                            tempFcCat2[key2]['checked'](true);
                        }
                        else
                            tempFcCat2[key2]['checked'](false);
                    }
                }
            }
        }
   
        var rx_dist = new RegExp('dst=(nl|[1-9][0-9]+)');
        match = rx_dist.exec(value);
        if ( match && typeof match[1]=='string' && match[1].length>0) {
            if ( match[1] == 'nl' )
                self.fcDistance( self.distanceDefaults.distanceSliderMax );
            else
                self.fcDistance(parseInt(match[1]));
        }
        else
            self.fcDistance( self.distanceDefaults.distanceSliderDefault );
        
        var rx_sortby = new RegExp('sb=([a-zA-Z]+)');
        match = rx_sortby.exec(value);
        if ( match && typeof match[1]=='string' && 
             self.inArrayElement(match[1], self.availableSorts(), 'optValue') ) {
             self.fcSortBy(match[1]);
        }
        else 
            self.fcSortBy(self.defaultSortBy);
        
        var rx_postdate = new RegExp('pd=([^/]+)');
        match = rx_postdate.exec(value);
        var resFcPostDate = null;
        if ( match && typeof match[1]=='string') {
            var postdate_value = match[1];
            if ( moment( postdate_value, 'YYYY-MM-DD' ).isValid() ) {
                resFcPostDate = postdate_value;
            }
            else if ( self.inArrayElement(postdate_value, self.availablePostDates(), 'id') ) {
                resFcPostDate = postdate_value;
            }
        }
        self.fcPostDate( resFcPostDate );

        var rx_phrase = new RegExp('phr=([^/]+)');
        match = rx_phrase.exec(value);
        if ( match && typeof match[1]=='string' && match[1].length>0 ) {
            self.fcPhrase(window.decodeURIComponent(match[1]));
        }
        else
            self.fcPhrase('');
        
        var rx_userid = new RegExp('uid=([0-9]+)');
        match = rx_userid.exec(value);
        if ( match && parseInt(match[1]) > 0 ) {
            self.fcUserId( parseInt(match[1]) );
        }
        else
            self.fcUserId( null );
        


        self.stateReadFromUri = true;
    }


    self.logg = function() { 
        //self.fcDistance(3000);
        //self.fcGLat.valueHasMutated();
        //console.log(self.toGoFilterUri());
        //console.log(self.toGoFilterCriteria()); 
        //console.log(self.fcCategories);
    };
    
}






GlobalFilterViewModel.prototype.afterBothRetrieveFilterContent = function( loadMore ) {
    var self = this;
    self.availablePostDates( self.filterContentResult.filter.postDate );

    if ( self.filterContentResult.notices 
        && self.filterContentResult.notices.length > self.pagination ) {
        self.filterContentResult.notices.pop();
        self.noticesLoadMore( true );
    }
    else
        self.noticesLoadMore( false );
    
    var notices = self.notices();
    if ( ! loadMore )
        self.notices( self.filterContentResult.notices );
    else 
        self.notices( self.notices().concat(self.filterContentResult.notices) );
    
    self.noticesCount(self.filterContentResult.noticesCount);
    
    var tmpCategories = jQuery.extend([], self.filterContentResult.filter.categories);
    for ( var key0 in tmpCategories ) {
        for ( var key1 in tmpCategories[key0].subcategories ) {
            for ( var key2 in tmpCategories[key0].subcategories[key1].subcategories ) {
                var chkVal;
                if ( typeof tmpCategories[key0].subcategories[key1].subcategories[key2].checked !== 'undefined')
                    chkVal = tmpCategories[key0].subcategories[key1].subcategories[key2].checked;
                else
                    chkVal = false;
                tmpCategories[key0].subcategories[key1].subcategories[key2].checked =
                    ko.observable(chkVal);
            }
            tmpCategories[key0].subcategories[key1].subcategoriesObs =
                jQuery.extend([],tmpCategories[key0].subcategories[key1].subcategories);
                ////ko.observableArray(tmpCategories[key0].subcategories[key1].subcategories);
            delete tmpCategories[key0].subcategories[key1].subcategories;
        }
        var chkVal;
        if ( typeof tmpCategories[key0].checked !== 'undefined' ) {
            chkVal = tmpCategories[key0].checked;
        }
        else
            chkVal = false;
        // TODO: odznaczanie root computed
        //tmpCategories[key0].checked = ko.observable(chkVal);

        tmpCategories[key0].subcategoriesObs =
            jQuery.extend([],tmpCategories[key0].subcategories);
            /////ko.observableArray( tmpCategories[key0].subcategories );
        delete tmpCategories[key0].subcategories;

        tmpCategories[key0].checkedLRV =  ko.observable( false );
        tmpCategories[key0].checked =  ko.computed({
            'read': function() {
                var all_unchecked = true;
                for( var key1 in this.subcategoriesObs ) {
                    for( var key2 in this.subcategoriesObs[key1]['subcategoriesObs'] ) {
                        if ( this.subcategoriesObs[key1]['subcategoriesObs'][key2]['checked']() ) {
                            all_unchecked = false;
                        }
                    }
                }

                if ( !all_unchecked ) {
                    this['checkedLRV'](true);
                    return true;
                }
                else {
                    return this['checkedLRV']();
                }
            },
            'write': function( value ) {
                if ( this['checkedLRV']() && value == false ) {
                    for( var key1 in this.subcategoriesObs ) {
                        for( var key2 in this.subcategoriesObs[key1]['subcategoriesObs'] ) {
                            this.subcategoriesObs[key1]['subcategoriesObs'][key2]['checked']( false );
                        }
                    }
                    this['checkedLRV'](false);
                } else if ( value == true ) {
                    this['checkedLRV'](true);
                }
            },
            'owner': tmpCategories[key0]
        });
        tmpCategories[key0].checked(chkVal);
    }
    self.fcCategories = jQuery.extend([], tmpCategories);
    delete tmpCategories;
    // Prompt Knockout to re-render categories selector by 'touch'ing'
    // observable template name. 
    self.categoriesTemplate.valueHasMutated();
    
    if ( loadMore ) {
        window.setTimeout( function() { 
            notice_tinyscroll.tinyscrollbar_update('bottom');
            self.moreNoticesBeingLoaded = false;
            console.log('bottom');
        }, 20 );
    }
};

GlobalFilterViewModel.prototype.loadListing = function( url ) {
    window.location = url;
};

GlobalFilterViewModel.prototype.loadProfile = function( url ) {
    window.location = url;
};


GlobalFilterViewModel.prototype.inputPressEnter = function(data, event) {
    var self = this;
    if ( event.keyCode == 13 ) {
        self.doSearch( false );
        //self.reloadDoSearch();
    }
    else {
        return true;
    }
};



GlobalFilterViewModel.prototype.anchorNoOp = function( data, event ) {
    event.preventDefault();
};

GlobalFilterViewModel.prototype.loadMoreNotices = function() {
    var self = this;
    if ( ! self.moreNoticesBeingLoaded && self.noticesLoadMore() ) {
        self.moreNoticesBeingLoaded = true;
        var fcRangeLow = self.fcRangeLow();
        self.fcRangeLow( fcRangeLow + self.pagination );
        self.doSearch( true );
    }
};