
var geocoder;
var infowindow;
var map;
var markersArray = [];
var typingTimeout;

var geoFormId = 'fenchy_userbundle_user_locationtype_location';

var componentForm = {
    street_number: 'long_name',
    route: 'long_name',
    locality: 'long_name',
    sublocality: 'long_name',
    administrative_area_level_1: 'long_name',
    administrative_area_level_2: 'long_name',
    country: 'long_name',
    postal_code: 'long_name'
};
        
function initialize() {
    geocoder = new google.maps.Geocoder();    
    var mapOptions = {
      zoom: 10,
      scale:5,      
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      navigationControlOptions: {
        style: google.maps.NavigationControlStyle.ZOOM_PAN
      }
    }
    
    map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);   
    infowindow = new google.maps.InfoWindow();
    
//    $('#'+geoFormId+'_country').keypress(mapTypingUpdate);
//    
//    $('#'+geoFormId+'_postcode').keypress(mapTypingUpdate);
//
//    $('#'+geoFormId+'_city').keypress(mapTypingUpdate);
//
//    $('#'+geoFormId+'_street').keypress(mapTypingUpdate);
    
    setLocation();
}
function fillInAddress() {
        
        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();
        //console.log(place);
        // Get each component of the address from the place details
        // and fill the corresponding field on the form.
        document.getElementById(geoFormId+'_street_number').value = '';
        document.getElementById(geoFormId+'_route').value = '';
        document.getElementById(geoFormId+'_locality').value = '';
        document.getElementById(geoFormId+'_sublocality').value = '';
        document.getElementById(geoFormId+'_administrative_area_level_1').value = '';
        document.getElementById(geoFormId+'_administrative_area_level_2').value = '';
        document.getElementById(geoFormId+'_country').value = '';
        document.getElementById(geoFormId+'_postal_code').value = '';
        
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            
            if (componentForm[addressType]) {
                var val = place.address_components[i][componentForm[addressType]];
                if(val !='')
                    document.getElementById(geoFormId+'_'+addressType).value = val;
                else
                    document.getElementById(geoFormId+'_'+addressType).value = null;
            }
            else
            {
                document.getElementById(geoFormId+'_street_address').value = val;
            }
//               console.log( place.address_components[i]);
//               console.log(place.address_components[i].types[0]);
                //document.getElementById(addressType).value = val;
            
        }
}

function callback(results, status) {
  if (status == google.maps.places.PlacesServiceStatus.OK) {
    for (var i = 0; i < results.length; i++) {
     createMarker(results[i]);
    }
  }
}

function savePosition(marker) {
    var point = marker.getPosition();
    
    document.getElementById(geoFormId+'_latitude').value = point.lat();
    document.getElementById(geoFormId+'_longitude').value = point.lng();

}

function createMarker(place, info, startPosition) {
    var placeLoc = place.geometry.location;
    
    var marker = new google.maps.Marker({
      position: place.geometry.location,
      map: map,
      draggable: true
    });
        
    if (info) {
        $('#fenchy_userbundle_user_locationtype_location_location').val(place.formatted_address);
        infowindow.setContent(place.formatted_address);
        infowindow.open(map, marker);
        
        document.getElementById(geoFormId+'_location').value = place.formatted_address;
        
        document.getElementById(geoFormId+'_street_number').value = '';
        document.getElementById(geoFormId+'_route').value = '';
        document.getElementById(geoFormId+'_locality').value = '';
        document.getElementById(geoFormId+'_sublocality').value = '';
        document.getElementById(geoFormId+'_administrative_area_level_1').value = '';
        document.getElementById(geoFormId+'_administrative_area_level_2').value = '';
        document.getElementById(geoFormId+'_country').value = '';
        document.getElementById(geoFormId+'_postal_code').value = '';
        
        
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            
            if (componentForm[addressType]) {
                var val = place.address_components[i][componentForm[addressType]];
                if(val != '')
                    document.getElementById(geoFormId+'_'+addressType).value = val;
                else
                    document.getElementById(geoFormId+'_'+addressType).value = null;
            }
            else
            {
                var val = place.address_components[i][undefined];
                document.getElementById(geoFormId+'_street_address').value = val;
            }
               //console.log( place.address_components[i]);
               //console.log(place.address_components[i].types[0]);
                //document.getElementById(addressType).value = val;
            
        }
    }
       
    markersArray.push(marker);
    
    if (startPosition) {
        savePosition(marker);
    } 
    
    google.maps.event.addListener(marker, 'dragend', function() {
        setPosition(marker, true);
  });  
}

function placeMarker(position, map) {
    var marker = new google.maps.Marker({
      position: position,
      map: map,
      draggable: true
    });
    
    markersArray.push(marker);
    map.panTo(position);
    setPosition(marker, true);
}

function putPin()
{
    clearOverlays(); 
    
    var marker = new google.maps.Marker({
      position: map.getCenter(),
      map: map,
      draggable: true
    });    
    
    google.maps.event.addListener(marker, 'dragend', function() {
        setPosition(marker, true);
  });
  
    markersArray.push(marker);
    setPosition(marker, true);
}

