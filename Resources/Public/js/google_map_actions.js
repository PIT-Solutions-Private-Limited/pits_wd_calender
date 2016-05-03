$(document).ready(function() {    
$('#datepicker_from').datetimepicker();
$('#datepicker_to').datetimepicker();
$('#saveNewEvtForm').click( function() {
    var form_valid  = 0;
    // get all the inputs into an array.
    var $inputs = $('#newEvent :input');
    // not sure if you wanted this, but I thought I'd add it.
    // get an associative array of just the values.
    var values = {};
    $inputs.each(function() {
        if(this.name == 'evt_name' || this.name == 'evt_sdate' || this.name == 'evt_edate' ){
             if($(this).val()==''){
                $('#'+this.name+'').html("Field Cannot Be Empty!"); 
             }
            else{
                $('#'+this.name+'').html(""); 
                form_valid++;
             }
        }
        values[this.name] = $(this).val();     
    });
    if(form_valid==3){
            saveEventForm(values);
    }
});
});
function saveEventForm(formData){
     $(".proces_bar").fadeIn("fast");
              var ACTION_URL = $('#page_id').val();
                  $.ajax({
                      type: "POST",
                      url: ACTION_URL+'create',
                      data: { formValues: formData},
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
        var ACTION_URL = $('#page_id').val();
                  $.ajax({
                      type: "POST",
                       url: ACTION_URL+'show',
                      data: { formValues: uid},
                      success: function (data) {
                           $('#detailed_view').html(data);
                       }
                  })

         $('#general_pp').slideUp(500);
         $('#detailed_view').slideDown(500);        

}