{{-- @HOOK_TEMPLATES --}}

@pushonceOnReady('below_js_on_ready')
<script>
</script>
@endpushonceOnReady


@pushonce('below_templates')
<div id="js_loader" class="d-none">
    <div class="spinner-border spinner-border-sm text-warning" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
@endpushonce
<x-admin.main>
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route("{$route_namespace}.home")}}"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item active">@lang('admin/orders_phone/orders_phone.orders_phone')</li>
        </ol>

        <div class="card">
            <div class="card-body">
                <form method="POST"
                      action="{{route("{$route_namespace}.orders_phone.process")}}"
                      autocomplete="off"
                      enctype="multipart/form-data">
                    @csrf

                    <x-admin.box_messages />

                    <x-admin.box_errors :inputBag="$inputBag" />

                    {{-- @HOOK_BEGINING --}}

                    @include('admin/orders_phone/orders_phone_table')
                    {{-- @HOOK_AFTER_TABLE --}}
                    @include('admin/orders_phone/orders_phone_add_product')
                    {{-- @HOOK_AFTER_ADD_PRODUCT --}}
                    @include('admin/orders_phone/orders_phone_taxes')
                    {{-- @HOOK_AFTER_TAXES --}}
                    @include('admin/orders_phone/orders_phone_addresses')
                    {{-- @HOOK_AFTER_ADDRESSES --}}
                    @include('admin/orders_phone/orders_phone_comments')
                    {{-- @HOOK_AFTER_COMENTS--}}

                    <div class="form-group row">
                        @can('order_phone_create', \App\Models\Cart::class)
                            <button class='btn btn-success mr-2'
                                    type='submit'
                                    name='create'>@lang('admin/orders_phone/orders_phone.create')</button>
                        @endcan

                        {{-- @HOOK_AFTER_BUTTONS --}}

                        <a class='btn btn-warning'
                           href="{{ route("{$route_namespace}.orders.index") }}"
                        >@lang('admin/orders_phone/orders_phone.orders')</a>
                    </div>
                </form>
            </div>
        </div>

    </div>

</x-admin.main>
