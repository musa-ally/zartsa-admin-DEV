<?php


namespace App\Http\Controllers\v1;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Spatie\ArrayToXml\ArrayToXml;
use Validator;

class GepgController extends Controller
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

    public function generatebill(Request $request) {
        $exp_date = Carbon::now()->addDays(7);
        $cn = IdGenerator::generate(['table' => 'customer_application_bills', 'field' => 'control_number', 'length' => 14, 'prefix' => 9989 .date('ymd')]);
        $itemrefno = rand(100000, 999999);
        $pyrid = rand(10000, 99999);
        $billid = rand(1000000, 3000000);
        $billexpdate = $exp_date;
        $cons = $request->input('consignee');
        $billdesc = $request->input('billdesc');
        $billdate = Carbon::now();
        $genby = $request->input('genby');
        $aprby = $request->input('aprby');
        $phoneno = $request->input('phoneno');
        $pyremail = $request->input('pyremail');
        $billamount = $request->input('billamount');

        return $this->gepgsendbill($pyrid, $billid, $billexpdate, $cons, $billdesc, $billdate, $genby, $aprby, $phoneno, $pyremail, $itemrefno, $billamount, $cn);
    }

    public function gepgsendbill($pyrid, $billid, $billexpdate, $cons, $billdesc, $billdate, $genby, $aprby, $phoneno, $pyremail, $itemrefno, $billamount, $cn)
    {
//        $client = new Client(['verify' => false]);
//        $apiurl = '';
        $gsb = [
            'gepgBillSubReq' => [
                'BillHdr' => [
                    'SpCode' => '',
                    'RtrRespFlg' => 'true'
                ],
                'BillTrxInf' => [
                    'BillId' => $billid,
                    'SubSpCode' => '',
                    'SpSysId' => '',
                    'BillAmt' => $billamount,
                    'MiscAmt' => 0,
                    'BillExprDt' => $billexpdate,
                    'PyrId' => $pyrid,
                    'PyrName' => $cons,
                    'BillDesc' => $billdesc,
                    'BillGenDt' => $billdate,
                    'BillGenBy' => $genby,
                    'BillApprBy' => $aprby,
                    'PyrCellNum' => $phoneno,
                    'PyrEmail' => $pyremail,
                    'Ccy' => 'TZS',
                    'BillEqvAmt' => $billamount,
                    'RemFlag' => 'true',
                    'BillPayOpt' => 1,
                    'BillItems' => [
                        'BillItem' => [
                            'BillItemRef' => $itemrefno,
                            'UseItemRefOnPay' => 'N',
                            'BillItemAmt' => $billamount,
                            'BillItemEqvAmt' => $billamount,
                            'BillItemMiscAmt' => 0,
                            'GfsCode' => ''
                        ]
                    ]
                ],
            ],
            'gepgSignature' => ''
        ];

        $result = ArrayToXml::convert($gsb, 'Gepg');
        $options = [
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF8',
            ],
            'body' => $result,
        ];
//        return $request = $client->post($apiurl, $options);
//        return $this->gepgbillsubresponse($cn);
          return $result = ArrayToXml::convert($gsb, 'gepgBillSubReq');
//        return File::put(public_path() . '/gepgpostbill.xml', $result);
    }

    public function gepgbillsubreqack()
    {
        $gbsra = [
            'TrxStsCode' => '7101'
        ];
        return $result = ArrayToXml::convert($gbsra, 'gepgBillSubReqAck');
    }

    public function gepgbillsubresponse()
    {
        $billid = rand(1000, 10000);
        $cn = IdGenerator::generate(['table' => 'customer_application_bills', 'field' => 'control_number', 'length' => 14, 'prefix' => 9989 .date('ymd')]);
        $gbsr = [
                'BillTrxInf' => [
                    'BillId' => $billid,
                    'TrxSts' => 'GS',
                    'PayCntrNum' => $cn,
                    'TrxStsCode' => 7242,
                  ]
        ];
        return $result = ArrayToXml::convert($gbsr, 'gepgBillSubResp');
//        return File::put(public_path() . '/gepgbillsubresp.xml', $result);
    }

    public function gepgbillsubreqackreturn()
    {
        $gbsrar = [
            'TrxStsCode' => 7101
        ];
        return $result = ArrayToXml::convert($gbsrar, 'gepgBillSubReqAck');
    }

    public function gepgpaybill(Request $request)
    {
        $billid = $request->input('bill_id');
        $cn = $request->input('control_number');
        $cons = $request->input('consignee');
        $phoneno = $request->input('phoneno');
        $pyremail = $request->input('pyremail');
        $billamount = $request->input('billamount');

        $gpb = [
            'PymtTrxInf' => [
                'TrxId' => '',
                'SpCode' => '',
                'PayRefId' => '',
                'BillId' => $billid,
                'PayCtrNum' => $cn,
                'BillAmt' => $billamount,
                'PaidAmt' => $billamount,
                'BillPayOpt' => 'EXACT',
                'CCy' => 'TZS',
                'TrxDtTm' => '',
                'UsdPayChnl' => '',
                'PyrCellNum' => $phoneno,
                'PyrName' => $cons,
                'PyrEmail' => $pyremail,
                'PspReceiptNumber' => '',
                'PspName' => '',
                'CtrAccNum' => ''
            ]
        ];
        return $this->gepgpaybillack($billid, $cn);
//        return $result = ArrayToXml::convert($gpb, 'gepgPmtSpInfo');
    }

    public function gepgpaybillack($billid, $cn)
    {
        $gpba = [
            'TrxStsCode' => 7101
        ];

        if($gpba['TrxStsCode'] === 7101){
            $bsid = 1;
            $bills = DB::select('CALL sp_update_customer_application_bill_status(?,?,?)', array($billid, $cn, $bsid));
            return response()->json($bills);
        } else{
            return 'Failed to Update';
        }
//        return $result = ArrayToXml::convert($gpba, 'gepgPmtSpInfoAck');
    }
}
