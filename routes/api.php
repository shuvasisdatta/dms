<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResources([
    'user'          => 'API\UserController',
    'role'          => 'API\RoleController',
    'category'      => 'API\CategoryController',
    'locker'        => 'API\LockerController',
    'department'    => 'API\DepartmentController',
    'document'      => 'API\DocumentController',
    'plant'         => 'API\PlantController',
    'equipment'     => 'API\EquipmentController',
]);

Route::get('fetchAllDepartment', 'API\DepartmentController@fetchAll');
Route::get('fetchAllLocker', 'API\LockerController@fetchAll');
Route::get('fetchAllCategory', 'API\CategoryController@fetchAll');
Route::get('fetchAllParentCategories', 'API\CategoryController@fetchAllParentCategories');
Route::get('fetchAllRoles', 'API\RoleController@fetchAll');
Route::get('fetchAllUsers', 'API\UserController@fetchAll');
Route::get('fetchDocumentRelatedModels', 'API\DocumentController@fetchDocumentRelatedModels');
Route::get('fetchUserRelatedModels', 'API\UserController@fetchUserRelatedModels');
Route::get('fetchEquipmentRelatedModels', 'API\EquipmentController@fetchUserRelatedModels');
Route::get('fetchCurrentUser', 'API\UserController@getCurrentUser');
Route::post('login', 'API\UserController@login');
