(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	 $(document).ready(function(){

	    $(document).on("click",".break_check",function(){
	        var id = $(this).data('rowid');
	        if(!$(this).is(':checked')){
	          $("#td_bs_"+id).hide();
	          $("#td_be_"+id).hide();
	        }else{
	          $("#td_bs_"+id).show();
	          $("#td_be_"+id).show();
	        }
	    });
	    $(document).on("click",".day_check",function(){
	        var id = $(this).data('rowid');
	        if(!$(this).is(':checked')){
	          $("#td_bs_"+id).hide();
	          $("#td_be_"+id).hide();
	          $("#td_is_bk_"+id).hide();
	          $("#td_st_"+id).hide();
	          $("#td_et_"+id).hide();
	        }else{

	          if( $("#td_is_bk_"+id+" .break_check").is(':checked') ){
	            $("#td_bs_"+id).show();
	            $("#td_be_"+id).show();
	          }
	          $("#td_is_bk_"+id).show();
	          $("#td_st_"+id).show();
	          $("#td_et_"+id).show();
	        }
	    });
	    

	    $('#datepairExample .time').timepicker({
	    'showDuration': true,
	    'timeFormat': 'g:ia',
	    'step': '60'

	  });

	  $('#datepairExample .date').datepicker({
	  	
	    'format': 'yyyy-m-d',
	    'autoclose': true
	  });

	  // initialize datepair
	  $('#datepairExample').datepair();

	  /*Admin block Show/hide*/
	  	$( "#show_json_block" ).click(function(event) {
	      event.preventDefault();
	      $(".json-block").removeClass('appointify_admin_hide');
	      $(".google-auth-block").addClass('appointify_admin_hide');
	    });
	    $( "#show_googleauth_block" ).click(function(event) {
	      event.preventDefault();
	      $(".google-auth-block").removeClass('appointify_admin_hide');
	      $(".json-block").addClass('appointify_admin_hide');
	    });
	  });


})( jQuery );