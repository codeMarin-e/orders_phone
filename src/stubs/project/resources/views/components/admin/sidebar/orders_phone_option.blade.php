@can('view_phone_order', \App\Models\Cart::class)
    {{-- PHONE ORDERS --}}
    <li class="nav-item @if(request()->route()->named("{$whereIam}.orders_phone.*")) active @endif">
        <a class="nav-link " href="{{route("{$whereIam}.orders_phone.index")}}">
            <i class="fa fa-fw fa-cubes mr-1"></i>
            <span>@lang("admin/orders_phone/orders_phone.sidebar")</span>
        </a>
    </li>
@endcan
