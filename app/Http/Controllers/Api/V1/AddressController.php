<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAddressRequest;
use App\Http\Resources\Api\V1\AddressCollection;
use App\Http\Resources\Api\V1\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new AddressCollection(Address::latest('created_at')->where('user_id', request()->user()->id)->filter(request()->query())->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Api\V1\StoreAddressRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAddressRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;
        $address = Address::create($validated);
        
        return new AddressResource($address);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function show(Address $address)
    {
        return new AddressResource($address);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Address $address)
    {
        //
    }
}
