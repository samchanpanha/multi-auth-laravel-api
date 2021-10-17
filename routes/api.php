<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v1'],function(){

    //general unauthenticated routes here

    Route::group(['prefix' => 'customer'],function(){

        Route::post('sign-up','CustomerController@signUp');
    //unauthenticated routes for customers here

    Route::group( ['middleware' => ['auth:customer','scope:customer'] ],function(){
           // authenticated customer routes here
           Route::post('dashboard','CustomerController@dashboard');
        });
    });

    Route::group(['prefix' => 'staff'],function(){

    Route::post('sign-up','StaffController@signUp');
    //unauthenticated routes for customers here

    Route::group( ['middleware' => ['auth:staff','scope:staff'] ],function(){
           // authenticated staff routes here
           Route::post('dashboard','StaffController@dashboard');
        });
    });

});
