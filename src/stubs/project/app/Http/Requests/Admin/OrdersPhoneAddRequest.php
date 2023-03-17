<?php

    namespace App\Http\Requests\Admin;

    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Support\Arr;
    use App\Models\Product;
    use App\Models\ProductSize;
    use Illuminate\Validation\ValidationException;

    class OrdersPhoneAddRequest extends FormRequest {
        private $mergeReturn = [];

        public function authorize() {
            return true;
        }

        public function rules() {
            $rules = [
                'owner_type' => ['required', function($attribute, $value, $fail) {
                    if(!in_array($value, [
                        ProductSize::class,

                        //PUT HERE OTHER CartProductable CLASSES
                        // @HOOK_RULES_OWNER_TYPE
                    ])) {
                        return $fail(trans('admin/orders_phone/validation.owner_type.not_correct_type'));
                    }
                }],
                'owner_id' => ['required', function($attribute, $value, $fail){
                    if($this->input('add.owner_type') == ProductSize::class) {
                        if(!($such = ProductSize::where([
                            'id' => $value,
                            'active' => true,
                        ])->whereHas('product', function($qry) {
                            $qry->where([
                                'active' => 1,
                                'site_id' => app()->make('Site')->id,
                            ]);
                        })->first())) {
                            return $fail(trans('admin/orders_phone/validation.owner_id.no_such'));
                        }
                        $this->mergeReturn['add'] = $such;
                        return;
                    }

                    //PUT HERE OTHER CartProductable CLASSES
                    // @HOOK_RULES_OWNER_ID

                    return $fail(trans('admin/orders_phone/validation.owner_id.no_such'));
                }],
                'quantity' => ['required', 'numeric', function($attribute, $value, $fail) {
                    if(isset($this->mergeReturn['add'])) {
                        if(!$this->mergeReturn['add']->checkQuantity($value)) {
                            return $fail(trans('admin/orders_phone/validation.quantity.not_enough_in_store'));
                        }
                    }
                    // @HOOK_RULES_QUANTITY

                }]
            ];

            // @HOOK_RULES

            return $rules;
        }

        public function messages() {
            $return = Arr::dot((array)trans('admin/orders_phone/validation'));

            // @HOOK_MESSAGES

            return $return;
        }

        public function validationData() {
            $inputBag = 'add';
            $this->errorBag = $inputBag;
            $inputs = $this->all();
            if(!isset($inputs[$inputBag])) {
                throw ValidationException::withMessages([
                    $inputBag => trans('admin/orders_phone/validation.no_inputs')
                ]);
            }
            $inputs[$inputBag]['quantity'] = isset($inputs[$inputBag]['quantity'])? (float)str_replace(',', '.', $inputs[$inputBag]['quantity']) : 1;
            // @HOOK_PREPARE

            $this->replace($inputs);
            request()->replace($inputs); //global request should be replaced, too
            return $inputs[$inputBag];
        }

        public function validated($key = null, $default = null) {
            $validatedData = parent::validated($key, $default);

            // @HOOK_VALIDATED

            if(is_null($key)) {

                // @HOOK_AFTER_VALIDATED

                return array_merge($validatedData, $this->mergeReturn);
            }

            // @HOOK_AFTER_VALIDATED_KEY

            return $validatedData;
        }

    }
