<?php

use App\Events\FunctionAnnounced;
use App\Http\Controllers\api\Admin_Dashboard\ClassReportController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});
Route::get('gfg', function () {
    return view('checkConnection');
});
Route::get('/chat', function () {
    return view('chat');
});
Route::get('/announcement', function () {
    return view('announcement');
});
Route::get('/private', function () {
    return view('private-channel');
});
Route::get('/chat-box', function () {
    return view('chat_box');
});
Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');

Route::get('/students',[ClassReportController::class,'index'])->name('students.index');
Route::get('/prnpriview',[ClassReportController::class,'print'])->name('students.print');