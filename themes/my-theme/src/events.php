<?php


require_once( __DIR__ . '/class-event.php');
require_once( __DIR__ . '/class-tags.php');
require_once( __DIR__ . '/events_custom.php');
require_once( __DIR__ . '/events_date.php');

function custom_js_events() {
  echo '<script src="' . get_stylesheet_directory_uri() . '/js/event_filter.js" type="text/javascript"></script>';
}
// Add hook for admin <head></head>
add_action('admin_head', 'custom_js_events');
// Add hook for front-end <head></head>
add_action('wp_head', 'custom_js_events');



/**
*   Get all events, sorted by date
*   @return array of sorted dynamic and static events
*/
function getEvents($debug = false){

  if(!$debug){
    //Add dynamic events
    $dynEvents = createEvents( getUrls($debug), $debug );
    //Add static events
    $allEvents = getStaticEvents($dynEvents);
    //Chronological sort events
    usort($allEvents, 'date_compare');
    return $allEvents;

  }else{
    //DEBUG
    $debugEvents = createEvents( getUrls($debug), $debug );
    //$allEvents = getStaticEvents($dynEvents);
    usort($debugEvents, 'date_compare');
    return $debugEvents;

  }
}



/**
*   Get data from websites
*   @return array of events
*/
function createEvents($urls, $debug = false){
  $url_counter = 0;
  $url = "";
  $events = array();

  /*** Iterate each website ***/
  foreach($urls as $url){
      $url_counter++;

      /*** Crawl the Website Using Plugin 'WP Web Scraper'***/
      //Custom Crawl
      $html = getCustomDOM($url);
      //Crawl body element
      if(empty($html))
        $html = wpws_get_content($url, 'body' );


      $dom = new domDocument;
      $rowsArr = array();

      /*** load the html into the dom document ***/
      libxml_use_internal_errors(true);
      $dom->loadHTML($html);
      libxml_use_internal_errors(false);

      /*** discard white space ***/
      $dom->preserveWhiteSpace = false;

      /*** build the DOM Structure ***/
      //TODO Check element type (table,list,div,..)
      $tableCount = $dom->getElementsByTagName('table')->length;
      $ulCount = $dom->getElementsByTagName('ul')->length;
      $CustomDOMtype = setCustomDomType($url);
      $DOMtype = "";

      // testData($url,$dom,$debug=false);


        /*** Create Event for table or list datatype ***/
        if($tableCount != 0 ){
          //table format
            for($i=0; $i<$tableCount; $i++){
              $rows = $dom->getElementsByTagName('table')->item($i)->getElementsByTagName('tr');
              array_push($rowsArr,$rows);
              $DOMtype = "table";
          }
        }else if($ulCount != 0){
          //list format
          for($i=0; $i<$ulCount; $i++){
            $rows = $dom->getElementsByTagName('ul')->item($i)->getElementsByTagName('li');
            array_push($rowsArr,$rows);
            $DOMtype = "list";
          }
        }

        if(!empty($CustomDOMtype))
          $DOMtype = $CustomDOMtype;



      if($debug)
        echo "<h2>ORIGINAL DATEN</h2> (Erkannte Daten von Website ohne Filterung) <br />";

      /*** All events of an URL ***/
      foreach($rowsArr as $rows){
        echo $debug ? "Gefunden: " . $DOMtype : "";


        if(strcmp($DOMtype, "custom") === 0){
          $event = createCustomEvent($url,$dom,$debug);

          //Last Filter
          if( $event->hasDate() && $event->content != "" && (strlen($event->content) >= 15))
            if(!inPast($event))
              $events[]  = $event;


          echo $debug ? "<script>console.log( '----------------' );</script>" : "";

        }else{



        /*** Single event ***/
        foreach ($rows as $row) {

            $dataArr = array();
            $cols;

                  //Check if the html-body contains a table or a list
                  if(strcmp($DOMtype, "table") === 0)
                    $cols = $row->getElementsByTagName('td');

                  if(strcmp($DOMtype, "list") === 0)
                    $cols = $row->getElementsByTagName('*');




                  if(empty($cols)){
                    //Exclude this url if no data was found
                    echo "<script>console.log( 'No Data found for ".$url."!' );</script>";
                    break;
                  }


                  if($debug){
                    /*** DEBUG echo the values ***/
                    echo '<pre>';
                    echo '0: '.$cols->item(0)->nodeValue.'<br />';
                    echo '1: '.$cols->item(1)->nodeValue.'<br />';
                    echo '2: '.$cols->item(2)->nodeValue.'<br />';
                    echo '3: '.$cols->item(3)->nodeValue.'<br />';
                    echo '4: '.$cols->item(4)->nodeValue.'<br />';
                    echo '5: '.$cols->item(5)->nodeValue;
                    echo '<hr />';
                    echo '</pre>';
                  }



                  /*** Fill data-array and filter empty or duplicate-data ***/

                  for($i=0; $i<6; $i++){
                    //Get one data-line here
                    $strtmp = $cols->item($i)->nodeValue;
                    $strtmp = trim($strtmp);

                    if($strtmp !== ""){
                      //There is some data

                      if(!empty($dataArr)){
                        //duplicate-data check
                        $duplicate = false;
                        foreach($dataArr as $data){
                          if (strcmp($data, $strtmp) === 0){
                            $duplicate = true;
                            break;
                          }
                        }

                        if(!$duplicate)
                          $dataArr[] = $strtmp;

                      }else{
                        //Its the first element
                        $dataArr[] = $strtmp;
                      }
                      echo $debug ? "<script>console.log( 'Data " . $i . ": " . $strtmp . "' );</script>" : "";
                    }
                  }

            $event = new event();


            /*** Create the event ***/
            try{
              $event = setEvent($dataArr, $debug);
            }catch (Exception $e) {
                echo "<script>console.log( 'Event ".$counter." for ".$url." could not be created: " . $e->getMessage() . "' );</script>";
                $counter++;
            }


            //Add domain
            $domain = parse_url($url, PHP_URL_HOST);
            $domain = str_replace('www.', '', $domain);
            $event->url = '<a style="color:rgb(161, 161, 161);text-decoration:none;" target="_blank" href="'.$url.'">'.$domain.'</a>';

            //ADD INDIVIDUAL CONTENT FOR EVENT HERE
            $event = getCustomData($url,$event);

            if($debug){
              echo "<script>console.log( 'EVENT -> date: " . $event->getStringDate() . "' );</script>";
              echo "<script>console.log( 'EVENT -> content: " . $event->content . "' );</script>";
              echo $event->tag ? "<script>console.log( 'EVENT -> tag: " . $event->tag . "' );</script>" : "";
              echo $event->time ? "<script>console.log( 'EVENT -> time: " . $event->time . "' );</script>" : "";
              echo $event->url ? "<script>console.log( 'EVENT -> url: " . $event->url . "' );</script>" : "";
              echo $event->location ? "<script>console.log( 'EVENT -> location: " . $event->location . "' );</script>" : "";
              echo $event->speaker ? "<script>console.log( 'EVENT -> speaker: " . $event->speaker . "' );</script>" : "";
            }

            //Last Filter
            if( $event->hasDate() && $event->content != "" && (strlen($event->content) >= 15))
              if(!inPast($event))
                $events[]  = $event;

            echo $debug ? "<script>console.log( '----------------' );</script>" : "";


          }// END OF EVENT
        }
      }// END OF ROWS
  }// END OF URL

  $events = array_unique($events);
  return $events;
}