function setPosition(marker, info)
{
    
    clearOverlays();
    var point = marker.getPosition();
    document.getElementById(geoFormId+'_latitude').value = point.lat();
    document.getElementById(geoFormId+'_longitude').value = point.lng();
    
    var obj = getAddressDetailsFromMarker(marker, fillLocationField);

    var latlng = new google.maps.LatLng(point.lat(), point.lng());
    
    geocoder.geocode({'latLng': latlng}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        if (results[0]) {
            createMarker(results[0], true, true);
        }
      }
    });    
}

function fillLocationField(object) {
    
    var tmp = new Array();
    var address = '';
    
    if(typeof object.locality != 'undefined') {
        tmp.push(object.locality);
    }
    
    if(typeof object.country != 'undefined') {
        tmp.push(object.country);
    }    
    
    if(tmp.length) {
    
        address = tmp.join(", ");
    }
    
    //$('#fenchy_userbundle_user_locationtype_location_location').val(address);
    
}

function codeAddress(info) {    
    
    var address='';
    
//    var country     = $('#'+geoFormId+'_country');
//    var postcode    = $('#'+geoFormId+'_postcode');
//    var city        = $('#'+geoFormId+'_city');
//    var street      = $('#'+geoFormId+'_street');
//    
//    if (country.val() !== country.attr('watermark')) {
//        address += country.val() + ' ';
//     }
//     
//    if (postcode.val() !== postcode.attr('watermark')) {
//        address+= postcode.val() + ' ';
//     }
//     
//    if (city.val() !== city.attr('watermark')) {
//        address+= city.val() + ' ';
//     }
//     
//    if (street.val() !== street.attr('watermark')) {
//        address+= street.val() + ' ';
//     }
 
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        if (info) {
            createMarker(results[0], true, true);
        } else {
            createMarker(results[0], false, true);
        }
        
      }
    });
    
}

function addMarker(location) {
    marker = new google.maps.Marker({
      position: location,
      map: map
    });

    markersArray.push(marker);
}

// Removes the overlays from the map, but keeps them in the array
function clearOverlays(excludeFirst) {
  if (markersArray) {  
    for (i in markersArray) {
        if (excludeFirst && i==0) {
        } else{
            markersArray[i].setMap(null);
        }
        
        
    }
  }
}

// Shows any overlays currently in the array
function showOverlays() {
  if (markersArray) {
    for (i in markersArray) {
      markersArray[i].setMap(map);
    }
  }
}

// Deletes all markers in the array by removing references to them
function deleteOverlays() {
  if (markersArray) {
    for (i in markersArray) {
      markersArray[i].setMap(null);
    }
    markersArray.length = 0;
  }
}



function setLocation(new_address) {
    clearOverlays();
    var address='';
    var isPlace     = false;

    
    if($('#fenchy_userbundle_user_locationtype_location_location').is('input')) {
        address = $('#fenchy_userbundle_user_locationtype_location_location').val();
        if(address.length) {
            isPlace     = true;
        }
    }

    if (typeof new_address != 'undefined') {
        address = new_address;
        isPlace = true;
    }
    

    if (isPlace) {
       geocoder.geocode( { 'address': address}, function(results, status) {
         if (status == google.maps.GeocoderStatus.OK) {
           map.setZoom(15);
           var bounds = new google.maps.LatLngBounds();
           map.fitBounds(bounds);
           map.setCenter(results[0].geometry.location);
           createMarker(results[0], true, true);
         }
       });           
    } else if (hasCoordinates()){
           var latlng = new google.maps.LatLng(getLatitude(), getLongitude());

           var mapOptions = {
             zoom: 10,
             scale:5,
             center: latlng,
             mapTypeId: google.maps.MapTypeId.ROADMAP,
             navigationControl: true,
           }

           map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);    

           geocoder.geocode({'latLng': latlng}, function(results, status) {
             if (status == google.maps.GeocoderStatus.OK) {
               if (results[0]) {               
                   createMarker(results[0], true, false)
               }
             }
           });                 
    } else {
           var latlng = new google.maps.LatLng(52.522, 13.409);
           var mapOptions = {
             zoom: 1,
             scale:5,
             center: latlng,
             mapTypeId: google.maps.MapTypeId.ROADMAP,
             navigationControlOptions: {
              style: google.maps.NavigationControlStyle.ZOOM_PAN
            },
            scaleControl: true,
           }

           map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);            
    }
}

function parseAddressComponents(address_components, object) {
    
    if(address_components.types.indexOf('locality') != -1) {
        object.locality = address_components.long_name;
    }
    
    if(address_components.types.indexOf('country') != -1) {
        object.country = address_components.long_name;
    }
    
    if(typeof object.locality != 'undefined' && typeof object.country != 'undefined') {
        return true;
    }
    
    return false;
    
}

function getAddressDetailsFromMarker(marker, callback) {
    
    var point = marker.getPosition();
    
    var obj = new Object();
    
    var latlng = new google.maps.LatLng(point.lat(),point.lng());
    
    geocoder.geocode( { 'latLng': latlng}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
            
            for(i in results) {
                
                for(j in results[i].address_components) {
                    
                    if(parseAddressComponents(results[i].address_components[j], obj)) {
                        callback(obj);
                        return obj;
                    }
                }
            }
            
            callback(obj);
      }
    });  
    
    
    return obj;
    
}


function mapTypingUpdate() {
    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(function(){
        clearOverlays();
        codeAddress(true);
    }, 700);
}

