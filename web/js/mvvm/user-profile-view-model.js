function UserProfileViewModel( 
    gcoder, 
    baseSearchViewUri, 
    routes,
    pagination,
    usersOwnProfile,
    displayUserId,
    loggedInUserId,
    initialReviews,
    initialListings,
    initialReviewsOO  )
{
    var self = this;
    
    self.gGeocoder = gcoder;
    self.baseSearchViewUri = baseSearchViewUri;
    self.routes = routes;
    self.displayUserId = displayUserId;
    
    self.putStateSupport = ko.computed( function() {
        if (('replaceState' in window.history) && window.history['replaceState'] !== null)
            return true;
        else
            return false;
    });
    
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
    
    self.usersOwnProfile = usersOwnProfile;
    self.loggedInUserId = loggedInUserId;
    self.pagination = pagination;
    
    self.reviewsCount = ko.observable( initialReviews.count );
    if ( initialReviews.list.length > self.pagination ) {
        initialReviews.list.pop();
        self.reviewsLoadMore = ko.observable(true);
    } else
        self.reviewsLoadMore = ko.observable(false);
    self.reviews = ko.observableArray( initialReviews.list );
    self.reviewsRangeMin = 0;
    self.reviewsRangeMax = self.reviewsRangeMin + self.pagination - 1;
    self.leaveReviewFormLoaded = false;
    
    self.listingsCount = ko.observable( initialListings.count );
    if ( initialListings.list.length > self.pagination ) {
        initialListings.list.pop();
        self.listingsLoadMore = ko.observable(true);
    } else
        self.listingsLoadMore = ko.observable(false);
    self.listings = ko.observableArray( initialListings.list );
    self.listingsRangeMin = 0;
    self.listingsRangeMax = self.listingsRangeMin + self.pagination -1;
    
    self.reviewsOOCount = ko.observable( initialReviewsOO.count );
    if ( initialReviewsOO.list.length > self.pagination ) {
        initialReviewsOO.list.pop();
        self.reviewsOOLoadMore = ko.observable(true);
    } else
        self.reviewsOOLoadMore = ko.observable(false);
    self.reviewsOO = ko.observableArray( initialReviewsOO.list );
    self.reviewsOORangeMin = 0;
    self.reviewsOORangeMax = self.reviewsOORangeMin + self.pagination - 1; 
    
    
    
    
    
    // For jQuery UI Autocomplete binding
    self.fenSuggestionsArr = ko.observableArray([]);
    // END - For jQuery UI Autocomplete binding
    
    self.toGoFilterUri = ko.computed( {
        read: function() {
            var uriComponents = new Array();
            var uriFromCriteria = '';
            
            var fcGLat = self.fcGLat();
            var fcGLng = self.fcGLng();
            
            if ( fcGLat && fcGLng ) {
                uriComponents.push('lt='+self.fcGLat());
                uriComponents.push('lg='+self.fcGLng());
            }
 
            var fcPhrase = self.fcPhrase();
            if ( fcPhrase && fcPhrase.length > 0) {
                uriComponents.push('phr='+window.encodeURIComponent(fcPhrase));
            }
            
            uriFromCriteria = uriComponents.join('/');
            return uriFromCriteria;
        }
    });

}



UserProfileViewModel.prototype.doSearch = function() {
        
    if ( ( this.fcGLat == null || this.fcLng == null ) && 
        this.fcGFormattedAddress() && this.fcGFormattedAddress().length > 0 ) {
        this.gGeocoder.geocode( {'address': this.fcGFormattedAddress() }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK ) {
                this.fcGFormattedAddress(results[0].formatted_address);
                this.fcGLat(results[0].geometry.location.lat());
                this.fcGLng(results[0].geometry.location.lng());
            }
            else {
                this.fcGFormattedAddress('');
                this.fcGLat(null);
                this.fcGLng(null);  
            }
        });
    };
    
    var resultLocation = this.baseSearchViewUri;
    var toGoFilterUriVal =  this.toGoFilterUri();
    if ( toGoFilterUriVal.length > 0 ) {
        if ( !this.putStateSupport() ) {
            resultLocation = resultLocation + '#';
        }
        resultLocation = resultLocation + '/' + toGoFilterUriVal;
    }
    
    window.document.location.pathname = resultLocation;
};