/**
 * Creates the event with data from the current url
 * @param array full of data from one dom element
 * @return event-object filled with the data from dom
 */
function setEvent($dataArr, $debug = false){

  if(empty($dataArr))
    throw new Exception("No Data found");

  $event = new event();
  $date = "";
  $flag = 0;

  //Date-match: '13.04.2016', '05  .12. 2017'
  $datePattern1 = '{.*?(\d\d?)(\s*)?[\\/\.\-](\s*)?([\d]{2})(\s*)?[\\/\.\-](\s*)?([\d]{4})}';
  //Date-match: 'Montag 12. Jänner 2012'
  $datePattern2 = "/(Montag?|Dienstag?|Mittwoch?|Donnerstag?|Freitag?|Samstag?|Sonntag?)\s+\d{1,2}\.?\s+(Januar?|Jan(när)?|Feb(ruar)?|Mär(z)?|Apr(il)?|Mai|Jun(i)?|Jul(i)?|Aug(ust)?|Sep(tember)?|Okt(ober)?|Nov(ember)?|Dez(ember)?)\s+\d{4}/";
  //Date-match: '13. August', '09.April'
  $datePattern3 = "/\d{1,2}.\s*(Januar?|Jan(när)?|Feb(ruar)?|Mär(z)?|Apr(il)?|Mai|Jun(i)?|Jul(i)?|Aug(ust)?|Sep(tember)?|Okt(ober)?|Nov(ember)?|Dez(ember)?)/";
  //$datePattern3 = "/\d{1,2}.\s*(Januar?|Jan(när)?|Feb(ruar)?|Mär(z)?|Apr(il)?|Mai|Jun(i)?|Jul(i)?|Aug(ust)?|Sep(tember)?|Okt(ober)?|Nov(ember)?|Dez(ember)?)\s*(19[5-9][0-9]|20[0-9][0-9])/";
  //Time-match: '18 Uhr'
  $timePattern1 = "/^([0-9]{2}(\s|\.|\-)?\s*Uhr)/";
  //Time-match: '16:00 - 9:00   Uhr', '16:00 bis 9:00  Uhr', '16:00    Uhr  bis 9:00  Uhr'
  $timePattern2 = "/(([0-2]?[0-9](\:|\/\s)[0-5]?[0-9]?)\s*((U|u)hr)*\s*(\-|\/\s*|bis)\s*([0-2]?[0-9](\:|\/\s\.)[0-5][0-9]))/";
  //Time-match: '16:00', '9:00   Uhr'
  $timePattern3 = "/([0-2]?[0-9](\:|\/\s)[0-5][0-9])/";
  //Get week days ('montag','Dienstag',..);
  $weekDay = "/((M|m)ontag?|(D|d)ienstag?|(M|m)ittwoch?|(D|d)onnerstag?|(F|f)reitag?|(S|s)amstag?|(S|s)onntag?)/";

  foreach($dataArr as $data){
    if(preg_match("/((W|w)eihnacht|(A|a)dvent)(s)(feier)/", $data))
      throw new Exception('Weihnachtsfeier!');

      //echo $debug ? "<script>console.log( 'Data " . key($dataArr) . ": " . $data . "' );</script>" : "";
        /*** Add TIME ***/
        $time = "";
        if($event->time == ""){
          if(preg_match($timePattern2, $data, $output_array)){
            $time = $output_array[0];
            $data = preg_replace($timePattern2, '', $data);
            $data = str_replace('Uhr', '', $data);
            $data = str_replace('uhr', '', $data);
            //echo $debug ? "<script>console.log( 'timePattern2: " . $time . "' );</script>" : "";
          }
          else if(  preg_match($timePattern3, $data, $output_array)){
            $time = $output_array[0];
            $data = preg_replace($timePattern3, '', $data);
            $data = str_replace('Uhr', '', $data);
            $data = str_replace('uhr', '', $data);
            //echo $debug ? "<script>console.log( 'timePattern3: " . $time . "' );</script>" : "";
          }
          else if(preg_match($timePattern1, $data, $output_array)){
            $time = $output_array[0];
            $data = preg_replace($timePattern1, '', $data);
            $data = str_replace('Uhr', '', $data);
            $data = str_replace('uhr', '', $data);
            // echo $debug ? "<script>console.log( 'timePattern1: " . $time . "' );</script>" : "";
          }
          //echo $debug ? "<script>console.log( 'Final Time: " . $time . "' );</script>" : "";
          $event->time = $time;
        }

        /*** Add DATE ***/
        //echo "<script>console.log( '1.Data: " . $data . "' );</script>";
        if( !$event->hasDate() ){



            if(preg_match($datePattern1, $data)){
              $flag = 1;
              $date = clean($data, "date");
            }
            if(preg_match($datePattern2, $data)){
              $flag = 2;
              $date = clean($data, "date");
            }
            //Add year for 'dd. Month'
            if(preg_match($datePattern3, $data, $result)){
              $flag = 3;
              $date = clean($result[0]." " . date("Y") , "date");

            }


            //convert string to date
            //clean the date (Parse String to format YYYY-MM-DD)
            if($flag != 0){
              $event->setDate( date_grab($date, $flag, $debug) );
              $flag = 0;
              //echo $debug ? "<script>console.log( 'Date ".$flag." (final): " .  $event->date->format('d.m.Y') . "' );</script>" : "";

            }



        /*** Add LOCATION ***/
      }else if(!$event->hasLocation() && isLocation($data)){
        $event->setLocation( clean($data, "text") );

      //Filter content
    }else if( (!preg_match($weekDay, $data)) && (!preg_match("/((K|k)affeetreffen)/", $data))  && (!preg_match("/((A|a)dvent)/", $data)) && (!preg_match("/((W|w)eihnachts(feier|))/", $data)) && (strpos($event->content, "entfällt") === false) ){

          if( !$event->hasSpeaker() && isPerson($data) ){
            /*** Add SPEAKER ***/
            $event->setSpeaker(clean($data, "text"));

            /*** Add CONTENT ***/
          }else if($event->content == ""){

            if(!isPerson($data) && !preg_match($datePattern1, $data) && !preg_match($datePattern2, $data) && !preg_match($datePattern3, $data))
              $event->content = clean($data, "text");

          }else{
            if((strpos($event->content, $data) !== false)){
            //Add ADDITIONAL CONTENT
            $content2 = "<br />";
            $content2 .= clean($data, "text");
            $event->content .= $content2;
            }
          }

      }//END if-else



      //Add TAG
      if($event->content != "")
        $event->setTag(getTag($event->content,$event->url));

    }
    return $event;
}





