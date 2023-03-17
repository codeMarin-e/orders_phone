@pushonceOnReady('below_js_on_ready')
<script>
    $(document).on('refresh_table', function() {
        var $tableCon = $('#js_table_container');
        $tableCon.html( $('#js_loader').html() );
        $.ajax({
            method: 'GET',
            timeout: 0,
            data: {
                'refresh_table': 1,
            },
            dataType: 'html',
            error: function(jqXHR, textStatus, errorThrown){
                if(jqXHR.responseJSON.message) alert(jqXHR.responseJSON.message);
                else alert('Error');
            },
            success: function(response) {
                $tableCon.html( $(response).filter('#js_table_container').first().html() );
            }
        });
    });

    var changing = false;
    var changeCartProduct = function($tr, callback) {
        if(changing) return;
        changing = true;
        // var $tr = $this.parents('tr[data-cart_product="'+ cartProductId +'"]').first();
        var cartProductId = $tr.attr('data-cart_product');
        $tr.find('.js_loader_row').first().removeClass('d-none');
        $.ajax({
            url: $tr.attr('data-src'),
            method: 'PATCH',
            timeout: 0,
            data: JSON.stringify({ change: {
                    'price': $tr.find("input[name='{{$inputBag}}\\[products\\]\\["+ cartProductId +"\\]\\[price\\]']").first().val(),
                    'quantity': $tr.find("input[name='{{$inputBag}}\\[products\\]\\["+ cartProductId +"\\]\\[quantity\\]']").first().val(),
                    'use_reprice': $tr.find("input[name='{{$inputBag}}\\[products\\]\\["+ cartProductId +"\\]\\[use_reprice\\]']").first().prop('checked'),
                }}),
            dataType: 'json',
            contentType: "application/json",
            error: function(jqXHR, textStatus, errorThrown){
                if(jqXHR.responseJSON.message) alert(jqXHR.responseJSON.message);
                else alert('Error');
                $(document).trigger('refresh_table');
            },
            success: function(response) {
                $tr.find('.js_loader_row').first().addClass('d-none');
                if(callback) callback();
                $(document).trigger('refresh_table');
                changing = false;
            }
        });
    }
    $(document).on('blur', '.js_change_cart_product', function() {
        changeCartProduct( $(this).parents('tr') );
    });
    $(document).on('click', '.js_change_cart_product_checkbox', function() {
        changeCartProduct( $(this).parents('tr') );
    });

    var removingProduct = false;
    $(document).on('click', '.js_remove', function(e) {
        e.preventDefault();
        if(removingProduct) return;
        var $this = $(this);
        if(!confirm($this.attr('data-ask'))) return false;
        removingProduct = true;
        var oldHtml = $this.html();
        $this.html( $('#js_loader').html() );
        $.ajax({
            url: $this.attr('href'),
            method: 'DELETE',
            timeout: 0,
            dataType: 'json',
            error: function(jqXHR, textStatus, errorThrown){
                if(jqXHR.responseJSON && jqXHR.responseJSON.message) alert(jqXHR.responseJSON.message);
                else alert('Error');
            },
            success: function(response) {
                $this.html( oldHtml );
                $(document).trigger('refresh_table');
                removingProduct = false;
            }
        });
    });

    var cleaning = false;
    $(document).on('click', '.js_clear', function(e) {
        e.preventDefault();
        if(cleaning) return;
        var $this = $(this);
        if(!confirm($this.attr('data-ask'))) return false;
        cleaning = true;
        var oldHtml = $this.html();
        $this.html( $('#js_loader').html() );
        $.ajax({
            url: $this.attr('href'),
            method: 'DELETE',
            timeout: 0,
            dataType: 'json',
            error: function(jqXHR, textStatus, errorThrown){
                if(jqXHR.responseJSON.message) alert(jqXHR.responseJSON.message);
                else alert('Error');
            },
            success: function(response) {
                $this.html( oldHtml );
                $(document).trigger('refresh_table');
                cleaning = false;
            }
        });
    });

</script>
@endpushonceOnReady

@pushonce('below_templates')

@endpushonce

