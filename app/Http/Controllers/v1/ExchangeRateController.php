<?php


namespace App\Http\Controllers\v1;

use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class ExchangeRateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function fetchrates()
    {
        $client = new Client(['verify' => false]);
        $apiurl = 'https://www.bot.go.tz/services/api/exrates';
        $resp = $client->get($apiurl);
        $result = $resp->getBody()->getContents();
        $results = json_decode($result);

        ExchangeRate::updateOrCreate(
            [ 'id' => 1 ],
            [
                'spot_buying' => $results[0]->SpotBuying,
                'mean' => $results[0]->Mean,
                'spot_selling' => $results[0]->SpotSelling,
                'exchange_date' => $results[0]->ExchangeDate
            ]
        );

        return response()->json('Exchange rate fetched');
    }

}
