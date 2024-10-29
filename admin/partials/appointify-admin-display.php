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
 * @subpackage Appointify/admin/partials
 */

$userdetails = wp_get_current_user();
   global $wpdb;

if($_REQUEST){

  if( isset($_REQUEST['type']) && $_REQUEST['type']=="delete_holiday"){
    $holidayid = sanitize_text_field($_REQUEST['id']);
    $delete_data_custom_holiday = 'DELETE FROM `'. $wpdb->prefix .'holidays` WHERE `id` = '.esc_attr($holidayid);

    $is_data_deleted = $wpdb->query($delete_data_custom_holiday);

    if( $is_data_deleted ){
      echo "Data deleted successfully!";
    }
  }
}

if($_POST){

  if( isset($_POST['working_hr_form']) ) {

    $working_hr = (isset($_POST['working_hr']) && !empty($_POST['working_hr']) && is_array($_POST['working_hr'])) ? wp_unslash($_POST['working_hr'] ) : array();

    $workinghrvalue = json_encode($working_hr);
    $working_hr_sani = wp_kses($workinghrvalue, array());
    if(isset($working_hr_sani) && !empty($working_hr_sani)){
        update_option( 'calendar_working_hr',  $working_hr_sani );
    }
  }

  if( isset($_POST['working_holiday']) ) {
      $date = (isset($_POST['date']) && $_POST['date']!="" )?sanitize_text_field($_POST['date']):'';
      
      $start_time = (isset($_POST['start_time']) && $_POST['start_time']!="" )?sanitize_text_field($_POST['start_time']):'';
      $end_time = (isset($_POST['end_time']) && $_POST['end_time']!="" )?sanitize_text_field($_POST['end_time']):'';


      $work_holidays_key_mixup = "calendar_working_holiday_".esc_attr($date).esc_attr($start_time).esc_attr($end_time);
     

      $table_name = $wpdb->prefix . 'holidays';
      $sqlQuery = "INSERT $table_name SET date = '".esc_attr($date)."', 
                    start_time='".date("H:i:s",strtotime(esc_attr($start_time)))."', 
                    end_time='".date("H:i:s",strtotime(esc_attr($end_time)))."'" ;
      $update_holidays = $wpdb->query( $sqlQuery );

      if( $update_holidays ){
        echo "Data Updated Successfully!!!";
      }

  }
  
  if( isset($_POST['slot_enter']) ) {
        $slot_gap = sanitize_text_field($_POST['slot_gap']);
        update_option('calendar_slot_display', esc_attr($slot_gap));
      
  }

}

?>
<h1 class="daily-title"><?php echo __( 'Daily Working Hours', 'appointify' ); ?></h1>

