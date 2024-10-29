<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       appointify.app
 * @since      1.0.0
 *
 * @package    Appointify
 * @subpackage PluginName/admin/partials
 */

$userdetails = wp_get_current_user();
   global $wpdb;

if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');
$json_file_inserted = get_option( 'json_file_inserted' );

if(isset($json_file_inserted) && !empty($json_file_inserted) && $json_file_inserted != 1){ 
  $jsonblock = "appointify_admin_hide";
}else{
  $jsonblock = "appointify_admin_show";
}
if(isset($_POST['submit_json'])){
      
        $wordpress_upload_dir = wp_upload_dir();
        
        $i = 1;

        if(!isset($_POST['upload_json_file']) || !is_string($_POST['upload_json_file']) || $_POST['upload_json_file'] == ''){
          
          $file_name = sanitize_file_name($_FILES['upload_json_file']['name']);
          $check_filetype = wp_check_filetype($_FILES['upload_json_file']['name']);
          $file_type = sanitize_text_field($check_filetype['type']);
          $temp_name = sanitize_text_field($_FILES['upload_json_file']['tmp_name']);
              $new_file_path = ABSPATH. '/' . $file_name;
              $file_url = $wordpress_upload_dir['url']. '/' . $file_name;
              if( file_exists( $new_file_path ) ) {
                  $i++;
                  $new_file_path = ABSPATH. '/' . $i . '_' . $file_name;
              }
             
              if( move_uploaded_file( $temp_name, $new_file_path ) ) {
                  $attachment = array(
                      'guid'           => $file_url, 
                      'post_mime_type' => $file_type,
                      'post_title'     => $file_name,
                      'post_content'   => '',
                      'post_parent'    => '',
                      'post_status'    => 'inherit'
                  );
                $credentials_data = file_get_contents($new_file_path);
                if ( isset($credentials_data) &&  !empty( $credentials_data ) ) {
                  $option_name = 'json_file_inserted' ;
                  if ( get_option( $option_name ) !== false ) {
                    update_option( $option_name, $credentials_data );  
                  }else {
                      $deprecated = null;
                      $autoload = 'no';
                      add_option( $option_name, $credentials_data, $deprecated, $autoload );
                  }
                  unlink($new_file_path);
                }  
                 
              }
        }
        
       
        if(!isset($_POST['upload_json_file_front']) || !is_string($_POST['upload_json_file_front']) || $_POST['upload_json_file_front'] == ''){

            $file_name = sanitize_file_name($_FILES['upload_json_file_front']['name']);
            $check_filetype = wp_check_filetype($_FILES['upload_json_file']['name']);
            $file_type = sanitize_text_field($check_filetype['type']);
            $temp_name = sanitize_text_field($_FILES['upload_json_file_front']['tmp_name']);

              $new_file_path = ABSPATH. '/'. $file_name;
              $file_url = $wordpress_upload_dir['url']. '/' . $file_name;
              if( file_exists( $new_file_path ) ) {
                  $i++;
                  $new_file_path = ABSPATH. '/' . $i . '_' . $file_name;
              }
              if( move_uploaded_file( $temp_name, $new_file_path ) ) {
                  $attachment = array(
                      'guid'           => $file_url, 
                      'post_mime_type' => $file_type,
                      'post_title'     => $file_name,
                      'post_content'   => '',
                      'post_parent'    => '',
                      'post_status'    => 'inherit'
                  );
                $credentials_data_front = file_get_contents($new_file_path);

                if ( isset($credentials_data_front) &&  !empty( $credentials_data_front ) ) {
                  $option_name = 'json_front_file_inserted' ;
                  if ( get_option( $option_name ) !== false ) {
                    update_option( $option_name, $credentials_data_front );  
                  }else {
                      $deprecated = null;
                      $autoload = 'no';
                      add_option( $option_name, $credentials_data_front, $deprecated, $autoload );
                  }
                  unlink($new_file_path);
                }
                 
              }
        }
        echo __( 'Data inserted successfully !!', 'appointify' );
        echo '<script type="text/javascript">location.reload(true);</script>';
    }
?>

