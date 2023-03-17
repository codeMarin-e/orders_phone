<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrdersPhoneChangeRequest;
use App\Http\Requests\Admin\OrdersPhoneProcessRequest;
use App\Models\CartProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\DeliveryMethod;
use App\Models\Product;
use App\Models\User;
use App\Models\Cart;
use App\Http\Requests\Admin\OrdersPhoneAddRequest;

class OrdersPhoneController extends Controller {

    public function __construct() {
        if(!request()->route()) return;

        $this->routeNamespace = Str::before(request()->route()->getName(), '.orders_phone');
        View::composer('admin/orders_phone/*', function($view)  {
            $viewData = [
                'route_namespace' => $this->routeNamespace,
            ];
            // @HOOK_VIEW_COMPOSERS
            $view->with($viewData);
        });
        // @HOOK_CONSTRUCT
    }

    public function index() {
        $viewData = [];
        $viewData['chOrder'] = app()->make('Cart');
        $viewData['chOrder']->loadMissing(["products", "delivery", 'payment', 'discounts']);
        $viewData['inputBag'] = 'order';

        // @HOOK_INDEX

        if(request()->has('refresh_table')) {
            return view('admin/orders_phone/orders_phone_table', $viewData);
        }

        return view('admin/orders_phone/orders_phone', $viewData);
    }

    public function showCatalog() {
        $viewData = [];
        $viewData['categories'] = Category::where("site_id", app()->make('Site')->id)->orderBy("ord", 'ASC');
        $viewData['categoriesSubQry'] = clone $viewData['categories'];

        // @HOOK_SHOW_CATALOG

        $viewData['categories'] = $viewData['categories']->where('parent_id', 0)->get();
        return view('admin/orders_phone/orders_phone_catalog', $viewData);
    }

    public function catalog(Category $chCategory) {
        $viewData = [];
        $products = $chCategory->products()->where(".site_id", app()->make('Site')->id);
        $subs = $chCategory->childrenQry()->where("site_id", app()->make('Site')->id);

        // @HOOK_CATALOG
        $viewData['products'] = $products->get();
        $viewData['subs'] = $subs->get();
        return view('admin/orders_phone/orders_phone_catalog_products', $viewData);
    }

    public function product(Product $chProduct) {
        $viewData = [];
        $chProduct->loadMissing(['sizes' => function($qry) {
            $qry->whereActive(true)->inStore();
        }]);

        // @HOOK_PRODUCT

        $viewData['sizes'] = $chProduct->sizes;
        return view('admin/orders_phone/orders_phone_catalog_sizes', $viewData);
    }

    public function autocomplete($type) {
        if(!request()->has('search'))  abort(402);
        $search = request()->get('search');
        if($type == 'users') {
            $bldQry = User::query();
            if(is_numeric($search)) {
                $bldQry->whereId((int)$search);
            } else {
                $searchParts = explode(' ', $search);
                $searchParts = array_filter($searchParts, 'trim');
                if(empty($searchParts)) abort(402);
                $bldQry->whereHas('addresses', function($qry) use ($searchParts) {
                    $qry->whereRaw('0 = 1');
                    foreach($searchParts as $searchPart) {
                        $qry->orWhere("fname", 'LIKE', "%{$searchPart}%");
                        $qry->orWhere("lname", 'LIKE', "%{$searchPart}%");
                        $qry->orWhere("email", 'LIKE', "%{$searchPart}%");
                    }
                });
            }

            // @HOOK_AUTOCOMPLETE_USERS
            return response()->json( $bldQry->limit(5)->get()->map(function ($user) {
                return [
                    'value' => $user->id,
                    'label' => $user->address? $user->address->fullName."[".$user->email."]" : 'N/A',
                    'url' => route("{$this->routeNamespace}.users.edit", [$user]),
                    'addr' => [
                        'fname' => $user->address?->fname,
                        'lname' => $user->address?->lname,
                        'phone' => $user->address?->phone,
                        'email' => $user->address?->email,
                        'street' => $user->address?->street,
                        'postcode' => $user->address?->postcode,
                        'city' => $user->address?->city,
                        'country' => $user->address?->country,
                        'company' => $user->address?->company,
                        'orgnum' => $user->address?->orgnum,
                    ]
                ];
            })->all() );
        }
        if($type == 'products') {
            $bldQry = Product::whereActive(true)->withWhereHas('sizes', function($qry) {
                $qry->whereActive(true)->inStore();
            });
            if(is_numeric($search)) {
                $bldQry->whereId((int)$search);
            } else {
                $searchParts = explode(' ', $search);
                $searchParts = array_filter($searchParts, 'trim');
                if(empty($searchParts)) abort(402);
                $bldQry->whereHas('addvariable', function($qry) use ($searchParts) {
                    $qry->where('site_id', app()->make('Site')->id);
                    $qry->where('var_name', 'name');
                    $qry->where(function($qry2) use($searchParts) {
                        $qry2->whereRaw('0 = 1');
                        foreach($searchParts as $searchPart) {
                            $qry2->orWhere('var_value', "LIKE", "%{$searchPart}%");
                        }
                    });
                });
            }
            $return = [];
            foreach($bldQry->limit(5)->get() as $product) {
                foreach($product->sizes as $productSize) {
                    $return[] = [
                        'value' => $productSize->id,
                        'label' => $productSize->getCartProductName()
                    ];
                }
            }
            // @HOOK_AUTOCOMPLETE_PRODUCTS
            return response()->json( $return );
        }

        abort(402);
    }

