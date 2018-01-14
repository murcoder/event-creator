<?php

  /*** Contains custom data for events ***/



/*
* Define URLS
* @return array of scraped urls
*/
function getUrls($debug = false){

  $urls;

  if($debug){

    //Insert new URL here
    $urls = array("http://my-url-for-debugging.com");


  }else{

    //Add all debugged URLs here
    $urls = array("http://my-url-1.com",
  "http://my-url-2.com");
  }


   return $urls;
}



function setCustomDomType($url){
  if($url == "http://my-url-with-list-dom.com")
    return "list";

  if($url == "http://my-url-with-custom-dom.com")
    return "custom";


  return "";
}

/*
*   Add individual url data to event
*   @Return the updated event
*/
function getCustomData($url,$event){

  if($url == "http://my-url-with-custom-data.com"){
    $event->setCountry('AT');
    $event->tag = Tags::Prostata;
    $event->location = '1080 Wien';
  }

  return $event;

}



function getCustomDOM($url){

  $result = "";
  if($url == "http://my-url-with-div"){
    $result = wpws_get_content($url, '#custom_id' );
    return $result;
  }


  return $result;

}


/*
*   Scrape Data in an individual way
*
*/
function createCustomEvent($url,$dom,$debug=false){
  $event = new event();
  $dataArr = array();


  if($url == "http://my-individual-url.com"){
    $rows = $dom->getElementsByTagName('tr');
    $xpath = new DomXPath($dom);
    $tds = $xpath->query("//td");

    foreach( $tds as $td ){
      $date = $td->getAttribute("title");
      $dataArr[] = $date;

      if(!$td->getAttribute("title")){
        $content = $td->nodeValue;
          $dataArr[] = $content;
      }
    }

    /*** Create the event ***/
    try{
      $event = setEvent($dataArr, $debug);
    }catch (Exception $e) {
        echo "<script>console.log( 'Event for ".$url." could not be created: " . $e->getMessage() . "' );</script>";
    }

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

    echo $debug ? "<script>console.log( '----------------' );</script>" : "";
    return $event;

}


function crawl_page($url, $depth = 5){

    static $seen = array();
    if (isset($seen[$url]) || $depth === 0) {
        return;
    }

    $seen[$url] = true;

    $dom = new DOMDocument('1.0');
    @$dom->loadHTMLFile($url);

    $anchors = $dom->getElementsByTagName('a');
    foreach ($anchors as $element) {
        $href = $element->getAttribute('href');
        if (0 !== strpos($href, 'http')) {
            $path = '/' . ltrim($href, '/');
            if (extension_loaded('http')) {
                $href = http_build_url($url, array('path' => $path));
            } else {
                $parts = parse_url($url);
                $href = $parts['scheme'] . '://';
                if (isset($parts['user']) && isset($parts['pass'])) {
                    $href .= $parts['user'] . ':' . $parts['pass'] . '@';
                }
                $href .= $parts['host'];
                if (isset($parts['port'])) {
                    $href .= ':' . $parts['port'];
                }
                $href .= dirname($parts['path'], 1).$path;
            }
        }
        crawl_page($href, $depth - 1);
    }
    echo "URL:",$url,PHP_EOL,"CONTENT:",PHP_EOL,$dom->saveHTML(),PHP_EOL,PHP_EOL;
}






























 ?>
