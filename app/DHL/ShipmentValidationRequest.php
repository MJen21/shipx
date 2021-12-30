<?php

namespace App\DHL;

use Illuminate\Support\Str;

class ShipmentValidationRequest
{
    private $account_number;

    public function __construct($account_number)
    {
        $this->account_number = $account_number;
    }
    
    public function toXML(array $data = [])
    {
        $file = file_get_contents(__DIR__ . '/XMLTemplates/ShipmentValidationRequest10.0.xml');
        $xml = new XML($file);
        $Request = $xml->Request;
        $Billing = $xml->Billing;
        $Consignee = $xml->Consignee;
        $Dutiable = $xml->Dutiable;
        $ExportDeclaration = $xml->ExportDeclaration;
        $ShipmentDetails = $xml->ShipmentDetails;
        $Pieces = $xml->ShipmentDetails->Pieces;
        $Shipper = $xml->Shipper;
        
        $Request->ServiceHeader->MessageTime = date(DATE_ATOM);
        $Request->ServiceHeader->MessageReference = Str::random(32);
        $Request->ServiceHeader->SiteID = env('DHL_XMLPI_SITE_ID');
        $Request->ServiceHeader->Password = env('DHL_XMLPI_PASSWORD');

        $Billing->ShipperAccountNumber = $this->account_number;

        // Shipment-Type Specific
        switch ($data['type']) {
            case 'Non-Doc':
                $customs_decl = $data['customs_declaration'];
                if ($customs_decl['incoterm'] === 'DDP') {
                    $Billing->addChild('DutyAccountNumber', $this->account_number);
                }
                $Dutiable->DeclaredValue = array_reduce($customs_decl['items'], function ($sum, $item) {
                    return $sum + ($item['unit_value'] * $item['quantity']);
                }, 0);
                $Dutiable->DeclaredCurrency = $customs_decl['currency'];
                $Dutiable->TermsOfTrade = $customs_decl['incoterm'];
                $ExportDeclaration->ExportReasonCode = Util::getExportReasonCode($data['purpose']);
                $ExportDeclaration->InvoiceNumber = $customs_decl['invoice_number'];
                $ExportDeclaration->InvoiceDate = $customs_decl['invoice_date'];
                // Line Items
                foreach ($customs_decl['items'] as $key => $item) {
                    $ExportLineItem = $ExportDeclaration->addChild('ExportLineItem');
                    $ExportLineItem->addChild('LineNumber', $key + 1);
                    $ExportLineItem->addChild('Quantity', $item['quantity']);
                    $ExportLineItem->addChild('QuantityUnit', in_array($item['quantity_unit'], array("BOX","2GM","2M","2M3","3M3","M3","DPR","DOZ","2NO","PCS","GM","GRS","KG","L","M","3GM","3L","X","NO","2KG","PRS","2L","3KG","CM2","2M2","3M2","M2","4M2","3M","CM","CONE","CT","EA","LBS","RILL","ROLL","SET","TU","YDS")) ? $item['quantity_unit'] : 'PCS');
                    $ExportLineItem->addChildWithCData('Description', $item['description']);
                    $ExportLineItem->addChild('Value', $item['unit_value']);
                    if ($item['tariff_number'] !== '') {
                        $ExportLineItem->addChild('CommodityCode', $item['tariff_number']);
                    }
                    $Weight = $ExportLineItem->addChild('Weight');
                    $Weight->addChild('Weight', $item['net_weight']);
                    $Weight->addChild('WeightUnit', 'K');
                    $GrossWeight = $ExportLineItem->addChild('GrossWeight');
                    $GrossWeight->addChild('Weight', $item['gross_weight']);
                    $GrossWeight->addChild('WeightUnit', 'K');
                    $ExportLineItem->addChild('ManufactureCountryCode', $item['origin_country']);
                }

                $ExportDeclaration->addChild('PlaceOfIncoterm', 'PlaceOfIncoterm');
                $ShipmentDetails->IsDutiable = 'Y';
                $ShipmentDetails->Contents->addCDataValue($customs_decl['items'][0]['description']);
                break;
            case 'Doc':
                unset($xml->Dutiable);
                unset($xml->ExportDeclaration);
                $ShipmentDetails->Contents->addCDataValue($data['contents']);
                $ShipmentDetails->IsDutiable = 'N';
                break;
        }

        // Parcels
        foreach ($data['parcels'] as $key => $parcel) {
            $Piece = $ShipmentDetails->Pieces->addChild('Piece');
            $Piece->addChild('PieceID', $key + 1);
            $Piece->addChild('PackageType', 'YP');
            $Piece->addChild('Weight', $parcel['weight']);
            $Piece->addChild('Width', ceil($parcel['width']));
            $Piece->addChild('Height', ceil($parcel['height']));
            $Piece->addChild('Depth', ceil($parcel['length']));
        }

        // DHL Product Code
        $ProductCode = Util::getProductCodeByServiceToken($data['service']);
        $ShipmentDetails->GlobalProductCode = $ProductCode;
        $ShipmentDetails->LocalProductCode = $ProductCode;

        // Shipment Date
        $ShipmentDetails->Date = $data['date'];

        // Shipper & Consignee
        foreach (array('Shipper','Consignee') as $a) {
            $address = $data[strtolower($a)];
            if ($a === 'Shipper') { $$a->ShipperID = $this->account_number; }
            if (empty($address['company'])) {
                $$a->addChildWithCData('CompanyName', $address['name']);
            } else {
                $$a->addChildWithCData('CompanyName', $address['company']);
            }
            foreach (range(1, 3, 1) as $i) {
                if (!empty($address['street' . $i])) { $$a->addChildWithCData('AddressLine' . $i, $address['street' . $i]); }
            }
            $$a->addChildWithCData('City', $address['city']);
            $$a->addChildWithCData('Division', $address['state']);
            if (!empty($address['postcode'])) {
                $$a->addChild('PostalCode', $address['postcode']);
            }
            $$a->CountryCode = $address['country'];
            $$a->CountryName = Util::getCountryName($address['country']);
            $Contact = $$a->addChild('Contact');
            $Contact->addChildWithCData('PersonName', $address['name']);
            $Contact->addChild('PhoneNumber', $address['phone']);
            $Contact->addChild('PhoneExtension', $address['extension']);
            $Contact->addChild('Email', $address['email']);

            foreach (['tax_id', 'eori_number'] as $key) {
                if (!empty($address[$key])) {
                    $RegistrationNumber = $RegistrationNumbers->addChild('RegistrationNumber');
                    $RegistrationNumber->addChild('Number', $address[$key]);
                    $RegistrationNumber->addChild('NumberTypeCode', $key === 'tax_id' ? 'VAT' : 'EOR');
                    $RegistrationNumber->addChild('NumberIssuerCountryCode', $address['country']);
                }
            }
        }

        // Shipment Reference
        $xml->Reference->addChildWithCData('ReferenceID', 'For testing purpose only.');

        // Label Configuration
        $xml->addChild('LabelImageFormat', 'PDF');
        $Label = $xml->addChild('Label', '');
        $Label->addChild('HideAccount', 'Y');
        $Label->addChild('LabelTemplate', '6X4_PDF');
        $xml->addChild('GetPriceEstimate', 'N');

        $xml = str_replace(array('<ShipmentRequest ', '</ShipmentRequest>'), array('<req:ShipmentRequest ', '</req:ShipmentRequest>'), $xml->asXML());
        
        return $xml;
    }
}

?>