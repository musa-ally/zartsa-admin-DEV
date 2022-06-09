<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Identifications\IdentificationController;
use App\Http\Controllers\Manifest\ManifestController;
use App\Http\Controllers\RolesPermissions\RolesPermissionController;
use App\Http\Controllers\Security\TwoFAController;
use App\Http\Controllers\Services\ServiceController;
use App\Http\Controllers\User\AccountController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Charges\ChargesController;

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

Route::get('/clear-cache', function() {
        $run = Artisan::call('config:clear');
        $run = Artisan::call('cache:clear');
        $run = Artisan::call('config:cache');
        $run = Artisan::call('view:cache');
        $run = Artisan::call('view:clear');
        return 'CACHE CLEARED';
    });

Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('reload-captcha', [LoginController::class, 'reloadCaptcha'])->name('reload.captcha');

Route::middleware(['auth'])->group(function () {
    Route::get('two-factor-auth-view', [TwoFAController::class, 'show2fa'])->name('2fa.index');
    Route::post('two-factor-auth', [TwoFAController::class, 'check2fa'])->name('2fa.check');
    Route::get('two-factor-auth/resend', [TwoFAController::class, 'resend2fa'])->name('2fa.resend');
});
Route::middleware(['2fa'])->group(function () {
    Route::get('home', [UserController::class, 'dashboard']);

//    ====================== users =========================== //
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/create',[UserController::class, 'create'])->name('user.create');
	Route::get('user/{id}',[UserController::class, 'show']);
    Route::post('users/create',  [UserController::class, 'store'])->name('user.register');
    Route::post('users/update',  [UserController::class, 'update'])->name('user.update');
    Route::get('users/search',  [UserController::class, 'searchUsers'])->name('user.search');
    Route::put('user/toggle-block', [UserController::class, 'toggleBlock'])->name('user.block.toggle');
    Route::get('user/edit/{id}', [UserController::class, 'editUserView'])->name('user.edit.view');
    Route::put('user/edit/{id}', [UserController::class, 'editUser'])->name('user.edit');
    Route::post('user/email', [UserController::class, 'sendEmail'])->name('user.email');
    Route::get('error', function (){
        $code = 401;
        return view('custom_error_page', compact('code'));
    });
    Route::post('password-policy/add-remove', [UserController::class, 'addRemovePasswordPolicy'])->name('policy.add_remove');


//    ====================== ROLES & PERMISSIONS ==================== //
    Route::get('roles-permissions', [RolesPermissionController::class, 'index']);
    Route::post('permission', [RolesPermissionController::class, 'storePermission'])->name('permission.create');
    Route::put('permission', [RolesPermissionController::class, 'editPermission'])->name('permission.edit');
    Route::post('role', [RolesPermissionController::class, 'storeRole'])->name('role.create');
    Route::put('role', [RolesPermissionController::class, 'editRole'])->name('role.edit');
    Route::put('role/block', [RolesPermissionController::class, 'blockRole'])->name('role.block.toggle');
    Route::put('permission/block', [RolesPermissionController::class, 'blockPermission'])->name('permission.block.toggle');
    Route::get('role/{name}/{id}', [RolesPermissionController::class, 'showRole']);
    Route::get('role/search', [RolesPermissionController::class, 'searchRoles'])->name('role.search');
    Route::get('permission/search', [RolesPermissionController::class, 'searchPermissions'])->name('permission.search');
    Route::post('permission/add-remove', [RolesPermissionController::class, 'addRemovePermissionToRole'])->name('permission.add_remove');
    Route::post('user-permission/add-remove', [RolesPermissionController::class, 'addRemovePermissionToUser'])->name('permission.user.add_remove');


//    ====================== IDENTIFICATIONS ==================== //
    Route::get('identifications', [IdentificationController::class, 'index']);
    Route::post('identifications', [IdentificationController::class, 'store'])->name('identity.create');
    Route::put('identifications', [IdentificationController::class, 'edit'])->name('identification.edit');
    Route::put('identifications/block', [IdentificationController::class, 'block'])->name('identification.block.toggle');
    Route::get('identifications/search', [IdentificationController::class, 'searchIdentity'])->name('identity.search');


//    ====================== SERVICES ==================== //
    Route::get('services', [ServiceController::class, 'index']);
//    Route::post('services', [ServiceController::class, 'store'])->name('service.create');
    Route::post('services', [ServiceController::class, 'edit'])->name('service.edit');
    Route::get('services/search', [ServiceController::class, 'searchService'])->name('service.search');
    Route::put('services/toggle-block', [ServiceController::class, 'toggleBlock'])->name('service.block.toggle');



//    ====================== TARIFF ==================== //
    Route::get('charges', [ChargesController::class, 'index'])->name('charge.index');
    Route::get('charges/create', [ChargesController::class, 'create'])->name('charge.create');
    Route::post('charges/store', [ChargesController::class, 'store'])->name('charge.store');
    Route::post('charges/edit', [ChargesController::class, 'edit'])->name('charge.edit');
    Route::post('charges/status_change', [ChargesController::class, 'status_change'])->name('charge.status_change');


//    ====================== VUE JS ROUTES ==================== //
   Route::get('staff_dashboard', function (){
        return view('staff.dashboard');
    })->name('staff.dashboard');

    Route::get('check_clearance', function (){
        return view('staff.clearance.checkclearance');
    });

    Route::get('customer_applications', function (){
        return view('staff.applications.customer_applications');
    })->name('applications.customer');

    Route::get('my_applications', function (){
        return view('staff.applications.my_applications');
    })->name('my.applications');

    Route::get('stuffing', function (){
        return view('staff.stuffing.stuffing');
    });

    Route::get('stuffing/create_stuffing_bill', function (){
        return view('staff.stuffing.createstuffingbill');
    });

    Route::get('destuffing', function (){
        return view('staff.destuffing.destuffing');
    });

    Route::get('destuffing/create_destuffing_bill', function (){
        return view('staff.destuffing.createdestuffingbill');
    });


    Route::get('customer_applications/bill_clearance', function (){
        return view('staff.clearance.billclearance');
    });

    Route::get('my_applications/bill_clearance_staff', function (){
        return view('staff.clearance.billclearancestaff');
    });

    Route::get('check_clearance/create_bill', function (){
        return view('staff.bills.createbill');
    });

    Route::get('customer_applications/bill_clearance/view_bill', function (){
        return view('staff.bills.viewbill');
    });

    Route::get('my_applications/bill_clearance_staff/view_bill', function (){
        return view('staff.bills.viewbillstaff');
    });



//    ====================== SETTINGS ==================== //
    Route::get('settings', function () {
        return view('account.settings');
    });
    Route::get('password-settings', [AccountController::class, 'passwordSettings']);
    Route::get('audit-logs', [AccountController::class, 'auditLogView']);
    Route::post('password/update', [AccountController::class, 'updatePassword'])->name('update.password');

    Route::get('help', function () {
        return view('account.help');
    });
});
Auth::routes();


//==================== DAVID testing routes =======================
Route::get('dc-request-form', function (){
    return view('development_pages.dc_request_form');
})->name('dc-request-form');
