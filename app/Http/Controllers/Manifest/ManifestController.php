<?php

namespace App\Http\Controllers\Manifest;

use App\Http\Controllers\Controller;
use App\Imports\CargoImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ManifestController extends Controller
{
    public function upload(Request $request, $shipLineCode){
        $request->validate([
            'number' => 'required|unique:voyages',
            'ead' => 'required',
            'vessel_name' => 'required',
            'dod' => 'required',
            'xlsFile' => 'required',
        ]);
        $voyageNumber = $request['number'];
        $estimatedADate = $request['ead'];
        $departureDate = $request['dod'];
        $vesselName = $request['vessel_name'];

        $allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        if(in_array($_FILES["xlsFile"]["type"],$allowedFileType)){
            $s_import = new CargoImport($shipLineCode, $vesselName, $voyageNumber, $estimatedADate, $departureDate);
            Excel::import($s_import, $request->file('xlsFile'));
            if (count($s_import->getErrors()) > 0){
                return redirect()->back()->withErrors($s_import->getErrors());
            }
            return redirect()->back()->with(['message' => 'Manifest added successfully']);
        }
        return redirect()->back()->withErrors(['Invalid File Type. Upload Excel File.']);
    }

    public function shoVoyages($code, $vesselId){
        $voyages = DB::select('call sp_get_voyages(?)', array(Crypt::decrypt($vesselId)));
        return view('admin.shipping_line.voyages',compact('voyages', 'code'));
    }

    public function showBillOfLadings($code, $voyageId){
        $bols = DB::select('call sp_get_bill_of_ladings(?)', array($voyageId));
        return view('admin.shipping_line.bill_of_ladings',compact('bols', 'code', 'voyageId'));
    }

    public function showTblBillOfLadings($voyageId){
        $bols = DB::select('call sp_get_tbl_bill_of_ladings(?)', array($voyageId));
        return view('admin.shipping_line.bill_of_ladings',compact('bols', 'voyageId'));
    }

    public function showCargo($code, $voyageId, $bolId){
        $cargos = DB::select('call sp_get_cargo(?)', array($bolId));
        return view('admin.shipping_line.cargo',compact('cargos', 'code', 'voyageId'));
    }

    public function showTblCargo($voyageId, $bolNumber){
        $cargos = DB::select('call sp_get_cargo_by_bol(?)', array($bolNumber));
        return view('admin.shipping_line.cargo',compact('cargos', 'voyageId'));
    }
}
