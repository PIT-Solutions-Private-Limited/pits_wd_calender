$(document).ready(function() {
    $('#datepicker_from, #datepicker_to').datetimepicker({
        locale: 'ru'
    });

$('#newEvent').submit( function(event) {
    event.preventDefault();
});
$('#saveNewEvtForm').click( function() {

    var form_valid  = 0;
    // get all the inputs into an array.
    var formData = $('form').serialize();
    var eventName = $('#Field4').val().trim();
    var startDate = $('#datepicker_from').val().trim();
    var endDate = $('#datepicker_to').val().trim();
    var fnameFlag = true;
    if (eventName == "") {
        fnameFlag = false;
        $('#evt_name').html("Field Cannot Be Empty!"); 
    }
    if (startDate == "") {
        fnameFlag = false;
        $('#evt_sdate').html("Field Cannot Be Empty!"); 
    }
    if (endDate == "") {
        fnameFlag = false;
        $('#evt_edate').html("Field Cannot Be Empty!"); 
    }
		
    if(fnameFlag==true){
          saveEventForm(formData);
    } 
});
});
function saveEventForm(formData){
     $(".proces_bar").fadeIn("fast");
        var ACTION_URL = $('#page_id').val();
        $.ajax({
            type: "POST",
            url: ACTION_URL,
            data:formData,
            success: function (data) {
                $('#evt_sucess').html(data)
                $(".proces_bar").fadeOut("fast");
                window.location.reload();
            }
        })
}
function viewMapEvent(uid){
        centerPopup();
        loadPopup();
        var ACTION_URL = $('#view_url').val();
                  $.ajax({
                      type: "POST",
                       url: ACTION_URL,
                      data: {'eventId' : uid},
                      action:'show',
                      success: function (data) {
                           $('#detailed_view').html(data);
                       }
                  })

         $('#general_pp').slideUp(500);
         $('#detailed_view').slideDown(500);        

}



