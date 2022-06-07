<?php

namespace App\Http\Controllers\Identifications;

use App\Http\Controllers\Controller;
use App\Models\IdType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IdentificationController extends Controller
{
    function index() {
        if (!Auth::user()->hasPermission('view_identification_list')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $ids = IdType::all();
        return view('admin.identifications.index', compact('ids'));
    }

    public function store(Request $request){
        if (!Auth::user()->hasPermission('create_identification')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $request->validate([
            'name' => 'required|unique:id_types',
            'description' => 'required'
        ]);

        $idType = new IdType();
        $idType->name = $request['name'];
        $idType->description = $request['description'];
        if ($idType->save()){
            return redirect()->back()->with(['message' => 'ID has been added successfully!', 'error' => false]);
        }
        return redirect()->back()->with(['message' => 'Something went wrong!', 'error' => true]);
    }

    public function edit(Request $request){
        if (!Auth::user()->hasPermission('edit_identification')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $request->validate([
            'identification_id' => 'required',
            'name' => 'required',
            'description' => 'required'
        ]);

        $identity = IdType::query()->where('id', $request['identification_id'])->first();
        if (!$identity){
            return back()->withErrors(['Identification does not exist']);
        }

        if($identity->update($request->all())){
            return back()->with(['message' => 'ID has been updated successfully!', 'error' => false]);
        }
        return redirect()->back()->with(['message' => 'Something went wrong!', 'error' => true]);
    }

    public function block(Request $request){
        if (!Auth::user()->hasPermission('block_identification')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $request->validate([
            'row_id' => 'required',
            'reason' => 'required'
        ]);
        $identity = IdType::query()->where('id', $request['row_id'])->first();
        if (!$identity){
            return back()->withErrors(['Identification does not exist']);
        }
        $status = 'IA001';
        if ($identity->service_status_code == 'IA001'){
            $status = 'AC001';
        }
        if ($identity->update(['service_status_code' => $status])){
            return back()->with(['message' => 'Operation success']);
        }
        return back()->withErrors(['Operation failed']);
    }

    public function searchIdentity(Request $request){
        $query = $request['body'];
        if (empty($query)){
            $identifications = IdType::select('id', 'name', 'description', 'service_status_id')->get();
        }else{
            $identifications = DB::select('call sp_search_id_types(?)', array($query));
        }
        return response()->json(['results' => $identifications]);
    }
}
