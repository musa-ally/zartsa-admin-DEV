<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Models\UserOtpCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class TwoFAController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }



    public function show2fa(Request $request){
//        check if latest otp code for this user has expired
        $otp = UserOtpCode::where(['user_id' => auth()->id(), 'user_type' => 'I'])
            ->where('updated_at', '>=', now()->subMinutes(2))
            ->first();
        if (!$otp){
            $loginCtrl = new LoginController();
            $loginCtrl->logout($request);
        }
        return view('account.password_security.2fa_index');
    }

    public function check2fa(Request $request){
        $request->validate([
            'token'=>'required',
        ]);

        $otp = UserOtpCode::query()->where(['user_id' => auth()->user()->id, 'user_type' => 'I'])->first();
          

        if($otp && Hash::check($request->token, $otp->code))
         {
            if ($otp->updated_at >= now()->subMinutes(2)){
                Session::put('user_2fa', auth()->user()->id);
                if (auth()->user()->hasRole('super_admin')) {
                    return redirect()->to('home');
                } else if (auth()->user()->hasRole('admin')) {
                    return redirect()->to('home');
                } else if (
                    auth()->user()->hasRole('staff')
                    || auth()->user()->hasRole('inspector')
                  
                ) {
                    return redirect()->to('applications');
                } else {
                    return 'error';
                }
            }
            return back()->withErrors(['The code you have entered has expired']);
        }

        return back()->withErrors(['You have entered a wrong code']);
    }

   

    public function resend2fa(){
        auth()->user()->generateCode();
        return back()->with('message', 'We sent you a code on your email address.');
    }
}
