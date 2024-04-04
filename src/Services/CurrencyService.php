<?php
namespace App\Services;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class CurrencyService
{
    public function getCoinsWithExchangeValue(): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://economia.awesomeapi.com.br/last/USD-BRL,EUR-BRL,BTC-BRL');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch), Response::HTTP_FAILED_DEPENDENCY);
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}
