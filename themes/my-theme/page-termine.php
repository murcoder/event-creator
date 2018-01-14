<?php

/**
 * Template Name: Termine
 * Author:  CHRISTOPH MURAUER
 * date: 2017-07-20
 */




 get_header(vibe_get_header());

 if ( have_posts() ) : while ( have_posts() ) : the_post();

 $title=get_post_meta(get_the_ID(),'vibe_title',true);
 if(vibe_validate($title) || empty($title)){


 ?>
 <section id="title">
     <div class="<?php echo vibe_get_container(); ?>">
         <div class="row">
             <div class="col-md-12">
                 <div class="pagetitle">
                     <?php
                         $breadcrumbs=get_post_meta(get_the_ID(),'vibe_breadcrumbs',true);
                         if(vibe_validate($breadcrumbs) || empty($breadcrumbs))
                             vibe_breadcrumbs();
                     ?>
                     <h1><?php the_title(); ?></h1>
                     <?php the_sub_title(); ?>
                 </div>
             </div>
         </div>
     </div>
 </section>
 <?php
 }

     $v_add_content = get_post_meta( $post->ID, '_add_content', true );

 ?>
 <section id="content">
     <div class="<?php echo vibe_get_container(); ?>">
         <div class="row">
             <div class="col-md-12">

                 <div class="<?php echo $v_add_content;?> content">
                     <?php
                         the_content();
                         $page_comments = vibe_get_option('page_comments');
                         if(!empty($page_comments))
                             comments_template();
                      ?>

                      <?php
                      //*** Turn DEBUG mode on/off here ***/
                      $debug = false;

                      //The HTML output of the events-page
                      $result = "";

                      //Get static and dynamic events in a chronological order
                      $events = getEvents($debug);

                      //BEGIN -- Build event container
                      $result .= '<div class="vc_row wpb_row vc_row-fluid"> <div class="maincontent-container wpb_column vc_column_container vc_col-sm-12  vc_col-md-9"> <div class="vc_column-inner "> <div class="wpb_wrapper"> <div class="wpb_text_column wpb_content_element "> <h1>Termine</h1> <p>&nbsp;</p><p>Um Ihnen die Bandbreite interessanter Veranstaltungen von Selbsthilfegruppen aufzuzeigen, finden Sie hier unsere Sammlung spannender Termine. Bitte informieren Sie sich vorab bei dem jeweiligen Veranstalter, ob eine Anmeldung notwendig ist.</p><p>Sollten Veranstaltungen Ihrer Selbsthilfegruppe fehlen, können Sie uns diese unter info[at]selpers.com zusenden.</p>';

                      if($debug){
                        echo '<hr>';
                        echo '<h2>EVENT OBJEKTE: '.count($events).' </h2> (Erstellte Objekte vor finalem Filter)';
                      foreach($events as $event){
                          echo '<pre>';
                          echo $event;
                          echo '</pre>';
                        }
                      }

                      // Create the Filter Form
                      $result .= getFilterForm($events);
                      //echo "<script>console.log( 'Filterung: " . $_POST['zeitraum'] . ", ".$_POST['thema']."' );</script>";


                      $current_year = date('Y');
                      $monthNum  = date('n');
                      $current_day = date('d');
                      $eventCounter = 0;

                      $result .= '<div class="events_container">';

                      // -- ACCORDIONS FOR 6 MONTHS
                      for($i=0; $i<4; $i++){

                          if($monthNum > 12){
                            $monthNum = 1;
                            $current_year++;  //New Year
                          }

                          $allEvents = "";

                          //Put event in the right month-year category
                          foreach($events as $event){
                            //Fill month-categories with events
                              if( !inPast($event) && ($event->date->format('n') == $monthNum) && ($event->date->format('Y') == $current_year) ){
                                $allEvents .= $event->__toString();
                                $eventCounter++;
                              }
                          }

                          //Create dynamic date-title
                          $dateObj   = DateTime::createFromFormat('!m', $monthNum);
                          $month = $dateObj->format('F');
                          $monthAndYear = monthGer($month). str_repeat('&nbsp;', 1) . $current_year;

                          //Create the visual-composer toggle
                          $result .= do_shortcode('[vc_toggle title='.$monthAndYear.' el_id="'.$monthAndYear.'"]'. $allEvents .'[/vc_toggle]');
                          $monthNum++;

                      }

                      $result .= '<div class="modal"></div></div>'; //Loading animation


                      if($debug){
                        $eventCounter = $eventCounter . " (aus "  . count($events) . ")";
                        $result .= '<p class="event_counter">Insgesamt listen wir aktuell '.$eventCounter.' Veranstaltungen von über '.count( getUrls() ).' verschiedenen Webseiten.</p></div></div>';
                      }else{
                        $result .= '<p class="event_counter">Insgesamt listen wir aktuell '.$eventCounter.' Veranstaltungen von über '.count( getUrls() ).' verschiedenen Webseiten.</p></div></div>';
                      }


                      //END -- close event container
                      $result .= '</div></div>';

                      //BEGIN -- Build widget container
                      $result .= '<div class="wpb_column vc_column_container vc_col-md-3 vc_col-sm-12"> <div class="vc_column-inner "> <div class="wpb_wrapper"> <div class="wpb_widgetised_column wpb_content_element"> <div class="wpb_wrapper">';

                      //Add widgets (MainSidebar)
                      $result .= do_shortcode('[vc_widget_sidebar sidebar_id="mainsidebar"]');

                      //END -- close widget container
                      $result .= ' </div></div></div></div></div>';

                      //close content container
                      $result .= '</div>';
                      echo $result;
                      ?>

                 </div>
             </div>
         </div>
     </div>
 </section>


 <?php
 endwhile;
 endif;
 ?>
 <?php
 get_footer( vibe_get_footer() );
