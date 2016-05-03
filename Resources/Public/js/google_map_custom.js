 $(document).ready(function(){
       google.maps.event.addDomListener(window, 'load', initialize);
    });
function createMapEvent(){
		centerPopup();
		//load popup
		loadPopup();
                $('#general_pp').slideDown(500);
                $('#detailed_view').slideUp(500);
      
}
function getLocationName(evt_lat,evt_long){
    var location_name
    var geocoder = new google.maps.Geocoder();
    var lat = evt_lat;
    var lng = evt_long;
    var latlng = new google.maps.LatLng(lat, lng);
    geocoder.geocode({'latLng': latlng}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        if (results[1]) {
          var location_name = results[1].formatted_address;
          $('#loc_name_field').val(location_name);
          $('#loc_name').html(location_name);
       
        }
      } else {
        alert("Geocoder failed due to: " + status);
      }
    });
    $('#lat_lat').val(evt_lat);
    $('#lat_log').val(evt_long);
    
    
}
function initialize()
{
////////////////////get all event  markers//////////////////////////////////
var decoded = $('<div/>').html(text).text();
var objEvents = jQuery.parseJSON(decoded);

var infowindow = new google.maps.InfoWindow();
var marker, i;
var objLen = parseInt(objEvents.length,10) - 1;
if(parseInt(objEvents.length,10)>=0 && objEvents !=''){
var intial_latitude = objEvents[objLen].wd_latitude;
var intial_longitude = objEvents[objLen].wd_longitude;
var map = new google.maps.Map(document.getElementById('map_canvas'), {
    zoom: 5,
    center: new google.maps.LatLng(intial_latitude, intial_longitude),
    mapTypeId: google.maps.MapTypeId.ROADMAP
});
    for (i = 0; i < objEvents.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(objEvents[i].wd_latitude, objEvents[i].wd_longitude),
        map: map
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
         var content = '<div class ="contents" style ="color:#000000;">\n\
               <label class="sub_label">'+objEvents[i].wd_subject+'</label><br/>\n\
               <label class="time_label">'+objEvents[i].wd_starttime+'</label><br/><a href="javascript:void(0)" onclick="viewMapEvent('+objEvents[i].uid+');">\n\
               <label class="link_label">ViewMore</label></a></div>';
        return function() {
          infowindow.setContent(content);
          infowindow.open(map, marker);
        }
      })(marker, i));
      
    }
 }else{
   var map = new google.maps.Map(document.getElementById('map_canvas'), {
    zoom: 5,
    center: new google.maps.LatLng(def_lat, def_log),
    mapTypeId: google.maps.MapTypeId.ROADMAP
});
 myMarker = new google.maps.Marker({
        position: new google.maps.LatLng(def_lat, def_log),
        map: map,
       draggable: true,
      });
 var infowindow = new google.maps.InfoWindow({
    content: '<div class ="contents"><a href="javascript:void(0)" onclick="createMapEvent();">Create An Event Here!</a></di>',
  });
  getLocationName(def_lat,def_log);
   //change the arguments on the deaging and droping
      google.maps.event.addListener(myMarker, 'dragend', function(evt){
        var infowindow = new google.maps.InfoWindow({
         content: '<div class ="contents"><a href="javascript:void(0)" onclick="createMapEvent();">Create An Event Here!</a></di>',
         maxWidth:100,
         disableAutoPan:true,
      });
      getLocationName(evt.latLng.lat().toFixed(3),evt.latLng.lng().toFixed(3));
      infowindow.open(map,myMarker);


    });
  
   infowindow.open(map,myMarker);
 }
    
    /////////////////////////////////////////////////////moving marker////////////////////////////////////////////////////////////////////////////////////////
if(!marker_view){
    google.maps.event.addListener(map, 'click', function(event) {
      placeMarker(event.latLng.lat().toFixed(3),event.latLng.lng().toFixed(3),event.latLng);
      });

    function placeMarker(evt_lat,evt_long,location) {
        var myMarker = new google.maps.Marker({
        position: location,
        map: map,
        draggable: true,

    });

      //info window for the intail marking
      var infowindow = new google.maps.InfoWindow({
        content: '<div class ="contents"><a href="javascript:void(0)" onclick="createMapEvent();">Create An Event Here!</a></di>',
      });
      infowindow.open(map,myMarker);
       //this function will get you the name of the location
      getLocationName(evt_lat,evt_long);

      //change the arguments on the deaging and droping
      google.maps.event.addListener(myMarker, 'dragend', function(evt){
        //document.getElementById('current').innerHTML = '<p>Marker dropped: Current Lat: ' + evt.latLng.lat().toFixed(3) + ' Current Lng: ' + evt.latLng.lng().toFixed(3) + '</p>';
        var infowindow = new google.maps.InfoWindow({
         content: '<div class ="contents"><a href="javascript:void(0)" onclick="createMapEvent();">Create An Event Here!</a></di>',
      });
      getLocationName(evt.latLng.lat().toFixed(3),evt.latLng.lng().toFixed(3));
      infowindow.open(map,myMarker);


    });
      google.maps.event.addListener(myMarker, 'click', function(evt){
      var infowindow = new google.maps.InfoWindow({
        content: '<div class ="contents"><a href="javascript:void(0)" onclick="createMapEvent();">Create An Event Here!</a></di>',
      });
      getLocationName(evt.latLng.lat().toFixed(3),evt.latLng.lng().toFixed(3));
      infowindow.open(map,myMarker);
    });

      google.maps.event.addListener(myMarker, 'dblclick', function(evt){

       myMarker.setMap(null);
    });

    }
}
}