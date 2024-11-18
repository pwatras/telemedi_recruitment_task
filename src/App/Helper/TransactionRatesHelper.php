<?php
namespace App\Helper;

const BUY_SPREADS = [
    'EUR'=>0.05,
    'USD'=>0.05
];

const SELL_SPREADS = [
    'EUR'=>0.07,
    'USD'=>0.07,
    'CZK'=>0.15,
    'IDR'=>0.15,
    'BRL'=>0.15
];


class TransactionRatesHelper {
    private $nbpHelper;

    function __construct(NbpRatesHelper $nbpHelper) {
        $this->nbpHelper = $nbpHelper;
    }

    public function getRates(string $date) {
        $nbpRates = $this->nbpHelper->getRates($date);
        $buySellRates = array_map(function($nbpRate) {
            $buyRate = array_key_exists($nbpRate->code,BUY_SPREADS)?$nbpRate->mid-BUY_SPREADS[$nbpRate->code]:null;
            $sellRate = array_key_exists($nbpRate->code,SELL_SPREADS)?$nbpRate->mid+SELL_SPREADS[$nbpRate->code]:null;
            return ['code'=>$nbpRate->code, 'buy'=>$buyRate, 'sell'=>$sellRate];
        }, $nbpRates['rates']);
        return ['date'=>$nbpRates['date'],'rates'=>$buySellRates];
    }

}
