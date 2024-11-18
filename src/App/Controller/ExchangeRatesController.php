<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Exception\UnexpectedValueException;
use App\Helper\TransactionRatesHelper;


class ExchangeRatesController extends AbstractController
{
    private $helper;

    public function __construct(TransactionRatesHelper $helper) {
        $this->helper = $helper;
    }

    public function getRates(Request $req, $date):Response {
        $parsedDate = date_parse($date);
        if($parsedDate['year']<2023) {
            return new Response('',Response::HTTP_FORBIDDEN);
        }

        $rates = $this->helper->getRates($date);
        return new Response(
                    json_encode($rates),
                    Response::HTTP_OK,
                    ['Content-type' => 'application/json']
                );
    }
}
