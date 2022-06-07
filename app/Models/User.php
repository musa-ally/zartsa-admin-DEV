<?php

namespace App\Models;

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
    public function hasRole($name){
        $role = DB::select('call sp_check_user_has_role (?,?)', array(Auth::id(), $name));
        return $role[0]->status_code == 300;
    }

	public function hasPermission($name){
        $permission = DB::select('call sp_check_user_has_permission (?,?)', array(Auth::id(), $name));
        return $permission[0]->status_code == 300;
    }

    public function generateCode(){
        $code = rand(1000, 9999);
        //$code = 1234;
        UserOtpCode::updateOrCreate(
            [ 'user_id' => auth()->user()->id ],
            [ 'code' => $code ],
            [ 'user_type' => 'I' ]
        );

        SendOTPSMS::dispatch($code, Auth::user()->first_name, Auth::user()->phone_number);
//        send email to user
        Log::info('AUTH EMAIL: '.Auth::user()->email);
        SendOTPEmail::dispatch($code, Auth::user()->email, Auth::user()->username);
    }
}
