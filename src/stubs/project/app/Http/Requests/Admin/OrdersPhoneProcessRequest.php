<?php

    namespace App\Http\Requests\Admin;

    use App\Models\User;
    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Support\Arr;
    use Illuminate\Validation\ValidationException;
    use App\Models\PaymentMethod;

    class OrdersPhoneProcessRequest extends FormRequest {
        private $mergeReturn = [];

        // @HOOK_METHODS

        public function authorize() {
            return true;
        }

        public function rules() {
            $currentCart = app()->make('Cart');
            $currentCart->loadMissing('products', 'delivery.delivery', 'payment.payment');
            $rules = [
                'delivery' => [ function($attribute, $value, $fail) use($currentCart) {
                    if(!($cartRealDelivery = $currentCart?->delivery?->delivery))
                        return $fail(trans('admin/orders_phone/validation.process.no_set_dm'));
//                    if(!$cartRealDelivery->active)
//                        return $fail(trans('admin/orders_phone/validation.process.no_set_dm'));
                }],
                'payment' => [ function($attribute, $value, $fail) use($currentCart) {
                    if(!($cartRealPayment = $currentCart?->payment?->payment)) {
                        return $fail(trans('admin/orders_phone/validation.process.no_set_pm'));
                    }
                    $cartRealDelivery = $currentCart?->delivery?->delivery;
                    $qryBld = $cartRealDelivery->payments(); //->whereActive(1);
                    if(!$qryBld->find($cartRealPayment->id))
                        return $fail(trans('admin/orders_phone/validation.process.no_set_pm_in_dm'));
                }],
                'count_products' => [ function($attribute, $value, $fail) use($currentCart) {
                    if(!$currentCart->products->count())
                        return $fail(trans('admin/orders_phone/validation.process.no_products'));
                }],
                'total' => [ function($attribute, $value, $fail) use($currentCart) {
                    if($currentCart->getTotalPrice() <= 0 )
                        return $fail(trans('admin/orders_phone/validation.process.no_paying'));
                }],
                'user_id' => [ 'nullable', 'numeric', function($attribute, $value, $fail) {
                    if(!($user = User::find($value))) {
                        return $fail(trans('admin/orders_phone/validation.process.user_id.not_found'));
                    }
                    $this->mergeReturn['user'] = $user;
                }],
                'add.comments' => ['nullable'],
            ];
            if($currentCart->payment) {
                $paymentClass = PaymentMethod::$types[$currentCart->payment->type];
                if(!property_exists($paymentClass ,'showType') || $paymentClass::$showType == 'address') {
                    $rules = array_merge($rules, [
                        'facAddr' => 'required|array',
                        'facAddr.fname' => 'required|string|max:255',
                        'facAddr.lname' => 'required|string|max:255',
                        'facAddr.email' => 'required|email|max:255',
                        'facAddr.phone' => 'required|string|max:255',
                        'facAddr.street' => 'required|string|max:255',
                        'facAddr.city' => 'required|string|max:255',
                        'facAddr.country' => 'nullable|string|max:255',
                        'facAddr.postcode' => 'required|string|max:6',
                        'facAddr.company' => 'nullable|string|max:255',
                        'facAddr.orgnum' => 'nullable|string|max:255',

                        'delAddr' => 'required|array',
                        'delAddr.fname' => 'required|string|max:255',
                        'delAddr.lname' => 'required|string|max:255',
                        'delAddr.email' => 'required|email|max:255',
                        'delAddr.phone' => 'required|string|max:255',
                        'delAddr.street' => 'required|string|max:255',
                        'delAddr.city' => 'required|string|max:255',
                        'delAddr.country' => 'nullable|string|max:255',
                        'delAddr.postcode' => 'required|string|max:255',
                        'delAddr.company' => 'nullable|string|max:255',
                        'delAddr.orgnum' => 'nullable|string|max:255',
                    ]);
                }
            }

            // @HOOK_RULES

            return $rules;
        }

        public function messages() {
            $return = Arr::dot((array)trans('admin/orders_phone/validation.process'));

            // @HOOK_MESSAGES

            return $return;
        }

        public function validationData() {
            $inputBag = 'order';
            $this->errorBag = $inputBag;
            $inputs = $this->all();

            if(!isset($inputs[$inputBag])) {
                throw ValidationException::withMessages([
                    $inputBag => trans('admin/orders_phone/validation.no_inputs'),
                ])->errorBag($inputBag);;
            }

            $inputs[$inputBag]['delivery'] = 1;
            $inputs[$inputBag]['payment'] = 1;
            $inputs[$inputBag]['count_products'] = 1;
            $inputs[$inputBag]['total'] = 1;
            $inputs[$inputBag]['no_delivery_addr'] = isset($inputs[$inputBag]['no_delivery_addr']);
            if(isset($inputs[$inputBag]['facAddr'])) {
                if( !isset($inputs[$inputBag]['delAddr']) || $inputs[$inputBag]['no_delivery_addr'])
                    $inputs[$inputBag]['delAddr'] = $inputs[$inputBag]['facAddr'];
            }

            // @HOOK_PREPARE

            $this->replace($inputs);
            request()->replace($inputs); //global request should be replaced, too
            return parent::all()[$inputBag];
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