<div class="wrap custom-wrap json-block <?php echo esc_attr($jsonblock); ?>">
  <h2 class="title-settings"><?php  echo __( 'Appointify JSON Settings', 'appointify' ); ?> </h2>
  
  <form method="POST" enctype="multipart/form-data">      
    <table class="form-table cust-form-table" role="presentation">
      <tbody>
        <tr>
          <th scope="row"><?php  echo __( 'Json file upload', 'appointify' ); ?> </th>
          <td class="fields">
            <p class="caption-label">
              <?php  echo __( 'Add Config File first to make the calendar configuration working with setting displaying.', 'appointify' ); ?>
            </p>
            <input type="file" name="upload_json_file" id="upload_json_file" class="upload_json_file" accept=".json" required/>
            <p class="caption-label"><?php  echo __( 'Upload google json config file from google cloud console.', 'appointify' ); ?>  <a href="https://console.cloud.google.com/apis/credentials/oauthclient"><?php  echo __( 'Get Google cloud json file', 'appointify' ); ?></a></p>
            <p><b><?php  echo __( 'File name should be -', 'appointify' ); ?> client_secret.json</b></p>
          </td>
        </tr>
        <tr>
          <th scope="row"><?php  echo __( 'Json file upload for frontend calendar', 'appointify' ); ?> </th>
          <td class="fields">
            <p class="caption-label">
              <?php  echo __( 'Add Config File first to make the calendar configuration working with calender displaying on frontend.', 'appointify' ); ?>
            </p>
            <input type="file" name="upload_json_file_front" id="upload_json_file_front" class="upload_json_file_front" accept=".json" required/>
            <p class="caption-label"><?php  echo __( 'Upload google json config file from google cloud console.', 'appointify' ); ?>  <a href="https://console.cloud.google.com/apis/credentials/oauthclient"><?php  echo __( 'Get Google cloud json file', 'appointify' ); ?></a></p>
            <p><b><?php  echo __( 'File name should be -', 'appointify' ); ?> client_secret_front_end.json</b></p>
          </td>
        </tr>
    </tbody>
    </table>      
      <p class="submit">
        <input type="submit"  name="submit_json" id="submit" class="button button-primary" value="Save Changes">
      </p>
      
  </form>
  <?php if(isset($json_file_inserted) && !empty($json_file_inserted) && $json_file_inserted != 1){ ?>
    <div class="shortcode-copy">
        <table class="form-table" role="presentation">
          <tbody>
            <tr>
              <th scope="row"><?php  echo __( 'Click Here if you want to login into google.', 'appointify' ); ?></th>
                <td><a id="show_googleauth_block" href="#" class="button button-primary "><?php  echo __( 'Show Google Login', 'appointify' ); ?></a> 
                </td>
            </tr>
          </tbody>
        </table>
    </div>
  <?php } ?>
</div>
<?php

if(isset($json_file_inserted) && !empty($json_file_inserted) && $json_file_inserted != 1){
    $credentials_file_data = '' ;
    $credentials_file_data = json_decode($json_file_inserted, true) ;


include ( APPOINTIFY_PLUGIN_PATH . 'includes/calendar_google/vendor/autoload.php' );

  $client = new Google_Client();
  $client->setApplicationName('Calendar');
  $client->setScopes(Google_Service_Calendar::CALENDAR);
  $client->setAuthConfig($credentials_file_data);
  $client->setAccessType('offline');
  $client->setPrompt('select_account consent');   
  $client->setApprovalPrompt('force');

  $wp_user_data = $wpdb->prefix . "user_data";
  $resultstoken = $wpdb->get_results("SELECT * FROM $wp_user_data WHERE status = 1 ORDER BY id DESC");

    $selfUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')?"https://":"http://";   
    $selfUrl .= $_SERVER['HTTP_HOST'];   
    $selfUrl .= $_SERVER['REQUEST_URI'];
    $selfUrl = preg_replace('~(\?|&)code=[^&]*~','$1',$selfUrl);
if($resultstoken && count( $resultstoken ) > 0 ){

  $access_token = $resultstoken[0]->token_data;
  $accessToken = json_decode($access_token, true);

  $client->setAccessToken($accessToken);

   if ($client->isAccessTokenExpired()) {  
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) { 
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $token_arr = addslashes( json_encode($client->getAccessToken()),  );
            $sqlInsert = "INSERT INTO $wp_user_data SET token_data = '".wp_kses($token_arr, array())."' ";
      $rows_affected = $wpdb->query($sqlInsert);
        }
    }

  
  $service = new Google_Service_Calendar($client);
 
}

if(isset($_POST['calendar_id'])) {

  $calendar_id = (isset($_POST['calendar_id']) && !empty($_POST['calendar_id']))?sanitize_text_field($_POST['calendar_id']):'';
  $sqlUpdate = "UPDATE $wp_user_data SET calendar_id='".esc_attr($calendar_id)."' WHERE status=1";

  $result2 = $wpdb->query($sqlUpdate);

}