    public function addProduct( OrdersPhoneAddRequest $request) {
        $validatedData = $request->validated();

        // @HOOK_ADD_PRODUCT_VALIDATED

        $currentCart = app()->make('Cart');
        $cartProduct = $currentCart->addProduct( $validatedData['add'], [
            'quantity' => $validatedData['quantity']
        ] );

        Log::info("Cart {$currentCart->id} added product: [{$validatedData['owner_type']}] [{$validatedData['owner_id']}] with quantity: {{$validatedData['quantity']}");
        $validatedData['add']->refresh();
        if($request->ajax()){
            return response()->json([
                'success' => $cartProduct->id,
            ]);
        }
        return back()->with('product_added', $cartProduct->id);
    }

    public function clear() {
        $currentCart = app()->make('Cart');

        // @HOOK_CLEAR

        $currentCart->clear();

        if(request()->ajax()){
            return response()->json(['success' => 1]);
        }
        session()->flash('cart_cleared', 1);
        return back();
    }

    public function removeProduct( CartProduct $chCartProduct, Request $request) {
        $currentCart = app()->make('Cart');
        $inputBag = 'cart';

        // @HOOK_REMOVE_PRODUCT

        $ownerClass = $chCartProduct->owner_type;
        $ownerId = $chCartProduct->owner_id;
        $validatedData = Validator::make(['cart_product' => $chCartProduct->id], [
            'cart_product' => ['required',  function($attribute, $value, $fail) use ($currentCart){
                if(!$currentCart->products()->find($value)) {
                    return $fail( trans('cart_validation.cart_product.required') );
                }
            }],
        ], Arr::dot((array)trans('cart_validation')))->validateWithBag($inputBag);
        $chCartProduct->delete();
        Log::info("Cart {$currentCart->id} removed product: [{$ownerClass}] [{$ownerId}]");
        if($request->ajax()){
            return response()->json(['success' => 1]);
        }
        session()->flash('product_removed', 1);
        return back();
    }

    public function changeProduct(CartProduct $chCartProduct, OrdersPhoneChangeRequest $request) {
        $validatedData = $request->validated();

        // @HOOK_CHANGE_PRODUCT

        if(isset($validatedData['size'])) {
            $chCartProduct->setOwner( $validatedData['size'] );
        }
        if(isset($validatedData['quantity'])) {
            $chCartProduct->setQuantity( $validatedData['quantity'] );
        }
        $chCartProduct->update([
            'use_reprice' => $validatedData['use_reprice'],
            'price' => $validatedData['price'],
        ]);

        if($request->ajax()){
            return response()->json(['success' => 1]);
        }
        session()->flash('product_changed', 1);
        return back();
    }

    public function payments() {
        $viewData = [];
        $currentCart = app()->make('Cart');
        $currentCart->loadMissing(['delivery.delivery.payments' => function($qry) {
            $qry->where('type', 'cod');
        }, 'payment.payment']);
        $viewData['paymentMethods'] = $currentCart?->delivery?->delivery?->payments;
        $viewData['cartPayment'] = $currentCart?->payment?->payment;

        // @HOOK_PAYMENTS

        return view('admin/orders_phone/orders_phone_taxes_payments', $viewData);
    }

    public function changePayment(Request $request) {
        $inputBag = 'cart';
        $inputs = $request->all()[$inputBag]?? [];
        $currentCart = app()->make('Cart');
        $currentCart->loadMissing('delivery.delivery');
        $payment = null;
        $validatedData = Validator::make($inputs, [
            'payment' => ['required',  function($attribute, $value, $fail) use ($currentCart, &$payment){
                if(!$currentCart->delivery || !$currentCart->delivery->delivery) {
                    return $fail(trans('admin/orders_phone/validation.no_set_dm'));
                }
                if(!($payment = $currentCart->delivery->delivery->payments()->where('type', 'cod')->find($value)) ) {
                    return $fail(trans('admin/orders_phone/validation.no_set_pm_in_dm'));
                }
            }]
        ], Arr::dot((array)trans('admin/orders_phone/validation')))->validateWithBag($inputBag);

        $currentCart->setPayment( $payment );
        $paymentType = new PaymentMethod::$types[$payment->type];
        $paymentType->init($currentCart);
        if($request->ajax()){
            return response()->json(['success' => 1]);
        }
        return back()->with('payment_changed', 1);
    }

