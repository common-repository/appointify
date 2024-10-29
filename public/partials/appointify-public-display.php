<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       appointify.app
 * @since      1.0.0
 *
 * @package    Appointify
 * @subpackage Appointify/public/partials
 */


 /* This file should primarily consist of HTML with a little bit of PHP. */

function appointify_frontend_shortcode() {

    ob_start();

    global $wpdb, $post;
    $wp_user_data = $wpdb->prefix . "user_data";
    $user = wp_get_current_user();
    $slot_gap = get_option( 'calendar_slot_display' ); 
    
    appointify_front_end_scripts();

    include ( APPOINTIFY_PLUGIN_PATH . 'includes/calendar_google/vendor/autoload.php' );

     $selfUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')?"https://":"http://";   
        $selfUrl .= $_SERVER['HTTP_HOST'];   
        $selfUrl .= $_SERVER['REQUEST_URI'];
        $selfUrl = preg_replace('~(\?|&)code=[^&]*~','$1',$selfUrl);
        $selfUrlredirect = esc_url_raw($selfUrl);
        $selfUrlred = esc_url($selfUrlredirect);
    $json_front_file_inserted = get_option( 'json_front_file_inserted' );
    $json_file_inserted = get_option( 'json_file_inserted' );
    $credentials_data_front = '' ;
    $credentials_data_front = json_decode($json_front_file_inserted, true) ;
    $credentials_data = json_decode($json_file_inserted, true) ;

    $client = new Google_Client();
    $client->setApplicationName('Calendar');
    $client->setScopes(Google_Service_Calendar::CALENDAR);
    $client->setAuthConfig($credentials_data_front);
    $client->setScopes(array(
            "https://www.googleapis.com/auth/userinfo.email",
            "https://www.googleapis.com/auth/userinfo.profile",
            "https://www.googleapis.com/auth/plus.login"
            )); 
    $client->setAccessType('offline');
    $client->addScope(array(
            "https://www.googleapis.com/auth/userinfo.email",
            "https://www.googleapis.com/auth/userinfo.profile",
            "https://www.googleapis.com/auth/plus.login"
            )); 
    $client->setPrompt('select_account consent'); 

    $calendarId = '';
    
      $resultstoken = $wpdb->get_results("SELECT * FROM $wp_user_data WHERE status = 1 ORDER BY id DESC" );

      if(is_array($resultstoken) &&  count( $resultstoken ) > 0 ){
        $access_token = $resultstoken[0]->token_data;
        $data_id = $resultstoken[0]->id;
        $accessToken_str = stripslashes($access_token);
        $accessToken = json_decode($accessToken_str, true);

        $client->setAccessToken($accessToken);
 
         if ($client->isAccessTokenExpired()) { 
                // Refresh the token if possible, else fetch a new one.
                if ($client->getRefreshToken()) { 
                 $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                  $accessToken = $client->getAccessToken();
                  $token_arr =  addslashes( json_encode($accessToken) );
                 
                }
            }
      if (!$client->isAccessTokenExpired()) {
        $service = new Google_Service_Calendar($client);
        $calendarId = $resultstoken[0]->calendar_id;
      }
   
      }else{
        echo __('Not found access token, Please login with google', 'appointify');
        die();
    }

     try {
        $service = new Google_Service_Calendar($client);
        if( isset($_COOKIE[ "token_data" ]) && isset($calendarId) && !empty($calendarId) && $calendarId != ''){ 
        if(isset($service) && !empty($service) && !empty($calendarId)){ 
          $events  = $service->events->listEvents( $calendarId);//$calendarId
          
        }

        $tempArray= array();
        if(isset($events) && !empty($events)){
          while(true) {
            foreach ($events->getItems() as $event) {
                  $tempArray[$event->getId()] =  $event->getSummary();
            }
            $pageToken = $events->getNextPageToken();
            if ($pageToken) {
              $optParams = array('pageToken' => $pageToken);
              $events = $service->events->listEvents('primary', $optParams);
            } else {
              break;
            }
          }
        }
      }
    } catch (Throwable $e) {
       echo 'Caught exception: ' . esc_attr($e->getMessage());
    }

    $client_frontend = new Google_Client();
    $client_frontend->setApplicationName('Calendar');
    $client_frontend->setScopes(Google_Service_Calendar::CALENDAR);
    $client_frontend->setScopes(array(
    "https://www.googleapis.com/auth/userinfo.email",
    "https://www.googleapis.com/auth/userinfo.profile",
    "https://www.googleapis.com/auth/plus.login",
    "https://www.googleapis.com/auth/calendar"
    )); 
    $client_frontend->setAuthConfig($credentials_data_front);
    $client_frontend->setIncludeGrantedScopes(true); 
    $client_frontend->setAccessType('offline');
    $client_frontend->setPrompt('select_account consent');  
     $client_frontend->addScope(array(
    "https://www.googleapis.com/auth/userinfo.email",
    "https://www.googleapis.com/auth/userinfo.profile",
    "https://www.googleapis.com/auth/plus.login",
    "https://www.googleapis.com/auth/calendar"
    ));
        
    if( isset($_COOKIE[ "token_data" ]) && !empty($_COOKIE[ "token_data" ])){ 
      $token_data_sani = wp_kses($_COOKIE[ "token_data" ], array());
      $token_google_en = json_encode( $token_data_sani );
      $token_google = wp_unslash($token_google_en);
            
    if(isset($token_google) && $token_google != "" ){
            $access_token_frontend = $token_google;
            $accessTokenFrontend =  stripslashes( json_decode($access_token_frontend, true) );
        
            $client_frontend->setAccessToken($accessTokenFrontend);
            $client->setAccessToken($accessToken);
 
           if ($client_frontend->isAccessTokenExpired()) {
                  // Refresh the token if possible, else fetch a new one.
                  if ($client_frontend->getRefreshToken()) { 
                   $client_frontend->fetchAccessTokenWithRefreshToken($client_frontend->getRefreshToken());
                  }
              }

          try {
              $service_frontend = new Google_Service_Calendar($client_frontend);
          } catch (Throwable $e) {
             echo 'Caught exception: ' . esc_attr($e->getMessage());
          }        
        }   
    }
    
    if( isset($_POST['submit']) ){

        if(isset($_POST['calenderid'])){
          $calenderid_sani = sanitize_email($_POST['calenderid']);
          $calenderid_esc = esc_attr($calenderid_sani);
          if(isset($calenderid_esc) && !empty($calenderid_esc)){
            $calid = $calenderid_esc;
          }else{
            $calid = '';
          }
        } else {
          if(isset($_GET['CalId']) && !empty($_GET['CalId']) ){
            $CalId_sani = sanitize_email($_POST['CalId']);
            $CalId_esc = esc_attr($CalId_sani);
            if(isset($CalId_esc) && !empty($CalId_esc)){
              $calid =  $CalId_esc;
            }else{
              $calid = '';
            }
          }else{
            $calid = '';
          }
        }
        if(isset($_REQUEST['starte_date_time']) && !empty($_REQUEST['starte_date_time'])){
          $starte_date_time = sanitize_text_field($_REQUEST['starte_date_time']);
          $starte_date_time_esc = esc_attr($starte_date_time);
          $start_date = isset($starte_date_time_esc) ? $starte_date_time_esc : ''; 
        }
        if(isset($_REQUEST['end_date_time']) && !empty($_REQUEST['end_date_time'])){
          $end_date_time = sanitize_text_field($_REQUEST['end_date_time']);
          $end_date_time_esc = esc_attr($end_date_time);
          $end_date = isset($end_date_time_esc) ? $end_date_time_esc : ''; 
        }
        if(isset($_REQUEST['end_user_name']) && !empty($_REQUEST['end_user_name'])){
          $end_user_name = sanitize_text_field($_REQUEST['end_user_name']);
          $end_user_name_esc = esc_attr($end_user_name);
          $name = isset($end_user_name_esc) ? $end_user_name_esc : ''; 
        }
        if(isset($_REQUEST['end_user_email']) && !empty($_REQUEST['end_user_email'])){
          $end_user_email = sanitize_email($_REQUEST['end_user_email']);
          $end_user_email_esc = esc_attr($end_user_email);
          $email = isset($end_user_email_esc) ? $end_user_email_esc : ''; 
        }

       $timezone_string =  get_option('timezone_string');
        if(!empty($timezone_string)){   
          date_default_timezone_set($timezone_string);
        }
        $s_date = date ('c', strtotime( $start_date ) );
        $e_date = date ('c', strtotime( $end_date ) );

        $event = new Google_Service_Calendar_Event(array(
          'summary' =>  __( 'Partnership Chat Book', 'appointify' ),
         
          'start' => array(
            'dateTime' => $s_date,
            'timeZone' => date_default_timezone_get(),
          ),
          'end' => array(
            'dateTime' => $e_date,
            'timeZone' => date_default_timezone_get(),
          ),
          'attendees' => array(
             array('email' => $email, 'displayName' => $name), 
           ),
        
        ));
        $accessToken = json_decode($access_token, true);
        $client->setAccessToken($accessToken);

        $event = new Google_Service_Calendar_Event(array(
          'summary' => __( 'Partnership Chat Book', 'appointify' ),
         
          'start' => array(
            'dateTime' => $s_date,
            'timeZone' => date_default_timezone_get(),
          ),
          'end' => array(
            'dateTime' => $e_date,
            'timeZone' => date_default_timezone_get(),
          ),
          'attendees' => array(
             array('email' => $email, 'displayName' => $name), 
           ),
        
        ));
        $event = $service->events->insert($calendarId, $event);
        $fn_table_name_tbl_events = $wpdb->prefix . 'tbl_events';
        $chat_title =  __( 'Partnership Chat Book', 'appointify' );
        $insertData = array(
            'title' => $chat_title.' - '.$name,
            'start' => $s_date,
            'end' => $e_date, // ... and so on
            'calendar_id'=> $calid,
            'event_id' => $event->id
        );
        $insert_event = $wpdb->insert($fn_table_name_tbl_events, $insertData  );
        if(isset($selfUrlred) && !empty($selfUrlred)){
        ?>
        <script type="text/javascript">
        window.location.href = '<?php echo $selfUrlred; ?>';
        </script>
        <?php
      }
        exit; die();
    }
    ?>

    <div class="content appointify-main-content">
    <div class="container-fluid">
        <h2><?php echo __( 'Calendar Demo', 'appointify' ); ?></h2>
       
        <div class="response"></div>
        <?php 
        
        $cookie_name_token_data = "token_data";
            // Google passes a parameter 'code' in the Redirect Url
            if(isset($_GET['code'])) {
                 $authCode = sanitize_text_field($_GET['code']);
                 $getauthCode = esc_attr($authCode);
                 if(isset($getauthCode) && !empty($getauthCode)){
                  $accessToken = $client_frontend->fetchAccessTokenWithAuthCode($getauthCode);
                  
                   $client_frontend->authenticate($getauthCode);
                  $clientaccess = $client_frontend->setAccessToken($accessToken);
                }
                $token_arr = addslashes( json_encode($client_frontend->getAccessToken()) );

                $cookie_name_token_data = "token_data";
                $cookie_value_token_data = json_encode($client_frontend->getAccessToken());

                ?>
                <script type="text/javascript">
                
                function setCookie(c_name, value, exdays) {
                    var exdate = new Date();
                    exdate.setDate(exdate.getDate() + exdays);
                    var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
                    document.cookie = c_name + "=" + c_value;
                }
                setCookie( "<?php echo esc_attr($cookie_name_token_data); ?>", JSON.stringify( <?php  _e($cookie_value_token_data); ?> ) , 1 );
                window.location.href = '<?php if(isset($selfUrlred) && !empty($selfUrlred)){ echo $selfUrlred; } ?>';
                    
                </script>
                <?php
            }
         $login_url  = $client_frontend->createAuthUrl(); ?>  
          
        <div class="row"> 
                 
                <div class="col-lg-3 col-sm-12 sidebar-area">
                  <?php if( $client_frontend->isAccessTokenExpired() ){ 
                      if( $client->isAccessTokenExpired() ){ 
                    ?><p class="not_authenticate"><?php echo __( 'Admin does not authenticate from google.', 'appointify' ); ?> </p>
                  <?php }else{ ?>
                      <a href="<?php echo esc_url($login_url); ?>" class="button google-btn button-primary"><?php echo __( 'Google', 'appointify' ) ?> </a>
                  <?php } }else{ 
                    $google_oauth =new Google_Service_Oauth2($client_frontend);
                  $cuurrentusername = $google_oauth->userinfo->get()->name;   
                    ?>
                   <div class="google-btn button-primary"><?php if(isset($cuurrentusername)){ echo esc_attr($cuurrentusername); } ?> <a class="log_out_btn btn btn-primary" id="appointify_logout" ><span>Log Out</span></a></div>
                  
                <?php } ?>
                    <p><?php echo __( 'If you want Add event in google calendar please login in google.', 'appointify' ) ?> </p>
                    <?php 
                    $useremail = ''; 
                    $currentuseremail = '';
                    $authusername = "";
                    if(!empty($token_google) ){
                        $google_oauthV2 = new Google_Service_Oauth2($client_frontend);

                    if ($client_frontend->getAccessToken()) // Sign in
                    {
                        try {
                            $user = $google_oauthV2->userinfo->get();
                            $authusername = $google_oauthV2->userinfo->get()->name;
                            $authusername_sani = sanitize_text_field($authusername);
                        } catch (Exception $e) { 
                            header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
                        }
                    }
                    $service_new = new Google_Service_Calendar($client_frontend);
                   
                    $list_cal = $service_new->calendarList->listCalendarList(array('maxResults'=>500), array('minAccessRole'=> 'owner'));
                    $useremail = $service_new->calendars->get('primary');

                    if(null !== $useremail->getSummary()){
                      $currentuseremail = $useremail->getSummary();
                    }
                    if(isset($_GET['CalId']) && !empty($_GET['CalId'])){
                      $CalId = sanitize_email($_GET['CalId']);
                      $CalId = esc_attr($CalId);
                        $calid = preg_replace('/[^a-zA-Z0-9\/:@\.\+-s]/', '', $CalId);
                    } else {
                        $calid_sani = sanitize_email($currentuseremail);
                        $calid_esc = esc_attr($calid_sani);
                        if(isset($calid_esc) && !empty($calid_esc)){
                          $calid = esc_attr($calid_esc);
                        }else{
                          $calid = '';
                        }                        
                    }
                    $events_front  = $service_new->events->listEvents( $calid );
                    
                    $evntarray = array();
                    $evntget = array();
                    $tempArray_new= array();
                    if($events_front){
                        while(true) {
                          foreach ($events_front->getItems() as $event_f) {
                              if($event_f){
                                if(null !== $event_f->getStart()){
                                $start = $event_f->getStart()->dateTime;
                                $end = $event_f->getEnd()->dateTime;
                              }else{
                                $start = '';
                                $end = '';
                              }
                              ?>
                              <script>
                              
                              jQuery(document).ready(function () {
                                jQuery.UNSAFE_restoreLegacyHtmlPrefilter();
                                  var event = {
                                        id: '<?php echo esc_attr( $event_f->getId() ); ?>',
                                        title: '<?php echo str_replace("'", "", $event_f->getSummary()); ?>',
                                        start: "<?php echo esc_attr( $start ); ?>",
                                        end: "<?php echo esc_attr( $end);?>",
                                        backgroundColor: 'blue',
                                        allDay: false,
                                        editable: false
                                    }
                                    setTimeout(function(){ 
                                      jQuery('#calendar').fullCalendar('renderEvent',event,true);
                                }, 800);
                              });
                              
                              </script>
                              <?php
                                
                                $evntarray["id"]= $event_f->getId();
                                $evntarray["title"]= $event_f->getSummary();
                                $evntarray["start"]= $start;
                                $evntarray["end"]= $end;
                                $evntarray['editable'] = false;
                                $evntarray['allDay'] = false;
                                
                                
                            $tempArray_new[$event_f->getId()] =  $event_f->getSummary(); 
                            }
                          }
                          $pageToken = $events_front->getNextPageToken();
                          if ($pageToken) {
                            $optParams = array('pageToken' => $pageToken);
                            $events_front = $service_new->events->listEvents('primary', $optParams);

                          } else {
                            break;
                          }
                        }                     
                    }
                     ?>
                     <div class="form-group">
                     <label for="currentuseremail"><?php  echo __( 'Selected Calendar:', 'appointify' ); ?></label>  
                     <select class="form-control" name="calendar_id" id="calendar_id">
                        <option value="" ><?php  echo __( 'Select Calendar:', 'appointify' ); ?></option>
                        <?php 
                        if( !empty($service_new) ){
                           
                            $eventArray = array();
                            foreach ($list_cal->getItems() as $calEntry) {
                              $calendr_id = $calEntry->getId();
                              ?>
                              <option value="<?php echo esc_url(get_permalink().'?CalId='.$calEntry->getId()); ?>" <?php   if($calendr_id == $calid){ echo 'selected'; }else{ echo ' '; } ?>><?php echo esc_attr($calEntry->getSummary()." - ".$calEntry->accessRole); ?></option>
                              <?php 
                            }
                        }
                        
                     ?>
                    </select>
                  </div>
                    <?php } // end if 
                    $calendarid_inpt = '';
                    if(isset($_GET['CalId']) && !empty($_GET['CalId'])){
                      $CalId_sani = sanitize_email($_GET['CalId']);
                      $CalId = esc_attr($CalId_sani);
                      if (isset($CalId) && !empty($CalId)) {
                        $calendarid_inpt =  preg_replace("/[^a-zA-Z0-9\/:@\.\+-s]/", "", $CalId);
                      }else{
                        $calendarid_inpt = '';
                      }
                      
                    }
                    ?>
                            
                    <form action="#" method="post" id="custom_booking_form">
                        <h3><?php  echo __( 'Partnership Chat', 'appointify' ); ?></h3>
                        <h4><?php  echo __( "Let's find a time to meet about our upcoming co-promotion!", "appointify" ); ?> </h4>
                        <div class="form-group">
                            <label for="exampleInputPassword1"  id="date_time">
                            </label>
                        </div>
                            <input type="hidden" required placeholder="Start date" name="starte_date_time" id="starte_date_time">
                            <input type="hidden" required placeholder="End Date" name="end_date_time" id="end_date_time">  
                        <div class="form-group">
                            <label for="exampleInputPassword1"><?php  echo __( 'Enter Name:', 'appointify' ); ?></label>    
                            <input type="text" required class="form-control"  placeholder="Name" name="end_user_name" value="<?php echo esc_attr( $authusername ); ?>"> 
                        </div>

                        <div class="form-group">
                            <label for="currentuseremail"><?php  echo __( 'Enter Email:', 'appointify' ); ?></label>  
                            <input type="email" required class="form-control"  placeholder="Email" name="end_user_email" value="<?php  echo esc_attr( $currentuseremail );  ?>"> 
                        </div>
                        <input type="hidden" name="calenderid" value="<?php if(isset($calendarid_inpt)){ echo esc_attr($calendarid_inpt); } ?>">
                        <button  type="submit" class="btn btn-primary" name="submit" value="submit">Save</button>
                    </form>
                </div>
                <div class="col-lg-9 col-sm-12">
                     <div id='calendar'></div>
                </div>
            </form>
        </div>
       
    </div>
</div>

   <input type="hidden" name="" id="uniqueId" value="0">

   <?php 
   $userdetails = wp_get_current_user();

   $calendarwh = get_option( 'calendar_working_hr', true);
   $working_hr_val = json_decode($calendarwh, true);

   $data_array = array();
   $eventArray = array();
   if(is_array($working_hr_val)){
     foreach($working_hr_val as $key => $data){

          $newRow = array();
          $newBreakRow = array();

           if( $data['is_working'] == "on" ){

               $newRow['dow'] = array($key-1);

               if($data['is_break']=="on"){
                      $newBreakRow['dow']  = array($key-1);
                      $newRow['start'] = date("H:i",strtotime($data['start_time']));
                      $newRow['end'] = date("H:i",strtotime($data['break_start']));
                      $newBreakRow['start'] = date("H:i",strtotime($data['break_end']));
                      $newBreakRow['end'] = date("H:i",strtotime($data['end_time']));

                  }else
                  {
                      $newRow['start'] = date("H:i",strtotime($data['start_time']));
                      $newRow['end'] = date("H:i",strtotime($data['end_time']));
                  }

              array_push($eventArray, $newRow);
              array_push($eventArray, $newBreakRow);
           }
      }
    }
    $table_name_holiday = $wpdb->prefix . 'holidays';
    $sqlQueryHoliday = "SELECT * FROM $table_name_holiday WHERE isDelete=0 ORDER BY id";

    $resultholiday =  $wpdb->get_results($sqlQueryHoliday, OBJECT); 
   
    $fn_table_name_tbl_events = $wpdb->prefix . 'tbl_events';
    $sqlQuerytbl_events  = "SELECT * FROM $fn_table_name_tbl_events WHERE  calendar_id='".$calendarId ."' AND  google_status=1 ORDER BY id";

    $resulttbl_events =  $wpdb->get_results($sqlQuerytbl_events, OBJECT); 
    

    $row_h = array();
    $eventArray_fechted = array();
    foreach ($resultholiday as $key => $value) {


            $row_h['editable'] = false;
            $row_h["id"]= "EVENT_5555";
            $row_h["title"]= "EVENT_REMOVE";
            $row_h["start"]= date("Y-m-d H:i:s",strtotime($value->date." ".$value->start_time));
            $row_h["end"]= date("Y-m-d H:i:s",strtotime($value->date." ".$value->end_time));

            array_push($eventArray_fechted, $row_h);
    }
    $row_tbl_e = array();
    if(isset($resulttbl_events) && !empty($resulttbl_events)){
      foreach ($resulttbl_events as $key => $value) {

           if(array_key_exists($value->event_id,$tempArray)){

              $row_tbl_e['editable'] = false;
              $row_tbl_e["id"]= $value->event_id;
              $row_tbl_e["title"]= $value->title;
              $row_tbl_e["start"]= date("Y-m-d H:i:s",strtotime($value->start));
              $row_tbl_e["end"]= date("Y-m-d H:i:s",strtotime($value->end));    
              array_push($eventArray_fechted, $row_tbl_e);
          }else{
              $data = [ 'google_status' => 0 ];
              $where = [ 'id' => $value->id ];

              $rslt_updated_tbl_events = $wpdb->update( $fn_table_name_tbl_events, $data, $where );
          }  
      }

   }
    if( isset($_COOKIE['token_data']) ){
        
        $sqlQuerytbl_events11  = "SELECT * FROM $fn_table_name_tbl_events WHERE  calendar_id !='".$calid ."' AND  google_status=1 ORDER BY id";

        $resulttbl_events11 =  $wpdb->get_results($sqlQuerytbl_events11, OBJECT); 
        
    }else{
        $resulttbl_events11 = array();
    }

    foreach($resulttbl_events11 as $resultEvents){
        $titleCal = $resultEvents->title;
        $calendrId = $resultEvents->calendar_id;
        
        
        if($calendrId != $calid){
            ?>
        <script>
                          
          jQuery(document).ready(function () {
            jQuery.UNSAFE_restoreLegacyHtmlPrefilter();
              var event = {
                    id: '<?php echo esc_attr( $resultEvents->calendar_id ); ?>',
                    title: '<?php echo esc_attr( $resultEvents->title ); ?>',
                    start: "<?php echo esc_attr( $resultEvents->start ); ?>",
                    end: "<?php echo esc_attr( $resultEvents->end ); ?>",
                    backgroundColor: 'black',
                    allDay: false,
                    editable: false
                }
            setTimeout(function(){ 
                jQuery('#calendar').fullCalendar('renderEvent',event,true);
            }, 800);
          });
          
          </script>
        <?php
        } else {
            $event_added_arry = '';
        }
    }
    $timezone_string =  get_option('timezone_string');
        if(!empty($timezone_string)){
          $timezone = $timezone_string;
        }else{
          $timezone = 'UTC';
        }
   ?>

   <script>
(function( $ ) {
  'use strict';
jQuery(document).ready(function () {
  jQuery.UNSAFE_restoreLegacyHtmlPrefilter();
    var calendar_id = 'calendar_id';
      <?php if( empty($_GET['CalId'])){ ?>
      var cal = jQuery('option:selected', $("#"+calendar_id)).val();
        if(cal != null){
          window.location.href = cal;
        }  
      <?php } ?>
    jQuery("#"+calendar_id).change(function() {
        var cal = jQuery('option:selected', $(this)).val();
        
        if(cal != ''){
          window.location.href = cal;
        }        
    });
    
});



jQuery(document).ready(function () {
    jQuery.UNSAFE_restoreLegacyHtmlPrefilter();
    var uniqueId;
    var calendar = jQuery('#calendar').fullCalendar({
        eventLimit : false,
        slotLabelInterval: 15,
        businessHours: <?php echo json_encode($eventArray); ?>,
        selectConstraint: "businessHours",
        showNonCurrentDates: false,
        defaultView: "agendaWeek", 
         views: {
            agendaWeek: { 
              titleFormat: 'dddd, D MMM',
              titleRangeSeparator: ' - ',
            }
          },
        header: {
                    left: "title",
                    center: "",
                    right: 'prev,next'
                },
        editable: true,
        slotDuration: '0<?php if(!empty($slot_gap[0])){ echo esc_attr($slot_gap[0]); }else {echo '1';} ?>:00:00',
        snapDuration: '01:00:00',
        allDaySlot : false,
        events: <?php echo json_encode($eventArray_fechted); ?>,
        timezone: <?php echo json_encode($timezone); ?>,
        displayEventTime: true,
        eventRender: function (event, element, view) { appointify_dispalyOption();
        },
        validRange: { start: '<?php echo date("Y-m-d",strtotime('last Sunday')); ?>' },
        selectable: true,
        selectHelper: true,
        eventAfterAllRender:function(view ) {
            appointify_dispalyOption()
        },
        select: function (start, end, allDay) {

                var uniqueId = jQuery("#uniqueId").val();
            
                if(uniqueId > 0  ){
                    console.log(uniqueId);
                    jQuery('#calendar').fullCalendar('removeEvents',uniqueId);
                }
             
            var uniqueId = Math.floor(Date.now() / 1000);
            jQuery("#uniqueId").val(uniqueId);
            end =  jQuery.fullCalendar.moment(start);
            end.add(1, 'hours');

            var start1 = jQuery.fullCalendar.formatDate(start, "h:mmA");
            var end1 = jQuery.fullCalendar.formatDate(end, "h:maxTime");

            var title =  'Partnership Chat';

            var asd = jQuery('#calendar').fullCalendar('renderEvent',
            {   id : uniqueId,
                title: title,
                start: start,
                end: end,
                editable:false,
                allDay: false
                },
                true // stick the event
            ); 
            var html = jQuery.fullCalendar.formatDate(start, "dddd, MMMM  D, Y ");
            html += '<br>';
            html += jQuery.fullCalendar.formatDate(start, "hh:mm A");
            html += ' - ';
            html += jQuery.fullCalendar.formatDate(end, "hh:mm A");

            jQuery("#date_time").html(html);
            jQuery("#starte_date_time").val(jQuery.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss"));
            jQuery("#end_date_time").val(jQuery.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss"));
            calendar.fullCalendar('unselect');
        },
        
        editable: true,
       
    });
    jQuery("#appointify_logout").click(function(){
        document.cookie = 'token_data' +'=; Path=; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        window.location.href = '<?php if(isset($selfUrlred) && !empty($selfUrlred)){ echo $selfUrlred; } ?>';
    });

});

function appointify_dispalyOption(){

    var that = jQuery('div.fc-title:contains("EVENT_REMOVE")').parent().parent();

    jQuery('div.fc-title:contains("EVENT_REMOVE")').parent().parent().attr('class', 'fc-nonbusiness fc-bgevent').css("margin","0 -2px");

    jQuery('div.fc-title:contains("EVENT_REMOVE")').parent().remove();
}
function displayMessage(message) {
        jQuery(".response").html("<div class='success'>"+message+"</div>");
    setInterval(function() { jQuery(".success").fadeOut(); }, 1000);
}
})( jQuery );
</script>
    
    <?php

    return ob_get_clean();
}
function appointify_front_end_scripts() {
  global $post;
  if(isset($post->post_content) && has_shortcode( $post->post_content, 'appointify_frontend') ) {
    wp_enqueue_style( "appointify-bootstrap", plugins_url( '/assets/css/bootstrap.min.css', __FILE__ ) , array(), time(), $media = 'all' );

    wp_enqueue_style( "appointify-style", plugins_url( '/assets/css/style.css', __FILE__ ) , array(), time(), $media = 'all' );

    wp_enqueue_style( "appointify-fullcalendar", plugins_url( '/assets/css/fullcalendar.min.css', __FILE__ ) , array(), time(), $media = 'all' );
    wp_enqueue_style( 'dashicons' );

    wp_enqueue_script( "jquery-moment", plugins_url( '/assets/js/moment.min.js', __FILE__ ), array( 'jquery'), $ver = false, $in_footer = true );
    wp_enqueue_script( "jquery-fullcalendar", plugins_url( '/assets/js/fullcalendar.min.js', __FILE__ ), array('jquery'), false, false);
    wp_enqueue_script( "jquery-bootstrap", plugins_url( '/assets/js/bootstrap.min.js', __FILE__ ), array( 'jquery'), $ver = false, $in_footer = true );
  }
}
add_shortcode( 'appointify_frontend', 'appointify_frontend_shortcode' );
