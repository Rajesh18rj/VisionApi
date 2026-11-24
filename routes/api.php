<?php

use App\Http\Middleware\BasicAuth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('APIToken')->group(function () {

    Route::get('UserRoleById/{id}', 'UserRoleController@ById');
    Route::post('UserRoleInsert', 'UserRoleController@Insert');
    Route::Put('UserRoleUpdate', 'UserRoleController@Update');
    Route::Delete('UserRoleDelete/{id}', 'UserRoleController@Delete');

    Route::get('UserList', 'UserController@List');
    Route::get('UserById/{id}', 'UserController@ById');
    Route::post('UserInsert', 'UserController@Insert');
    Route::post('NewUserInsert', 'UserController@NewUserInsert');
    Route::Put('UserUpdate', 'UserController@Update');
    Route::get('UserDelete/{id}', 'UserController@Delete');
    Route::post('UserLogout', 'UserController@UserLogout');
    Route::post('ChangePassword', 'UserController@ChangePassword');
    Route::post('DecryptPassword', 'UserController@DecryptPassord');
    Route::Put('RefreshOneSignalUpdate', 'UserController@RefreshOneSignalUpdate');

    Route::get('AppSettingsList', 'AppSettingsController@List');
    Route::get('AppSettingsById/{id}', 'AppSettingsController@ById');
    Route::post('AppSettingsInsert', 'AppSettingsController@Insert');
    Route::Put('AppSettingsUpdate', 'AppSettingsController@Update');
    Route::Delete('AppSettingsDelete/{id}', 'AppSettingsController@Delete');

//    Route::get('BlockList', 'BlockController@List');
//    Route::get('BlockById/{id}', 'BlockController@ById');
//    Route::post('BlockInsert', 'BlockController@Insert');
//    Route::Put('BlockUpdate', 'BlockController@Update');
//    Route::Delete('BlockDelete/{id}', 'BlockController@Delete');

//    Route::get('FloorList', 'FloorController@List');
//    Route::get('FloorById/{id}', 'FloorController@ById');
//    Route::post('FloorInsert', 'FloorController@Insert');
//    Route::Put('FloorUpdate', 'FloorController@Update');
//    Route::Delete('FloorDelete/{id}', 'FloorController@Delete');

    Route::get('SectionList', 'SectionController@List');
    Route::get('SectionById/{id}', 'SectionController@ById');
    Route::post('SectionInsert', 'SectionController@Insert');
    Route::Put('SectionUpdate', 'SectionController@Update');
    Route::Delete('SectionDelete/{id}', 'SectionController@Delete');

//    Route::get('NvrList', 'NvrController@List');
//    Route::get('NvrById/{id}', 'NvrController@ById');
//    Route::post('NvrInsert', 'NvrController@Insert');
//    Route::Put('NvrUpdate', 'NvrController@Update');
//    Route::Delete('NvrDelete/{id}', 'NvrController@Delete');

//    Route::get('CameraList', 'CameraController@List');
//    Route::get('CameraById/{id}', 'CameraController@ById');
//    Route::post('CameraInsert', 'CameraController@Insert');
//    Route::Put('CameraUpdate', 'CameraController@Update');
//    Route::Delete('CameraDelete/{id}', 'CameraController@Delete');

    Route::get('PersonTypeList', 'PersonTypeController@List');
    Route::get('PersonTypeById/{id}', 'PersonTypeController@ById');
    Route::post('PersonTypeInsert', 'PersonTypeController@Insert');
    Route::Put('PersonTypeUpdate', 'PersonTypeController@Update');
    Route::Delete('PersonTypeDelete/{id}', 'PersonTypeController@Delete');

    Route::get('DeviceConnectedList', 'DeviceConnectedController@List');
    Route::get('DeviceConnectedById/{id}', 'DeviceConnectedController@ById');
    Route::post('DeviceConnectedInsert', 'DeviceConnectedController@Insert');
    Route::Put('DeviceConnectedUpdate', 'DeviceConnectedController@Update');
    Route::Delete('DeviceConnectedDelete/{id}', 'DeviceConnectedController@Delete');

    Route::get('PersonList', 'PersonController@List');
    Route::get('PersonById/{id}', 'PersonController@ById');
    Route::post('PersonInsert', 'PersonController@Insert');
    Route::Put('PersonUpdate', 'PersonController@Update');
    Route::Delete('PersonDelete/{id}', 'PersonController@Delete');
    // Person Type filter dropdown
    Route::get('PersonTypeFilterList', 'PersonController@PersonTypeFilterList');

    Route::get('EmployeeList', 'EmployeeController@List');
    Route::get('EmployeeById/{id}', 'EmployeeController@ById');
    Route::post('EmployeeInsert', 'EmployeeController@Insert');
    Route::put('EmployeeUpdate', 'EmployeeController@Update');
    Route::delete('EmployeeDelete/{id}', 'EmployeeController@Delete');
    Route::get('EmployeeTypeFilterList', 'EmployeeController@EmployeeTypeFilterList');

    Route::get('CategoryList', 'CategoryController@List');
    Route::get('CategoryById/{id}', 'CategoryController@ById');
    Route::post('CategoryInsert', 'CategoryController@Insert');
    Route::Put('CategoryUpdate', 'CategoryController@Update');
    Route::Delete('CategoryDelete/{id}', 'CategoryController@Delete');

    Route::get('SessionList', 'SessionController@List');
    Route::get('SessionById/{id}', 'SessionController@ById');
    Route::post('SessionInsert', 'SessionController@Insert');
    Route::Put('SessionUpdate', 'SessionController@Update');
    Route::Delete('SessionDelete/{id}', 'SessionController@Delete');

    Route::get('DepartmentList', 'DepartmentController@List');
    Route::get('DepartmentById/{id}', 'DepartmentController@ById');
    Route::post('DepartmentInsert', 'DepartmentController@Insert');
    Route::Put('DepartmentUpdate', 'DepartmentController@Update');
    Route::Delete('DepartmentDelete/{id}', 'DepartmentController@Delete');
    Route::get('DepartmentByCategoryId/{category_id}', 'DepartmentController@DepartmentByCategoryId');

    Route::get('DesignationList', 'DesignationController@List');
    Route::get('DesignationById/{id}', 'DesignationController@ById');
    Route::post('DesignationInsert', 'DesignationController@Insert');
    Route::Put('DesignationUpdate', 'DesignationController@Update');
    Route::Delete('DesignationDelete/{id}', 'DesignationController@Delete');
    Route::get('DesignationByDepartmentId/{department_id}', 'DesignationController@DesignationByDepartmentId');

    Route::post('PersonFaceInsert', 'PersonFaceController@Insert');
    Route::post('PersonFaceSave', 'PersonFaceController@Save');
    Route::get('AttendanceList/{start_date}/{end_date}/{student_id}', 'PersonFaceController@AttendanceList');

    Route::post('DashBoardStaffList', 'DashbardController@DashBoardStaffList');
    Route::get('DashBoardStudentDetailList/{person_id}', 'DashbardController@DashBoardStudentDetailList');
    Route::get('Footprint/{person_id}/{date}','DashbardController@Footprint');
    Route::get('DashBoardChartList','DashbardController@DashBoardChartList');
    Route::get('Footprint/{person_id}/{date}', 'DashbardController@Footprint');
    Route::get('CameraPing', 'DashbardController@pingCamera');
    Route::get('DashBoardChartList', 'DashbardController@DashBoardChartList');
    Route::get('LiveAttendanceList', 'DashbardController@LiveAttendanceList');
    Route::get('LiveGroupAttendanceList/{group_id}', 'DashbardController@LiveGroupAttendanceList');
    Route::get('DashBoardStudentViewById/{person_id}', 'DashbardController@DashBoardStudentViewById');
    Route::get('DashBoardNotificationList/{user_id}/{gender}/{department_id}', 'DashbardController@DashBoardNotificationList');

    Route::post('ReportStaffList', 'ReportController@ReportStaffList');
    Route::get('PersonFaceList', 'PersonFaceController@PersonFaceList');

//    Route::get('GroupCameraList', 'GroupCameraController@List');
//    Route::get('GroupCameraById/{id}', 'GroupCameraController@ById');
//    Route::get('GroupCameraDetailById/{group_camera_id}', 'GroupCameraController@GroupCameraDetailById');
//    Route::post('GroupCameraInsert', 'GroupCameraController@Insert');
//    Route::Put('GroupCameraUpdate', 'GroupCameraController@Update');
//    Route::Delete('GroupCameraDelete/{id}', 'GroupCameraController@Delete');
//    Route::get('GroupCameraDetailList', 'GroupCameraController@GroupCameraDetailList');



});

Route::middleware([BasicAuth::class])->group(function () {

    Route::post('ImageInsert', 'PersonController@ImageInsert');
    Route::post('animal-detected', 'NotificationController@animalDetected');

});

Route::post('UserLogin', 'UserController@Login');
Route::post('ForgetPassword', 'UserController@ForgetPassword');
Route::post('ResetPassword', 'UserController@ResetPassword');
Route::get('UserRoleList', 'UserRoleController@List');
