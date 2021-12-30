<?php

namespace App\DHL;

class WebClient
{
    private $developmentUrl = 'https://xmlpitest-ea.dhl.com/XMLShippingServlet?isUTF8Support=true';
    private $productionUrl = 'https://xmlpi-ea.dhl.com/XMLShippingServlet?isUTF8Support=true';

    protected $mode = 'development';

    public function __construct($mode = 'production')
    {
        if (!in_array($mode, array('development', 'production'))) {
            $message = 'Invalid mode : ' . $mode . '. Accepted values are : development or production.';
            throw new \InvalidArgumentException($message);
        }
        $this->mode = $mode;
    }

    public function call($payload)
    {
        if (!$ch = curl_init()) {
            throw new \Exception('could not initialize curl');
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $result = curl_exec($ch);

        if (curl_error($ch)) {
            return false;
        }
        curl_close($ch);

        return $result;
    }

    private function getUrl()
    {
        return $this->mode ==='development' ? $this->developmentUrl : $this->productionUrl;
    }
}