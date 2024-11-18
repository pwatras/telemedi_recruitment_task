<?php


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Helper\NbpRatesHelper;
use App\Helper\TransactionRatesHelper;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransactionRatesTest extends WebTestCase {
    public function testRatesCalculation() {
        $mockDate = '2024-11-01';
        $mockNbp =  $this->getMockBuilder(NbpRatesHelper::class)->disableOriginalConstructor()->setMethods(['getRates'])->getMock();

        $mockNbp->method('getRates')->willReturn(['date'=>$mockDate, 'rates'=>$this->createMockRates()]);
        $transactionHelper = new TransactionRatesHelper($mockNbp);

        $expectedRateUSD = ['code'=>'USD','buy'=>1.2-0.05, 'sell'=>1.2+0.07];
        $expectedRateCZK = ['code'=>'CZK','buy'=>null, 'sell'=>1.2+0.15];
        $this->assertEquals(['date'=>$mockDate, 'rates'=>[$expectedRateUSD, $expectedRateCZK]],$transactionHelper->getRates($mockDate));
    }

    private function createMockRates() {
        $rates = [];
        foreach(['USD','CZK'] as $code) {
            $rate = new stdClass();
            $rate->code = $code;
            $rate->mid = 1.20;
            $rates[] = $rate;
        }
        return $rates;
    }
}
