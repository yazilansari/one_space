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
				$q = $this->db->where('phone', $mobile_no)->get('users');
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

		            	$this->db->where('phone', $mobile_no)->update('users', array('otp' => $otp));

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
				$q = $this->db->insert('signup_otp', array('mobile_number' => $mobile_no, 'device_id' => $device_id));
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

		            	$this->db->where('mobile_number', $mobile_no)->update('signup_otp', array('otp' => $otp));

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
			$q = $this->db->select('id AS customer_id, first_name AS firstname, last_name AS lastname, email, phone AS telephone')->where(array('phone' => $mobile_no, 'otp' => $otp))->get('users');
			$response = array();
			if($q->num_rows() > 0) {

				$customer_id = (int) $q->row()->customer_id;
				$firstname = $q->row()->firstname;
				$lastname = $q->row()->lastname;
				$email = $q->row()->email;
				$telephone = $q->row()->telephone;

				$data = array('customer_id' => $customer_id, 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'telephone' => $telephone,);

				$this->db->where('phone', $mobile_no)->update('users', array('otp' => 0));

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
			$q = $this->db->where(array('mobile_number' => $mobile_no, 'otp' => $otp))->get('signup_otp');
			$response = array();
			if($q->num_rows() > 0) {

				$this->db->where('mobile_number', $mobile_no)->delete('signup_otp');

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
		$q = $this->db->select('id, name')->get('cities');
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
		$city_id = $this->input->post('signup_location_id');
		$flat_type = $this->input->post('flat_type');
		// $project_type = $this->input->post('project_type');
		$referred_from = $this->input->post('referred_from');
		$device_id = $this->input->post('device_id');
		$device_type = $this->input->post('device_type');
		$fcm_token = $this->input->post('fcm_token');
		$other_reference = $this->input->post('other_reference');
		$country_code = $this->input->post('country_code');

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
		if(empty($email)) {
			$response['success'] = false;
			$response['message'] = 'Please Enter Email Id.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($mobile_no)) {
			$response['success'] = false;
			$response['message'] = 'Please Enter Mobile Number.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($city_id)) {
			$response['success'] = false;
			$response['message'] = 'Please Select City.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		$q = $this->db->where('phone', $mobile_no)->get('users');
		$response = array();
		if($q->num_rows() > 0) {
			$response['success'] = false;
			$response['message'] = 'Mobile Number Already Exist.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		$q2 = $this->db->where('email', $email)->get('users');
		if($q2->num_rows() > 0) {
			$response['success'] = false;
			$response['message'] = 'Email Id Already Exist.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		// else {
			$q = $this->db->insert('users', array('first_name' => $first_name, 'last_name' => $last_name, 'email' => $email, 'phone' => $mobile_no, 'city_id' => $city_id, 'device_id' => $device_id, 'device_type' => $device_type, 'fcm_token' => $fcm_token, 'referred_from' => $referred_from, 'other_reference' => $other_reference, 'room_type_id' => $flat_type, 'country_code' => $country_code));
			if($q) {
				$customer_id = $this->db->insert_id();
				$q2 = $this->db->select('id')->where('name', 'Customer')->get('roles');
				$role_id = $q2->row()->id;
				$this->db->insert('model_has_roles', array('model_id' => $customer_id, 'model_type' => 'App\Models\User', 'role_id' => $role_id));

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
		// }
	}

	public function fetch_packages()
	{
		$city_id = $this->input->post('signup_location_id');
		if(empty($city_id)) {
			$response['success'] = false;
			$response['message'] = 'City Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		$q = $this->db->select('id, name, description, image_path AS image')->where(['is_active' => 1, 'city_id' => $city_id])->get('packages');
		$response = array();
		if($q->num_rows() > 0) {

			foreach ($q->result() as $key => $value) {
				if($value->image) {
					$value->image = base_url().'image/packages/'.$value->image;
				} else {
					$value->image = '';
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
		$q = $this->db->select('id, name')->get('project_partners');
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
		$q = $this->db->select('id, name, image_path AS image')->get('brandsheets');
		$response = array();
		if($q->num_rows() > 0) {

			foreach ($q->result() as $key => $value) {
				if($value->image) {
					$value->image = base_url().'image/brand_sheets/'.$value->image;
				} else {
					$value->image = '';
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
		$q = $this->db->select('id, name')->where('is_active', 1)->get('room_types');
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
		$theme_id = $this->input->post('theme_id');
		$city_id = $this->input->post('city_id');

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
		if(empty($theme_id)) {
			$response['success'] = false;
			$response['message'] = 'Please Select Theme.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($city_id)) {
			$response['success'] = false;
			$response['message'] = 'Please Select City.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		$q = $this->db->insert('projects', array('user_id' => $user_id, 'name' => $property_name, 'package_id ' => $package, 'brandsheet_id ' => $brandsheet, 'property_type' => $property_type, 'project_partner_id' => $partner_project, 'room_type_id' => $room_type, 'project_type' => $project_type, 'start_from' => $project_start_from, 'selected_budget' => $budget, 'carpet_area' => $total_area, 'theme_id' => $theme_id, 'city_id' => $city_id, 'created_at' => date('Y-m-d H:i:s')));
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
		$project_id = $this->input->post('project_id');

		if(empty($project_id)) {
			$response['success'] = false;
			$response['message'] = 'Project Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		$q2 = $this->db->select('room_type_id')->where('id', $project_id)->get('projects');
		if($q2->num_rows() > 0) {
			$room_type_id = $q2->row()->room_type_id;
			if($room_type_id == 4) {
				$q = $this->db->select('id, name')->where('id !=', 6)->get('parent_categories');
			} else {
				$q = $this->db->select('id, name')->get('parent_categories');
			}
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
		} else {
			$response['success'] = false;
			$response['message'] = 'No Project Found.';
			$response['response'] = [];
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}

	public function fetch_themes()
	{
		$q = $this->db->select('id, name')->get('themes');
		$response = array();
		if($q->num_rows() > 0) {

			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			$response['response'] = $q->result();
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		} else {
			$response['success'] = false;
			$response['message'] = 'No Theme Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}

	public function fetch_categories()
	{
		$user_id = $this->input->post('user_id');
		$project_id = $this->input->post('project_id');
		$parent_category_id = $this->input->post('parent_category_id');

		if(empty($user_id)) {
			$response['success'] = false;
			$response['message'] = 'User Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($project_id)) {
			$response['success'] = false;
			$response['message'] = 'Project Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($parent_category_id)) {
			$response['success'] = false;
			$response['message'] = 'Parent Category Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		$q3 = $this->db->select('package_id, brandsheet_id')->where(['user_id' => $user_id, 'id' => $project_id])->get('projects');
		if($q3->num_rows() > 0) {
			$package_id = $q3->row()->package_id;
			$brand_sheet_id = $q3->row()->brandsheet_id;
			$q4 = $this->db->select('skus.category_id AS category_id')->where('package_id', $package_id)->join('skus', 'skus.id = sku_packages.sku_id', 'left')->get('sku_packages');
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
		$q = $this->db->select('id, name')->where('id', $parent_category_id)->get('parent_categories');
		$response = array();
		if($q->num_rows() > 0) {

			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			foreach ($q->result() as $key => $value) {
				$value->categories = array();
				$response['response'] = $value;
				$q2 = $this->db->select('id, name, image_path AS image, has_subcategories AS isSubCategoryEnabled')->where('parent_category_id', $parent_category_id)->get('categories');
				if($q2->num_rows() > 0) {
					foreach ($q2->result() as $key => $val) {
						$isSubCategoryEnabled = ($val->isSubCategoryEnabled == '1') ? true : false;
						if($val->image) {
							$val->image = base_url().'image/categories/'.$val->image;
						} else {
							$val->image = '';
						}
						if(!$val->isSubCategoryEnabled) {
							unset($val->isSubCategoryEnabled);
							$q5 = $this->db->select('brandsheets.id AS brand_sheet_id, brandsheets.name AS name')->where('category_id', $val->id)->join('brandsheets', 'brandsheets.id = skus.brandsheet_id', 'left')->get('skus');
							if($q5->num_rows() > 0) {
								$brand_sheet_id_arr = [];
								foreach ($q5->result() as $key => $v) {
									$q6 = $this->db->select('price')->where(['category_id' => $val->id, 'brandsheet_id' => $v->brand_sheet_id])->get('skus');
									if($q6->num_rows() > 0) {
										$price = $q6->row()->price;
									} else {
										$price = 0.00;
									}

									if($brand_sheet_id == $v->brand_sheet_id) {
										// unset($v->brand_sheet_id);
										$v->price = $price;
										$v->isSelected = true;
									} else {
										// unset($v->brand_sheet_id);
										$v->price = $price;
										$v->isSelected = false;
									}
								}
							}
							$val->options = array(array("name" => "Yes", "isSelected" => in_array($val->id, $category_id) ? true : false, 'isSubCategoryEnabled' => $isSubCategoryEnabled, 'innerOptions' => $q5->result()), array("name" => "No", "isSelected" => !in_array($val->id, $category_id) ? true : false, 'innerOptions' => array()));
							array_push($value->categories, $val);
						} else {
							unset($val->isSubCategoryEnabled);
							$q7 = $this->db->select('id, name')->where('category_id', $val->id)->get('sub_categories');
							if($q7->num_rows() > 0) {
								foreach ($q7->result() as $key => $sub_category) {
									$sub_category->innerOptions = array();
									$q5 = $this->db->select('brandsheets.id AS brand_sheet_id, brandsheets.name AS name')->where(['category_id' => $val->id, 'sub_category_id' => $sub_category->id])->join('brandsheets', 'brandsheets.id = skus.brandsheet_id', 'left')->get('skus');
									if($q5->num_rows() > 0) {
										$brand_sheet_id_arr = [];
										foreach ($q5->result() as $key => $v) {
											$q6 = $this->db->select('price')->where(['category_id' => $val->id, 'sub_category_id' => $sub_category->id, 'brandsheet_id' => $v->brand_sheet_id])->get('skus');
											if($q6->num_rows() > 0) {
												$price = $q6->row()->price;
											} else {
												$price = 0.00;
											}

											if($brand_sheet_id == $v->brand_sheet_id) {
												// unset($v->brand_sheet_id);
												$v->price = $price;
												$v->isSelected = true;
											} else {
												// unset($v->brand_sheet_id);
												$v->price = $price;
												$v->isSelected = false;
											}
										}
									}
									$sub_category->innerOptions = $q5->result();
								}
							}
							$val->options = array(array("name" => "Yes", "isSelected" => in_array($val->id, $category_id) ? true : false, 'isSubCategoryEnabled' => $isSubCategoryEnabled, 'subCategories' => $q7->result()), array("name" => "No", "isSelected" => !in_array($val->id, $category_id) ? true : false, 'innerOptions' => array()));
							array_push($value->categories, $val);
						}
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

	public function fetch_products()
	{
		$category_id = $this->input->post('category_id');

		if(empty($category_id)) {
			$response['success'] = false;
			$response['message'] = 'Category Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		$q = $this->db->select('brandsheets.id, brandsheets.name AS name')->where('category_id', $category_id)->join('brandsheets', 'brandsheets.id = skus.brandsheet_id', 'left')->get('skus');

		$response = array();
		if($q->num_rows() > 0) {

			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			$response['response'] = array();
			foreach($q->result() as $key => $value) {
				$q2 = $this->db->select('name, image_path AS image')->where('brandsheet_id', $value->id)->get('skus');
				unset($value->id);
				$value->products = array();
				if($q2->num_rows() > 0) {
					foreach ($q2->result() as $key => $val) {
						if($val->image) {
							$val->image = base_url().'image/skus/'.$val->image;
						} else {
							$val->image = '';
						}
					}
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

	public function store_requirement()
	{
		$post_data = file_get_contents("php://input");
    	$request = json_decode($post_data);

    	if($request->project_id == '') {
			$response['success'] = false;
			$response['message'] = 'Project Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if($request->user_id == '') {
			$response['success'] = false;
			$response['message'] = 'User Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		// if($request->theme_id == '') {
		// 	$response['success'] = false;
		// 	$response['message'] = 'Theme Id is Missing.';
		// 	header('Content-Type: application/json; charset=utf-8');
		// 	echo json_encode($response);exit();
		// }
		// if($request->parent_category[0]->parent_category_id == '') {
		// 	$response['success'] = false;
		// 	$response['message'] = 'Parent Category Id is Missing.';
		// 	header('Content-Type: application/json; charset=utf-8');
		// 	echo json_encode($response);exit();
		// }
		// if($request->parent_category[0]->category[0]->category_id == '') {
		// 	$response['success'] = false;
		// 	$response['message'] = 'Category Id is Missing.';
		// 	header('Content-Type: application/json; charset=utf-8');
		// 	echo json_encode($response);exit();
		// }
		// if($request->parent_category[0]->category[0]->is_selected_id == '') {
		// 	$response['success'] = false;
		// 	$response['message'] = 'Please Select Yes or No.';
		// 	header('Content-Type: application/json; charset=utf-8');
		// 	echo json_encode($response);exit();
		// }
		// if($request->parent_category[0]->category[0]->brand_sheet_id == '') {
		// 	$response['success'] = false;
		// 	$response['message'] = 'Brand Sheet Id is Missing.';
		// 	header('Content-Type: application/json; charset=utf-8');
		// 	echo json_encode($response);exit();
		// }
		// die(';;');
		// $max_req_no = 1;
		// $q = $this->db->select_max('requirement_no')->where('cus_project_id', $request->project_id)->get('osl_cus_project_requirements');
		// if($q->num_rows() > 0) {
		// 	$max_req_no = $q->row()->requirement_no + 1;
		// }
		$q2 = $this->db->insert('requirements', array('user_id' => $request->user_id, 'project_id' => $request->project_id, 'created_at' => date('Y-m-d H:i:s')));
		if($q2) {
			$requirement_id = $this->db->insert_id();
			for ($i=0; $i < count($request->categories); $i++) {
				// for($j=0; $j < count($request->categories[$i]->category); $j++) {
					// echo "<pre>";print_r($request->parent_category[$i]->category[$j]);
					if($request->categories[$i]->has_subcategory == '0') {
						$q3 = $this->db->select('id')->where(['skus.brandsheet_id' => $request->categories[$i]->brand_sheet_id, 'skus.category_id' => $request->categories[$i]->category_id])->get('skus');
						if($q3->num_rows() > 0) {
							$sku_id = $q3->row()->id;
						}
						$q = $this->db->insert('requirement_skus', array('category_id' => $request->categories[$i]->category_id, 'is_selected ' => $request->categories[$i]->is_selected_id, 'sku_id ' => $sku_id, 'requirement_id' => $requirement_id, 'created_at' => date('Y-m-d H:i:s')));
					} else {
						for($k=0; $k < count($request->categories[$i]->sub_categories); $k++) {
							$q3 = $this->db->select('id')->where(['skus.brandsheet_id' => $request->categories[$i]->sub_categories[$k]->brand_sheet_id, 'skus.category_id' => $request->categories[$i]->category_id, 'skus.sub_category_id' => $request->categories[$i]->sub_categories[$k]->sub_category_id])->get('skus');
							if($q3->num_rows() > 0) {
								$sku_id = $q3->row()->id;
							}
							$q = $this->db->insert('requirement_skus', array('category_id' => $request->categories[$i]->category_id, 'sub_category_id' => $request->categories[$i]->sub_categories[$k]->sub_category_id, 'is_selected ' => $request->categories[$i]->sub_categories[$k]->is_selected_id, 'sku_id ' => $sku_id, 'requirement_id' => $requirement_id, 'created_at' => date('Y-m-d H:i:s')));
						}
					}
				// }
			}
		}
		// die();
		// Include the main TCPDF library (search for installation path).
		require_once(APPPATH.'TCPDF/tcpdf.php');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		// $pdf->SetCreator(PDF_CREATOR);

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Quotation');
		// $pdf->setFooterData(array(0,64,0), array(0,64,128));

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		// $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		// $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		// $pdf->SetFont('helvetica', '', 12, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// Set some content to print
		$html = '<table border="1" cellpadding="5">';
		$total = 0;

		$q4 = $this->db->select('categories.name AS category_name, sub_categories.name AS sub_category_name, brandsheets.name AS brand_sheet_name, requirement_skus.is_selected, price')->join('categories', 'categories.id = requirement_skus.category_id', 'left')->join('sub_categories', 'sub_categories.id = requirement_skus.sub_category_id', 'left')->join('skus', 'skus.id = requirement_skus.sku_id', 'left')->join('brandsheets', 'brandsheets.id = skus.brandsheet_id', 'left')->where('requirement_skus.requirement_id', $requirement_id)->order_by('categories.id', 'asc')->get('requirement_skus');
			// echo $this->db->last_query();die();
			if($q4->num_rows() > 0) {
				foreach ($q4->result() as $key => $value) {
					$html .= '<tr>
			    				<td style="font-size: 14px; font-weight: bold;">' . $value->category_name . '</td>
			    			</tr>';
			    	if($value->is_selected == "1") {
						$selected = 'Yes';
					} else {
						$selected = 'No';
					}
					$html .= '<tr>
			    				<td style="font-size: 14px; font-weight: bold;">' . $value->sub_category_name . '</td>
			    			</tr>
			    			<tr>
			        			<td style="font-size: 13px; font-weight: bold;">' . $selected . '</td>
			        		</tr>
			    			<tr>
			        			<td style="font-size: 12px; font-weight: bold;">' . $value->brand_sheet_name . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $value->price . '</td>
			        		</tr>';
			        $total += $value->price;
				}
				$html .= '<tr><td style="font-size: 16px; font-weight: bold;">Sub Total: &nbsp;&nbsp;&nbsp;&nbsp;'. number_format($total, 2) .'</td></tr></table>';
			}

		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		// $requirement_id = 1;
		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		if(!file_exists($_SERVER['DOCUMENT_ROOT'].'customer/projects/'.$request->project_id)) {
			mkdir($_SERVER['DOCUMENT_ROOT'].'customer/projects/'.$request->project_id);
		}
		$requirement_quotation_path = $_SERVER['DOCUMENT_ROOT'].'customer/projects/'.$request->project_id.'/requirement-'.$requirement_id.'.pdf';
		$requirement_quotation = 'customer/projects/'.$request->project_id.'/requirement-'.$requirement_id.'.pdf';
		$pdf->Output($requirement_quotation_path, 'F');
		// die;
		
		$this->db->where(['project_id' => $request->project_id, 'id' => $requirement_id])->update('requirements', ['quotation_path' => $requirement_quotation, 'quotation_price' => $total, 'updated_at' => date('Y-m-d H:i:s')]);

		// echo $this->db->last_query();die();

		if($q) {
			$response['success'] = true;
			$response['message'] = 'Data Inserted Successfully.';
			$response['requirement_quotation'] = base_url().$requirement_quotation;
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		} else {
			$response['success'] = false;
			$response['message'] = 'Error Occurred While Inserting Data.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}

	public function fetch_projects()
	{
		$user_id = $this->input->post('user_id');

		if(empty($user_id)) {
			$response['success'] = false;
			$response['message'] = 'User Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		$q = $this->db->select('projects.id, projects.name AS project_name, CONCAT(first_name, " ", last_name) AS username, cities.name AS city, room_types.name AS room_type')->where('projects.user_id', $user_id)->join('users', 'users.id = projects.user_id', 'left')->join('cities', 'cities.id = users.city_id', 'left')->join('room_types', 'room_types.id = projects.room_type_id', 'left')->get('projects');
		$response = array();
		if($q->num_rows() > 0) {

			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			$response['response'] = $q->result();
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		} else {
			$response['success'] = false;
			$response['message'] = 'No Project Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}

	public function fetch_requirement()
	{
		$project_id = $this->input->post('project_id');
		$requirement_id = $this->input->post('requirement_id');

		if(empty($project_id)) {
			$response['success'] = false;
			$response['message'] = 'Project Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		if(empty($requirement_id)) {
			$response['success'] = false;
			$response['message'] = 'Requirement Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		$q = $this->db->select('parent_categories.id, parent_categories.name AS parent_category')->where('requirement_skus.requirement_id', $requirement_id)->join('categories', 'categories.id = requirement_skus.category_id', 'left')->join('parent_categories', 'parent_categories.id = categories.parent_category_id', 'left')->group_by('parent_categories.id')->get('requirement_skus');
		// echo $this->db->last_query();die();
		$response = array();
		if($q->num_rows() > 0) {
			
			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			$response['response'] = array();
			$total = 0;
			foreach($q->result() as $key => $value) {
				$value->values = array();
				$q2 = $this->db->select('requirement_skus.category_id, categories.name AS category, sku_id, brandsheets.name AS brandsheet, requirements.id AS requirement_no, is_freezed, quotation_path, price, has_subcategories')->where('categories.parent_category_id', $value->id)->where('requirement_skus.requirement_id', $requirement_id)->join('categories', 'categories.id = requirement_skus.category_id', 'left')->join('skus', 'skus.id = requirement_skus.sku_id', 'left')->join('brandsheets', 'brandsheets.id = skus.brandsheet_id', 'left')->join('requirements', 'requirements.id = requirement_skus.requirement_id', 'left')->get('requirement_skus');
				// echo $this->db->last_query();
				foreach($q2->result() as $key => $val) {
					if($val->has_subcategories != '0') {
						$q3 = $this->db->select('sub_categories.name AS sub_category_name, brandsheets.name AS brandsheet, price')->where(['sub_categories.category_id' => $val->category_id, 'skus.id' => $val->sku_id])->join('skus', 'skus.sub_category_id = sub_categories.id', 'left')->join('brandsheets', 'brandsheets.id = skus.brandsheet_id', 'left')->order_by('sub_categories.category_id', 'asc')->get('sub_categories');
						// unset($value->parent_category_id);
						$val->sub_categories = array();
						if($q3->num_rows() > 0) {
							foreach($q3->result() as $key => $valu) {
								$total += $valu->price;
								// if($value->quotation_path) {
								// 	$value->quotation_path = base_url().$value->quotation_path;
								// } else {
								// 	$value->quotation_path = '';
								// }
								$val->sub_categories = $q3->result();
							}
						}
						unset($val->brandsheet);
						unset($val->price);
					} else {
						$total += $val->price;
					}
					unset($val->category_id);
					unset($val->sku_id);
					// else {

					// }
					if($val->quotation_path) {
						$val->quotation_path = base_url().$val->quotation_path;
					} else {
						$val->quotation_path = '';
					}
					$value->values = $q2->result();
				}
					unset($value->id);
					array_push($response['response'], $value);
			}
			
			$response['sub_total'] = number_format($total, 2);
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		} else {
			$response['success'] = false;
			$response['message'] = 'No Requirement Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}

	public function fetch_requirement_without_parent_category()
	{
		$project_id = $this->input->post('project_id');
		$requirement_id = $this->input->post('requirement_id');

		if(empty($project_id)) {
			$response['success'] = false;
			$response['message'] = 'Project Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		if(empty($requirement_id)) {
			$response['success'] = false;
			$response['message'] = 'Requirement Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		$q = $this->db->select('requirement_skus.category_id, categories.name AS category, sku_id, brandsheets.name AS brandsheet, requirements.id AS requirement_no, is_freezed, quotation_path, price, has_subcategories')->where('requirement_skus.requirement_id', $requirement_id)->join('categories', 'categories.id = requirement_skus.category_id', 'left')->join('skus', 'skus.id = requirement_skus.sku_id', 'left')->join('brandsheets', 'brandsheets.id = skus.brandsheet_id', 'left')->join('requirements', 'requirements.id = requirement_skus.requirement_id', 'left')->get('requirement_skus');
		// echo $this->db->last_query();die();
		$response = array();
		if($q->num_rows() > 0) {
			
			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			$response['response'] = array();
			$total = 0;
			foreach($q->result() as $key => $value) {
				if($value->has_subcategories != '0') {
					unset($value->price);
					unset($value->brandsheet);
					$q2 = $this->db->select('sub_categories.name AS sub_category_name, brandsheets.name AS brandsheet, price')->where(['sub_categories.category_id' => $value->category_id, 'skus.id' => $value->sku_id])->join('skus', 'skus.sub_category_id = sub_categories.id', 'left')->join('brandsheets', 'brandsheets.id = skus.brandsheet_id', 'left')->order_by('sub_categories.category_id', 'asc')->get('sub_categories');
					// unset($value->parent_category_id);
					$value->sub_categories = array();
					if($q2->num_rows() > 0) {
						foreach($q2->result() as $key => $val) {
							$total += $val->price;
							// if($value->quotation_path) {
							// 	$value->quotation_path = base_url().$value->quotation_path;
							// } else {
							// 	$value->quotation_path = '';
							// }
							$value->sub_categories = $q2->result();
						}
					}
				} else {
					$total += $value->price;
				}
				unset($value->category_id);
				unset($value->sku_id);
				// else {

				// }
				if($value->quotation_path) {
					$value->quotation_path = base_url().$value->quotation_path;
				} else {
					$value->quotation_path = '';
				}
				array_push($response['response'], $value);
			}
			
			$response['sub_total'] = number_format($total, 2);
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		} else {
			$response['success'] = false;
			$response['message'] = 'No Requirement Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}

	public function freeze_requirement()
	{
		$project_id = $this->input->post('project_id');
		$requirement_id = $this->input->post('requirement_id');

		if(empty($project_id)) {
			$response['success'] = false;
			$response['message'] = 'Project Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		if(empty($requirement_id)) {
			$response['success'] = false;
			$response['message'] = 'Requirement Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		$q = $this->db->where(['project_id' => $project_id, 'id' => $requirement_id])->get('requirements');
		$response = array();
		if($q->num_rows() > 0) {

			$q = $this->db->where(['project_id' => $project_id, 'id' => $requirement_id])->update('requirements', array('is_freezed' => 1, 'freezed_at' => date('Y-m-d H:i:s')));
			if($q) {

				$response['success'] = true;
				$response['message'] = 'Data Updated Successfully.';
				// $response['response'] = $data;
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($response);exit();
			} else {
				$response['success'] = false;
				$response['message'] = 'Error Occurred While Updating.';
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($response);exit();
			}
		} else {
			$response['success'] = false;
			$response['message'] = 'No Project or Requirement Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}

	public function compare_requirements()
	{
		$project_id = $this->input->post('project_id');
		$requirement_ids = $this->input->post('requirement_ids');

		if(empty($project_id)) {
			$response['success'] = false;
			$response['message'] = 'Project Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		if(empty($requirement_ids)) {
			$response['success'] = false;
			$response['message'] = 'Requirement Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		$explode_requirement_ids = explode(',', $requirement_ids);
		// print_r($explode_requirement_ids);die;
		$response = array();
		$response['response'] = array();
			$q = $this->db->select('osl_cus_project_requirements.parent_category_id, osl_parent_categories.name AS parent_category')->where('osl_cus_project_requirements.cus_project_id', $project_id)->join('osl_parent_categories', 'osl_parent_categories.id = osl_cus_project_requirements.parent_category_id', 'left')->group_by('osl_cus_project_requirements.parent_category_id')->get('osl_cus_project_requirements');
		if($q->num_rows() > 0) {
			foreach($q->result() as $key => $value) {
				$value->values = array();
				$q2 = $this->db->select('osl_cus_project_requirements.category_id, osl_categories.name AS category')->where(['osl_cus_project_requirements.cus_project_id' => $project_id, 'osl_cus_project_requirements.parent_category_id' => $value->parent_category_id])->join('osl_categories', 'osl_categories.id = osl_cus_project_requirements.category_id', 'left')->group_by('osl_cus_project_requirements.category_id')->order_by('osl_cus_project_requirements.category_id', 'asc')->get('osl_cus_project_requirements');
				unset($value->parent_category_id);
				foreach($q2->result() as $val) {
					$q3 = $this->db->select('requirement_no, osl_parent_brand_sheets.name, price')->where(['osl_cus_project_requirements.cus_project_id' => $project_id, 'osl_cus_project_requirements.category_id' => $val->category_id])->where_in('requirement_no', $explode_requirement_ids)->join(' osl_parent_brand_sheets', ' osl_parent_brand_sheets.id = osl_cus_project_requirements.brand_sheet_id', 'left')->join('osl_products', ' osl_products.brand_sheet_id = osl_cus_project_requirements.brand_sheet_id', 'left')->group_by('requirement_no')->order_by('osl_cus_project_requirements.requirement_no', 'asc')->get('osl_cus_project_requirements');
					// echo $this->db->last_query();
					$requirement = $q3->result();
					// echo "<pre>";print_r($requirement);
					for ($i=0; $i < count($requirement); $i++) { 
						$prev = ($i != 0) ? $requirement[$i-1]->name : $requirement[$i]->name;
						if($requirement[$i]->name == $prev) {
							$val->is_different = false;
						} else {
							$val->is_different = true;
						}
					}
					foreach ($requirement as $key => $va) {
						if(empty($va->name)) {
							$va->name = 'NA';
						}
						if(empty($va->price)) {
							$va->price = 'NA';
						}
					}
					$val->requirements = array();
					$val->requirements = $q3->result();
					unset($val->category_id);
				}
				$value->values = $q2->result();
				array_push($response['response'], $value);
			}
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		} else {
			$response['success'] = false;
			$response['message'] = 'No Parent Category Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}

	public function fetch_project_requirements()
	{
		$project_id = $this->input->post('project_id');

		if(empty($project_id)) {
			$response['success'] = false;
			$response['message'] = 'Project Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		$q = $this->db->select('requirements.id AS requirement_no, is_freezed, quotation_path')->where('requirements.project_id', $project_id)->join('requirement_skus', 'requirement_skus.requirement_id = requirements.id', 'left')->group_by('requirement_no')->order_by('requirement_no', 'asc')->get('requirements');

		$response = array();
		if($q->num_rows() > 0) {

			foreach($q->result() as $key => $val) {
				if($val->quotation_path) {
					$val->quotation_path = base_url().$val->quotation_path;
				} else {
					$val->quotation_path = "";
				}
			}
			
			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			$response['response'] = $q->result();
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		} else {
			$response['success'] = false;
			$response['message'] = 'No Requirement Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}

	public function sign_PDF() {
		if(empty($_FILES['signature_image']['name']) || $_FILES['signature_image']['name'] == '') {
			$response['success'] = false;
			$response['message'] = 'Signature Image is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
		// Include the main TCPDF library and TCPDI.
		require_once(APPPATH.'TCPDF/tcpdf.php');
		require_once(APPPATH.'TCPDF/FPDI/src/autoload.php');
		require_once(APPPATH.'TCPDF/signpdf.php');

		$path = $_FILES['signature_image']['tmp_name'];
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		// echo base64_encode($data);die();
		$img_base64_encoded = base64_encode($data);
		$img_base64_decoded = base64_decode($img_base64_encoded);

		// initiate PDF
		$pdf = new Pdf();

		// if ($tplId === null) {
        $pdf->setSourceFile(APPPATH.'TCPDF/examples/sample.pdf');
        $tplId = $pdf->importPage(1);
        // }
        // $size = $this->useImportedPage($this->tplId, 130, 5, 60);
        $pdf->AddPage();
        $pdf->useTemplate($tplId);
        $pdf->SetFont('freesans', 'B', 20);
        $pdf->SetTextColor(0);
        $pdf->SetXY(PDF_MARGIN_LEFT, 5);
        // $pdf->Image(APPPATH.'TCPDF/examples/images/img.png', 150, 100, 20, 20);
        $pdf->Image('@'.$img_base64_decoded, 150, 100, 50, 50);
		// $pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
		// $pdf->SetAutoPageBreak(true, 40);

		// add a page
		// $pdf->AddPage();

		// get external file content
		// $utf8text = file_get_contents(APPPATH.'TCPDF/examples/data/utf8test.txt', true);

		// $pdf->SetFont('freeserif', '', 12);
		// now write some text above the imported page
		// $pdf->Write(5, $utf8text);
		$signed_pdf = time().'_signed.pdf';
        $signed_pdf_path = $_SERVER['DOCUMENT_ROOT'].'api/signed_pdf/'.$signed_pdf;
		$pdf->Output($signed_pdf_path, 'F');
		$response['success'] = true;
		$response['message'] = 'PDF Signed Successfully.';
		$response['signed_pdf'] = base_url().'signed_pdf/'.$signed_pdf;
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($response);exit();
	}

	public function dashboard()
	{
		$user_id = $this->input->post('user_id');

		if(empty($user_id)) {
			$response['success'] = false;
			$response['message'] = 'User Id is Missing.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}

		$q = $this->db->select('projects.id, name, start_date, project_stage_id AS current_status')->select_max('project_stage_id' , 'current_status')->join('project_status', 'project_status.project_id = projects.id', 'left')->where('user_id', $user_id)->where('status', '4')->group_by('projects.id')->order_by('project_stage_id', 'DESC')->get('projects');
		// echo $this->db->last_query();

		$response = array();
		if($q->num_rows() > 0) {
			$count = 1;
			foreach ($q->result() as $key => $value) {
				$value->project_name = 'Project '.$count;
				$count++;
				if($value->start_date) {
					$value->start_date = date('d/m/Y', strtotime($value->start_date));
				} else {
					$value->start_date = "";
				}
				if($value->current_status) {
					$value->per_completed = (($value->current_status*100)/10)."%";
				}
			}
			
			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			$response['response'] = $q->result();
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		} else {
			$response['success'] = false;
			$response['message'] = 'No Project Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}

	public function fetch_banners()
	{
		$q = $this->db->select('id, image_path AS image, link')->where('is_active', 10)->get('banners');

		$response = array();
		if($q->num_rows() > 0) {

			foreach($q->result() as $key => $val) {
				if($val->image) {
					$val->image = base_url().$val->image;
				} else {
					$val->image = "";
				}
			}
			
			$response['success'] = true;
			$response['message'] = 'Fetched Successfully.';
			$response['response']['banners'] = $q->result();
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		} else {
			$response['success'] = false;
			$response['message'] = 'No Banner Found.';
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($response);exit();
		}
	}
}