    public function deliveries() {
        $viewData = [];
        $currentCart = app()->make('Cart');
        $currentCart->loadMissing(['delivery.delivery.payments' => function($qry) {
            $qry->where('type', 'cod');
        }, 'payment.payment']);
        $viewData['deliveryMethods'] = DeliveryMethod::where("site_id", app()->make('Site')->id)->orderBy('ord')->get();
        $viewData['paymentMethods'] = $currentCart?->delivery?->delivery?->payments;
        $viewData['cartDelivery'] = $currentCart?->delivery?->delivery;
        $viewData['cartPayment'] = $currentCart?->payment?->payment;

        // @HOOK_DELIVERIES

        return view('admin/orders_phone/orders_phone_taxes', $viewData);
    }

    public function changeDelivery(Request $request) {
        $inputBag = 'cart';
        $inputs = $request->all()[$inputBag]?? [];
        $currentCart = app()->make('Cart');
        $delivery = null;
        $validatedData = Validator::make($inputs, [
            'delivery' => ['required',  function($attribute, $value, $fail) use ($currentCart, &$delivery){
                if(!($delivery = DeliveryMethod::where('site_id', app()->make('Site')->id)->find($value))) {
                    return $fail(trans('admin/orders_phone/validation.delivery.required'));
                }
            }]
        ], Arr::dot((array)trans('admin/orders_phone/validation')))->validateWithBag($inputBag);

        $currentCart->setDelivery( $delivery );
        $deliveryType = new DeliveryMethod::$types[$delivery->type];
        $deliveryType->init($currentCart);

        $deliveryPMBQ = $delivery->payments()->whereType('cod')->whereActive(true);
        if(!($cartPM = $currentCart->getRealPayment()) ||
            !(clone $deliveryPMBQ)->find($cartPM->id)) {
            if($defaultPM = $deliveryPMBQ->orderBy('default', 'DESC')->first()) {
                $currentCart->setPayment( $defaultPM );
                $paymentType = new PaymentMethod::$types[$defaultPM->type];
                $paymentType->init($currentCart);
            } else {
                $currentCart->payment()->delete();
            }
        }

        if($request->ajax()){
            return response()->json(['success' => 1]);
        }
        return back()->with('delivery_changed', 1);
    }

    public function process(OrdersPhoneProcessRequest $request) {
        $validatedData = $request->validated();
        $currentCart = app()->make('Cart');

        // @HOOK_PROCESS_AFTER_VALIDATION
        if(isset($validatedData['user'])) {
            $currentCart->update(['user_id' => $validatedData['user']->id]);
        }
        if(isset($validatedData['facAddr'])) {
            $facturaAddr = $currentCart->getFacturaAddress();
            $facturaAddr->update($validatedData['facAddr']);
        }
        if(isset($validatedData['delAddr'])) {
            $deliveryAddr = $currentCart->getDeliveryAddress();
            $deliveryAddr->update($validatedData['delAddr']);
        }
        if(isset($validatedData['add']['comments'])) {
            $currentCart->setAVar("comments", $validatedData['add']['comments']);
        }
        $payment = new PaymentMethod::$types[$currentCart->payment->type];
        $result = $payment->process($currentCart);if(isset($result['error'])) {
            return redirect()->back()->with(['cart_error' => $result['error']]);
        }
        switch ($result['type']) {
            case 'done':
                return back()->with('message_success', trans('admin/orders_phone/orders_phone.created'));;
                break;
            case 'iframe':
                //return redirect()->route('cart.process_next', []);
                break;
            case 'redirect':
                if (isset($result['route'])) {
                    $routeParam = marinar_assoc_arr_merge(($result['params'] ?? []), [
                        'order' => $currentCart->id,
                    ]);
                    if (Route::exists($result['route'])) {
                        return redirect()->route($result['route'], $routeParam);
                    }
                    return redirect()->route($result['route'], $result['params'] ?? []);
                }
                if (isset($result['link'])) {
                    return redirect($result['link']);
                }
                abort(404);
                break;
            case 'form-submit':
                break;
            case 'error':
                break;
        }
    }

    // @HOOK_METHODS
}
