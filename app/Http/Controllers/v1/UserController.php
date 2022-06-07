<?php
namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\User;
use Validator;
use Illuminate\Validation\Rule;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class UserController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = DB::table('internal_users')
			   ->OrderBy('userid', 'ASC')
		       ->get();
			 
		return response()->json($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'email' => 'unique:internal_users',
		], [
			'email.unique' => 'Email Already Registered',
			
		]);
		
		if ($validator->fails()) { 
           return response()->json(['errors'=>$validator->errors()], 422);
        }
		
	   $id = IdGenerator::generate(['table' => 'internal_users', 'field' => 'userid', 'length' => 7, 'prefix' =>'USR', '-' ]);
		
	   $usr = $id;
	   $user = new User();
	   $user->userid   = $usr;
	   $user->fname    = $request->fname;
	   $user->sname    = $request->sname;
	   $user->email    = $request->email;
	   $user->phoneno  = $request->phoneno;
	   $user->role_name  = $request->role_name;
	   $user->password = bcrypt($request->password);
       $user->save();
		
	   return response()->json($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
	  $user = User::findOrFail($id);
      $validator = Validator::make($request->all(), [
			'email' => Rule::unique('internal_users')->ignore($user),
		], [
			'email.unique' => 'Email Already Registered',
			
		]);
		
		if ($validator->fails()) { 
           return response()->json(['errors'=>$validator->errors()], 422);
        }
		
	   $user = User::findOrFail($id);
       $user->fname     = $request->fname;
       $user->sname     = $request->sname;
       $user->email     = $request->email;
       $user->phoneno   = $request->phoneno;
       $user->role_name = $request->role_name;
       $user->save();
		
	   return response()->json($user);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $user = User::findOrFail($id);
      $user->delete();
      return response()->json(['User Deleted']);
    }
	
}
