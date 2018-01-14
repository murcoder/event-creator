<?php
/* Tags Enumerations | usage $tag = Tags::Allgemein;*/
abstract class Tags
 {
     const Allgemein = "Allgemein";
     const Krebs = "Krebs";
     const Prostata = "Prostatakrebs";
     const Myelom = "Myelom und Lymphom";
     const FrauenKrebs = "Krebs bei Frauen";
     const Psyche = "Psychische Gesundheit";
     const Darm = "Magen und Darm";
     const Brust = "Brustkrebs";
     const Blase = "Blasenkrebs";
     const Leber = "Leberkrebs";

     // Returns all tags as array
     public static function getTags(){
       $array = array(self::Allgemein,self::Krebs,self::Prostata,self::Myelom,self::FrauenKrebs,self::Psyche,self::Darm,self::Brust);
       return $array;
     }
 }