// Google passes a parameter 'code' in the Redirect Url
if(isset($_GET['code'])) {

  $authCode = sanitize_text_field($_GET['code']);

  $accessToken = $client->fetchAccessTokenWithAuthCode(esc_attr($authCode));
    $clientaccess = $client->setAccessToken($accessToken);
  $token_arr = addslashes( json_encode($client->getAccessToken()) );
  $sqlUpdate = "UPDATE $wp_user_data SET status=0";

  $result2 = $wpdb->query($sqlUpdate);
  if(isset($token_arr) && !empty($token_arr)){
    $sqlInsert = "INSERT INTO $wp_user_data SET token_data = '".wp_kses($token_arr, array())."' ";
    $rows_affected = $wpdb->query($sqlInsert);
    $selfUrlred = esc_url_raw($selfUrl);
    echo "<script>window.location.href='".esc_url($selfUrlred)."';</script>";
    exit();
  }
}else{

   if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
          $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $token_arr = addslashes( json_encode($client->getAccessToken()) );
            if(isset($token_arr) && !empty($token_arr)){
              $sqlInsert = "INSERT INTO $wp_user_data SET token_data = '".wp_kses($token_arr, array())."' ";
              $rows_affected = $wpdb->query($sqlInsert);
            }
        }
    }
}

  if(isset($json_file_inserted) && !empty($json_file_inserted) && $json_file_inserted != 1){ 
    $jsonblock = "appointify_admin_show";
  }else{
    $jsonblock = "appointify_admin_hide";
  }
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap custom-wrap google-auth-block <?php echo esc_attr($jsonblock); ?>">
  <div id="icon-themes" class="icon32"></div>  

  <h2 class="title-settings"><?php  echo __( 'Appointify Settings', 'appointify' ); ?></h2>  
  <!-- <h5>If you want Add event in google calendar please login in google</h5> -->
  <?php $login_url  = $client->createAuthUrl(); 
  $login_text = __( 'Connect With Google', 'appointify' );
  if( !empty($service) && !$client->isAccessTokenExpired() ){
      $calendarList = $service->calendarList->listCalendarList(array('maxResults'=>500));
      $login_text =  __( 'Connect With Another ID', 'appointify' );
    }
   ?>      

  <form method="POST" >  
    <table class="form-table cust-form-table" role="presentation">
      <tbody>
        <tr>
          <th scope="row"><?php  echo __( 'Login with Google', 'appointify' ); ?></th>
          <td><a href="<?php echo esc_url($login_url); ?>" class="button button-primary"><?php  echo esc_attr($login_text); ?></a>  
            <p class="caption-label"> <?php  echo __( 'If you want Add event in google calendar please login in google.', 'appointify' ); ?></p>
          </td>
        </tr>

        <tr>
          <th scope="row"><?php  echo __( 'Select Calender', 'appointify' ); ?></th>
          <td>
            <select class="form-control" name="calendar_id" id="calendar_id">
              <option value="" ><?php  echo __( 'Select Calender', 'appointify' ); ?></option>
              <?php if( !empty($calendarList) ){
                        
                        $resultsquery = $wpdb->get_results("SELECT * FROM $wp_user_data WHERE status = 1 ORDER BY id");
                          $eventArray = array();
                          foreach ($calendarList->getItems() as $calendarListEntry) {
                            ?>
                            <option value="<?php echo esc_attr($calendarListEntry->getId()); ?>" <?php echo ($calendarListEntry->getId()==$resultsquery[0]->calendar_id)?"SELECTED":""; ?>><?php echo esc_attr($calendarListEntry->getSummary()); ?></option>
                            <?php 
                          }
                          $pageToken = $calendarList->getNextPageToken();
                          if ($pageToken) {
                            $optParams = array('pageToken' => $pageToken);
                            $calendarList = $service->calendarList->listCalendarList($optParams);
                          }
                      } ?>
            </select>
          </td>
          </tr>     
        </tbody>
    </table>
    <p class="submit">
      <input type="submit"  name="submit" id="submit" class="button button-primary" value="Save Changes">
    </p>
  </form>   
  <div class="shortcode-copy">
      <table class="form-table" role="presentation">
        <tbody>
          <tr>
            <th scope="row"><?php  echo __( 'Shortcode for google calendar to display on frontend', 'appointify' ); ?></th>
              <td><input type="text" value="[appointify_frontend]"></td>
          </tr>
          <tr>
            <th scope="row"><?php  echo __( 'Click Here if you want to re-upload the json files.', 'appointify' ); ?></th>
              <td><a id="show_json_block" href="#" class="button button-primary "><?php  echo __( 'Re-upload JSON', 'appointify' ); ?></a> 
              </td>
          </tr>
        </tbody>
      </table>
  </div>
</div>
<?php
}