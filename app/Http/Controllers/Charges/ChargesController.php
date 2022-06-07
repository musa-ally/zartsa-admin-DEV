<?php

namespace App\Http\Controllers\Charges;

use App\Http\Controllers\Controller;
use App\Models\InternalUserRole;
use App\Models\Service;
use App\Models\ServiceCharges;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChargesController extends Controller
{
    public function index(){
        $charges = ServiceCharges::with('serviceDetails')->get();
        $services = Service::query()->select('id','name')->get();
        return view('admin.charges.index',compact('charges','services'));
    }

    public function store(Request $request){
        if (!Auth::user()->hasPermission('create_charges')){
            return back()->withErrors(['You are not Authorized!']);
        }
        $request->validate([
            'service_id' => 'required|unique:service_charges',
            'service_charge' => 'required',
            'charge_discount' => 'required',
            'charge_vat' => 'required',
            'grace_period' => 'required'
        ]);

        $new_service_charge = new ServiceCharges();
        $new_service_charge->service_id = $request->service_id;
        $new_service_charge->service_charge = doubleval($request->service_charge);
        $new_service_charge->service_charge_discount = doubleval($request->charge_discount);
        $new_service_charge->service_charge_vat = $request->charge_vat;
        $new_service_charge->payment_grace_period = $request->grace_period;
        if ($new_service_charge->save()){
            return redirect()->back()->with(['message' => 'Service Charge has been added Successfully!', 'error' => false]);
        }
        return redirect()->back()->with(['message' => 'Something went wrong!', 'error' => true]);
    }

    public function edit(Request $request){
//        if (!Auth::user()->hasPermission('edit_charges')){
//            return back()->withErrors(['You are not Authorized!']);
//        }

        $request->validate([
            'charge_id' => 'required',
            'service_id' => 'required',
            'service_charge' => 'required',
            'charge_discount' => 'required',
            'charge_vat' => 'required',
            'grace_period' => 'required'
        ]);

        $update_service_charge = ServiceCharges::query()->where(['id'=> $request->charge_id])->first();
        $update_service_charge->service_id = $request->service_id;
        $update_service_charge->service_charge = doubleval($request->service_charge);
        $update_service_charge->service_charge_discount = doubleval($request->charge_discount);
        $update_service_charge->service_charge_vat = $request->charge_vat;
        $update_service_charge->payment_grace_period = $request->grace_period;
        if ($update_service_charge->save()){
            return redirect()->back()->with(['message' => 'Service Charge has been updated Successfully!', 'error' => false]);
        }
        return redirect()->back()->with(['message' => 'Something went wrong!', 'error' => true]);
    }

    public function status_change(Request $request){
//        if (!Auth::user()->hasPermission('edit_charges')){
//            return back()->withErrors(['You are not Authorized!']);
//        }

        $request->validate([
            'charge_id' => 'required',
            'status' => 'required',
            'status_reason' => 'required'
        ]);

        $update_service_charge_status = ServiceCharges::query()->where(['id'=> $request->charge_id])->first();
        $update_service_charge_status->service_approval = $request->status;
        $update_service_charge_status->status_reason = $request->status_reason;
        if ($update_service_charge_status->save()){
            return redirect()->back()->with(['message' => 'Service Charge Status has been updated Successfully!', 'error' => false]);
        }
        return redirect()->back()->with(['message' => 'Something went wrong!', 'error' => true]);
    }
}
