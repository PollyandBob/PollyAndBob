
var geocoder;
var infowindow;
var map;
var markersArray = [];

function initialize() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(-34.397, 150.644);
    var mapOptions = {
      zoom: 15,
      scale:5,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
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

function createMarker(place) {
    var placeLoc = place.geometry.location;
    
    var marker = new google.maps.Marker({
      position: place.geometry.location,
      map: map
    });
    
    markersArray.push(marker);
    
    google.maps.event.addListener(marker, 'click', function() {
    infowindow.setContent(place.name);
    infowindow.open(map,this);
  });
}

function codeAddress() {
    clearOverlays();
    
    var address='';

    if (document.getElementById('fenchy_regularuserbundle_userregular_locationtype_postcode').value) {
        address+= document.getElementById('fenchy_regularuserbundle_userregular_locationtype_postcode').value;
     }

    if (document.getElementById('fenchy_regularuserbundle_userregular_locationtype_city').value) {
        address+= document.getElementById('fenchy_regularuserbundle_userregular_locationtype_city').value;
     }

    if (document.getElementById('fenchy_regularuserbundle_userregular_locationtype_street').value) {
        address+= ',' + document.getElementById('fenchy_regularuserbundle_userregular_locationtype_street').value;
     }        

    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        createMarker(results[0]);
        
        //search near places
        var nearPlace = document.getElementById('fenchy_regularuserbundle_userregular_locationtype_near_place').value;
        
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
        
      } else {
        alert('Geocode was not successful for the following reason: ' + status);
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
function clearOverlays() {
  if (markersArray) {
    for (i in markersArray) {
      markersArray[i].setMap(null);
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

    if (document.getElementById('fenchy_regularuserbundle_userregular_locationtype_postcode').value) {
        address+= document.getElementById('fenchy_regularuserbundle_userregular_locationtype_postcode').value;
     }

    if (document.getElementById('fenchy_regularuserbundle_userregular_locationtype_city').value) {
        address+= document.getElementById('fenchy_regularuserbundle_userregular_locationtype_city').value;
     }

    if (document.getElementById('fenchy_regularuserbundle_userregular_locationtype_street').value) {
        address+= ',' + document.getElementById('fenchy_regularuserbundle_userregular_locationtype_street').value;
     }        

    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        createMarker(results[0]);
        
        //search near places
        var nearPlace = document.getElementById('fenchy_regularuserbundle_userregular_locationtype_near_place').value;
        
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

    $('#fenchy_regularuserbundle_userregular_locationtype_postcode').change(function() {
        codeAddress();
    }); 

    $('#fenchy_regularuserbundle_userregular_locationtype_city').change(function() {
        codeAddress();
    });  

    $('#fenchy_regularuserbundle_userregular_locationtype_street').change(function() {
        codeAddress();
    });     

    $('#fenchy_regularuserbundle_userregular_locationtype_near_place').change(function() {
        codeAddress();
    });  

});