/**
*  Add static events from database to an array
*/
function getStaticEvents($events){
  //$events[]  = buildCustomEvent(Tags::Prostata,"20.09.2017","18:00","Rezidiv des Prostatakarzinoms – Diagnose + Behandlung","63785 Obernburg am Main, Bay. Rotes Kreuz Zentrum, Römerstraße 93","","http://www.selbsthilfe-maennergesundheit.de/");
  global $wpdb;
  $results = $wpdb->get_results ( "SELECT * FROM wp_static_events" );
  foreach ( $results as $DBevent )   {
    try{
    $events[]  = buildCustomEvent(
      $DBevent->tag,
      $DBevent->event_date,
      $DBevent->event_time,
      $DBevent->event_content,
      $DBevent->event_location,
      $DBevent->event_speaker,
      $DBevent->event_country,
      $DBevent->url,
      $DBevent->file_url);
    }catch (Exception $e) {
        echo "<script>console.log( 'Static Event could not set (getStaticEvents(array)): " . $e->getMessage() . "' );</script>";
    }
  }
  return $events;

}



/**
*    @return event-object filled with custom data
*/
function buildCustomEvent($tag,$strDate,$time,$content,$location,$speaker,$country,$url,$fileurl){
    $event = new event();

    if($tag)
      $event->setTag( $tag );
    else
      $event->setTag( getTag($content,$url) );

    //Add date
    $dateTime = strtotime($strDate);
    $date = date('d.m.Y',$dateTime);

    //Exception Handling
    if(!$date)
      throw new Exception("DB Error: Date is empty in buildCustomEvent()");
    if(!checkIsAValidDate($date))
      throw new Exception("DB Error: Invalid date format in buildCustomEvent()");

    $event_date = new \DateTime($date);
    $event->setDate($event_date);

    if($time)
        $event->time = $time;
    if($content)
      $event->content = $content;
    if($location)
      $event->setLocation($location);
    if($speaker)
      $event->setSpeaker($speaker);
    if($country)
      $event->setCountry($country);

    if($fileurl){
      $file = '<a style="color:rgb(161, 161, 161);text-decoration:none;" target="_blank" href="'.$fileurl.'">weiterlesen</a>';
      $event->setFileurl($file);
    }

    if($url){
    $domain = parse_url($url, PHP_URL_HOST);
    $domain = str_replace('www.', '', $domain);
    $event->url = '<a style="color:rgb(161, 161, 161);text-decoration:none;" target="_blank" href="'.$url.'">'.$domain.'</a>';
    }

    return $event;
}




