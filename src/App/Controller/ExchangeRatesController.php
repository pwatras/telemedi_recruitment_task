<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helper\TransactionRatesHelper;

class ExchangeRatesController extends AbstractController
{
    private $helper;

    public function __construct(TransactionRatesHelper $helper) {
        $this->helper = $helper;
    }

    public function getRates(Request $req, $date):Response {
        $rates = $this->helper->getRates($date==='today'?date('Y-m-d'):$date);
        return new Response(
                    json_encode($rates),
                    Response::HTTP_OK,
                    ['Content-type' => 'application/json']
                );
    }
}
