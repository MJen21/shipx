<?php

namespace App\DHL;

use Illuminate\Support\Str;

class QuoteRequest
{
    private $account_number;

    public function __construct($account_number)
    {
        $this->account_number = $account_number;
    }

    public function toXML(array $data)
    {
        $file = file_get_contents(__DIR__ . '/XMLTemplates/QuoteRequest2.0.xml');
        $xml = new XML($file);
        $Request = $xml->GetQuote->Request;
        $From = $xml->GetQuote->From;
        $BkgDetails = $xml->GetQuote->BkgDetails;
        $To = $xml->GetQuote->To;
        $Dutiable = $xml->GetQuote->Dutiable;
        
        $Request->ServiceHeader->MessageTime = date(DATE_ATOM);
        $Request->ServiceHeader->MessageReference = Str::random(32);
        $Request->ServiceHeader->SiteID = env('DHL_XMLPI_SITE_ID');
        $Request->ServiceHeader->Password = env('DHL_XMLPI_PASSWORD');

        $BkgDetails->PaymentCountryCode = 'TW';
        $BkgDetails->Date = $data['date'];
        $BkgDetails->ReadyTime = date('\P\TH\Hm\M');
        $BkgDetails->ReadyTimeGMTOffset = date('P');
        $BkgDetails->DimensionUnit = 'CM';
        $BkgDetails->WeightUnit = 'KG';
        $BkgDetails->PaymentAccountNumber = $this->account_number;
        
        foreach ($data['parcels'] as $key => $parcel) {
            $Piece = $BkgDetails->Pieces->addChild('Piece');
            $Piece->addChild('PieceID', $key + 1);
            $Piece->addChild('PackageTypeCode', 'BOX');
            $Piece->addChild('Height', $parcel['height']);
            $Piece->addChild('Depth', $parcel['length']);
            $Piece->addChild('Width', $parcel['width']);
            $Piece->addChild('Weight', $parcel['weight']);
        }

        unset($BkgDetails->QtdShp->GlobalProductCode);
        unset($BkgDetails->QtdShp->LocalProductCode);

        switch ($data['type']) {
            case 'Doc':
                $BkgDetails->IsDutiable = 'N';
                unset($xml->GetQuote->Dutiable);
                break;
            case 'Non-Doc':
                $BkgDetails->IsDutiable = 'Y';
                $Dutiable->DeclaredCurrency = $data['customs_declaration']['declared_currency'];
                $Dutiable->DeclaredValue = $data['customs_declaration']['declared_value'];
                break;
        }

        foreach (['From' => 'shipper', 'To' => 'consignee'] as $key => $address) {
            $$key->CountryCode = $data[$address]['country'];
            $$key->Postalcode = $data[$address]['postcode'];
            $$key->City = $data[$address]['city'];
        }

        $xml = str_replace(array('<DCTRequest ', '</DCTRequest>'), array('<p:DCTRequest ', '</p:DCTRequest>'), $xml->asXML());
        
        return $xml;
    }
}

?>