/**
*   Get a specific amount of the dynamic events
*   @return string representation of a specific number of events
*   @usage [get_events anzahl='10']
*/
function getEventsAsString($atts){

  //shortcode input
  $a = shortcode_atts( array(
      'anzahl' => '3'
  ), $atts );

  $size = 0;
  $result = "";
  $events = getEvents();

  foreach($events as $event){

    if( !inPast($event) ){
      $size++;
      $event->url = '<a style="color:rgb(161, 161, 161);text-decoration:none;" target="_blank" href="'.get_site_url().'/termine">mehr</a>';
      $result .= $event->__toString();
    }

    if($size >= $a['anzahl'])
      break;
  }

  return $result;
}
add_shortcode( 'get_events', 'getEventsAsString' );




/**
*   shape a string (date or text)
*/
function clean($string, $case) {

  switch($case){
    case "date":
       $cleaned = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
       $cleaned = trim($cleaned);
       $cleaned = str_replace('Â','',$cleaned);
       $cleaned = utf8_decode($cleaned);
       return preg_replace('/[^A-Za-z0-9\-\.]/', '', $cleaned); // Removes special chars. exept '-' and '.'
     case "text":
        $cleaned = trim($string);
        $cleaned = str_replace('Â','',$cleaned);
        $cleaned = utf8_decode($cleaned);
        $cleaned = preg_replace('/[^A-Za-z0-9\-\–\.\s\?\!\,äöüÄÖÜß\“\„\[\]]/', '', $cleaned);
        $cleaned= preg_replace('/(\p{Ll})(\p{Lu})/u', '$1 $2', $cleaned); //Add whitespace between captalized words

        /* invdividual filter */
        $cleaned = str_replace('[anmelden]', '', $cleaned);
        return $cleaned; //Remove special characters and digit except german letters
    case "":
     return $string;
  }


}







