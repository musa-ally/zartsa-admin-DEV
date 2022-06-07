<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{

    public function index(){
        if (!Auth::user()->hasPermission('view_list_of_services')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $services = DB::select('CALL sp_get_services');
        return view('admin.services.index', compact('services'));
    }

    public function store(Request $request){
        if (!Auth::user()->hasPermission('create_service')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $request->validate([
            'name' => 'required|unique:services',
            'description' => 'required'
        ]);

        $service_name = strtoupper($request['name']);

        $service = DB::select('CALL sp_insert_service(?,?,?)',array(
            $request['name'],$request['description'],substr($service_name, 0, 2).'001')
        );

        if ($service[0]->status_code == 300){
            return redirect()->back()->with(['message' => $service[0]->message, 'error' => false]);
        }
        return redirect()->back()->with(['message' => $service[0]->message, 'error' => true]);
    }

    public function edit(Request $request){
        Log::info($request->all());
        if (!Auth::user()->hasPermission('edit_service')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $request->validate([
            'name' => 'required',
            'description' => 'required'
        ]);

        $update_services = Service::query()->where(['id' => $request['service_id']])->first();
        $update_services->name = strtoupper($request->name);
        $update_services->description = $request->description;

        if ($update_services->save()){
            return back()->with(['message' => 'Service updated successfully']);
        }

        return back()->withErrors(['Could not update service']);
    }

    public function searchService(Request $request){
        $query = $request['body'];
        if (empty($query)){
            $services = Service::select('id', 'name', 'description', 'service_status_id', 'service_code')->get();
        }else{
            $services = DB::select('call sp_search_services(?)', array($query));
        }
        return response()->json(['results' => $services]);
    }

    public function toggleBlock(Request $request){
        if (!Auth::user()->hasPermission('block_services')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $request->validate([
            'row_id' => 'required',
            'reason' => 'required'
        ]);
        $service = Service::query()->where('id', $request['row_id'])->first();
        if (!$service){
            return back()->withErrors(['Service does not exist']);
        }
        $status = 'IA001';
        if ($service->service_status_code == 'IA001'){
            $status = 'AC001';
        }
        if ($service->update(['service_status_code' => $status])){
            return back()->with(['message' => 'Operation success']);
        }
        return back()->withErrors(['Operation failed']);
    }
}
