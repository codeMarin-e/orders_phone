@pushonce('above_css')
<!-- JQUERY UI -->
<link href="{{ asset('admin/vendor/jquery-ui-1.12.1/jquery-ui.min.css') }}" rel="stylesheet" type="text/css" />
@endpushonce
@pushonce('above_css')
<style>
    .ui-autocomplete {
        max-height: 100px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
    }
    /* IE 6 doesn't support max-height
     * we use height instead, but this forces the menu to always be this tall
     */
    * html .ui-autocomplete {
        height: 100px;
    }
</style>
@endpushonce

@pushonce('below_js')
<script language="javascript"
        type="text/javascript"
        src="{{ asset('admin/vendor/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>
@endpushonce

@pushonceOnReady('below_js_on_ready')
<script>
    $addProductSizeBtn = $('#add_product_size');
    $autocompleteProducts = $('#autocomplete_product');
    $autocompleteProducts.autocomplete({
        source: function( request, response ) {
            $.getJSON( $autocompleteProducts.attr('data-src'), {
                search: request.term,
            }, response );
        },
        change: function( event, ui ) {
            if(this.value.length) return;
            $addProductSizeBtn.attr('data-size_id', 0).attr('disabled', 'disabled');
        },
        search: function() {
            $('#js_catalog_sizes .js_catalog_size').removeClass('active');
            $addProductSizeBtn.attr('data-size_id', 0).attr('disabled', 'disabled');
            // if ( this.value.length < 2 ) {
            //
            //     return false;
            // }
        },
        focus: function() {
            // prevent value inserted on focus
            return false;
        },
        select: function( event, ui ) {
            $addProductSizeBtn.attr('data-size_id', ui.item.value).removeAttr('disabled');
            this.value = ui.item.label;
            // setTimeout(function () {
            //     $(event.target).blur();
            // });
            return false;
        }
    });

    var addingProduct = false;
    $(document).on('click', '#add_product_size', function() {
        var $this = $(this);
        if($this.attr('disabled') || addingProduct) return;
        addingProduct = true;
        var oldHtml = $this.html();
        $this.html( $('#js_loader').html() );
        $.ajax({
            url: $this.attr('data-src'),
            method: 'POST',
            timeout: 0,
            data: JSON.stringify({ add: {
                    'owner_type': '{{ str_replace("\\", "\\\\", \App\Models\ProductSize::class) }}',
                    'owner_id': $this.attr('data-size_id'),
                    'quantity': 1,
                }}),
            dataType: 'json',
            contentType: "application/json",
            error: function(jqXHR, textStatus, errorThrown){
                if(jqXHR.responseJSON.message) alert(jqXHR.responseJSON.message);
                else alert('Error');
                $this.html( oldHtml );
                addingProduct = false;
            },
            success: function(response) {
                addingProduct = false;
                $this.html( oldHtml );
                $autocompleteProducts.val('').trigger('change');
                $(document).trigger('refresh_table');
            }
        });
    });
</script>
@endpushonceOnReady

<div class="form-group row">
    <div class="col-lg-12">
        <div class="input-group">
            <div class="input-group-prepend" >
                <button type="button" class="btn btn-warning" id="js_toggle_catalog">@lang('admin/orders_phone/orders_phone.toggle_catalog')</button>
                <label class="input-group-text" for="autocomplete_product">@lang('admin/orders_phone/orders_phone.add_product_label'):</label>
            </div>
            <input type="text"
                   id="autocomplete_product"
                   class="form-control"
                   data-src="{{route("{$route_namespace}.orders_phone.autocomplete", ['products'])}}"
            />
            <div class="input-group-append" >
                <button type="button"
                        disabled
                        data-src="{{route("{$route_namespace}.orders_phone.add_product")}}"
                       class="btn btn-success"
                       id="add_product_size"
               >@lang('admin/orders_phone/orders_phone.add_product')</button>
            </div>
        </div>
    </div>
</div>

@include('admin/orders_phone/orders_phone_catalog')
