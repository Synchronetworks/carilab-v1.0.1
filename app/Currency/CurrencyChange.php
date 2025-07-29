<?php

namespace App\Currency;
 use App\Models\Setting;
 use Modules\Currency\Models\Currency;

class CurrencyChange
{
    public $defaultCurrency;

    public $currencyList;

    public function __construct()
    {
        $this->currencyList = Currency::all();
        $this->defaultCurrency = $this->currencyList->where('is_primary', 1)->first();

    }

    public function getDefaultCurrency($array = false)
    {
        if ($array && isset($this->defaultCurrency)) {
            return $this->defaultCurrency->toArray() ?? [];
        }

        return $this->defaultCurrency;
    }

    public function defaultSymbol()
    {
        return $this->defaultCurrency->currency_symbol ?? '';
    }

    public function format($amount)
{
 
    $noOfDecimal = Setting::getSettings('digitafter_decimal_point') ?? 2;
    $currencyCode = Setting::getSettings('default_currency') ?? null; // Country ID
    $currencyPosition = Setting::getSettings('currency_position') ?? 'left';
    $decimal_separator = '.';
    $thousand_separator = ',';

    $currency = \DB::table('countries')
        ->where('id', $currencyCode) // Search by ID instead of currency_code
        ->select('symbol as currency_symbol', 'currency_code', 'currency_name')
        ->first();

    
    if (!$currency) {
        // Fallback default values if currency not found
        $currency = (object) [
            'currency_symbol' => '$',
            'decimal_separator' => '.',
            'thousand_separator' => ','
        ];
    }

    return formatCurrency(
        $amount,
        $noOfDecimal,
        $decimal_separator,
        $thousand_separator,
        $currencyPosition,
        $currency->currency_symbol
    );
}
}
