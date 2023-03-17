@pushonceOnReady('below_js_on_ready')
<script>
    $(document).on('refresh_payments', function() {
        var $paymentsCon = $('#js_payments');
        $paymentsCon.html( $('#js_loader').html() );
        $.ajax({
            url: $paymentsCon.attr('data-src'),
            method: 'GET',
            timeout: 0,
            dataType: 'html',
            error: function(jqXHR, textStatus, errorThrown){
                if(jqXHR.responseJSON.message) alert(jqXHR.responseJSON.message);
                else alert('Error');
            },
            success: function(response) {
                $paymentsCon.html( $(response).filter('#js_payments').first().html() );
            }
        });
    });
    var changingPayment = false;
    $(document).on("click", '.js_payment', function(e) {
        e.preventDefault();
        if(changingPayment) return;
        var $this = $(this);
        changingPayment = true;
        $('.js_payment').removeClass('active');
        $.ajax({
            url: $this.attr('data-src'),
            method: 'PATCH',
            timeout: 0,
            data: JSON.stringify({ cart: {
                'payment': $this.attr('data-id'),
            }}),
            dataType: 'json',
            contentType: "application/json",
            error: function(jqXHR, textStatus, errorThrown){
                if(jqXHR.responseJSON && jqXHR.responseJSON.message) alert(jqXHR.responseJSON.message);
                else alert('Error');
                changingPayment = false;
                $(document).trigger('refresh_table');
            },
            success: function(response) {
                changingPayment = false;
                $this.addClass('active');
                $(document).trigger('refresh_table');
            }
        });
    });
</script>
@endpushonceOnReady
<div class="card" id="js_payments" data-src="{{route("{$route_namespace}.orders_phone.payments")}}">
    <div class="card-header">
        @lang('admin/orders_phone/orders_phone.payments')
    </div>
    <div class="card-body">
        @isset($paymentMethods)
            <div class="list-group" data-spy="scroll"  data-offset="0" style="height: 244px; overflow-y: scroll;">
                @php $changePaymentRoute = route("{$route_namespace}.orders_phone.change_payment"); @endphp
                @foreach($paymentMethods as $paymentMethod)
                    <a href="#" class="list-group-item list-group-item-action js_payment @if($cartPayment?->is($paymentMethod)) active @endif"
                       data-src="{{$changePaymentRoute}}"
                       data-id="{{$paymentMethod->id}}"
                    >{{$paymentMethod->aVar('name')}}</a>
                @endforeach
            </div>
        @endisset
    </div>
</div>