// For jQuery UI Autocomplete binding
UserProfileViewModel.prototype.fenRetrieveSuggestions = function(searchTerm, sourceArray) {
    var request = jQuery.ajax({
        url: "/app_dev.php/listing/acsuggest",
        type: "POST",
        data: {phrase: searchTerm},
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            converters: { 'application json': jQuery.parseJSON   },   
        });

    request.done(function( data, textStatus, jqXHR ) {
        sourceArray(data);
    });

    request.fail(function( jqXHR, textStatus, errorThrown ) {
    });
};
// END - For jQuery UI Autocomplete binding


UserProfileViewModel.prototype.reloadReviews = function( doneCallback ) {
    var self = this;
    var request = jQuery.ajax({
        url: self.routes.fenchy_regular_user_reviewslist + '/' + self.displayUserId,
        type: "POST",
        data: 'reload=reviews',
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
    });
    request.done(function( data, textStatus, jqXHR ){
        if ( typeof doneCallback == 'function' )
            doneCallback();
        if ( data.list.length > self.pagination ) {
            self.reviewsLoadMore(true);
            data.list.pop();
        }
        else {
            self.reviewsLoadMore(false);
        }
        self.reviews( data.list );
        self.reviewsCount( data.count );
        self.reviewsRangeMin = 0;
        self.reviewsRangeMax = self.pagination - 1;
    });
}


UserProfileViewModel.prototype.reloadReviewsOO = function( doneCallback ) {
    var self = this;
    var request = jQuery.ajax({
        url: self.routes.fenchy_regular_user_reviewslist + '/' + self.displayUserId,
        type: "POST",
        data: 'reload=reviewsoo',
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
    });
    request.done(function( data, textStatus, jqXHR ){
        if ( typeof doneCallback == 'function' )
            doneCallback();
        if ( data.list.length > self.pagination ) {
            self.reviewsLoadMore(true);
            data.list.pop();
        }
        else {
            self.reviewsLoadMore(false);
        }
        self.reviewsOO( data.list );
        self.reviewsOOCount( data.count );
        self.reviewsOORangeMin = 0;
        self.reviewsOORangeMax = self.pagination - 1;
    });
}

UserProfileViewModel.prototype.loadMoreReviews = function( ) {
    var self = this;
    var request = jQuery.ajax({
        url: self.routes.fenchy_regular_user_reviewslist + '/' + self.displayUserId,
        type: "POST",
        data: 'loadmore=reviews'+
              '&min='+(self.reviewsRangeMax+1).toString()+
              '&max='+(self.reviewsRangeMax+self.pagination).toString(),
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8'        
    });
    request.done(function( data, textStatus, jqXHR) {
        if ( data.list.length > self.pagination ) {
            self.reviewsLoadMore(true);
            data.list.pop();
        }
        else {
            self.reviewsLoadMore(false);
        }
        self.reviews( self.reviews().concat(data.list) );
        self.reviewsCount( data.count );
        
        self.reviewsRangeMin = self.reviewsRangeMin + self.pagination;
        self.reviewsRangeMax = self.reviewsRangeMax + self.pagination;

        $('#spinner-div').detach();
    });
    
    $('div#tabs-1  .load-more-button').insertBefore( spinnerHTML );
}

UserProfileViewModel.prototype.anchorNoOp = function( data, event ) {
    event.preventDefault();
};

UserProfileViewModel.prototype.anchorPosRev = function( data, event ) {
    tabsJQ.tabs('option', 'active', 0);
    event.preventDefault();
};

