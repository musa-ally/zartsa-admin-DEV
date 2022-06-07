<?php

namespace App\Http\Controllers\Port;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ZpcPortController extends Controller{

    public function index(){
        $ports = DB::select('CALL sp_get_ports');
        return view('admin.ports.index', compact('ports'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|unique:zpc_ports',
            'address' => 'required'
        ]);


        $port = DB::select('CALL sp_insert_port(?,?)',array(
                $request['name'],$request['address'])
        );

        if ($port[0]->status_code == 300){
            return redirect()->back()->with(['message' => $port[0]->message, 'error' => false]);
        }
        return redirect()->back()->with(['message' => $port[0]->message, 'error' => true]);
    }
}
