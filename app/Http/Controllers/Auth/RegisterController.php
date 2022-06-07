<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendRegistrationEmail;
use App\Mail\UserRegistration;
use App\Models\InternalUsersHasInternalUserRole;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::USER_LIST;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data){
        return Validator::make($data, [
            'username' => ['required', 'string', 'max:45'],
            'first_name' => ['required', 'string', 'max:45'],
            'last_name' => ['required', 'string', 'max:45'],
            'email' => ['required', 'email', 'max:255', 'unique:internal_users'],
            'phone_number' => ['max:10', 'min:10', 'unique:internal_users'],
            'id_number' => ['required','unique:internal_users'],
            'gender' => ['required'],
            'id_types_id' => ['required'],
            'role' => ['required'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data){
        DB::beginTransaction();
        try {
            $password = $this->randomPassword();
            $user =  User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone_number' => $data['phone_number'],
                'username' => $data['username'],
                'id_number' => $data['id_number'],
                'email' => $data['email'],
                'gender' => $data['gender'],
                'id_types_id' => $data['id_types_id'],
                'password' => Hash::make($password),
            ]);

//            save
            $userRole = new InternalUsersHasInternalUserRole();
            $userRole->internal_users_id = $user->id;
            $userRole->internal_users_account_status_code = 'AC001';
            $userRole->internal_user_roles_id = $data['role'];

            if (!$userRole->save()){
                Log::error('REGISTRATION_ERROR', ['Could not save user role']);
                DB::rollBack();
                return null;
            }

            DB::commit();

            $full_name = $user->first_name.' '.$user->last_name;
            SendRegistrationEmail::dispatch($full_name, $user->username, $user->email, $password);
            return $user;

        }catch (\Exception $ex){
            DB::rollBack();
            Log::error('REGISTRATION_ERROR', [$ex->getMessage()]);
            return null;
        }
    }

    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());
        event(new Registered($user));

        if ($user != null) {
            return redirect()->back()->with(['message' => 'User successfully created!', 'error' => false]);
        }
        return redirect()->back()->with(['message' => "Something went wrong!", 'error' => true]);
    }

    /**
     * Show the application registration form.
     *
     * @return View
     */
    public function showRegistrationForm(){
        return view('login');
    }
}
