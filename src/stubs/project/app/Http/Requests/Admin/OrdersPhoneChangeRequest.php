<?php

    namespace App\Http\Requests\Admin;

    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Support\Arr;
    use App\Models\ProductSize;
    use Illuminate\Validation\ValidationException;

    class OrdersPhoneChangeRequest extends FormRequest {
        private $mergeReturn = [];

        // @HOOK_METHODS

        public function authorize() {
            return true;
        }

        public function rules() {
            $currentCart = app()->make('Cart');
            $chCartProduct = request()->route('chCartProduct');
            $rules = [
                'cart_product' => [ function($attribute, $value, $fail) use ($currentCart, $chCartProduct) {
                    // @HOOK_RULES_CART_PRODUCT
                    if(!$currentCart->is( $chCartProduct->cart ))
                        return $fail(trans('admin/orders_phone/validation.wrong_data'));
                }],
                'size' => ['nullable', function($attribute, $value, $fail) use ($chCartProduct) {
                    // @HOOK_RULES_SIZE
                    if(!$value) return;
                    if($chCartProduct->owner_id == $value) return;
                    if($chCartProduct->owner_type != ProductSize::class) return;
                    $chCartProduct->loadMissing('owner');
                    if(!($chSize = ProductSize::where([
                            'active' => 1,
                            'id' => $value,
                        ])->inStore()->where('product_id', $chCartProduct->owner->product_id)->first())) {
                        return $fail(trans('admin/orders_phone/validation.size.no_such_size'));
                    }
                    $this->mergeReturn['size'] = $chSize;
                }],
                'quantity' => ['nullable', 'numeric', 'min:0', function($attribute, $value, $fail) use ($chCartProduct) {
                    if(isset($this->mergeReturn['size'])) {
                        if (!$this->mergeReturn['size']->checkQuantity($value)) {
                            return $fail(trans('admin/orders_phone/validation.quantity.not_enough_in_store'));
                        }
                    }
                    if(!$chCartProduct->checkQuantity($value)) {
                        return $fail(trans('admin/orders_phone/validation.quantity.not_enough_in_store'));
                    }
                    // @HOOK_RULES_QUANTITY
                }],
                'price' => ['nullable', 'numeric', 'min:0', function($attribute, $value, $fail) use ($chCartProduct) {
                    // @HOOK_RULES_PRICE
                }],
                'use_reprice' => 'boolean'
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
            $inputBag = 'change';
            $this->errorBag = $inputBag;
            $inputs = $this->all();

            if(!isset($inputs[$inputBag])) {
                throw new ValidationException(trans('admin/orders_phone/validation.no_inputs') );
            }
            $inputs[$inputBag]['cart_product'] = 1;
            if(isset($inputs[$inputBag]['size']))
                $inputs[$inputBag]['size'] = (int)$inputs[$inputBag]['size'];
            if(isset($inputs[$inputBag]['quantity']))
                $inputs[$inputBag]['quantity'] = (float)str_replace(',', '.', $inputs[$inputBag]['quantity']);
            if(isset($inputs[$inputBag]['price']))
                $inputs[$inputBag]['price'] = (float)str_replace(',', '.', $inputs[$inputBag]['price']);
            $inputs[$inputBag]['use_reprice'] = (boolean)($inputs[$inputBag]['use_reprice']?? false);

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
