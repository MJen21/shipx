<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CustomsDeclarationResource;
use App\Models\CustomsDeclaration;
use Illuminate\Http\Request;

class CustomsDeclarationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomsDeclaration  $customsDeclaration
     * @return \Illuminate\Http\Response
     */
    public function show(CustomsDeclaration $customsDeclaration)
    {
        return new CustomsDeclarationResource($customsDeclaration);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomsDeclaration  $customsDeclaration
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomsDeclaration $customsDeclaration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomsDeclaration  $customsDeclaration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomsDeclaration $customsDeclaration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomsDeclaration  $customsDeclaration
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomsDeclaration $customsDeclaration)
    {
        //
    }
}
