<?php

namespace App\Helper;
use DateInterval;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

const SUPPORTED_CURRENCY_CODES = ['EUR','USD','CZK','IDR','BRL'];

const MAX_RECURSIONS = 5;

class NbpRatesHelper {
    private $httpClient;
    private $cache;

    public function __construct(HttpClientInterface $httpCli, AdapterInterface $cache) {
        $this->httpClient = $httpCli;
        $this->cache = $cache;
    }

    public function getRates($date, $depth = 0) {
            $rates = $this->cache->getItem('exchangerates.'.$date);

            if(!$rates->get()) {

                $response = $this->httpClient->request('GET',"https://api.nbp.pl/api/exchangerates/tables/A/$date?format=json");
                if($response->getStatusCode()===404) {
                    if($depth<MAX_RECURSIONS) {
                        return $this->getRates($this->getPreviousDate($date),$depth+1);
                    } else {
                        throw new NotFoundHttpException("Cannot fetch data");
                    }

                }
                $responseData = json_decode($response->getContent());
                $supportedRates = array_values(array_filter($responseData[0]->rates, function($item) {
                    return in_array($item->code,SUPPORTED_CURRENCY_CODES);
                }));
                $rates->set(['date'=>$date,'rates'=>$supportedRates]);
                $this->cache->save($rates);


            }
        return $rates->get();
    }

    private function getPreviousDate($date) {
        $parsed = date_create_immutable_from_format('Y-m-d',$date);
        switch($parsed->format('D')) {
            case 'Mon': $lookBack = 3; break;
            case 'Sun': $lookBack = 2; break;
            default: $lookBack = 1; break;
        }
        $lookBackPeriod = sprintf("P%dD",$lookBack);
        return $parsed->sub(new DateInterval($lookBackPeriod))->format('Y-m-d');
    }


}
