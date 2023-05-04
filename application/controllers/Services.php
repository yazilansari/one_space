<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Services extends CI_Controller {

	public function verify_mobile_number()
	{
		$mobile_no = $this->input->post('mobile_no');
		if(!empty($mobile_no)) {
			$q = $this->db->where('telephone', $mobile_no)->get('os_customer');
			$response = array();
			if($q->num_rows() > 0) {
				$otp = rand(100000, 999999);
				$message = $otp.' is your Onespace Verification Code. Enjoy Making Your Dream Space With Onespace';
	            $apiKey = urlencode('6348eda2cf8cf');
	            // Message details
	            $numbers = array($mobile_no);
	            $sender = urlencode('ONESPC');
	            $message = rawurlencode($message);

	            $numbers = implode(',', $numbers);

	            // Prepare data for POST request
	            $data = array('apikey' => $apiKey, 'mobileno' => $numbers, 'sender' => $sender, 'text' => $message);

	            $curl = curl_init();

	            curl_setopt_array($curl, array(
	                CURLOPT_URL => 'http://sms.mobileadz.in/api/push.json?apikey='.$apiKey.'&sender='.$sender.'&mobileno='.$numbers.'&text='.$message,
	                CURLOPT_RETURNTRANSFER => true,
	                CURLOPT_ENCODING => '',
	                CURLOPT_MAXREDIRS => 10,
	                CURLOPT_TIMEOUT => 0,
	                CURLOPT_FOLLOWLOCATION => true,
	                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	                CURLOPT_CUSTOMREQUEST => 'GET',
	            ));

	            $curl_response = curl_exec($curl);
	            $decoded_res = json_decode($curl_response);
	            // echo "<pre>";print_r($decoded_res);die();
	            if($decoded_res && $decoded_res->status == 'success') {

	            	$this->db->where('telephone', $mobile_no)->update('os_customer', array('otp' => $otp));

	            	$response['success'] = true;
					$response['message'] = 'Your OTP has been Sent.';
					header('Content-Type: application/json; charset=utf-8');
					echo json_encode($response);
	            } else {
	            	$response['success'] = false;
					$response['message'] = 'Error Occurred While Sending OTP.';
					header('Content-Type: application/json; charset=utf-8');
					echo json_encode($response);
	            }
		        curl_close($curl);
			} else {
				$response['success'] = false;
				$response['message'] = 'Your Mobile Number is Not Registered.';
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($response);
			}
		} else {
			$response['success'] = false;
			$response['message'] = 'Please Enter Mobile Number.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);
		}
	}

	public function verify_otp()
	{
		$mobile_no = $this->input->post('mobile_no');
		$otp = $this->input->post('otp');
		if(empty($mobile_no)) {
			$response['success'] = false;
			$response['message'] = 'Please Enter Mobile Number.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($otp)) {
			$response['success'] = false;
			$response['message'] = 'Please Enter OTP.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		$q = $this->db->select('customer_id, firstname, lastname, email, telephone')->where(array('telephone' => $mobile_no, 'otp' => $otp))->get('os_customer');
		$response = array();
		if($q->num_rows() > 0) {

			$this->db->where('telephone', $mobile_no)->update('os_customer', array('otp' => 0));

			$response['success'] = true;
			$response['message'] = 'Logged in Successfully.';
			$response['response'] = $q->row();
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);
		} else {
			$response['success'] = false;
			$response['message'] = 'Invalid OTP.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);
		}
	}

	public function fetch_cities()
	{
		$q = $this->db->select('id, city')->where('status', 1)->get('os_signup_location');
		$response = array();
		if($q->num_rows() > 0) {

			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			$response['response'] = $q->result();
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);
		} else {
			$response['success'] = false;
			$response['message'] = 'No City Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);
		}
	}
}
