<?php

namespace App\DHL;

use Illuminate\Support\Facades\Storage;

class ShipmentValidationResponse extends \SimpleXMLElement
{
    public function successful()
    {
        return !empty($this->AirwayBillNumber);
    }

    public function getTrackingNumber()
    {
        return $this->AirwayBillNumber;
    }

    public function getLabelImageString()
    {
        return $this->LabelImage->OutputImage;
    }

    public function storeLabelImage()
    {
        $bin = base64_decode($this->getLabelImageString(), true);
        if (strpos($bin, '%PDF') !== 0) {
            throw new \Exception('Missing the PDF file signature');
        }
        return Storage::put("public/labels/$this->AirwayBillNumber.pdf", $bin);
    }

    public function getErrorMessage()
    {
        $messages = array();

        if ($this->Response->Status->Condition->count() === 1) {
            $messages[] = sprintf("[%s] %s", (string) $this->Response->Status->Condition->ConditionCode, (string) $this->Response->Status->Condition->ConditionData);
        } else {
            foreach ($this->Response->Status->Condition as $condition) {
                $messages[] = sprintf("[%s] %s", (string) $condition->ConditionCode, (string) $condition->ConditionData);
            }
        }
        
        return implode('\n', $messages);
    }
}

?>