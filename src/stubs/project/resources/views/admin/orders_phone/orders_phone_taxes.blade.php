@pushonceOnReady('below_js_on_ready')
<script>
    $(document).on('refresh_deliveries', function() {
        var $deliveriesCon = $('#js_deliveries');
        $deliveriesCon.html( $('#js_loader').html() );
        $.ajax({
            url: $deliveriesCon.attr('data-src'),
            method: 'GET',
            timeout: 0,
            dataType: 'html',
            error: function(jqXHR, textStatus, errorThrown){
                if(jqXHR.responseJSON.message) alert(jqXHR.responseJSON.message);
                else alert('Error');
            },
            success: function(response) {
                $deliveriesCon.html( $(response).filter('#js_deliveries').first().html() );
            }
        });
    });
    $(document).trigger('refresh_deliveries');

    var changingDelivery = false;
    $(document).on("click", '.js_delivery', function(e) {
        e.preventDefault();
        if(changingDelivery) return;
        var $this = $(this);
        changingDelivery = true;
        $('.js_delivery').removeClass('active');
        $.ajax({
            url: $this.attr('data-src'),
            method: 'PATCH',
            timeout: 0,
            data: JSON.stringify({ cart: {
                'delivery': $this.attr('data-id'),
            }}),
            dataType: 'json',
            contentType: "application/json",
            error: function(jqXHR, textStatus, errorThrown){
                if(jqXHR.responseJSON && jqXHR.responseJSON.message) alert(jqXHR.responseJSON.message);
                else alert('Error');
                changingDelivery = false;
                $(document).trigger('refresh_table');
                $(document).trigger('refresh_payments');
            },
            success: function(response) {
                changingDelivery = false;
                $this.addClass('active');
                $(document).trigger('refresh_table');
                $(document).trigger('refresh_payments');
            }
        });
    });
</script>
@endpushonceOnReady

<div class="row mb-4" id="js_deliveries" data-src="{{route("{$route_namespace}.orders_phone.deliveries")}}">
    <!-- DELIVERY METHODS -->
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                @lang('admin/orders_phone/orders_phone.deliveries')
            </div>
            <div class="card-body">
                @isset($deliveryMethods)
                    <div class="list-group" data-spy="scroll"  data-offset="0" style="height: 244px; overflow-y: scroll;">
                        @php $changeDeliveryRoute = route("{$route_namespace}.orders_phone.change_delivery"); @endphp
                        @foreach($deliveryMethods as $deliveryMethod)
                            <a href="#" class="list-group-item list-group-item-action js_delivery @if($cartDelivery?->is($deliveryMethod)) active @endif"
                               data-src="{{$changeDeliveryRoute}}"
                               data-id="{{$deliveryMethod->id}}"
                            >{{$deliveryMethod->aVar('name')}}</a>
                        @endforeach
                    </div>
                @endisset
            </div>
        </div>
    </div>
    {{-- @HOOK_AFTER_DELIVERY_METHOD--}}
    <!-- PAYMENT METHODS -->
    <div class="col-6">
        @include('admin/orders_phone/orders_phone_taxes_payments')
    </div>
    {{-- @HOOK_AFTER_PAYMENT_METHOD --}}
</div>
