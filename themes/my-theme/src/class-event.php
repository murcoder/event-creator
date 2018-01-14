<?php

/* Definition of event-class */
class event{

  public $tag;
  public $date;  //required
  public $time;
  public $content; //required
  public $location;
  public $speaker;
  public $url;
  public $fileurl;
  private $hasDate;
  private $hasSpeaker;
  private $hasTag;
  private $hasLocation;
  private $hasFile;
  private $country;
  private $hasCountry;



  //Constructor
  function __construct(){
    $this->tag = Tags::Allgemein;
    $this->date = date('d.m.Y');
    $this->time = "";
    $this->content = "";
    $this->location = "";
    $this->speaker = "";
    $this->url = "#";
    $this->fileurl = "#";
    $this->hasDate = FALSE;
    $this->hasSpeaker = FALSE;
    $this->hasTag = FALSE;
    $this->hasLocation = FALSE;
    $this->hasFile = FALSE;
    $this->country = "";

  }

  function setDate($d){
    $this->date = $d;
    $this->hasDate = TRUE;
  }
  function hasDate(){
    return $this->hasDate;
  }
  function getStringDate(){
    if($this->hasDate())
      return $this->date->format('d.m.Y');
  }
  function setSpeaker($a){
    $this->speaker = $a;
    $this->hasSpeaker = TRUE;
  }
  function hasSpeaker(){
    return $this->hasSpeaker;
  }
  function setTag($t){
    $this->tag = $t;
    $this->hasTag = TRUE;
  }
  function hasTag(){
    return $this->hasTag;
  }
  function getTag(){
    return $this->tag;
  }
  function setLocation($l){
    $this->location = $l;
    $this->hasLocation = TRUE;
  }
  function hasLocation(){
    return $this->hasLocation;
  }
  function hasFile(){
    return $this->hasFile;
  }
  function setFileurl($url){
    $this->fileurl = $url;
    $this->hasFile = TRUE;
  }
  function setCountry($c){
    $this->country = $c;
    $this->hasCountry = TRUE;
  }
  function hasCountry(){
    return $this->hasCountry;
  }


  /* Compare two events
  *  @Return: true if the url and the content of two events are equal
  */
  public function isEqual($event){
    if(strcmp($this->url, $event->url) === 0 && strcmp($this->content, $event->content) === 0 )
      return true;
    else
      return false;
  }



  /*  Returns string representation of the object
  *   call with 'echo'
  */
  public function __toString(){
    $result = "<div class='event'>";
    $result .= "<div class='event_headline'><strong><span class='tag'>$this->tag</span>";

    if($this->hasCountry())
      $result .= " (<span class='country'>$this->country</span>)</strong> ";
    else
      $result .= "</strong> ";


    $result .= "</div><br />";

    //date and content has to exist
    $strDate = $this->date->format('d.m.Y');
    $result .= "<div class='date'><strong>$strDate";

    //Check parameter 'time'
    if($this->time != "")
      $result .= ", $this->time Uhr</strong></div><br />";
    else
      $result .= "</strong></div><br />";

    //add 'content'
    if($this->content != "")
      $result .= "<div class='text'>$this->content</div><br />";

    //Check parameter 'speaker'
    if($this->speaker != "")
      $result .= "<div class='speaker'>$this->speaker</div><br />";

    //Check parameter 'location'
    if($this->location != "")
      $result .= "<div class='location'><i>$this->location</i></div><br />";

      if($this->hasFile())
        $result .= "<div class='file_url'>$this->fileurl</div><br />";

    if($this->url != "")
      $result .= "<div class='url'>$this->url</div><br /><br />";
    else
      $result .= "<br />";

    $result .= "</div><br />";
    return $result;
  }


}
