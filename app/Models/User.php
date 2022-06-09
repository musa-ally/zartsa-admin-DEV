<?php

namespace App\Models;

use App\Helpers\ZmCore;
use App\Jobs\SendOTPEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use App\Jobs\SendOTPSMS;
use Illuminate\Support\Facades\Hash;
use \DateTimeInterface;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "internal_users";

    public function idType(){
        return $this->hasOne(IdType::class,'id','id_types_id');
    }

    public function roleRelationship(){
        return $this->hasOne(InternalUsersHasInternalUserRole::class,'internal_users_id','id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'phone_number',
        'id_types_id',
        'id_number',
        'email',
        'gender',
        'account_status_code',
        'password',
        'is_approver'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @param $name
     * @return bool
     */

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function hasRole($name){
        $role = DB::select('call sp_check_user_has_role (?,?)', array(Auth::id(), $name));
        return $role[0]->status_code == 300;
    }

	public function hasPermission($name){
        $permission = DB::select('call sp_check_user_has_permission (?,?)', array(Auth::id(), $name));
        return $permission[0]->status_code == 300;
    }

    public function status(){
        return $this->belongsTo(AccountStatus::class, 'account_status_code', 'code');
    }

    public function generateCode(){
        $code = rand(1000, 9999);
       
        $user_otp = UserOtpCode::query()->where(['user_id' => auth()->id(), 'user_type' => 'I'])->first();
        
        if ($user_otp){
            $user_otp->update(['code' => Hash::make($code)]);
        }else{
            UserOtpCode::create(['user_id' => auth()->id(), 'code' => Hash::make($code), 'user_type' => 'I']);
        }

        // send message to user
        SendOTPSMS::dispatch($code, Auth::user()->first_name, ZmCore::formatPhone(Auth::user()->phone_number));;
        Log::info('send OTP to user ===');
        // send email to user
        if(config('app.env') == 'production') {
            SendOTPEmail::dispatch($code, Auth::user()->email, Auth::user()->username);;
        }
    }
}