<div class="container-fluid">

    <div class="response"></div>
    <div class="row">
        <div class="col-sm-2 "></div>
    </div>
    <div class="row c-row">
        <div class="col-lg-8 col-md-12 col-sm-12 left-area">
            <form method="post" class="appointify-bform table-responsive">
                <table width="" cellpadding="5" cellspacing="5" class="table">
                    <tbody>
                        <tr>
                            <th rowspan="2"><?php echo __( 'Day', 'appointify' ); ?></th>
                            <th colspan="2" class="text-center"><?php echo __( 'Working Time', 'appointify' ); ?></th>
                            <th rowspan="2"><?php echo __( 'Break', 'appointify' ); ?></th>
                            <th colspan="2" class="text-center"><?php echo __( 'Break Time', 'appointify' ); ?></th>
                        </tr>
						<tr>
                            <th><?php echo __( 'Start Time', 'appointify' ); ?></th>
                            <th><?php echo __( 'End Time', 'appointify' ); ?></th>
                            <th><?php echo __( 'Start Time', 'appointify' ); ?></th>
                            <th><?php echo __( 'End Time', 'appointify' ); ?></th>
                        </tr>
                        <?php
                        $calendar_working_hr = get_option( 'calendar_working_hr');

                        if( empty($calendar_working_hr) ){
                            $working_hr_val = '{"1":{"is_working":"on","start_time":"09:00","end_time":"17:00","is_break":"on","break_start":"14:00","break_end":"15:00"},"2":{"is_working":"on","start_time":"09:00","end_time":"17:00","is_break":"on","break_start":"14:00","break_end":"15:00"},"3":{"is_working":"on","start_time":"09:00","end_time":"17:00","is_break":"on","break_start":"14:00","break_end":"16:00"},"4":{"is_working":"on","start_time":"09:00","end_time":"17:00","is_break":"on","break_start":"14:00","break_end":"15:00"},"5":{"is_working":"on","start_time":"09:00","end_time":"17:00","is_break":"on","break_start":"14:00","break_end":"15:00"},"6":{"is_working":"on","start_time":"09:00","end_time":"17:00","is_break":"on","break_start":"14:00","break_end":"15:00"},"7":{"is_working":"on","start_time":"09:00","end_time":"17:00","is_break":"on","break_start":"14:00","break_end":"15:00"}}';
                            $working_hr_val = json_decode($working_hr_val,true);
                        }else{
                            $working_hr_val = json_decode($calendar_working_hr,true);
                        }

                        $data_array = array();
                       
                        foreach($working_hr_val as $key => $data){
                          
                            $data_array[$key] = array(
                                "is_working" => $data['is_working'],
                                "start_time" => $data['start_time'],
                                "end_time" => $data['end_time'],
                                "is_break" => $data['is_break'],
                                "break_start" => $data['break_start'],
                                "break_end" => $data['break_end'],
                            );
                        }
                        
                        ?>
                        <tr>

                            <td width="15%">
                                <div class="form-group">
                                    <?php 
                            if($data_array['1']['is_working'] == 'on'){
                            $sisworking = 'checked="checked"';
                            }else{
                            $sisworking = '';
                            }
                        ?>
                                    <label for="working_hr[1][is_working]">
                                        <input type="checkbox" class="day_check" data-rowid="1"
                                            id="working_hr[1][is_working]" name="working_hr[1][is_working]" <?php echo
                                            $sisworking; ?>> Sunday</label>
                                </div>


                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_st_1" <?php echo ($data_array['1']['is_working']=='on'
                                    )?"":"style='display:none;'" ?>>
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[1][start_time]" id="working_hr[1][start_time]"
                                        value="<?php echo esc_attr($data_array['1']['start_time']); ?>">
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_et_1" <?php echo ($data_array['1']['is_working']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[1][end_time]" id="working_hr[1][end_time]"
                                        value="<?php echo esc_attr($data_array['1']['end_time']); ?>">
                                </div>
                            </td>

                            <?php 
                            if($data_array['1']['is_break'] == 'on'){
                            $sisbreak = 'checked="checked"';
                            }else{
                            $sisbreak = '';
                            }

                        ?>

                            <td width="5%">
                                <div class="form-group " id="td_is_bk_1">
                                    <label for="working_hr[1][is_break]">
                                        <input type="checkbox" class="break_check" data-rowid="1"
                                            id="working_hr[1][is_break]" name="working_hr[1][is_break]" <?php echo
                                            $sisbreak; ?>></label>
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_bs_1" <?php echo ($data_array['1']['is_break']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[1][break_start]" id="working_hr[1][break_start]"
                                        value="<?php echo esc_attr( $data_array['1']['break_start']); ?>">
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_be_1" <?php echo ($data_array['1']['is_break']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[1][break_end]" id="working_hr[1][break_end]"
                                        value="<?php echo esc_attr( $data_array['1']['break_end']); ?>">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <?php 
                            if($data_array['2']['is_working'] == 'on'){
                            $misworking = 'checked="checked"';
                            }else{
                            $misworking = '';
                            }
                        ?>
                            <td width="15%">
                                <div class="form-group">
                                    <label for="working_hr[2][is_working]">
                                        <input type="checkbox" class="day_check" data-rowid="2"
                                            id="working_hr[2][is_working]" name="working_hr[2][is_working]" <?php echo
                                            $misworking; ?>> Monday</label>
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_st_2" <?php echo ($data_array['2']['is_working']=='on'
                                    )?"":"style='display:none;'" ?>>
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[2][start_time]" id="working_hr[2][start_time]"
                                        value="<?php echo esc_attr($data_array['2']['start_time']); ?>">
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_et_2" <?php echo ($data_array['2']['is_working']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[2][end_time]" id="working_hr[2][end_time]"
                                        value="<?php echo esc_attr($data_array['2']['end_time']); ?>">
                                </div>
                            </td>
                            <?php 
                            if($data_array['2']['is_break'] == 'on'){
                            $misbreak = 'checked="checked"';
                            }else{
                            $misbreak = '';
                            }
                        ?>
                            <td width="5%">
                                <div class="form-group " id="td_is_bk_2">
                                    <label for="working_hr[2][is_break]">
                                        <input type="checkbox" class="break_check" data-rowid="2"
                                            id="working_hr[2][is_break]" name="working_hr[2][is_break]" <?php echo
                                            $misbreak; ?>></label>
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_bs_2" <?php echo ($data_array['2']['is_break']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[2][break_start]" id="working_hr[2][break_start]"
                                        value="<?php echo esc_attr($data_array['2']['break_start']); ?>">
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_be_2" <?php echo ($data_array['2']['is_break']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[2][break_end]" id="working_hr[2][break_end]"
                                        value="<?php echo esc_attr($data_array['2']['break_end']); ?>">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <?php 
                            if($data_array['3']['is_working'] == 'on'){
                            $tisworking = 'checked="checked"';
                            }else{
                            $tisworking = '';
                            }
                        ?>
                            <td width="15%">
                                <div class="form-group">
                                    <label for="working_hr[3][is_working]">
                                        <input type="checkbox" class="day_check" data-rowid="3"
                                            id="working_hr[3][is_working]" name="working_hr[3][is_working]" <?php echo
                                            $tisworking; ?>> Tuesday</label>
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_st_3" <?php echo ($data_array['3']['is_working']=='on'
                                    )?"":"style='display:none;'" ?>>
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[3][start_time]" id="working_hr[3][start_time]"
                                        value="<?php echo esc_attr($data_array['3']['start_time']); ?>">
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_et_3" <?php echo ($data_array['3']['is_working']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[3][end_time]" id="working_hr[3][end_time]"
                                        value="<?php echo esc_attr($data_array['3']['end_time']); ?>">
                                </div>
                            </td>

                            <?php 
                            if($data_array['3']['is_break'] == 'on'){
                            $tisbreak = 'checked="checked"';
                            }else{
                            $tisbreak = '';
                            }
                        ?>
                            <td width="5%">
                                <div class="form-group " id="td_is_bk_3">
                                    <label for="working_hr[3][is_break]">
                                        <input type="checkbox" class="break_check" data-rowid="3"
                                            id="working_hr[3][is_break]" name="working_hr[3][is_break]" <?php echo
                                            $tisbreak; ?>></label>
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_bs_3" <?php echo ($data_array['3']['is_break']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[3][break_start]" id="working_hr[3][break_start]"
                                        value="<?php echo esc_attr($data_array['3']['break_start']); ?>">
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_be_3" <?php echo ($data_array['3']['is_break']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[3][break_end]" id="working_hr[3][break_end]"
                                        value="<?php echo esc_attr($data_array['3']['break_end']); ?>">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <?php 
                            if($data_array['4']['is_working'] == 'on'){
                            $wisworking = 'checked="checked"';
                            }else{
                            $wisworking = '';
                            }
                        ?>
                            <td width="15%">
                                <div class="form-group">
                                    <label for="working_hr[4][is_working]">
                                        <input type="checkbox" class="day_check" data-rowid="4"
                                            id="working_hr[4][is_working]" name="working_hr[4][is_working]" <?php echo
                                            $wisworking; ?>> Wednesday</label>
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_st_4" <?php echo ($data_array['4']['is_working']=='on'
                                    )?"":"style='display:none;'" ?>>
                                     <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[4][start_time]" id="working_hr[4][start_time]"
                                        value="<?php echo esc_attr($data_array['4']['start_time']); ?>">
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_et_4" <?php echo ($data_array['4']['is_working']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[4][end_time]" id="working_hr[4][end_time]"
                                        value="<?php echo esc_attr($data_array['4']['end_time']); ?>">
                                </div>
                            </td>
                            <?php 
                            if($data_array['4']['is_break'] == 'on'){
                            $wisbreak = 'checked="checked"';
                            }else{
                            $wisbreak = '';
                            }
                        ?>
                            <td width="5%">
                                <div class="form-group " id="td_is_bk_4">
                                    <label for="working_hr[4][is_break]">
                                        <input type="checkbox" class="break_check" data-rowid="4"
                                            id="working_hr[4][is_break]" name="working_hr[4][is_break]" <?php echo
                                            $wisbreak; ?>></label>
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_bs_4" <?php echo ($data_array['4']['is_break']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[4][break_start]" id="working_hr[4][break_start]"
                                        value="<?php echo esc_attr( $data_array['4']['break_start']); ?>">
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_be_4" <?php echo ($data_array['4']['is_break']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[4][break_end]" id="working_hr[4][break_end]"
                                        value="<?php echo esc_attr($data_array['4']['break_end']); ?>">
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <?php 
                            if($data_array['5']['is_working'] == 'on'){
                            $thisworking = 'checked="checked"';
                            }else{
                            $thisworking = '';
                            }
                        ?>
                            <td width="15%">
                                <div class="form-group">
                                    <label for="working_hr[5][is_working]">
                                        <input type="checkbox" class="day_check" data-rowid="5"
                                            id="working_hr[5][is_working]" name="working_hr[5][is_working]" <?php echo
                                            $thisworking; ?>> Thursday</label>
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_st_5" <?php echo ($data_array['5']['is_working']=='on'
                                    )?"":"style='display:none;'" ?>>
                                     <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[5][start_time]" id="working_hr[5][start_time]"
                                        value="<?php echo esc_attr($data_array['5']['start_time']); ?>">
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_et_5" <?php echo ($data_array['5']['is_working']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[5][end_time]" id="working_hr[5][end_time]"
                                        value="<?php echo esc_attr($data_array['5']['end_time']); ?>">
                                </div>
                            </td>

                            <?php 
                            if($data_array['5']['is_break'] == 'on'){
                            $thisbreak = 'checked="checked"';
                            }else{
                            $thisbreak = '';
                            }
                        ?>
                            <td width="5%">
                                <div class="form-group " id="td_is_bk_5">
                                    <label for="working_hr[5][is_break]">
                                        <input type="checkbox" class="break_check" data-rowid="5"
                                            id="working_hr[5][is_break]" name="working_hr[5][is_break]" <?php echo
                                            $thisbreak; ?>></label>
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_bs_5" <?php echo ($data_array['5']['is_break']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[5][break_start]" id="working_hr[5][break_start]"
                                        value="<?php echo esc_attr($data_array['5']['break_start']); ?>">
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_be_5" <?php echo ($data_array['5']['is_break']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[5][break_end]" id="working_hr[5][break_end]"
                                        value="<?php echo esc_attr($data_array['5']['break_end']); ?>">
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <?php 
                            if($data_array['6']['is_working'] == 'on'){
                            $fisworking = 'checked="checked"';
                            }else{
                            $fisworking = '';
                            }
                        ?>
                            <td width="15%">
                                <div class="form-group">
                                    <label for="working_hr[6][is_working]">
                                        <input type="checkbox" class="day_check" data-rowid="6"
                                            id="working_hr[6][is_working]" name="working_hr[6][is_working]" <?php echo
                                            $fisworking; ?>> Friday</label>
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_st_6" <?php echo ($data_array['6']['is_working']=='on'
                                    )?"":"style='display:none;'" ?>>
                                     <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[6][start_time]" id="working_hr[6][start_time]"
                                        value="<?php echo esc_attr($data_array['6']['start_time']); ?>">
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_et_6" <?php echo ($data_array['6']['is_working']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[6][end_time]" id="working_hr[6][end_time]"
                                        value="<?php echo esc_attr($data_array['6']['end_time']); ?>">
                                </div>
                            </td>

                            <?php 
                            if($data_array['6']['is_break'] == 'on'){
                            $fhisbreak = 'checked="checked"';
                            }else{
                            $fhisbreak = '';
                            }
                        ?>
                            <td width="5%">
                                <div class="form-group " id="td_is_bk_6">
                                    <label for="working_hr[6][is_break]">
                                        <input type="checkbox" class="break_check" data-rowid="6"
                                            id="working_hr[6][is_break]" name="working_hr[6][is_break]" <?php echo
                                            $fhisbreak; ?>></label>
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_bs_6" <?php echo ($data_array['6']['is_break']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[6][break_start]" id="working_hr[6][break_start]"
                                        value="<?php echo esc_attr($data_array['6']['break_start']); ?>">
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_be_6" <?php echo ($data_array['6']['is_break']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[6][break_end]" id="working_hr[6][break_end]"
                                        value="<?php echo esc_attr($data_array['6']['break_end']); ?>">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <?php 
                            if($data_array['7']['is_working'] == 'on'){
                            $saisworking = 'checked="checked"';
                            }else{
                            $saisworking = '';
                            }
                        ?>
                            <td width="15%">
                                <div class="form-group">
                                    <label for="working_hr[7][is_working]">
                                        <input type="checkbox" class="day_check" data-rowid="7"
                                            id="working_hr[7][is_working]" name="working_hr[7][is_working]" <?php echo
                                            $saisworking; ?>> Saturday</label>
                                </div>
                            </td>

                            <td width="20%">
                                <div class="form-group " id="td_st_7" <?php echo ($data_array['7']['is_working']=='on'
                                    )?"":"style='display:none;'" ?>>
                                     <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[7][start_time]" id="working_hr[7][start_time]"
                                        value="<?php echo esc_attr($data_array['7']['start_time']); ?>">
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_et_7" <?php echo ($data_array['7']['is_working']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[7][end_time]" id="working_hr[7][end_time]"
                                        value="<?php echo esc_attr($data_array['7']['end_time']); ?>">
                                </div>
                            </td>

                            <?php 
                            if($data_array['7']['is_break'] == 'on'){
                            $sahisbreak = 'checked="checked"';
                            }else{
                            $sahisbreak = '';
                            }
                        ?>

                            <td width="5%">
                                <div class="form-group " id="td_is_bk_7">
                                    <label for="working_hr[7][is_break]">
                                        <input type="checkbox" class="break_check" data-rowid="7"
                                            id="working_hr[7][is_break]" name="working_hr[7][is_break]" <?php echo
                                            $sahisbreak; ?>></label>
                                </div>
                            </td>


                            <td width="20%">
                                <div class="form-group " id="td_bs_7" <?php echo ($data_array['7']['is_break']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[7][break_start]" id="working_hr[7][break_start]"
                                        value="<?php echo esc_attr($data_array['7']['break_start']); ?>">
                                </div>
                            </td>
                            <td width="20%">
                                <div class="form-group " id="td_be_7" <?php echo ($data_array['7']['is_break']=='on'
                                    )?"":"style='display:none;'" ?>>
                              
                                    <input type="time" class="form-control" placeholder="Enter URL" required=""
                                        name="working_hr[7][break_end]" id="working_hr[7][break_end]"
                                        value="<?php echo esc_attr($data_array['7']['break_end']); ?>">
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="submit" name="working_hr_form" class="btn btn-primary">Submit</button>
            </form>
        </div>

        <div class="col-lg-3 col-md-12 col-sm-12 sidebar-area" id="datepairExample">
            <h4 class="slot-title">Slot gap set</h4>
            <form method="post" class="slot_settting_set">
                <div class="col-sm-12" >
                    <div class="form-group">
                        <label for="date">Enter Slot gap:</label>
                        <?php $slot_gap = get_option( 'calendar_slot_display');
                      
                      if( $slot_gap != "" ){
                          
                      ?>

                        <select name="slot_gap">
                            <option value="1" <?php if($slot_gap[0]=='1' ){ echo 'selected=selected' ;} else { echo ' '
                                ;}?>>1</option>
                            <option value="2" <?php if($slot_gap[0]=='2' ){ echo 'selected=selected' ;} else { echo ' '
                                ;}?>>2</option>
                            <option value="3" <?php if($slot_gap[0]=='3' ){ echo 'selected=selected' ;} else { echo ' '
                                ;}?>>3</option>
                            <option value="4" <?php if($slot_gap[0]=='4' ){ echo 'selected=selected' ;} else { echo ' '
                                ;}?>>4</option>
                            <option value="5" <?php if($slot_gap[0]=='5' ){ echo 'selected=selected' ;} else { echo ' '
                                ;}?>>5</option>
                            <option value="6" <?php if($slot_gap[0]=='6' ){ echo 'selected=selected' ;} else { echo ' '
                                ;}?>>6</option>
                        </select>

                        <?php }else{
                      ?>
                        <select name="slot_gap">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="5">6</option>
                        </select>

                        <?php
                      } ?>
                    <button type="submit" name="slot_enter" class="btn btn-primary">Save</button>

                    </div>
                </div>
                <br><br><br>
            </form>
            <form method="post" class="extra_days_leave">
                <h4 class="title-holiday">Add Extra holiday</h4>

                <div class="form-group">
                    <label for="date">Enter Date:</label>
                    <input type="text" required="" class="form-control date start" placeholder="Date" name="date"
                        id="date" autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="time">Start Time:</label>
                    <input type="text" required="" class="form-control time start ui-timepicker-input"
                        placeholder="Time" name="start_time" id="start_time" autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="time">End Time:</label>
                    <input type="text" required="" class="form-control time end ui-timepicker-input" placeholder="Time"
                        name="end_time" id="end_time" autocomplete="off">
                </div>

                <button type="submit" name="working_holiday" class="btn btn-primary">Save</button>

                <br><br>

                <?php
                    $query_custom_holidays = "SELECT * FROM {$wpdb->prefix}holidays";
                    $list_of_user_meta_data = $wpdb->get_results($query_custom_holidays);
                ?>

                <div class="table-responsive">
					<table style="width: 100%" class="table sidebar-table">
                    <tbody>
                        <tr>
                            <th>Date</th>
                            <th>Start </th>
                            <th>End </th>
                            <th>Action</th>
                        </tr>
                        <?php 
                            foreach ($list_of_user_meta_data as $value) {
                              ?>
                        <tr>
                            <td>
                                <?php echo date("Y-m-d", strtotime($value->date)); ?>
                            </td>
                            <td>
                                <?php echo date("h:i A",strtotime($value->start_time)); ?>
                            </td>
                            <td>
                                <?php echo date("h:i A",strtotime($value->end_time)); ?>
                            </td>
                            <td><a class="btn btn-xs btn-danger"
                                    href="?page=appointify&type=delete_holiday&id=<?php echo esc_attr( $value->id); ?>">x</a></td>

                        </tr>
                        <?php 
                            }
                        ?>
                    </tbody>
                </table>
				</div>
            </form>
        </div>
    </div>
</div>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->