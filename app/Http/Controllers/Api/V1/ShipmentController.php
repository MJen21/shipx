<?php

namespace App\Http\Controllers\Api\V1;

use App\DHL\ShipmentValidationRequest;
use App\DHL\ShipmentValidationResponse;
use App\DHL\WebClient;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreShipmentRequest;
use App\Http\Resources\Api\V1\ShipmentCollection;
use App\Http\Resources\Api\V1\ShipmentResource;
use App\Http\Resources\Api\V1\TrackingResource;
use App\Models\Address;
use App\Models\CustomsDeclaration;
use App\Models\CustomsItem;
use App\Models\Parcel;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shipments = Shipment::with(['shipper', 'consignee', 'customs_declaration', 'parcels'])->latest('created_at')->where('user_id', request()->user()->id)->filter(request()->query())->get();
        return new ShipmentCollection($shipments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Api\V1\StoreShipmentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreShipmentRequest $request)
    {
        $validated = $request->validated();

        $dhl_req = new ShipmentValidationRequest(env('DHL_ACCOUNT_NUMBER'));
        $xml = $dhl_req->toXML($validated);
        $client = new WebClient();
        $dhl_res = $client->call($xml);
        $dhl_res = new ShipmentValidationResponse($dhl_res);

        if (!$dhl_res->successful() || !$dhl_res->storeLabelImage()) {
            return response()->json(['message' => $dhl_res->getErrorMessage()], Response::HTTP_BAD_REQUEST);
        }
        
        $shipper = new Address(array_merge($validated['shipper'], ['user_id' => $request->user()->id]));
        $shipper->save();
        $consignee = new Address(array_merge($validated['consignee'], ['user_id' => $request->user()->id]));
        $consignee->save();
        
        $shipment = new Shipment();
        $shipment->user_id         = $request->user()->id;
        $shipment->date            = $validated['date'];
        $shipment->type            = $validated['type'];
        $shipment->service         = $validated['service'];
        $shipment->tracking_number = $dhl_res->getTrackingNumber();
        $shipment->shipper         = $shipper->id;
        $shipment->consignee       = $consignee->id;
        $shipment->purpose         = $validated['purpose'];
        $shipment->Contents        = $validated['contents'];
        $shipment->status          = 'InfoReceived';
        $shipment->save();

        if ($validated['type'] === 'Non-Doc') {
            $customs_declaration = new CustomsDeclaration();
            $customs_declaration->user_id        = $request->user()->id;
            $customs_declaration->shipment_id    = $shipment->id;
            $customs_declaration->invoice_date   = $validated['customs_declaration']['invoice_date'];
            $customs_declaration->invoice_number = $validated['customs_declaration']['invoice_number'];
            $customs_declaration->incoterm       = $validated['customs_declaration']['incoterm'];
            $customs_declaration->currency       = $validated['customs_declaration']['currency'];
            $customs_declaration->save();
            
            foreach ($validated['customs_declaration']['items'] as $key => $item) {
                $item = new CustomsItem(array_merge($item, [
                    'user_id'     => $request->user()->id,
                    'shipment_id' => $shipment->id,
                    'line_number' => $key + 1
                ]));
                $item->save();
            }
        }

        foreach ($validated['parcels'] as $parcel) {
            $parcel = new Parcel(array_merge($parcel, [
                'user_id'     => $request->user()->id,
                'shipment_id' => $shipment->id
            ]));
            $parcel->save();
        }
        
        return new ShipmentResource(Shipment::find($shipment->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    // public function show(Shipment $shipment)
    // {
    //     return $shipment;
    // }
    public function show(Shipment $shipment)
    {
        if ($shipment->user_id !== request()->user()->id)
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        
        return new ShipmentResource($shipment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Requests\Api\V1\AddressStoreRequest   $request
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shipment $shipment)
    {
        //
    }

    /**
     * Get tracking results of a single shipment by id.
     *
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function track($id)
    {
        $shipment = Shipment::with(['checkpoints'])->where('id', $id)->firstOrFail();

        if ($shipment->user_id !== request()->user()->id)
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        
        return new TrackingResource($shipment);
    }
}
