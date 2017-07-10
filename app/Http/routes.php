<?php

Route::auth();

Route::post('/deploy', 'GitController@deploy');

Route::get('/forgot-password', 'Auth\AuthController@showReset')->middleware('guest');

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', 'PagesController@home');

    Route::get('/register/payment', 'PaymentsController@create');
    Route::post('/register/payment', 'PaymentsController@store');

    Route::post('/inventory/bulk_update', 'InventoryController@bulkUpdate');
    Route::get('/inventory/csv', 'InventoryController@getImportCsv');
    Route::get('/inventory/csv/categories', 'InventoryController@getCategories');
    Route::post('/inventory/csv', 'InventoryController@postImportCsv');
    Route::resource('inventory', 'InventoryController');
    Route::resource('inventory.images', 'InventoryImagesController');
    Route::get('/inventory/disable/{id}',   ['as' => 'inventory.product.disable',   'uses' => 'InventoryController@disable']);
    Route::get('/inventory/enable/{id}',    ['as' => 'inventory.product.enable',    'uses' => 'InventoryController@enable']);
    

    Route::get('/profile', 'SettingsController@profile');
    Route::patch('/profile', 'SettingsController@updateProfile');
    Route::post('/settings/deductions', 'SettingsController@storeDeduction');
    Route::delete('/settings/deductions', 'SettingsController@destroyDeduction');
    Route::post('/settings/amazon/connect', 'SettingsController@connectAmazon');
    Route::get('/settings/ebay/get_ebay_auth_url', 'SettingsController@getEbayAuthUrl');
    Route::get('/settings/ebay/accepted', 'SettingsController@accepted');
    Route::delete('/settings/disconnect', 'SettingsController@disconnect');
    Route::get('/settings', 'SettingsController@index');

    Route::get('/merchants/{id}/impersonate', 'MerchantsController@impersonate')->middleware('admin');
    Route::resource('merchants', 'MerchantsController');
    Route::post('/merchants/{id}', 'MerchantsController@update');

    Route::resource('schedules', 'SchedulesController', ['except' => ['show']]);
    Route::get('schedules/clear-history',               ['as' => 'schedules.clear',     'uses' => 'SchedulesController@clearHistory']);
    Route::get('schedules/download-history/{id}',       ['as' => 'schedules.download',  'uses' => 'SchedulesController@downloadHistory']);
    
    Route::resource('orders', 'OrdersController');
    Route::resource('shipments', 'ShipmentsController');
    Route::resource('reports', 'ReportsController');
    Route::resource('categories', 'CategoriesController');
    Route::resource('categories.taxes', 'CategoriesTaxesController');
//    Route::resource('shipping_deductions', 'ShippingDeductionsController');

});

Route::group(['prefix' => 'verify'], function () {

    Route::get('/', 'VerificationsController@notify');
    Route::get('/failure', 'VerificationsController@failure');
    Route::get('/{email}/{token}', 'VerificationsController@verify');

});

Route::get('{slug}', 'ErrorsController@notFound')->where('slug', '.*');