UserProfileViewModel.prototype.anchorRevWrit = function( data, event ) {
    tabsJQ.tabs('option', 'active', 2);
    event.preventDefault();
};


//    tabsJQ.tabs('option', 'active', 0);



UserProfileViewModel.prototype.loadEditReview = function( data, event, parent ) {

    var self = parent;
    var articleElement = $(event.target).parents('article');
    
    if (  !self.leaveReviewFormLoaded ) {
        var request = jQuery.ajax({
            url: self.routes.fenchy_regular_user_reviewseditform + '/' + data.id.toString(),
            type: "POST",
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
        });
           
        request.done(function( html, textStatus, jqXHR ){
            $('#spinner-div').replaceWith( html );
            $('form#leave-review a.leave-review-submit').click(function(event) {
                request = jQuery.ajax({
                    url:  self.routes.fenchy_regular_user_reviewssave + '/' + data.id.toString(),
                    type: "POST",
                    data: $('form#leave-review').serialize(),
                    contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
                });
                
                request.done(function( html, textStatus, jqXHR ) {
                    //$('#spinner-div').detach();
                    self.reloadReviews();
                    self.leaveReviewFormLoaded = false;
                });
                $('form#leave-review').replaceWith( spinnerHTML ); 
                event.preventDefault();
            });

            $('form#leave-review a.leave-review-cancel').click(function(event) {
                 self.leaveReviewFormLoaded = false;
                self.reloadReviews();
                event.preventDefault();
            });
              
        });
            
        self.leaveReviewFormLoaded = true; 
        $(articleElement[0]).replaceWith( spinnerHTML );
    };
};


UserProfileViewModel.prototype.loadEditReviewOO = function( data, event, parent ) {

    var self = parent;
    var articleElement = $(event.target).parents('article');
    
    if (  !self.leaveReviewFormLoaded ) {
        var request = jQuery.ajax({
            url: self.routes.fenchy_regular_user_reviewseditform + '/' + data.id.toString(),
            type: "POST",
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
        });
           
        request.done(function( html, textStatus, jqXHR ){
            $('#spinner-div').replaceWith( html );
            $('form#leave-review a.leave-review-submit').click(function(event) {
                request = jQuery.ajax({
                    url:  self.routes.fenchy_regular_user_reviewssave + '/' + data.id.toString(),
                    type: "POST",
                    data: $('form#leave-review').serialize(),
                    contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
                });
                
                request.done(function( html, textStatus, jqXHR ) {
                    //$('#spinner-div').detach();
                    self.reloadReviewsOO();
                    self.leaveReviewFormLoaded = false;
                });
                $('form#leave-review').replaceWith( spinnerHTML ); 
                event.preventDefault();
            });

            $('form#leave-review a.leave-review-cancel').click(function(event) {
                 self.leaveReviewFormLoaded = false;
                self.reloadReviewsOO();
                event.preventDefault();
            });
              
        });
            
        self.leaveReviewFormLoaded = true; 
        $(articleElement[0]).replaceWith( spinnerHTML );
    };
};




UserProfileViewModel.prototype.loadMoreReviewsOO = function( ) {
    var self = this;
    var request = jQuery.ajax({
        url:  self.routes.fenchy_regular_user_reviewslist + '/' + self.displayUserId,
        type: "POST",
        data: 'loadmore=reviewsoo'+
              '&min='+(self.reviewsOORangeMax+1).toString()+
              '&max='+(self.reviewsOORangeMax+self.pagination).toString(),
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8'        
    });
    request.done(function( data, textStatus, jqXHR) {
        if ( data.list.length > self.pagination ) {
            self.reviewsOOLoadMore(true);
            data.list.pop();
        }
        else {
            self.reviewsOOLoadMore(false);
        }
        self.reviewsOO( self.reviewsOO().concat(data.list) );
        self.reviewsOOCount( data.count );
        
        self.reviewsOORangeMin = self.reviewsOORangeMin + self.pagination;
        self.reviewsOORangeMax = self.reviewsOORangeMax + self.pagination;

        $('#spinner-div').detach();
    });
    
    $('div#tabs-3  .load-more-button').insertBefore( spinnerHTML );
}


