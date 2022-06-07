<?php

namespace App\Http\Controllers\User;

use AdventDev\SuspiciousLogins\Models\LoginAttempt;
use App\Http\Controllers\Controller;
use App\Jobs\SendPasswordChangeEmail;
use App\Models\PasswordPolicy;
use Eddytim\Auditlog\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller{

    public function __construct(){
        $this->middleware('2fa');
    }

    public function passwordSettings() {
        $policies = PasswordPolicy::all();
        $requiredPolicy = [];
        foreach ($policies as $policy){
            if ($policy->value >= 1){
                if ($policy->name == 'length'){
                    $requiredPolicy[] = 'Length >= '.$policy->value.', ';
                }else{
                    $requiredPolicy[] = $policy->display_name.', ';
                }
            }
        }
        return view('account.password_security.index', compact('policies', 'requiredPolicy'));
    }

    public function updatePassword(Request $request){
        $request->validate([
            'old_pass' => 'required',
            'password' => ['required', 'string', 'min:4', 'confirmed']
        ]);
        $password = $request['password'];

        if (Hash::check($request['old_pass'], Auth::user()->getAuthPassword())){
//            check password policy
            $policies = PasswordPolicy::all();
            foreach ($policies as $policy){
                if ($policy->name == 'length'){
//                    check password length
                    if (strlen($password) < $policy->value){
                        return redirect()->back()->withErrors(['Password length should be equal or greater than '.$policy->value]);
                    }
                }
                if ($policy->name == 'has_special_characters' && $policy->value == 1 && !preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $password)){
//                    policy requires special characters but non found in password
                    return redirect()->back()->withErrors(['Include at least a special character in your password']);
                }
                if ($policy->name == 'has_alphabtes' && $policy->value == 1 && !preg_match("/[a-z]/i", $password)){
//                    policy requires alphabets but non found in password
                    return redirect()->back()->withErrors(['Include at least an alphabet in your password']);
                }
                if ($policy->name == 'capital_letters' && $policy->value == 1 && !preg_match("/[A-Z]/", $password)){
//                    policy requires capital letter but non found in password
                    return redirect()->back()->withErrors(['Include at least an uppercase in your password']);
                }
                if ($policy->name == 'has_numeric' && $policy->value == 1 && !preg_match('~[0-9]+~', $password)){
//                    policy requires numbers but non found in password
                    return redirect()->back()->withErrors(['Include at least a number in your password']);
                }
            }
            $user = Auth::user();
            $user->password = Hash::make($password);
            if ($user->save()){
                SendPasswordChangeEmail::dispatch(Auth::user()->email);
                return redirect()->back()->with(['message' => 'Password updated successfully']);
            }
            return redirect()->back()->withErrors(['Password could not be updated']);
        }
        return redirect()->back()->withErrors(['Incorrect old password']);
    }

    public function auditLogView(){
        $logs = AuditLog::getAuditLogs(0);
        $login_logs = LoginAttempt::query()->latest()->limit(50)->get();
        return view('account.audit_logs', compact('logs', 'login_logs'));
    }
}
