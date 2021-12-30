<?php

namespace App\Http\Requests\Api\V1;

use App\DHL\Util as DHL;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShipmentRequest extends FormRequest
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
        // Sanitize request data due to the effect of middleware ConvertEmptyStringsToNull...
        $inputs = $this->all();

        foreach (['shipper', 'consignee'] as $key) {
            if (!isset($inputs[$key]) || !is_array($inputs[$key])) continue;
            $inputs[$key] = array_merge($inputs[$key], [
                'company'        => $inputs[$key]['company'] ?? '',
                'street2'        => $inputs[$key]['street2'] ?? '',
                'street3'        => $inputs[$key]['street3'] ?? '',
                'postcode'       => $inputs[$key]['postcode'] ?? '',
                'state'          => $inputs[$key]['state'] ?? '',
                'extension'      => $inputs[$key]['extension'] ?? '',
                'email'          => $inputs[$key]['email'] ?? '',
                'tax_id'         => $inputs[$key]['tax_id'] ?? '',
                'eori_number'    => $inputs[$key]['eori_number'] ?? '',
                'is_residential' => $inputs[$key]['is_residential'] ?? null
            ]);
        }

        $inputs['contents'] = $inputs['contents'] ?? '';

        if (isset($inputs['customs_declaration']) && is_array($inputs['customs_declaration'])) {
            $customs_declaration = &$inputs['customs_declaration'];
            $customs_declaration['invoice_number'] = $customs_declaration['invoice_number'] ?? '';
            if (isset($customs_declaration['items']) && is_array($customs_declaration['items'])) {
                foreach ($customs_declaration['items'] as &$item) {
                    $item['tariff_number'] = $item['tariff_number'] ?? '';
                }
            }

        }

        $this->replace($inputs);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        
        $address_rules = (new StoreAddressRequest())->rules();
        foreach (['shipper', 'consignee'] as $address) {
            $rules[$address] = ['required', 'array'];
            $rules = array_merge($rules,
                        collect(array_map(
                            fn($rules, $field) => ["$address.$field" => $rules],
                            $address_rules,
                            array_keys($address_rules))
                        )->collapse()->toArray()
                    );
        }
        $rules = array_merge($rules,
                    [
                        'date'                                       => ['required', 'date', 'after_or_equal:today'],
                        'type'                                       => ['required', Rule::in(['Doc', 'Non-Doc'])],
                        'service'                                    => ['required', Rule::in(DHL::getServiceTokens())],
                        'purpose'                                    => ['required', Rule::in(['Commercial', 'Gift', 'Sample', 'Return', 'Repair', 'Personal Effects', 'Personal Use', 'Documents'])],
                        'contents'                                   => ['required_if:type,Doc', 'string', 'max:90'],
                        'parcels'                                    => ['required', 'array'],
                        'parcels.*.weight'                           => ['required', 'numeric'],
                        'parcels.*.weight_unit'                      => ['required', Rule::in(['kg'])],
                        'parcels.*.length'                           => ['required', 'numeric'],
                        'parcels.*.width'                            => ['required', 'numeric'],
                        'parcels.*.height'                           => ['required', 'numeric'],
                        'parcels.*.dimension_unit'                   => ['required', Rule::in(['cm'])],
                        'customs_declaration'                        => ['required_if:type,Non-Doc', 'array'],
                        'customs_declaration.currency'               => ['required_if:type,Non-Doc', 'string', 'min:3', 'max:3'],
                        'customs_declaration.items'                  => ['required_if:type,Non-Doc', 'array'],
                        'customs_declaration.items.*.description'    => ['bail', 'required_if:type,Non-Doc', 'string', 'max:75'],
                        'customs_declaration.items.*.quantity'       => ['bail', 'required_if:type,Non-Doc', 'integer'],
                        'customs_declaration.items.*.quantity_unit'  => ['bail', 'required_if:type,Non-Doc', Rule::in(['BOX','2GM','2M','2M3','3M3','M3','DPR','DOZ','2NO','PCS','GM','GRS','KG','L','M','3GM','3L','X','NO','2KG','PRS','2L','3KG','CM2','2M2','3M2','M2','4M2','3M','CM','CONE','CT','EA','LBS','RILL','ROLL','SET','TU','YDS'])],
                        'customs_declaration.items.*.net_weight'     => ['bail', 'required_if:type,Non-Doc', 'numeric'],
                        'customs_declaration.items.*.gross_weight'   => ['bail', 'required_if:type,Non-Doc', 'numeric'],
                        'customs_declaration.items.*.weight_unit'    => ['bail', 'required_if:type,Non-Doc', Rule::in(['kg'])],
                        'customs_declaration.items.*.unit_value'     => ['bail', 'required_if:type,Non-Doc', 'numeric'],
                        'customs_declaration.items.*.tariff_number'  => ['bail', 'string', 'max:12'],
                        'customs_declaration.items.*.origin_country' => ['bail', 'required_if:type,Non-Doc', 'string', 'min:2', 'max:2'],
                    ]
                );
        return $rules;
    }
}
