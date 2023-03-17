<div class="list-group" data-spy="scroll"  data-offset="0" style="height: 244px; overflow-y: scroll;">
    @foreach($subs as $category)
        <a href="#" class="list-group-item list-group-item-action list-group-item-primary js_catalog_category"
           data-id="{{$category->id}}"
        >{{$category->aVar('name')}}</a>
    @endforeach
    @foreach($products as $product)
        <a href="#" class="list-group-item list-group-item-action js_catalog_product"
           data-src="{{route("{$route_namespace}.orders_phone.product", [$product])}}"
        >{{$product->aVar('name')}}</a>
    @endforeach
</div>
