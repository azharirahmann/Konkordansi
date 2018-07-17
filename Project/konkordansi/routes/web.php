<?php

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

Route::get('/', 'HomeController@showHome')->name('showHome');
Route::get('hasil-pencarian', 'HomeController@cari')->name('hasilPencarian');
Route::get('hasil-pencarian-range/{kata_kunci}', 'HomeController@applyCari')->name('hasilPencarianApply');
Route::get('getAya', 'HomeController@getAyaFromSura')->name('getAya');
Route::get('submitAya', 'HomeController@submitAyat')->name('submitAya');

Route::get('hasil-pencarian-lemma/{id}', 'HomeController@lemma')->name('lemma');
Route::get('hasil-pencarian-katafrasa/{id}', 'HomeController@kataFrasa')->name('kataFrasa');
Route::get('generate-sinonim/{id}', 'HomeController@generateSyn')->name('gen-sinonim');
Route::get('hasil-pencarian-sinonim/{id}', 'HomeController@sinonim')->name('sinonim');
Route::get('get-result-sinonim', 'HomeController@getResultSyn');
Route::get('tesLCS', 'HomeController@getLongestCommonSubstring')->name('LCS');
Route::get('lcs', 'HomeController@lcs')->name('tesPy');
Route::get('sendArray', 'HomeController@sendArray');