/**
* Print messages for database-errors
*
* @since    1.0.0
*/
function db_print_error(){

		    global $wpdb;

		    if($wpdb->last_error !== ''){
		        $str   = htmlspecialchars( $wpdb->last_result, ENT_QUOTES );
						$query = "";

						if($this->wp_event_creator_options['debug'])
		        	$query = htmlspecialchars( $wpdb->last_query, ENT_QUOTES );

          $result = "[$str]<br /><code>$query</code></p></div>";
          echo "<script>console.log( '<strong>WordPress database error:</strong>" . $result . "' );</script>";
		    }
}


/*
*
*/
function addEventsToDB($events){
  global $wpdb;
  global $charset_collate;
  global $db_version;
  $charset_collate = $wpdb->get_charset_collate();
  $table_name = $wpdb->prefix . "speedy_dyn_events";
  $counter = 0;

  if($wpdb->get_var("SHOW TABLES LIKE '" . $table_name . "'") !=  $table_name){
    //Create Table if it doesn't exist
    $sql = "CREATE TABLE " . $table_name . "  (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      tag varchar(255) NOT NULL,
      event_date date DEFAULT '0000-00-00' NOT NULL,
      event_time varchar(255) NOT NULL,
      event_content text NOT NULL,
      event_speaker varchar(255) NOT NULL,
      event_location text NOT NULL,
      url text NOT NULL,
      added datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      PRIMARY KEY  (id)
    ) $charset_collate;";
    echo "<script>console.log( 'TABLE CREATED' );</script>";
  }
  require_once(ABSPATH . "wp-admin/includes/upgrade.php");
  dbDelta( $sql );

  //Add all events to database
  foreach($events as $event){
    $wpdb->insert( $table_name, array(
      'added' => current_time( 'mysql' ),
      'tag' => $event->tag,
      'event_date' => $event->date,
      'event_time' => $event->time,
      'event_content' => $event->content,
      'event_speaker' => $event->speaker,
      'event_location' => $event->location,
      'url' => $event->url
  ));
  $counter++;
  }

  if($wpdb->last_error !== ''){
    $this->db_print_error();
  }else{
    echo "<script>console.log( '" . $counter . " dynamic Events added to database!' );</script>";
  }
}