UserProfileViewModel.prototype.loadLeaveReviewFrom = function() {
    var self = this;

    tabsJQ.tabs("select", "tabs-1");

    if ( !self.leaveReviewFormLoaded && !self.usersOwnListing && self.loggedInUserId) {
        
        var request = jQuery.ajax({
            url: self.routes.fenchy_regular_user_reviewseditform,
            type: "POST",
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            data: "profile=1"
        });

        self.leaveReviewFormLoaded = true; 
        $('#tabs-1').prepend( spinnerHTML );

        request.done(function( html, textStatus, jqXHR ){ 
            self.reviewAttachForm(html, null);
        });
    }
};



UserProfileViewModel.prototype.reviewAttachForm = function(html, editId) {
    var self = this;

    $('#spinner-div').replaceWith(html);
    $('form#leave-review a.leave-review-submit').click(function(event) {
        self.reviewSubmit.apply(self, [event, editId]);
    });
    $('form#leave-review a.leave-review-cancel').click(function(event) {
        self.reviewCancel.apply(self, [event]);
    });
};

UserProfileViewModel.prototype.reviewSubmit = function(event, editId) {
    var self = this;
    var req_url = '';
    if (editId)
        req_url = self.routes.fenchy_regular_user_reviewssave + '/' + editId;
    else
        req_url = self.routes.fenchy_regular_user_reviewssave + '///' + self.displayUserId;

    var request = jQuery.ajax({
        url: req_url,
        type: "POST",
        data: $('form#leave-review').serialize(),
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
    });

    request.done(function(html, textStatus, jqXHR) {
        var validate = jqXHR.getResponseHeader('validate');
        if (validate === 'pass') {
            self.reloadReviews(function() {
                $('#spinner-div').detach();
            });
            self.leaveReviewFormLoaded = false;
        }
        else {
            self.reviewAttachForm(html, editId);
        }
    });

    $('form#leave-review').replaceWith(spinnerHTML);
    event.preventDefault();
};

UserProfileViewModel.prototype.reviewCancel = function(event) {
    var self = this;
    $('form#leave-review').detach();
    self.leaveReviewFormLoaded = false;
    event.preventDefault();
};




UserProfileViewModel.prototype.loadMoreListings = function( ) {
    var self = this;
    var request = jQuery.ajax({
        url: self.routes.fenchy_regular_user_listingslist + '/' + self.displayUserId,
        type: "POST",
        data: 'loadmore=listings'+
              '&min='+(self.listingsRangeMax+1).toString()+
              '&max='+(self.listingsRangeMax+self.pagination).toString(),
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8'        
    });
    request.done(function( data, textStatus, jqXHR) {
        if ( data.list.length > self.pagination ) {
            self.listingsLoadMore(true);
            data.list.pop();
        }
        else {
            self.listingsLoadMore(false);
        }
        self.listings( self.listings().concat(data.list) );
        self.listingsCount( data.count );
        
        self.listingsRangeMin = self.listingsRangeMin + self.pagination;
        self.listingsRangeMax = self.listingsRangeMax + self.pagination;

        $('#spinner-div').detach();
    });
    
    $('div#tabs-2  .load-more-button').insertBefore( spinnerHTML );
}


UserProfileViewModel.prototype.inputPressEnter = function(data, event) {
       var self = this;
       if ( event.keyCode == 13 )
           self.doSearch( false );
       else
           return true;
};

UserProfileViewModel.prototype.loadListing = function( url ) {
    window.location = url;
};

UserProfileViewModel.prototype.loadProfile = function( url ) {
    window.location = url;
};