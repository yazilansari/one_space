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
						echo json_encode($response);exit();
		            } else {
		            	$response['success'] = false;
						$response['message'] = 'Error Occurred While Sending OTP.';
						header('Content-Type: application/json; charset=utf-8');
						echo json_encode($response);exit();
		            }
			        curl_close($curl);
				} else {
					$response['success'] = false;
					$response['message'] = 'Your Mobile Number is Not Registered.';
					header('Content-Type: application/json; charset=utf-8');
					echo json_encode($response);exit();
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
						echo json_encode($response);exit();
		            } else {
		            	$response['success'] = false;
						$response['message'] = 'Error Occurred While Sending OTP.';
						header('Content-Type: application/json; charset=utf-8');
						echo json_encode($response);exit();
		            }
			        curl_close($curl);
				} else {
					$response['success'] = false;
					$response['message'] = 'Error Occurred While Register.';
					header('Content-Type: application/json; charset=utf-8');
					echo json_encode($response);exit();
				}
			}
		} else {
			$response['success'] = false;
			$response['message'] = 'Please Enter Mobile Number.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
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

				$customer_id = (int) $q->row()->customer_id;
				$firstname = $q->row()->firstname;
				$lastname = $q->row()->lastname;
				$email = $q->row()->email;
				$telephone = $q->row()->telephone;

				$data = array('customer_id' => $customer_id, 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'telephone' => $telephone,);

				$this->db->where('telephone', $mobile_no)->update('os_customer', array('otp' => 0));

				$response['success'] = true;
				$response['message'] =  'Logged in Successfully.';
				$response['response'] = $data;
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($response);exit();
			} else {
				$response['success'] = false;
				$response['message'] = 'Invalid OTP.';
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($response);exit();
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
				echo json_encode($response);exit();
			} else {
				$response['success'] = false;
				$response['message'] = 'Invalid OTP.';
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($response);exit();
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
			echo json_encode($response);exit();
		} else {
			$response['success'] = false;
			$response['message'] = 'No City Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
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
		$device_id = $this->input->post('device_id');
		$device_type = $this->input->post('device_type');
		$fcm_token = $this->input->post('fcm_token');

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
			echo json_encode($response);exit();
		} else {
			$q = $this->db->insert('os_customer', array('firstname' => $first_name, 'lastname' => $last_name, 'email' => $email, 'telephone' => $mobile_no, 'signup_location_id' => $signup_location_id, 'date_added' => date('Y-m-d H:i:s'), 'device_id' => $device_id, 'device_type' => $device_type, 'fcm_token' => $fcm_token));
			if($q) {
				$customer_id = $this->db->insert_id();
				$this->db->insert('os_st_leads', array('customer_id' => $customer_id, 'flat_type' => $flat_type, 'project_type' => $project_type));

				// $this->verify_mobile_number($mobile_no);

				$data = array('customer_id' => $customer_id, 'firstname' => $first_name, 'lastname' => $last_name, 'email' => $email, 'telephone' => $mobile_no);

				$response['success'] = true;
				$response['message'] = 'Registered Successfully.';
				$response['response'] = $data;
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($response);exit();
			} else {
				$response['success'] = false;
				$response['message'] = 'Error Occurred While Register.';
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($response);exit();
			}
		}
	}

	public function fetch_packages()
	{
		$q = $this->db->select('id, name, description, image')->where('status', 1)->get('osl_packages');
		$response = array();
		if($q->num_rows() > 0) {

			foreach ($q->result() as $key => $value) {
				if($value->image) {
					$value->image = base_url().'image/packages/'.$value->image;
				}
			}

			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			$response['response'] = $q->result();
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		} else {
			$response['success'] = false;
			$response['message'] = 'No Package Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}

	public function fetch_partner_projects()
	{
		$q = $this->db->select('sbad.simple_blog_article_description_id, sbad.simple_blog_article_id, sbad.article_title')
					->from('os_simple_blog_article sba')
					->join('os_simple_blog_article_description sbad', 'sba.simple_blog_article_id = sbad.simple_blog_article_id', 'left')
					->join('os_simple_blog_article_to_store sbas', 'sba.simple_blog_article_id = sbas.simple_blog_article_id', 'left')
					->join('os_simple_blog_author sbau', 'sba.simple_blog_author_id = sbau.simple_blog_author_id', 'left')
					->join('os_simple_blog_article_to_category sbac', 'sba.simple_blog_article_id = sbac.simple_blog_article_id', 'left')
					->where(['sbac.simple_blog_category_id' => 5, 'sba.status' => 1, 'sbau.status' => 1, 'sbas.store_id' => 0, 'language_id' => 1])
					->get();
		$response = array();
		if($q->num_rows() > 0) {

			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			$response['response'] = $q->result();
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		} else {
			$response['success'] = false;
			$response['message'] = 'No Partner Project Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}

	public function fetch_brand_sheets()
	{
		$q = $this->db->select('id, name, image, description')->where('status', 1)->get('osl_parent_brand_sheets');
		$response = array();
		if($q->num_rows() > 0) {

			foreach ($q->result() as $key => $value) {
				if($value->image) {
					$value->image = base_url().'image/brand_sheets/'.$value->image;
				}
			}

			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			$response['response'] = $q->result();
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		} else {
			$response['success'] = false;
			$response['message'] = 'No Brand Sheet Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}

	public function fetch_room_types()
	{
		$q = $this->db->select('id, room_type')->where('status', 1)->get('osl_room_types');
		$response = array();
		if($q->num_rows() > 0) {

			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			$response['response'] = $q->result();
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		} else {
			$response['success'] = false;
			$response['message'] = 'No Room Type Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}

	public function store_project()
	{
		$user_id = $this->input->post('user_id');
		$property_name = $this->input->post('property_name');
		$package = $this->input->post('package');
		$brandsheet = $this->input->post('brandsheet');
		$property_type = $this->input->post('property_type');
		$partner_project = $this->input->post('partner_project');
		$room_type = $this->input->post('room_type');
		$project_type = $this->input->post('project_type');
		$project_start_from = $this->input->post('project_start_from');
		$budget = $this->input->post('budget');
		$total_area = $this->input->post('total_area');

		if(empty($user_id)) {
			$response['success'] = false;
			$response['message'] = 'User Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($property_name)) {
			$response['success'] = false;
			$response['message'] = 'Please Enter Property Name.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($package)) {
			$response['success'] = false;
			$response['message'] = 'Please Select Package.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($brandsheet)) {
			$response['success'] = false;
			$response['message'] = 'Please Select Brand Sheet.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($property_type)) {
			$response['success'] = false;
			$response['message'] = 'Please Select Property Type.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($partner_project)) {
			$response['success'] = false;
			$response['message'] = 'Please Select Partner Project.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($room_type)) {
			$response['success'] = false;
			$response['message'] = 'Please Select Room Type.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($project_type)) {
			$response['success'] = false;
			$response['message'] = 'Please Select Project Type.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($project_start_from)) {
			$response['success'] = false;
			$response['message'] = 'Please Select When to Start.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($budget)) {
			$response['success'] = false;
			$response['message'] = 'Please Select Budget.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($total_area)) {
			$response['success'] = false;
			$response['message'] = 'Please Enter Tentative Sq. Ft.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		$q = $this->db->insert('osl_cus_projects', array('user_id' => $user_id, 'name' => $property_name, 'package_id ' => $package, 'brand_sheet_id ' => $brandsheet, 'property_type' => $property_type, 'partner_project_id' => $partner_project, 'room_type_id' => $room_type, 'project_type' => $project_type, 'project_start_from' => $project_start_from, 'budget' => $budget, 'total_area' => $total_area, 'created_at' => date('Y-m-d H:i:s')));
		if($q) {
			$response['success'] = true;
			$response['message'] = 'Data Inserted Successfully.';
			$response['project_id'] = $this->db->insert_id();
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		} else {
			$response['success'] = false;
			$response['message'] = 'Error Occurred While Inserting Data.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}

	public function fetch_parent_categories()
	{
		$q = $this->db->select('id, name')->get('osl_parent_categories');
		$response = array();
		if($q->num_rows() > 0) {

			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			$response['response'] = $q->result();
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		} else {
			$response['success'] = false;
			$response['message'] = 'No Parent Category Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}

	public function fetch_categories($user_id, $project_id, $parent_category_id)
	{
		$q3 = $this->db->select('package_id, brand_sheet_id')->where(['user_id' => $user_id, 'id' => $project_id])->get('osl_cus_projects');
		if($q3->num_rows() > 0) {
			$package_id = $q3->row()->package_id;
			$brand_sheet_id = $q3->row()->brand_sheet_id;
			$q4 = $this->db->select('category_id')->where('package_id', $package_id)->get('osl_categories_packages');
			if($q4->num_rows() > 0) {
				$category_id = [];
				foreach($q4->result() as $valu) {
					array_push($category_id, $valu->category_id);
				}
			} else {
				$category_id = [];
			}
		} else {
			// $package_id = 0;
			// $brand_sheet_id = 0;
			$response['success'] = false;
			$response['message'] = 'No Project Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		// die();
		$q = $this->db->select('id, name')->where('id', $parent_category_id)->get('osl_parent_categories');
		$response = array();
		if($q->num_rows() > 0) {

			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			foreach ($q->result() as $key => $value) {
				$value->categories = array();
				$response['response'] = $value;
				$q2 = $this->db->select('id, name')->where(['parent_category_id' => $parent_category_id, 'status' => 1])->get('osl_categories');
				if($q2->num_rows() > 0) {
					foreach ($q2->result() as $key => $val) {
						$q5 = $this->db->select('brand_sheet_id, name')->where('category_id', $val->id)->join('osl_parent_brand_sheets', 'osl_parent_brand_sheets.id = osl_brand_sheets_categories.brand_sheet_id', 'left')->get('osl_brand_sheets_categories');
						if($q5->num_rows() > 0) {
							$brand_sheet_id_arr = [];
							foreach ($q5->result() as $key => $v) {
								$q6 = $this->db->select('price')->where(['category_id' => $val->id, 'brand_sheet_id' => $v->brand_sheet_id])->get('osl_products');
								if($q6->num_rows() > 0) {
									$price = $q6->row()->price;
								} else {
									$price = 0.00;
								}

								if($brand_sheet_id == $v->brand_sheet_id) {
									unset($v->brand_sheet_id);
									$v->price = $price;
									$v->isSelected = true;
								} else {
									unset($v->brand_sheet_id);
									$v->price = $price;
									$v->isSelected = false;
								}
							}
						}
						$val->options = array(array("name" => "Yes", "isSelected" => in_array($val->id, $category_id) ? true : false, 'innerOptions' => $q5->result()), array("name" => "No", "isSelected" => !in_array($val->id, $category_id) ? true : false, 'innerOptions' => array()));
						array_push($value->categories, $val);
					}
					header('Content-Type: application/json; charset=utf-8');
					echo json_encode($response);exit();
					// die();
				} else {
					$response['success'] = false;
					$response['message'] = 'No Category Found.';
					header('Content-Type: application/json; charset=utf-8');
					echo json_encode($response);exit();
				}
			}
		} else {
			$response['success'] = false;
			$response['message'] = 'No Parent Category Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}

	public function fetch_products($category_id)
	{
		$q = $this->db->select('osl_parent_brand_sheets.id, name')->where('category_id', $category_id)->join('osl_parent_brand_sheets', 'osl_parent_brand_sheets.id = osl_brand_sheets_categories.brand_sheet_id', 'left')->get('osl_brand_sheets_categories');
		$response = array();
		if($q->num_rows() > 0) {

			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			$response['response'] = array();
			foreach($q->result() as $key => $value) {
				$q2 = $this->db->select('name, image')->where('brand_sheet_id', $value->id)->get('osl_products');
				unset($value->id);
				$value->products = array();
				if($q2->num_rows() > 0) {
					$value->products = $q2->result();
				}
				array_push($response['response'], $value);
			}
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		} else {
			$response['success'] = false;
			$response['message'] = 'No Product Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}
}
