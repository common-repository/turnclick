<?php
namespace turnclickApp;

/************************************************************************************/
class turnclick{
	public $turnclick_customer_id, $turnclick_customer_email, $message;
	

	/*****************************************/
	function __construct(){
		$this->turnclick_customer_id = sanitize_text_field(get_option('turnclick_customer_id'));
		$this->turnclick_customer_email = sanitize_email(get_option('turnclick_customer_email'));
	}
	/*****************************************/


	/*****************************************/
	private function lookup_turnclick_email(){
		$post_url = 'http://www.turnclick.com/api/public/v1/tools/check_email';
		$data = array(
			'email' => $this->turnclick_customer_email
		);

		$data = json_encode($data);

		$args = array('headers' => array('Content-Type' => 'application/json'), 'body' => $data);
		$response = wp_remote_post(esc_url_raw($post_url), $args);
		$response_code = wp_remote_retrieve_response_code($response);
		$response_body = wp_remote_retrieve_body($response);

		if(!in_array($response_code, array(200,201)) || is_wp_error($response_body)){
			$data = array(
				'result' => false,
				'message' => "I'm having problems talking with the turnclick servers. Please contact us for help at help@turnclick.com"
			);
		}
		else{
			$data = json_decode($response_body, true);
		}

		return $data;
	}
	/*****************************************/


	/*****************************************/
	function process_form(){
		if($_POST['turnclick_action'] == "save"){
			$proceed = true;
	
			$email = $_POST['turnclick_customer_email'];
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
				$this->message = '<p style="color:#f51490;font-weight:bold;">Oops, there seems to be a problem with that email address.</p>';
				$proceed = false;
			}

			if($proceed){
				$this->turnclick_customer_email = sanitize_email($email);
				$check = $this->lookup_turnclick_email();

				if($check['result']){
					$this->turnclick_customer_id = sanitize_text_field($check['turnclick_customer_id']);
					
					update_option('turnclick_customer_id', $this->turnclick_customer_id);
					update_option('turnclick_customer_email', $this->turnclick_customer_email);
					
					$this->message = '<p style="font-weight:bold;">You\'re all set! The TurnClick embed code is now active on your site. <a href="https://www.turnclick.com/dashboard">Go to your TurnClick Dashboard &rarr;</a></p>';
				}
				else{
					$this->turnclick_customer_email = '';
					$this->message = '<p style="color:#f51490;font-weight:bold;">'.sanitize_text_field($check['message']).'</p>';
				}
			}
		}
		
		if($_POST['turnclick_action'] == "delete"){
			$this->turnclick_customer_email = '';
			$this->turnclick_customer_id = '';

			delete_option('turnclick_customer_id');
			delete_option('turnclick_customer_email');
		}
	}
	/*****************************************/


	/*****************************************/
	function build_form() {
		print '<div class="wrap">';
		print '	<form name="form1" method="post" action="">';
		print '	<h2>TurnClick Embed Code Settings</h2>';

		if(empty($this->turnclick_customer_email)){
			$email_from_post = '';
			if(isset($_POST['turnclick_customer_email'])){
				$email_from_post = sanitize_text_field($_POST['turnclick_customer_email']);
			}

			print '	<input type="hidden" name="turnclick_action" value="save">';
			print $this->message;
			print '	<table class="form-table">';
			print '		<tbody>';
			print '			<tr>';
			print '				<td>';
			print '					<label for="turnclick_customer_email"><b>TurnClick Username (email address):</b></label>';
			print '					&nbsp;<input name="turnclick_customer_email" value="'.$email_from_post.'" type="text" id="turnclick_customer_email" aria-describedby="turnclick_customer_email-description" class="regular-text">';
			print '					<p class="description" id="turnclick_customer_email-description">Enter your TurnClick username (your email address) and we\'ll take care of the rest. (Your email will only be used to link to your TurnClick account.)</p>';
			print '				</td>';
			print '			</tr>';
			print '		</tbody>';
			print '	</table>';
			print '	<p class="submit"><input type="submit" name="Submit" class="button-primary" value="Save Settings" /></p>';
		}
		else{
			print '	<input type="hidden" name="turnclick_action" value="delete">';
			print $this->message;
			print '	<table class="form-table">';
			print '		<tbody>';
			print '			<tr>';
			print '				<td>';
			print '					<p>This WordPress site is linked to the TurnClick account registered to: <b>'.$this->turnclick_customer_email.'</b>.</p>';
			print '					<p>To switch TurnClick accounts, press the "Unlink Account" button below and enter a new username.</p>';
			print '				</td>';
			print '			</tr>';
			print '		</tbody>';
			print '	</table>';
			print '	<p class="submit"><input type="submit" name="Submit" class="button-primary" value="Unlink Account" /></p>';
		}

		print '</form>';
		print '</div>';
	}
	/*****************************************/

}
/************************************************************************************/