/**
*   Filter events from the past
*/
function chronologicalFilter($event){
  $current_year = date('Y');
  $current_month  = date('n');


  if((($event->date->format('n') >= $current_month) && ($event->date->format('Y') >= $current_year)))
    return true;
  else
    return false;

}







/**
* Looking if the text contains a tag
*/
function getTag($text,$url){

  $tag = Tags::Allgemein;
  //DYNAMIC TAG GENERATOR
  //Get Tags from DB
  // global $wpdb;
  // $table_name = $wpdb->prefix . 'static_events_tags';
  // $tags = $wpdb->get_results ( "SELECT * FROM $table_name" );
  // foreach ( $tags as $singleTag )   {
  //   $singleWords = preg_split('/\s+/', $singleTag->tag);
  //   foreach ( $singleWords as $word )   {
  //       if ((strcmp("und", $word) !== 0) && (strcmp("oder", $word) !== 0) && (strcmp("bei", $word) !== 0)) {
  //         if(preg_match("/(".$word.")/", $text) || preg_match("/(".$word.")/", $url))
  //           $tag = $singleTag->tag;
  //     }
  //   }
  // }

  if(empty($tags)){
    //If there is no database table or the plugin deactivated (faster)
    if(preg_match("/((K|k)arzinom|(k|K)rebs)/", $text) || preg_match("/((K|k)arzinom|(k|K)rebs)/", $url))
      $tag = Tags::Krebs;
    if(preg_match("/((P|p)rostata)/", $text) || preg_match("/((P|p)rostata)/", $url))
      $tag = Tags::Prostata;
    if(preg_match("/((M|m)yelom|Lymph)/", $text) || preg_match("/((M|m)yelom|Lymph)/", $url))
      $tag = Tags::Myelom;
    if((preg_match("/((K|k)rebs)/", $text) && preg_match("/((f|F)rau)/", $text)) || (preg_match("/((K|k)rebs)/", $url) && preg_match("/((f|F)rau)/", $url)))
      $tag = Tags::FrauenKrebs;
    if(preg_match("/((P|p)sych)/", $text) || preg_match("/((P|p)sych)/", $url))
      $tag = Tags::Psyche;
    if(preg_match("/((D|d)arm)/", $text) || preg_match("/((M|m)agen)/", $text) || preg_match("/((M|m)agen)/", $url) || preg_match("/((D|d)arm)/", $url))
      $tag = Tags::Darm;
    if(((preg_match("/((B|b)rust)/", $text) && preg_match("/((K|k)rebs)/", $text)) || preg_match("/((T|t)ast)/", $text) )|| ((preg_match("/((B|b)rust)/", $url) && preg_match("/((K|k)rebs)/", $url)) || preg_match("/((T|t)ast)/", $url)) )
      $tag = Tags::Brust;
  }

  return $tag;
}


/**
*   Requires the plugin Event_Creator
*/
function getTags($events){
  include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
  $tags = array();
  $tmp = 0;

  if(is_plugin_active("event-creator/event-creator.php") ){
    //Get Tags from Database | this requires the plugin Event_Creator to be active!
    global $wpdb;
    $table_name = $wpdb->prefix . 'static_events_tags';
    $DBTags = $wpdb->get_results ( "SELECT * FROM $table_name" );

    foreach ( $DBTags as $tag ){
      if(getTagAmount($tag->tag,$events) != 0){
        $tags[] = $tag->tag;
        // $tmp++;
        // echo "<script>console.log( 'Tag ".$tmp.": " . $tag->tag . "' );</script>";
      }
    }
    //echo "<script>console.log( 'Plugin is active: " . $tags . "' );</script>";
  }else{
    //Get static Tags
    $tags = Tags::getTags();
    //echo "<script>console.log( 'Plugin NOT active: " . $tags . "' );</script>";
  }
  return $tags;
}



