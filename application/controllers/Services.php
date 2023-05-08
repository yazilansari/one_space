<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Services extends CI_Controller {

	public function verify_mobile_number()
	{
		$mobile_no = $this->input->post('mobile_no');
		$is_login = $this->input->post('is_login');
		$device_id = $this->input->post('device_id');
		if(!empty($mobile_no)) {
			if($is_login) {
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
				$q = $this->db->insert('os_signup_otp', array('mobile_number' => $mobile_no, 'deviceid' => $device_id));
				$response = array();
				if($q) {
					$otp = rand(100000, 999999);
					$message = $otp.' is your Onespace Verification Code. Enjoy Making Your Dream Space With Onespace';
		            $apiKey = urlencode('6348eda2cf8cf');
		            // Message details
		            $numbers = array($mobile_no);
		            $sender = urlencode('ONESPC');
		            $message = rawurlencode($message);

		            $numbers = implode(',', $numbers);

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

		            	$this->db->where('mobile_number', $mobile_no)->update('os_signup_otp', array('otp' => $otp));

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
					$response['message'] = 'Error Occurred While Register.';
					header('Content-Type: application/json; charset=utf-8');
					echo json_encode($response);
				}
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
		$is_login = $this->input->post('is_login');
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

		if($is_login) {
			$q = $this->db->select('customer_id, firstname, lastname, email, telephone')->where(array('telephone' => $mobile_no, 'otp' => $otp))->get('os_customer');
			$response = array();
			if($q->num_rows() > 0) {

				$this->db->where('telephone', $mobile_no)->update('os_customer', array('otp' => 0));

				$response['success'] = true;
				$response['message'] =  'Logged in Successfully.';
				$response['response'] = $q->row();
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($response);
			} else {
				$response['success'] = false;
				$response['message'] = 'Invalid OTP.';
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($response);
			}
		} else {
			$q = $this->db->where(array('mobile_number' => $mobile_no, 'otp' => $otp))->get('os_signup_otp');
			$response = array();
			if($q->num_rows() > 0) {

				$this->db->where('mobile_number', $mobile_no)->delete('os_signup_otp');

				$response['success'] = true;
				$response['message'] =  'Verified Successfully.';
				// $response['response'] = $q->row();
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($response);
			} else {
				$response['success'] = false;
				$response['message'] = 'Invalid OTP.';
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($response);
			}
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

	public function register()
	{
		$first_name = $this->input->post('first_name');
		$last_name = $this->input->post('last_name');
		$email = $this->input->post('email');
		$mobile_no = $this->input->post('mobile_no');
		$signup_location_id = $this->input->post('signup_location_id');
		$flat_type = $this->input->post('flat_type');
		$project_type = $this->input->post('project_type');

		if(empty($first_name)) {
			$response['success'] = false;
			$response['message'] = 'Please Enter First Name.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($last_name)) {
			$response['success'] = false;
			$response['message'] = 'Please Enter Last Name.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($mobile_no)) {
			$response['success'] = false;
			$response['message'] = 'Please Enter Mobile Number.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($signup_location_id)) {
			$response['success'] = false;
			$response['message'] = 'Please Select City.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		$q = $this->db->where('telephone', $mobile_no)->get('os_customer');
		$response = array();
		if($q->num_rows() > 0) {

			$response['success'] = false;
			$response['message'] = 'Mobile Number Already Exist.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);
		} else {
			$q = $this->db->insert('os_customer', array('firstname' => $first_name, 'lastname' => $last_name, 'email' => $email, 'telephone' => $mobile_no, 'signup_location_id' => $signup_location_id, 'date_added' => date('Y-m-d H:i:s')));
			if($q) {
				$customer_id = $this->db->insert_id();
				$this->db->insert('os_st_leads', array('customer_id' => $customer_id, 'flat_type' => $flat_type, 'project_type' => $project_type));

				// $this->verify_mobile_number($mobile_no);

				$data = array('customer_id' => $customer_id, 'firstname' => $first_name, 'lastname' => $last_name, 'email' => $email, 'telephone' => $mobile_no);

				$response['success'] = true;
				$response['message'] = 'Registered Successfully.';
				$response['response'] = $data;
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($response);
			} else {
				$response['success'] = false;
				$response['message'] = 'Error Occurred While Register.';
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($response);
			}
		}
	}

	public function fetch_packages()
	{
		$q = $this->db->select('id, package_img')->where('status', 1)->get('os_predefined_packages');
		$response = array();
		if($q->num_rows() > 0) {

			foreach ($q->result() as $key => $value) {
				if($value->package_img) {
					$value->package_img = base_url().'image/predefined_packages/'.$value->package_img;
				}
			}

			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			$response['response'] = $q->result();
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);
		} else {
			$response['success'] = false;
			$response['message'] = 'No Package Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);
		}
	}
}
