<?php

namespace App\Http\Controllers\ShipLine;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShippingLineController extends Controller
{
    public function dischargeList(){
        if (!Auth::user()->hasPermission('view_discharge_list')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $voyages = DB::select('CALL sp_get_tbl_voyages(?)', array(0));
        return view('admin.shipping_line.discharge_list', compact('voyages'));
    }

    public function index(){
        $ships = DB::select('CALL sp_get_ship_lines');
        return view('admin.shipping_line.index', compact('ships'));
    }

    public function showVessel($code){
        $vessels = DB::select('CALL sp_get_vessels(?)', array($code));
        return view('admin.shipping_line.vessels', compact('vessels', 'code'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|unique:zpc_ports'
        ]);


        $port = DB::select('CALL sp_insert_shipping_line(?,?)',array(
                $request['name'],substr(strtoupper($request['name']), 0, 2).'001'));

        if ($port[0]->status_code == 300){
            return redirect()->back()->with(['message' => $port[0]->message, 'error' => false]);
        }
        return redirect()->back()->with(['message' => $port[0]->message, 'error' => true]);
    }

    public function storeVessel(Request $request, $code){
        $request->validate([
            'name' => 'required|unique:vessels'
        ]);

        $vessel = DB::select('CALL sp_insert_vessel(?,?)',array($request['name'],$code));

        if ($vessel[0]->status_code == 300){
            return redirect()->back()->with(['message' => $vessel[0]->message, 'error' => false]);
        }
        return redirect()->back()->with(['message' => $vessel[0]->message, 'error' => true]);
    }

    public function searchShippingLine(Request $request){
        $query = $request['body'];
        if (empty($query)){
            $ships = DB::select('CALL sp_get_ship_lines');
        }else{
            $ships = DB::select('call sp_search_shipping_lines(?)', array($query));
        }
        return response()->json(['results' => $ships]);
    }
}
