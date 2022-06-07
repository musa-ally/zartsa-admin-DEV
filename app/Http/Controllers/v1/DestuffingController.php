<?php


namespace App\Http\Controllers\v1;

use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;
use App\Models\Application;
use App\Models\Bill;
use App\Models\CargoSelection;

class DestuffingController extends Controller
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
        $bol = $request->input('bol');
//        $service_name = $request->input('service_name');
        $checkbill = DB::select('CALL sp_check_bol_destuffing(?)', array($bol));

        if ($checkbill == null || '') {
            return response()->json(['message' => 'BoL Does not exist'], 404);
        } else {
            return response()->json($checkbill);
        }
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
        $cn = IdGenerator::generate(['table' => 'customer_application_bills', 'field' => 'control_number', 'length' => 14, 'prefix' => 9989 .date('ymd')]);
        $service_code = 'DS001';

        $application = new Application();
        $application->services_code      = $service_code;
        $application->external_users_id  = Auth::user()->id;
        $application->user_type  = 'I';
        $application->application_status_code = 'DS001';
        $application->payer_full_name = $request->payer_full_name;
        $application->payer_phone_number = $request->payer_phone_number;
        $application->payer_email_address = $request->payer_email_address;
        $application->save();

        $bill = new Bill();
        $bill->amount_tzs    = $request->amount_tzs;
        $bill->amount_usd    = $request->amount_usd;
        $bill->exchange_rate = $request->xchange_rate;
        $bill->control_number = $cn;
        $bill->customer_service_applications_id = $application->id;
        $bill->customer_service_applications_services_code = $service_code;
        $bill->customer_service_applications_external_users_id = Auth::user()->id;
        $bill->bill_status_code = 'PE001';
        $bill->user_type = 'I';
        $bill->save();

        foreach ($request->cargoselection as $key => $value) {

            $bid = $request->input('bid');
            $extu = Auth::user()->id;
            $service_code = 'DS001';

            $cargosel = new CargoSelection();
            $cargosel->cargo_id = $value['id'];
            $cargosel->cargo_bill_of_ladings_id = $bid;
            $cargosel->customer_service_applications_id = $application->id;
            $cargosel->customer_service_applications_services_code = $service_code;
            $cargosel->customer_service_applications_external_users_id = $extu;
            $cargosel->cargo_cargo_types_id = $value['cargo_type_id'];
            $cargosel->cargo_bill_of_ladings_voyages_id = $value['voyage_id'];
            $cargosel->cargo_bill_of_ladings_voyages_zpc_ports_id = 1;
            $cargosel->cargo_bill_of_ladings_voyages_vessels_id = $value['vessel_id'];
            $cargosel->customer_service_applications_application_status_code = $application->application_status_code;
            $cargosel->user_type = 'I';
            $cargosel->service_bill_formulars_id = 1;
            $cargosel->save();

            $val = CargoSelection::where('id', $cargosel->id)->update(['is_destuffed' => 'YES']);
            $chval = CargoSelection::where('id', $value['ccsid'])->update(['stage' => 'Destuffing']);
        }
        return response()->json($bill);
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
     //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    //
    }
	
}