@php $columnsCount = 8; @endphp
{{-- @HOOK_AFTER_COLUMNS_SPAN_COUNT --}}
<!-- ORDER -->
<div class="row" id="js_table_container">
    <div class="table-responsive rounded ">
        <table class="table table-sm ">
            <thead class="thead-light">
            <tr>
                <th class="text-center align-middle">
                    @if($chOrder->products->count())
                        <a href="{{route("{$route_namespace}.orders_phone.clear")}}"
                           class="js_clear"
                        title="@lang('admin/orders_phone/orders_phone.table.clear')"
                        data-ask="@lang('admin/orders_phone/orders_phone.table.remove_ask')"><i class="fa fa-trash text-danger"></i></a>
                    @endif
                </th>
                {{-- @HOOK_AFTER_REMOVE_TH --}}

                <th class="align-middle">@lang('admin/orders_phone/orders_phone.table.product_name')</th>
                {{-- @HOOK_AFTER_NAME_TH --}}

                <th class="text-center align-middle">@lang('admin/orders_phone/orders_phone.table.product_size')</th>
                {{-- @HOOK_AFTER_SIZE_TH --}}

                <th class="text-center align-middle">@lang('admin/orders_phone/orders_phone.table.product_quantity')</th>
                {{-- @HOOK_AFTER_QUANTITY_TH --}}

                <th class="text-center align-middle">@lang('admin/orders_phone/orders_phone.table.product_price')</th>
                {{-- @HOOK_AFTER_PRICE_TH --}}

                <th class="text-center align-middle">@lang('admin/orders_phone/orders_phone.table.product_vat')</th>
                {{-- @HOOK_AFTER_VAT_TH --}}

                <th class="text-center align-middle">@lang('admin/orders_phone/orders_phone.table.product_discount')</th>
                {{-- @HOOK_AFTER_DISCOUNT_TH --}}

                <th class="text-center align-middle">@lang('admin/orders_phone/orders_phone.table.product_sum')</th>
                {{-- @HOOK_AFTER_SUM_TH --}}
            </tr>
            </thead>
            <tbody>
            @foreach($chOrder->products as $cartProduct)
                @if($cartProduct->owner_type === \App\Models\ProductSize::class)
                    @php
                        $productableLink = '';
                        if(($size = $cartProduct->owner) && $authUser->can('products.view', \App\Models\Product::class)) {
                            $productableLink = route("{$route_namespace}.categories.products.edit", [ $size->product->getMainCategory(), $size->product]);
                        }
                        $name = explode('-', $size->getCartProductName());
                        $productName = array_shift($name);
                        $sizeName = empty($name)? '#' : implode('-', $name);
                    @endphp
                    <tr data-src="{{route("{$route_namespace}.orders_phone.change_product", [$cartProduct])}}"
                        data-cart_product="{{$cartProduct->id}}">
                        <td class="text-center align-middle">
                            <a href="{{route("{$route_namespace}.orders_phone.remove_product", [$cartProduct])}}"
                                title="@lang('admin/orders_phone/orders_phone.table.remove')"
                                data-ask="@lang('admin/orders_phone/orders_phone.table.remove_ask')"
                                class="js_remove"
                            ><i class="fa fa-trash text-danger"></i></a>
                        </td>
                        {{-- @HOOK_AFTER_REMOVE --}}

                        <td class="align-middle">
                            <a href="{{ $productableLink}}" target="_blank">{{$productName}}</a>
                            <span class="d-none spinner-border spinner-border-sm text-warning js_loader_row" role="status">
                                <span class="sr-only">Loading...</span>
                            </span >
                        </td>
                        {{-- @HOOK_AFTER_NAME --}}

                        <td class="text-center align-middle">
                            <a href="{{ $productableLink}}" target="_blank">{{$sizeName}}</a>
                        </td>
                        {{-- @HOOK_AFTER_SIZE --}}

                        <td class="text-center align-middle w-10">
                            <input type="text"
                                   name="{{$inputBag}}[products][{{$cartProduct->id}}][quantity]"
                                   value="{{$cartProduct->quantity}}"
                                   onkeyup="this.classList.remove('is-invalid')"
                                   class="js_change_cart_product form-control text-center @if($errors->{$inputBag}->has("products.{$cartProduct->id}.quantity")) is-invalid @endif"/>
                        </td>
                        {{-- @HOOK_AFTER_QUANTITY --}}

                        <td class="text-center align-middle w-10">
                            <div class="input-group">
                                <input type="text"
                                       name="{{$inputBag}}[products][{{$cartProduct->id}}][price]"
                                       value="{{number_format($cartProduct->getPrice(false, false), 2)}}"
                                       onkeyup="this.classList.remove('is-invalid')"
                                       class="js_change_cart_product form-control text-center @if($errors->{$inputBag}->has("products.{$cartProduct->id}.price")) is-invalid @endif"/>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <input type="checkbox"
                                               name="{{$inputBag}}[products][{{$cartProduct->id}}][use_reprice]"
                                               class="js_change_cart_product_checkbox"
                                               title="@lang('admin/orders_phone/orders_phone.table.use_reprice')"
                                                @if($cartProduct->use_reprice)checked="checked"@endif
                                        />
                                    </div>
                                </div>
                            </div>
                         </td>
                        {{-- @HOOK_AFTER_PRICE --}}

                        <td class="text-center align-middle">
                            {{ number_format($cartProduct->getVat(), 2, '.', ' ') }} {{ $siteCurrency }}
                        </td>
                        {{-- @HOOK_AFTER_VAT --}}

                        <td class="text-center align-middle">
                            {{ number_format($cartProduct->getDiscountValue(), 2, '.', ' ') }} {{ $siteCurrency }}
                        </td>
                        {{-- @HOOK_AFTER_DISCOUNT --}}

                        <td class="text-center align-middle">
                            {{ number_format($cartProduct->getTotalPrice(), 2, '.', ' ') }} {{ $siteCurrency }}
                        </td>
                        {{-- @HOOK_AFTER_SUM --}}
                    </tr>
                @endif
                {{-- @HOOK_AFTER_ROW --}}
            @endforeach
            </tbody>
            <tbody>
            @if($chOrder->delivery)
                <tr>
                    <td class="text-center align-center"
                        colspan="{{$columnsCount-4}}">
                        @if($chOrder->delivery?->delivery)
                            <a href='{{route("{$route_namespace}.deliveries.edit", [$chOrder->delivery->delivery])}}'>
                                {{$chOrder->delivery->aVar('name')}}
                            </a>
                        @else
                            {{$chOrder->delivery->aVar('name')}}
                        @endif
                    </td>
                    {{-- @HOOK_AFTER_DELIVERY_NAME --}}
                    <td  class="text-center align-middle">{{ number_format($chOrder->delivery->getTax(false, false), 2, '.', ' ') }} {{ $siteCurrency }}</td>
                    {{-- @HOOK_AFTER_DELIVERY_PURE_TAX --}}
                    <td  class="text-center align-middle">{{ number_format($chOrder->delivery->getVat(), 2, '.', ' ') }} {{ $siteCurrency }}</td>
                    {{-- @HOOK_AFTER_DELIVERY_VAT --}}
                    <td  class="text-center align-middle">{{ number_format($chOrder->delivery->getDiscountValue(), 2, '.', ' ') }} {{ $siteCurrency }}</td>
                    {{-- @HOOK_AFTER_DELIVERY_DISCOUNT --}}
                    <td  class="text-center align-middle">{{ number_format($chOrder->delivery->getTax(), 2, '.', ' ') }} {{ $siteCurrency }}</td>
                    {{-- @HOOK_AFTER_DELIVERY_TAX --}}
                </tr>
            @endif
            {{-- @HOOK_AFTER_DELIVERY --}}

            @if($chOrder->payment)
                <tr>
                    <td  class="text-center align-center"
                         colspan="{{$columnsCount-4}}">
                        @if($chOrder->payment?->payment)
                            <a href='{{route("{$route_namespace}.payments.edit", [$chOrder->payment->payment])}}'>
                                {{$chOrder->payment->aVar('name')}}
                            </a>
                        @else
                            {{$chOrder->payment->aVar('name')}}
                        @endif
                    </td>
                    {{-- @HOOK_AFTER_PAYMENT_NAME --}}
                    <td  class="text-center align-middle">{{ number_format($chOrder->payment->getTax(false), 2, '.', ' ') }} {{ $siteCurrency }}</td>
                    {{-- @HOOK_AFTER_PAYMENT_PURE_TAX --}}
                    <td  class="text-center align-middle">{{ number_format($chOrder->payment->getVat(), 2, '.', ' ') }} {{ $siteCurrency }}</td>
                    {{-- @HOOK_AFTER_PAYMENT_VAT --}}
                    <td  class="text-center align-middle">{{ number_format($chOrder->payment->getDiscountValue(), 2, '.', ' ') }} {{ $siteCurrency }}</td>
                    {{-- @HOOK_AFTER_PAYMENT_DISCOUNT --}}
                    <td  class="text-center align-middle">{{ number_format($chOrder->payment->getTax(), 2, '.', ' ') }} {{ $siteCurrency }}</td>
                    {{-- @HOOK_AFTER_PAYMENT_TAX --}}
                </tr>
            @endif
            {{-- @HOOK_AFTER_PAYMENT --}}
            <tr>
                <td  class="text-right align-right"
                     colspan="{{$columnsCount-1}}">@lang('admin/orders_phone/orders_phone.table.main_discount')</td>
                <td  class="text-center align-middle">{{ number_format($chOrder->getDiscountValue(), 2, '.', ' ') }} {{ $siteCurrency }}</td>
            </tr>
            {{-- @HOOK_AFTER_DISCOUNT --}}
            <tr>
                <td  class="text-right align-right"
                     colspan="{{$columnsCount-1}}">@lang('admin/orders_phone/orders_phone.table.total')</td>
                <td  class="text-center align-middle">{{ number_format($chOrder->getTotalPrice(), 2, '.', ' ') }} {{ $siteCurrency }}</td>
            </tr>
            {{-- @HOOK_AFTER_TOTAL --}}
            </tbody>
        </table>
    </div>
</div>
<!-- END ORDER -->
