<?php

namespace App\DHL;

class QuoteResponse extends \SimpleXMLElement
{
    public function successful()
    {
        if (!empty($this->GetQuoteResponse->Note->ActionStatus) && (string) $this->GetQuoteResponse->Note->ActionStatus == 'Success') {
            return true;
        }
        
        return false;
    }

    public function hasQuotes()
    {
        return !empty($this->GetQuoteResponse->BkgDetails->QtdShp);
    }

    public function getQuotes()
    {
        $QtdShp = $this->GetQuoteResponse->BkgDetails->QtdShp;
        $quotes = [];
        
        if ($QtdShp->count() === 1) {
            $quotes[] = $this->xxx($QtdShp);
        } else {
            foreach ($QtdShp as $quote) {
                if ((float) $quote->ShippingCharge <= 0) continue;
                $quotes[] = $this->xxx($quote);
            }
        }

        return $quotes;
    }
    
    private function xxx($quote)
    {
        return [
            'carrier' => 'DHL Express',
            'service' =>  [
                'name' => Util::getServiceNameByProductCode((string) $quote->GlobalProductCode),
                'token' => Util::getServiceTokenByProductCode((string) $quote->GlobalProductCode),
            ],
            'amount' => (float) $quote->ShippingCharge,
            'currency' => (string) $quote->CurrencyCode,
            'estimated_days' => (int) $quote->TotalTransitDays,
            'arrives_by' => (string) $quote->DeliveryDate->DlvyDateTime ?: null
        ];
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