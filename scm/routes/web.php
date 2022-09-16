<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::get('/', 'HomeController@index')->name('home');
	Route::get('/home', 'HomeController@index')->name('home');

	/* ROUTE FOR DEPARTMENT */
	Route::get('/department', 'DepartmentController@index');
	Route::post('/department/add', 'DepartmentController@addDepartment');
	Route::post('/department/addsub', 'DepartmentController@addSubDepartment');
	Route::post('/department/update', 'DepartmentController@updateDepartment');
	Route::get('/department/delete-{id}', 'DepartmentController@deleteDepartment');
	Route::get('/department/assign-manager/{dept_id}/{user_id}', 'DepartmentController@assignManager');
	Route::get('/department/list-for-transfer/{staff_id}', 'DepartmentController@loadDeptTreeForTransfer');

	/* ROUTE FOR USER */
	Route::get('/staff', 'StaffController@index');
	Route::post('/staff/add', 'StaffController@addStaff');
	Route::post('/staff/add-in-dept', 'StaffController@addStaffInDept');
	Route::post('/staff/update-in-dept', 'StaffController@updateStaffInDept');
	Route::get('/staff/load-staff/{dept_id}', 'StaffController@loadStaffList');
	Route::get('/staff/assign-manager/{dept_id}/{user_id}', 'StaffController@assignManager');
	Route::get('/staff/load-staff-info/{staff_id}', 'StaffController@loadStaffInfo');
	Route::get('/staff/transfer/{staff_id}/to-dept/{new_dept_id}', 'StaffController@transferToDepartment');
	Route::get('/staff/status/{staff_id}/{new_status}', 'StaffController@updateStaffStatus');
	Route::get('/manager/status/{staff_id}/{new_status}', 'StaffController@updateManagerStatus');
	Route::get('/staff/get-delete-staff/{staff_id}', 'StaffController@getDeleteStaffInfo');
	Route::get('/staff/delete-staff/{staff_id}', 'StaffController@deleteStaff');

	/* ROUTE FOR GENERAL SETTING */
	Route::get('/setting', 'SettingController@index');
	Route::post('/setting/add-prefix', 'SettingController@addDeptPrefix');
	Route::post('/setting/save-prefix', 'SettingController@saveDeptPrefix');
	Route::get('/setting/delete-prefix-{id}', 'SettingController@deleteDeptPrefix');
	Route::post('/setting/add-title', 'SettingController@addTitle');
	Route::post('/setting/save-title', 'SettingController@saveTitle');
	Route::get('/setting/delete-title-{id}', 'SettingController@deleteTitle');

	/* ROUTE FOR CONFIG */
	Route::get('/config', 'ConfigController@index');

	/* ROUTE FOR MEDIA */
	Route::get('/platform', 'PlatformController@index');
	Route::post('/platform/save', 'PlatformController@savePlatform');
	Route::get('/platform/delete/{id}', 'PlatformController@deletePlatform');

	Route::get('/origin-product', 'OriginController@index');
	Route::post('/origin-product/save-folder', 'OriginController@saveFolder');
	Route::post('/origin-product/save-file', 'OriginController@saveFile');
	Route::get('/origin-product/delete-folder/{id}', 'OriginController@deleteFolder');
	Route::get('/origin-product/delete-file/{id}', 'OriginController@deleteFile');

	Route::get('/topic', 'TopicController@index');
	Route::post('/topic/save', 'TopicController@saveTopic');
	Route::get('/topic/delete/{id}', 'TopicController@deleteTopic');

	Route::get('/channel', 'ChannelController@index');
	Route::post('/channel/save', 'ChannelController@saveChannel');
	Route::get('/channel/delete', 'ChannelController@deleteChannel');
	Route::get('/channel-type', 'ChannelController@channelType');
	Route::post('/channel-type/save', 'ChannelController@saveChannelType');
	Route::get('/channel-type/delete/{ct_id}', 'ChannelController@deleteChannelType');
	Route::get('/channel/collect/{channel_id}', 'ChannelController@collectChannelInfo');

	Route::get('/video', 'VideoController@index');
	Route::get('/video/detail', 'VideoController@detail');
	Route::get('/video-file/load/{video_id}/{current_folder_id}/{current_dept_id}', 'VideoController@loadFileForVideo');
	Route::get('/video-file/assign/{video_id}/{file_id}', 'VideoController@assignFileForVideo');

    Route::resource('fanpage', FanpageController::class);

	Route::get('/promotion', 'PromotionController@index');
	Route::get('/promotion/get-ticket-by-id/{id}', 'TicketController@getTicket');
	Route::match(['get', 'post'], '/ticket/save', 'TicketController@saveTicket');
	Route::match(['get', 'post'], '/comment/save', 'TicketController@saveComment');
	Route::get('/promote/tab/{tab_id}', function($tab_id) {
		session(['tab_id' => $tab_id]);
	});
	Route::get('/dl/{file_name}', function($file_name) {
		return \App\Utils::download('/download/' . $file_name, $file_name);
	});


	/* ROUTE FOR AUTHENTICATION */
	Route::match(['get', 'post'], '/login', 'Auth\LoginController@login')->name('login');
	Route::match(['get', 'post'], '/logout', 'Auth\LoginController@logout')->name('logout');
	Route::get('/auth/google', 'Auth\LoginController@redirectToGoogle')->name('login.google');
	Route::get('/auth/google-prompt', 'Auth\LoginController@redirectToGooglePrompt')->name('login.google.prompt');
	Route::get('/auth/google/callback', 'Auth\LoginController@handleGoogleCallback');

	/* BITRIX24 - IT - SUPPORT */
	Route::match(['get', 'post'], '/ticket24', 'Ticket24\Ticket24Controller@home')->name('ticket24.index');
	Route::match(['get', 'post'], '/ticket24/setting', 'Ticket24\Ticket24Controller@setting')->name('ticket24.setting');

});

//Auth::routes();
//luan
//h
/*




*/
