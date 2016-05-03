/***************************/
//@website: www.ZwebbieZ.com
//@license: Feel free to use it, but keep this credits please!					
/***************************/

//SETTING UP OUR POPUP
//0 means disabled; 1 means enabled;
var popupStatus = 0;

//loading popup with jQuery magic!
function loadPopup(){

	//loads popup only if it is disabled
	if(popupStatus==0){
		$("#pits_gmap_backgroundPopup").css({
			"opacity": "0.7"
		});
		$("#pits_gmap_backgroundPopup").fadeIn("fast");
		$("#pits_gmap_popup").fadeIn("fast");
		popupStatus = 1;
	}
}

//disabling popup with jQuery magic!
function disablePopup(){
    
        $("#newEvent").dataFormReset();
        $('.evt_error').html('');
	//disables popup only if it is enabled
	if(popupStatus==1){
		$("#pits_gmap_backgroundPopup").fadeOut("slow");
		$("#pits_gmap_popup").fadeOut("slow");
		popupStatus = 0;

	}
}

//centering popup
function centerPopup(){
	//request data for centering
	var windowWidth = $(window).width();
	var windowHeight = $(window).height();
	var popupHeight = $("#pits_gmap_popup").height();
	var popupWidth = $("#pits_gmap_popup").width();
	//centering
	$("#pits_gmap_popup").css({
		"position": "fixed",
		"top": 0,
		"left": windowWidth/2-popupWidth/2
	});
	//only need force for IE6
	
	$("#pits_gmap_backgroundPopup").css({
		"height": windowHeight
	});
	
}

//CONTROLLING EVENTS IN jQuery
$(document).ready(function(){
	//LOADING POPUP		
	//Click the x event!
	$("#pits_gmap_popupClose").click(function(){
		disablePopup();
	});
	//Click out event!
	$("#pits_gmap_backgroundPopup").click(function(){
		disablePopup();
	});
        
	//Press Escape event!
	$(document).keypress(function(e){
		if(e.keyCode==27 && popupStatus==1){
			disablePopup();
		}
	});

});

//to rest all form data
jQuery.fn.dataFormReset = function () {
  $(this).each (function() { this.reset(); });
}
