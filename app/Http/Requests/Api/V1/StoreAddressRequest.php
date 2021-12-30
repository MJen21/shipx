<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'company'        => $this->company ?? '',
            'street2'        => $this->street2 ?? '',
            'street3'        => $this->street3 ?? '',
            'postcode'       => $this->postcode ?? '',
            'state'          => $this->state ?? '',
            'extension'      => $this->extension ?? '',
            'email'          => $this->email ?? '',
            'tax_id'         => $this->tax_id ?? '',
            'eori_number'    => $this->eori_number ?? '',
            'is_residential' => $this->is_residential ?? null
        ]);
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'           => ['required', 'string', 'max:35'],
            'company'        => ['string', 'max:35'],
            'street1'        => ['required', 'string', 'max:35'],
            'street2'        => ['string', 'max:35'],
            'street3'        => ['string', 'max:35'],
            'postcode'       => ['string', 'max:12'],
            'city'           => ['required', 'string', 'max:35'],
            'state'          => ['string', 'max:35'],
            'country'        => ['required', 'string', 'min:2', 'max:2'],
            'phone'          => ['required', 'string', 'max:35'],
            'extension'      => ['string', 'max:35'],
            'email'          => ['email', 'max:35'],
            'tax_id'         => ['string', 'max:20'],
            'eori_number'    => ['string', 'max:20'],
            'is_residential' => ['nullable', 'boolean']
        ];
    }

}
