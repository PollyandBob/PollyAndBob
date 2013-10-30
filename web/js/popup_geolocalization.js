
var geocoder;
var infowindow;
var map;
var markersArray = [];
var typingTimeout;

function initialize() {
    geocoder = new google.maps.Geocoder();    
    var mapOptions = {
      zoom: 10,
      scale:5,      
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      draggable: true,
      navigationControlOptions: {
        style: google.maps.NavigationControlStyle.ZOOM_PAN
      }
    }
    
    map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);   
    infowindow = new google.maps.InfoWindow();
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
    document.getElementById('fenchy_regularuserbundle_userregular_popup_locationtype_latitude').value = point.lat();
    document.getElementById('fenchy_regularuserbundle_userregular_popup_locationtype_longitude').value = point.lng();
}

function createMarker(place, info, startPosition) {
    var placeLoc = place.geometry.location;
    
    var marker = new google.maps.Marker({
      position: place.geometry.location,
      map: map,
      draggable: true
    });
        
    if (info) {
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
    document.getElementById('fenchy_regularuserbundle_userregular_popup_locationtype_latitude').value = point.lat();
    document.getElementById('fenchy_regularuserbundle_userregular_popup_locationtype_longitude').value = point.lng();

    var latlng = new google.maps.LatLng(point.lat(), point.lng());
    
    geocoder.geocode({'latLng': latlng}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        if (results[0]) {
            createMarker(results[0], true, true);
        }
      }
    });    
}

function codeAddress(info) {    
    
    var address='';
    
    var country = $('#fenchy_regularuserbundle_userregular_popup_locationtype_country');
    var postcode = $('#fenchy_regularuserbundle_userregular_popup_locationtype_postcode');
    var city = $('#fenchy_regularuserbundle_userregular_popup_locationtype_city');
    var street = $('#fenchy_regularuserbundle_userregular_popup_locationtype_street');
    
    if (country.val() !== country.attr('watermark')) {
        address += country.val() + ' ';
     }
     
    if (postcode.val() !== postcode.attr('watermark')) {
        address += postcode.val() + ' ';
     }
     
    if (city.val() !== city.attr('watermark')) {
        address += city.val() + ' ';
     }
     
    if (street.val() !== street.attr('watermark')) {
        address += street.val() + ' ';
     }
 
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        map.setZoom(1);
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



function setLocation() {
    clearOverlays();
    var address='';
    var isPlace     = false;
    
    if($('#fenchy_userbundle_user_locationtype_location_location').is('input')) {
        address = $('#fenchy_userbundle_user_locationtype_location_location').val();
        if(address.length) {
            isPlace     = true;
        }
    }

    if (isPlace) {
       geocoder.geocode( { 'address': address}, function(results, status) {
         if (status == google.maps.GeocoderStatus.OK) {
           map.setCenter(results[0].geometry.location);
           map.setZoom(1);
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

function mapTypingUpdate() {
    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(function(){
        clearOverlays();
        codeAddress(true);
    }, 700);
}

$(document).ready(function(){    
    initialize();    
    setLocation();
    
    $('#fenchy_regularuserbundle_userregular_popup_locationtype_country').keypress(mapTypingUpdate); 
    $('#fenchy_regularuserbundle_userregular_popup_locationtype_postcode').keypress(mapTypingUpdate);
    $('#fenchy_regularuserbundle_userregular_popup_locationtype_city').keypress(mapTypingUpdate);
    $('#fenchy_regularuserbundle_userregular_popup_locationtype_street').keypress(mapTypingUpdate);

    $('a#dropPin').click(function(event) {
        putPin();
        event.preventDefault();
    })
});
