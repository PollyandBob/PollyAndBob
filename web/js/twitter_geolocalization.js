
var geocoder;
var infowindow;
var map;
var markersArray = [];

function initialize() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(52.522, 13.409);
    var mapOptions = {
      zoom: 15,
      scale:5,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
        
    map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions); 
    infowindow = new google.maps.InfoWindow();

    var input = document.getElementById('fenchy_regularuserbundle_userregular_locationtype_near_place');
    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo('bounds', map);
    autocomplete.setTypes(['establishment']);  
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
    document.getElementById('fenchy_userbundle_twitter_regular_user_latitude').value = point.lat();
    document.getElementById('fenchy_userbundle_twitter_regular_user_longitude').value = point.lng();
}

function createMarker(place, info, startPosition) {
    var placeLoc = place.geometry.location;
    
    var marker = new google.maps.Marker({
      position: place.geometry.location,
      map: map,
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
      map: map 
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
    document.getElementById('fenchy_userbundle_twitter_regular_user_latitude').value = point.lat();
    document.getElementById('fenchy_userbundle_twitter_regular_user_longitude').value = point.lng();

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
    
    var country = $('#fenchy_userbundle_twitter_regular_user_country');
    var postcode = $('#fenchy_userbundle_twitter_regular_user_postcode');
    var city = $('#fenchy_userbundle_twitter_regular_user_city');
    var street = $('#fenchy_userbundle_twitter_regular_user_street');
    
    if (country.val() !== country.attr('watermark')) {
        address+= country.val();
     }
     
    if (postcode.val() !== postcode.attr('watermark')) {
        address+= postcode.val();
     }
     
    if (city.val() !== city.attr('watermark')) {
        address+= city.val();
     }
     
    if (street.val() !== street.attr('watermark')) {
        address+= street.val();
     }
 
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        if (info) {
            createMarker(results[0], true, true);
        } else {
            createMarker(results[0], false, true);
        }
        
        //search near places
        var nearPlace = document.getElementById('fenchy_userbundle_twitter_regular_user_near_place').value;
        
        var request = {
          location: results[0].geometry.location,
          radius: 500,
          name: nearPlace ? nearPlace:'',
          types: ['store']
        };

        var service = new google.maps.places.PlacesService(map);
        
        if (nearPlace) {
            service.search(request, callback);   
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

    var country = $('#fenchy_userbundle_twitter_regular_user_country');
    var postcode = $('#fenchy_userbundle_twitter_regular_user_postcode');
    var city = $('#fenchy_userbundle_twitter_regular_user_city');
    var street = $('#fenchy_userbundle_twitter_regular_user_street');
    var nearPlace = $('#fenchy_regularuserbundle_userregular_locationtype_near_place');
    var isPlace = false;
    
    if (country.val() !== country.attr('watermark')) {
        address+= country.val();
     }
     
    if (postcode.val() !== postcode.attr('watermark')) {
        address+= postcode.val();
     }
     
    if (city.val() !== city.attr('watermark')) {
        address+= city.val();
     }
     
    if (street.val() !== street.attr('watermark')) {
        address+= street.val();
     }
     
    if (nearPlace.val() !== nearPlace.attr('watermark')) {
        address+= nearPlace.val();
        isPlace = true;
     }       
          
    
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        createMarker(results[0], true, true);        
        
        var request = {
          location: results[0].geometry.location,
          radius: 500,
          name: nearPlace ? nearPlace:'',
          types: ['store']
        };

        var service = new google.maps.places.PlacesService(map);
        
        if (nearPlace) {
            service.search(request, callback);   
        }
        
      } 
    });
    
}

$(document).ready(function(){
    initialize();    
    setLocation();
    
    $('#fenchy_userbundle_twitter_regular_user_country').change(function() {
        clearOverlays();
        codeAddress(true);
    }); 
    
    $('#fenchy_userbundle_twitter_regular_user_postcode').change(function() {
        clearOverlays();
        codeAddress(true);
    }); 

    $('#fenchy_userbundle_twitter_regular_user_city').change(function() {
        clearOverlays();
        codeAddress(true);
    });  

    $('#fenchy_userbundle_twitter_regular_user_street').change(function() {
        clearOverlays();
        codeAddress(true);
    });     

    $('#fenchy_userbundle_twitter_regular_user_near_place').change(function() {
        clearOverlays(true);
        codeAddress();
    });  

});
