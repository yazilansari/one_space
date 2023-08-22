<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['v1/verifyMobileNumber'] = 'services/verify_mobile_number';
$route['v1/verifyOTP'] = 'services/verify_otp';
$route['v1/fetchCities'] = 'services/fetch_cities';
$route['v1/register'] = 'services/register';
$route['v1/fetchPackages'] = 'services/fetch_packages';
$route['v1/fetchPartnerProjects'] = 'services/fetch_partner_projects';
$route['v1/fetchBrandSheets'] = 'services/fetch_brand_sheets';
$route['v1/fetchRoomTypes'] = 'services/fetch_room_types';
$route['v1/storeProject'] = 'services/store_project';
$route['v1/fetchParentCategories'] = 'services/fetch_parent_categories';
$route['v1/fetchCategories'] = 'services/fetch_categories';
$route['v1/fetchProducts'] = 'services/fetch_products';
$route['v1/storeRequirement'] = 'services/store_requirement';
$route['v1/fetchProjects'] = 'services/fetch_projects';
$route['v1/freezeRequirement'] = 'services/freeze_requirement';
$route['v1/compareRequirements'] = 'services/compare_requirements';
$route['v1/fetchRequirement'] = 'services/fetch_requirement';
$route['v1/fetchProjectRequirements'] = 'services/fetch_project_requirements';
$route['v1/signPDF'] = 'services/sign_PDF';
$route['v1/fetchThemes'] = 'services/fetch_themes';
$route['v1/dashboard'] = 'services/dashboard';
$route['v1/fetchBanners'] = 'services/fetch_banners';
$route['v1/fetchParentCategoriesSelection'] = 'services/fetch_parent_categories_selection';
$route['v1/fetchCategoriesSelection'] = 'services/fetch_categories_selection';
$route['v1/fetchPenalist'] = 'services/fetch_penalist';
$route['v1/updateFCMTokenDeviceId'] = 'services/update_fcm_token_device_id';
$route['v1/updateProjectPenalist'] = 'services/update_project_penalist';
$route['v1/fetchProjectDetails'] = 'services/fetch_project_details';
$route['v1/fetchProjectTimelines'] = 'services/fetch_project_timelines';
$route['v1/fetchUser'] = 'services/fetch_user';
$route['v1/fetchProductsSelection'] = 'services/fetch_products_selection';
$route['v1/storeProductSelection'] = 'services/store_product_selection';
$route['v1/fetchWorkStatus'] = 'services/fetch_work_status';
