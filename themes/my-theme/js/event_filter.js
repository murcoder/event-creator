jQuery(document).ready(function(event) {


  $body = jQuery("body");

  jQuery(document).on({
      ajaxStart: function() { $body.addClass("loading");    },
      ajaxStop: function() { $body.removeClass("loading"); }
  });



  //AJAX request
 jQuery('#events_filter_form').submit(ajaxSubmit);

 function ajaxSubmit() {
    var filterForm = jQuery(this).serialize();
    var vthema = jQuery("#thema").val();
    var vland = jQuery("#land").val();
    var eventCounter = 0;

    //Message box
    var close = document.getElementsByClassName("closebtn");
    for (var i = 0; i < close.length; i++) {
        close[i].onclick = function(){
          jQuery.when(jQuery('.events_msg').fadeOut(600)).done(function() {
              jQuery('.event_filter_text').remove();
          });
        }
    }


    jQuery.ajax({
      action:  'filter_events',
      type:    'POST',
      url:     'event_filter_ajax'.ajax_url,
      data:    'event_filter_ajax'.filterForm,


      success: function(data) {

        //Delete previous message
        jQuery.when(jQuery('.events_msg').hide()).done(function() {
            jQuery('.event_filter_text').remove();
        });


        jQuery( ".event" ).each(function( index ) {
          var tag = jQuery(this).find('.tag').text();
          var country = jQuery(this).find('.country').text();

          //Event has to match THEMA and LAND
          if(vthema != "Alle Themen" && vland != "Alle"){
              if(vthema == tag && vland == country){
                jQuery(this).fadeIn();
                eventCounter++;
              }else
                jQuery(this).hide();


          //Show ALL
          }else if(vthema == "Alle Themen" && vland == "Alle"){
            eventCounter++;
            jQuery(this).fadeIn();


          //Event has to match THEMA
          }else if(vthema != "Alle Themen" && vland == "Alle"){
            if(vthema == tag){
              jQuery(this).fadeIn();
              eventCounter++;
            }else
              jQuery(this).hide();


          //Event has to match LAND
          }else if(vthema == "Alle Themen" && vland != "Alle"){
            if(vland == country){
              jQuery(this).fadeIn();
              eventCounter++;
            }else
              jQuery(this).hide();


          }

        });


        //Add success message
        var msg = "";
        var wurdeN = "wurden";
        var TerminE = "Veranstaltungen";
        var land = jQuery("#land option:selected").text();

        if(eventCounter != 0){
          if(eventCounter == 1){
            wurdeN = "wurde";
            var TerminE = "Veranstaltung";
          }

          if(vthema != "Alle Themen"){
            msg += "Es "+wurdeN+" " + eventCounter + " " + TerminE+" zum Thema <i>" + vthema + "</i>";
            //msg += "Für das Thema <i>" + vthema + "</i> "+wurdeN+" " + eventCounter + " " + TerminE;
            if(vland != "Alle"){
              if(land == "Schweiz")
                msg += " in der <i>" + land +"</i>";
              else
                msg += " in <i>" + land +"</i>";
            }

          }else if(vland != "Alle"){
            if(land == "Schweiz")
              msg += "Es "+wurdeN+" "+eventCounter+" "+TerminE+" in der <i>" + land+"</i>";
            else
              msg += "Es "+wurdeN+" "+eventCounter+" "+TerminE+" in <i>" + land+"</i>";
          }

          if(msg != ""){
            jQuery('.events_msg.filter_success').append(jQuery('<span class="event_filter_text">' +
              msg + ' gefunden</span>'));
            jQuery('.events_msg.filter_success').fadeIn(600);
          }

        }else{
          //No search results

          jQuery( ".vc_toggle" ).hide();

          if(vthema != "Alle Themen"){
            msg += "zum Thema <i>" + vthema + "</i>";
          }
          if(vland != "Alle"){
            if(land == "Schweiz")
              msg += " in der <i>" + land+"</i>";
            else
              msg += " in <i>" + land+"</i>";
          }

          jQuery('.events_msg.filter_warning').append(jQuery('<span class="event_filter_text">' +
            'Leider konnten keine Veranstaltungen ' + msg + ' gefunden werden</span>'));
          jQuery('.events_msg.filter_warning').fadeIn(600);

        }

      },

      error: function(errorThrown){console.log(errorThrown);}

    }).done(function()  {
        hideEmptyMonths();
    }).fail(function()  {
        console.log("Ajax error: Server unavailable. ");
  });

    return false;
  }






function hideEmptyMonths(){
  jQuery( ".vc_toggle" ).each(function( index ) {
    var oneVisible = false;

    jQuery(this).children('.vc_toggle_content').children('.event').each(function( index ) {
      if( jQuery(this).css("display") != "none" ){
        oneVisible = true
        return false;
      }
    });

    if(!oneVisible){
      jQuery(this).hide();
    }else{
      jQuery(this).fadeIn(600);
    }
  });
}



});
