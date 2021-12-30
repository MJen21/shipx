<?php

namespace App\DHL;

class XML extends \SimpleXMLElement
{
    public function prependChild($name, $value)
    {
        $dom = dom_import_simplexml($this);
        $new = $dom->insertBefore(
            $dom->ownerDocument->createElement($name, $value),
            $dom->firstChild
        );
        return simplexml_import_dom($new, get_class($this));
    }

    public function addCDataValue($value = '')
    {
        $this->addCDataToNode($this, "{$value}");
    }

    public function addChildWithCData($name = '', $value = '')
    {
        $newChild = parent::addChild($name);
        if ($value) $this->addCDataToNode($newChild, "{$value}");
        return $newChild;
    }

    private function addCDataToNode($node, $value = '')
    {
        if ($domElement = dom_import_simplexml($node))
        {
            $domOwner = $domElement->ownerDocument;
            $domElement->appendChild($domOwner->createCDATASection("{$value}"));
        }
    }
}

?>