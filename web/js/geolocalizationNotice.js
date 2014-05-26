
var geocoder;
var infowindow;
var map;
var markersArray = [];
var typingTimeout;

var geoFormId = 'fenchy_noticebundle_noticetype_location_location';

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

function callback(results, status) {
  if (status == google.maps.places.PlacesServiceStatus.OK) {
    for (var i = 0; i < results.length; i++) {
     createMarker(results[i]);
    }
  }
}

function savePosition(marker) {
    var point = marker.getPosition();
    
    document.getElementById('fenchy_noticebundle_noticetype_location_latitude').value = point.lat();
    document.getElementById('fenchy_noticebundle_noticetype_location_latitude').value = point.lng();

}

function createMarker(place, info, startPosition) {
    var placeLoc = place.geometry.location;
    
    var marker = new google.maps.Marker({
      position: place.geometry.location,
      map: map,
      draggable: true
    });
        
    if (info) {
        $('#fenchy_noticebundle_noticetype_location_location').val(place.formatted_address);
        infowindow.setContent(place.formatted_address);
        infowindow.open(map, marker);           
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
    
    $('#fenchy_noticebundle_noticetype_location_location').val(address);
    
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
    alert('ddd');
    if($('#fenchy_noticebundle_noticetype_location_location').is('input')) {
        address = $('#fenchy_noticebundle_noticetype_location_location').val();
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

