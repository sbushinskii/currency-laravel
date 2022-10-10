<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\Http\Resources\ProductResource;

class BitcounController extends BaseController
{
    /**
     * Get and filter currency rates, apply fee
     *
     * @param Request $request
     * @return Response
     */
    public function get_rates(Request $request)
    {
        $filter_currencies = $request->currency;
        if (!is_null($filter_currencies)) {
           $currency_needed = explode(',', $filter_currencies);
           $currency_needed = array_map('trim', $currency_needed);
        }

        $currencies_list = $this->fetch_currencies();

        $result = [];
        foreach($currencies_list as $currency_node) {
            $currency_code = $currency_node['symbol'];
            if ($filter_currencies) {
                if (in_array($currency_code, $currency_needed)) {
                   $this->apply_comission($currency_node);
                   $result[$currency_code] = $currency_node;
                }
            } else {
                $this->apply_comission($currency_node);
                $result[$currency_code] = $currency_node;
            }
        }
        return $this->sendResponse($result);
    }

    /**
     * Convert currency, apply fee
     *
     * @param Request $request
     * @return Response
     */
    public function convert_rates(Request $request){
        $currency_from = $request->input('currency_from');
        $currency_to = $request->input('currency_to');
        $value = $request->input('value');

        if(floatval($value) < 0.01) {
            return $this->sendError('Invalid token');
        }

        $currencies_list = $this->fetch_currencies();
        $all_currencies_list = array_keys($currencies_list);

        // All currencies are calculated againts 1 BTC
        $default_currency = [
            '15m' => 1,
            'last' => 1,
            'buy' => 1,
            'sell' => 1,
        ];

        if($currency_from == 'BTC' && in_array($currency_to, $all_currencies_list)) {
            $currency_rate = number_format($currencies_list[$currency_to]['15m'] / $default_currency['15m'], 2, '.', '');

            // Apply fee
            $needed_currency = $currencies_list[$currency_to];
            $this->apply_fee($needed_currency);

            $converted_currency = $this->convert_currency($default_currency, $needed_currency, $value, 2);
        } elseif ($currency_from != 'BTC' && in_array($currency_from, $all_currencies_list)) {
            $currency_rate = number_format($default_currency['15m'] / $currencies_list[$currency_from]['15m'], 10, '.', '');

            $needed_currency = $currencies_list[$currency_from];
            $this->apply_fee($needed_currency);

            $converted_currency = $this->convert_currency($currencies_list[$currency_from], $default_currency, $value, 10);
        } else {
            return $this->sendError('Invalid token');
        }

        $result = [
            'currency_from' => $currency_from,
            'currency_to' => $currency_to,
            'value' => $value,
            'converted_value' => $converted_currency['15m'],
            'rate' => $currency_rate,
        ];

        return $this->sendResponse($result);
    }


    /**
     * Calculate converted currency
     *
     * @param $currency_from array
     * @param $currency_to array
     * @param $amount float
     * @param $precision int
     *
     * @return array
     */
    private function convert_currency($currency_from, $currency_to, $amount, $precision = 2) {
        $currency['15m'] = number_format($currency_to['15m'] / $currency_from['15m'] * $amount, $precision, '.', '');
        $currency['last'] = number_format($currency_to['last'] / $currency_from['last'] * $amount, $precision, '.', '');
        $currency['buy'] = number_format($currency_to['buy'] / $currency_from['buy'] * $amount, $precision, '.', '');
        $currency['sell'] = number_format($currency_to['sell'] / $currency_from['buy'] * $amount, $precision, '.', '');
        return $currency;
    }


    /**
     * Fetch currencies list and sort it ASC
     *
     * @return array
     */
    private function fetch_currencies() {
        $ticker_url = 'https://blockchain.info/ticker';
        $currency_data = json_decode(file_get_contents($ticker_url), true);
        $sorted_currencies = collect($currency_data)->sortBy('15m')->toArray();
        return $sorted_currencies;
    }

    /**
     * Apply 2% comission
     *
     * @param $node array
     * @param $precision int
     */
    private function apply_fee(&$node, $precision = 2) {
        // Apply 2% fee
        $fee_rate = 0.98;
        $node['15m'] = round(floatval($node['15m']) * $fee_rate, $precision);
        $node['last'] = round($node['last'] * $fee_rate, $precision);
        $node['buy'] = round($node['buy'] * $fee_rate, $precision);
        $node['sell'] = round($node['sell'] * $fee_rate, $precision);
    }
}

