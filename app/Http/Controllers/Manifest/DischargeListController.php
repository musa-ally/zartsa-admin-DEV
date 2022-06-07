<?php

namespace App\Http\Controllers\Manifest;

use App\Http\Controllers\Controller;
use App\Models\TblBillOfLading;
use App\Models\TblCargo;
use App\Models\TblVoyage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DischargeListController extends Controller
{
    public function store(Request $request){
        $data = $request->json()->all();
        Log::info('INSERT DISCHARGE PAYLOAD: '.$request->getContent());
        $rules = [
            "referenceNumber" => "required",
            "shippingLine" => "required",
            "voyageNumber" => 'required',
            "vesselCode" => "required",
            "vesselName" => "required",
            "expectedArrivalDate" => "required|date",
            "expectedDepartureDate" => "required|date",
            "actualArrivalDate" => "required|date",
            "lastUpdated" => "required|date_format:Y-m-d H:i:s"
        ];

        $validator = Validator::make($data, $rules);
        zpc_abort_if($validator->fails(), zpc_parse_message_bag($validator->errors()));

        DB::beginTransaction();
        try {
            $voyage = TblVoyage::create([
                'number' => $data['voyageNumber'],
                'estimated_arrival_date' => $data['expectedArrivalDate'],
                'arrival_date' => $data['actualArrivalDate'],
                'departure_date' => $data['expectedDepartureDate'],
                'vessels_name' => $data['vesselName'],
                'vessel_code' => $data['vesselCode'],
                'shipping_line' => $data['shippingLine'],
            ]);

            if (!$voyage){
                DB::rollBack();
                zpc_abort('Could not insert voyage', 500);
            }

            foreach ($data['bolList'] as $bol){
                $tblBol = TblBillOfLading::create([
                    'number' => $bol['blNumber'],
                    'consignee' => $bol['consignee'],
                    'notify' => $bol['notifyParty'],
                    'port_of_lading' => $bol['portOfLoading'],
                    'tbl_voyages_id' => $voyage['id']
                ]);
                if (!$tblBol){
                    DB::rollBack();
                    zpc_abort('Could not insert bill of lading', 500);
                }
                foreach ($bol['cargoList'] as $cargo){
                    $tblCargo = TblCargo::create([
                        'number' => $cargo['containerNumber'],
                        'weight_kg' => $cargo['weight'],
                        'remarks' => $cargo['containerRemarks'],
                        'cargo_type' => $cargo['cargoType'],
                        'container_type' => $cargo['containerType'],
                        'container_size' => $cargo['containerSize'].'',
                        'tbl_bill_of_ladings_id' => $tblBol['id'],
                        'tbl_bill_of_ladings_voyages_id' => $voyage['id'],
                        'is_electric' => $cargo['is_electric'],
                        'content' => $cargo['containerContent'],
                    ]);
                    if (!$tblCargo){
                        DB::rollBack();
                        zpc_abort('Could not insert cargo', 500);
                    }
                }
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Discharge list successfully inserted', 'code' => 200]);
        }catch (\Throwable $ex){
            DB::rollBack();
            Log::error('INSERT DISCHARGE LIST ERROR: '.$ex);
            return response()->json(['status' => 'failed', 'message' => $ex->getMessage(), 'code' => 500]);
        }
    }

    public function update($number){}
}