/**
*   Returns the amount of events with the given tag
*/
function getTagAmount($tag,$events){
  $size = 0;
  foreach($events as $event){
    if($event->tag == $tag)
      $size++;
  }
  return $size;
}

function isLocation($str){
  //german postcode
  $gerPostcode = "/(?!01000|99999)(0[1-9]\d{3}|[1-9]\d{4})/";
  //austria postcode
  $autPostcode = "/^[0-9]{4}/";
  // $thisYear = date('Y');
  // if((int)($thisYear + 1) == (int)$str)
  //   echo "<script>console.log( 'this year".$thisYear."; next year: ".(int)($thisYear + 1)."' );</script>";

  //popular austrian places
  //$autPlaces = "/((w|W)ien)|((l|L)inz)|((s|S)alzburg)|((k|K)lagenfurt)|((g|G)raz)/";
  if((preg_match($gerPostcode, $str)) || (preg_match($autPostcode, $str)) /*|| (preg_match($autPlaces, $str))*/)
    return true;
  else
    return false;
}



function isPerson($str){
  //$potentialPerson = "/[\^<,\"@\/\{\}\(\)\*\$%\?=>:\|;#]+/i";
  //$doctor = "/(Fach)?(A|a|Ä|ä)rzt(in)?/";
  $personCheck = "/([P,p]prof|[f,F]rau|[h,H]err|[d,D]r\.|[f,F]r\.)/";
  $noDigit = "/[^.0-9]+/";

  if( preg_match($personCheck, $str) && preg_match($noDigit, $str)  )
    return true;
  else
    return false;
}



/**
* Return the text between html-tags
*/
function getTextBetweenTags($string, $tagname) {
    $pattern = "/<$tagname ?.*>(.*)<\/$tagname>/";
    preg_match_all($pattern, $string, $matches);
    return $matches[0];
}




/**
*   Returns the filter form as html-string
*/
function getFilterForm($events){

  $tags = getTags($events);
  $current_year = date('Y');
  $monthNum  = date('n');

  $result = '<form id="events_filter_form" class="events_filter_form" action="" method="post">' .
              '<ul>'.
                '<li><h4>Land</h4>'.
                '<label>'.
                  '<select id="land" name="land">';

                  if(isset($_POST['land']))
                    $result .= '<option selected disabled>'.$_POST['land'].'</option>';
                  else
                    $result .= '<option selected>Alle</option>';

                  $result .=        '<option value="AT">Österreich</option>';
                  $result .=        '<option value="DE">Deutschland</option>';
                  $result .=        '<option value="CH">Schweiz</option>';



  $result .=       '</select>'.
                '</label>'.
                '</li>' .
                '<li><h4>Thema</h4>'.
                '<label>'.
                  '<select id="thema" name="thema">';

                  if(isset($_POST['thema']))
                    $result .= '<option selected disabled>'.$_POST['thema'].'</option>';
                  else
                    $result .= '<option selected>Alle Themen</option>';

  foreach ( $tags as $tag )
    $result .=        '<option value="'.$tag.'">'.$tag.'</option>';

  $result .=       '</select>'.
                '</label>'.
                '</li>' .
                '<li><h4>Filter anwenden</h4>'.
                  '<input type="hidden" name="action" value="filterEvents"/>'.
                  '<input type="submit" value="FILTERN"></input>'.
                '</li>' .
              '</ul>'.
            '</form>';

    $result .= '<div class="events_msg filter_success" style="display:none;"><span class="closebtn">&times;</span></div>';
    $result .= '<div class="events_msg filter_warning" style="display:none;"><span class="closebtn">&times;</span></div>';

  return $result;
}
