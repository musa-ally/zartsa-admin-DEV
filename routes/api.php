<?php


use App\Http\Controllers\Manifest\DischargeListController;
use App\Http\Controllers\v1\DashboardController;
use App\Http\Controllers\v1\ExchangeRateController;
use App\Http\Controllers\v1\GepgController;
use App\Http\Controllers\v1\SMSController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\StuffingController;
use App\Http\Controllers\v1\DestuffingController;
use App\Http\Controllers\v1\ApplicationController;
use App\Http\Controllers\v1\ClearanceController;
use App\Http\Controllers\v1\CustomerController;
use App\Http\Controllers\v1\BillController;
use App\Http\Controllers\v1\DischargeController;
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

Route::group([
    'middleware' => 'api',
    'prefix'     => 'auth'
], function ($router) {

    //Applications
    Route::get('customer_applications', [ApplicationController::class, 'index']);
    Route::post('update_application_status', [ApplicationController::class, 'update']);

    //Dashboard
    Route::get('applications_stats', [DashboardController::class, 'index']);

    //Customer
    Route::get('customer_personal_data', [CustomerController::class, 'index']);

    //Bills
    Route::get('customer_bills', [BillController::class, 'index']);
    Route::post('save_customer_bills', [BillController::class, 'store']);

    //Clearance
    Route::get('check_bol', [ClearanceController::class, 'index']);
    Route::post('clear_cargo', [ClearanceController::class, 'store']);


    //Discharge
    Route::post('discharge_cargo', [DischargeController::class, 'update']);


    //Stuffing
    Route::get('check_bol_stuffing', [StuffingController::class, 'index']);


    //Destuffing
    Route::get('check_bol_destuffing', [DestuffingController::class, 'index']);
    Route::post('create_destuffing_bill', [DestuffingController::class, 'store']);

    //gepg
    Route::post('generate_gepg_bill', [GepgController::class, 'generatebill']);
    Route::get('gepg_bill_req_ack_received', [GepgController::class, 'gepgbillsubreqack']);
    Route::get('gepg_bill_req_resp', [GepgController::class, 'gepgbillsubresponse']);
    Route::post('gepg_bill_req_ack_send', [GepgController::class, 'gepgbillsubreqackreturn']);
    Route::post('gepg_pay_bill', [GepgController::class, 'gepgpaybill']);
    Route::get('gepg_pay_bill_ack', [GepgController::class, 'gepgpaybillack']);

    //Exchange Rate
    Route::get('fetch_exchange_rates', [ExchangeRateController::class, 'fetchrates']);

    //SMS
    Route::post('send_msg', [SMSController::class, 'initSMS']);
});

Route::post('port/discharge-list', [DischargeListController::class, 'store']);
Route::put('port/discharge-list/{id}', [DischargeListController::class, 'update']);
