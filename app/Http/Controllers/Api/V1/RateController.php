<?php

namespace App\Http\Controllers\Api\V1;

use App\DHL\QuoteRequest;
use App\DHL\QuoteResponse;
use App\DHL\WebClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $validated = $request->validate([
            'date'                                  => ['required', 'date', 'after_or_equal:today'],
            'type'                                  => ['required', Rule::in(['Doc', 'Non-Doc'])],
            'shipper'                               => ['required', 'array'],
            'shipper.postcode'                      => ['string', 'max:12'],
            'shipper.city'                          => ['required', 'string', 'max:35'],
            'shipper.state'                         => ['string', 'max:35'],
            'shipper.country'                       => ['required', Rule::in(['TW'])],
            'consignee'                             => ['required', 'array'],
            'consignee.postcode'                    => ['string', 'max:12'],
            'consignee.city'                        => ['required', 'string', 'max:35'],
            'consignee.state'                       => ['string', 'max:35'],
            'consignee.country'                     => ['required', 'string', 'min:2', 'max:2'],
            'parcels'                               => ['required', 'array'],
            'parcels.*.weight'                      => ['required', 'numeric'],
            'parcels.*.weight_unit'                 => ['required', Rule::in(['kg'])],
            'parcels.*.length'                      => ['required', 'numeric'],
            'parcels.*.width'                       => ['required', 'numeric'],
            'parcels.*.height'                      => ['required', 'numeric'],
            'parcels.*.dimension_unit'              => ['required', Rule::in(['cm'])],
            'customs_declaration'                   => ['required_if:type,Non-Doc', 'array'],
            'customs_declaration.declared_currency' => ['required_if:type,Non-Doc', 'min:3', 'max:3'],
            'customs_declaration.declared_value'    => ['required_if:type,Non-Doc', 'numeric'],
        ]);


        $request = new QuoteRequest(env('DHL_ACCOUNT_NUMBER'));
        $xml = $request->toXML($validated);
        
        $client = new WebClient();
        $response = $client->call($xml);

        $response = new QuoteResponse($response);
        
        if ($response->hasQuotes()) {
            $rates = $response->getQuotes();
            response()->json(['results' => $rates], Response::HTTP_OK)->send();

        } else {
            print_r($response->getErrorMessage());
        }
    }
}
