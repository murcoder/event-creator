<?php

  /*** Contains all date functions for events ***/




/**
 * Parses date from string
 * Parse String to format YYYY-MM-DD
 * @param string,int $str Uncorrected date string, and date pattern
 * @return DateTime PHP datetime object
 */
function date_grab($str, $type, $debug = false){
  //1-parse: '12.04.2017'
  $datePattern = '{.*?(\d\d?)[\\/\.\-]([\d]{2})[\\/\.\-]([\d]{4}).*}';
  //2-parse: '(Mittwoch) 12. Oktober 2016 (18 Uhr)'
  $datePattern2 = "/(Montag?|Dienstag?|Mittwoch?|Donnerstag?|Freitag?|Samstag?|Sonntag?){0,1}\s*\-*(\d{1,2})\.*\-*\s*(Januar?|Jan(när)?|Feb(ruar)?|Mär(z)?|Apr(il)?|Mai|Jun(i)?|Jul(i)?|Aug(ust)?|Sep(tember)?|Okt(ober)?|Nov(ember)?|Dez(ember)?)\-*\s*([\d]{4}).*/";
  //3-parse: '12. Januar 2017'
  $getMonthName = "/(Januar?|Jan(när)?|Feb(ruar)?|Mär(z)?|Apr(il)?|Mai|Jun(i)?|Jul(i)?|Aug(ust)?|Sep(tember)?|Okt(ober)?|Nov(ember)?|Dez(ember)?)/";
  $date = "";


  //1 ----- match 'dd-mm-yyy' or 'd.mm.yyyy'
  if($type == 1 ){
    $finalFormat = str_replace('-', '', $str);
    $date = preg_replace($datePattern, '$3-$2-$1', $finalFormat);


  //2 ----- match 'Mittwoch 12. Oktober 2016'
  }else if($type == 2) {
      $dateTmp = preg_replace($datePattern2, '$2-$3-$15', $str);


      //Parse month to number
      preg_match($getMonthName, $dateTmp, $output_array);


      if( (strlen($output_array[0]) == 3) && (strcmp($output_array[0],"Mai") != 0) && (strcmp($output_array[0],"May") != 0) ){
        //case: Jan, Jun, Sep, Nov, ...
        $monthName = getFullMonthname($output_array[0]);
      }else{
        $monthName = monthEng($output_array[0]);
      }
      $tmp = date_parse($monthName);
      $monthNr = $tmp["month"];
      $date = preg_replace($getMonthName, $monthNr, $dateTmp);


  //3 ----- match '12. Okt. 2020'
  }else if($type == 3){
    //Parse month to number
    preg_match($getMonthName, $str, $output_array);
    //echo $debug ? "<script>console.log( 'output_array (grap): " . $output_array[0] . "' );</script>" : "";

    if( (strlen($output_array[0]) == 3) && (strcmp($output_array[0],"Mai") != 0) && (strcmp($output_array[0],"May") != 0) ){
      //case: Jan, Jun, Sep, Nov, ...
      $monthName = getFullMonthname($output_array[0]);
    }else{
      $monthName = monthEng($output_array[0]);
    }
    $tmp = date_parse($monthName);
    $monthNr = $tmp["month"];
    $str = str_replace('.','',$str);
    $date = preg_replace($getMonthName, $monthNr, $str);
  }


  //echo $debug ? "<script>console.log( 'Date ".$flag." (grap): " . $date . "' );</script>" : "";

  //Exception Handling
  if(!$date)
    throw new Exception("Date is empty");
  if(!checkIsAValidDate($date))
    throw new Exception("Invalid date format");

  //echo "Replaced-date: " . $date . " || ";
  return new \DateTime($date);
}




function checkIsAValidDate($myDateString){
  return (bool)strtotime($myDateString);
}




function date_compare($a, $b){
    $t1 = $a->date->format('d.m.Y');
    $t2 = $b->date->format('d.m.Y');
    return strtotime($t1) - strtotime($t2);
}


/**
*   Returns true if the given event is in past
*   @param event
*   @return true if the given event_date is in past
*/
function inPast($event){
  $today = date('d.m.Y');
  $eventDate = $event->date->format('d.m.Y');

  if((strtotime($today) - strtotime($eventDate)) > 0)
    return true;
  else
    return false;
}


/**
*   Retruns true if the given date is in the past
*   @param String
*   @return bool true if date is in past, false otherwise
*/
function isDateInPast($date){
  $today = date('Y-m-d', time());
  //echo "<script>console.log( 'current date: " . $today . "; given date: ".$date."' );</script>";

  if((strtotime($today) - strtotime($date)) > 0)
    return true;
  else
    return false;
}



/*
* Translate month from eng to ger
*/
function monthGer($month){

  switch ($month) {
      case "January":
          return "Jänner";
      case "February":
          return "Februar";
      case "March":
          return "März";
      case "April":
          return "April";
      case "May":
          return "Mai";
      case "June":
          return "Juni";
      case "July":
          return "Juli";
      case "August":
          return "August";
      case "September":
          return "September";
      case "October":
          return "Oktober";
      case "November":
          return "November";
      case "December":
          return "Dezember";
      case "":
          return "";
  }


}


/*
* Translate month from ger to eng
*/
function monthEng($month){

  switch ($month) {
      case "Jänner":
          return "January";
      case "Januar":
          return "January";
      case "Februar":
          return "February";
      case "März":
          return "March";
      case "April":
          return "April";
      case "Mai":
          return "May";
      case "Juni":
          return "June";
      case "Juli":
          return "July";
      case "August":
          return "August";
      case "September":
          return "September";
      case "Oktober":
          return "October";
      case "November":
          return "November";
      case "Dezember":
          return "December";
      case "":
          return "";
  }


}



function getFullMonthname($month){

  switch ($month) {
      case "Jän":
          return "January";
      case "Jan":
          return "January";
      case "Feb":
          return "February";
      case "Mär":
          return "March";
      case "Mar":
          return "March";
      case "Apr":
          return "April";
      case "Jun":
          return "June";
      case "Jul":
          return "July";
      case "Aug":
          return "August";
      case "Sep":
          return "September";
      case "Okt":
          return "October";
      case "Oct":
          return "October";
      case "Nov":
          return "November";
      case "Dez":
          return "December";
      case "Dec":
          return "December";
      case "":
          return "";
  }


}



 ?>
