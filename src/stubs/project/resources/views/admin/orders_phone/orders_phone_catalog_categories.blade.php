@foreach($categories as $category)
    @php $subCategories = $category->childrenQry($categoriesSubQry)->get(); @endphp
    <a href="#"
       data-src="{{route("{$route_namespace}.orders_phone.catalog", [$category])}}"
       data-id="{{$category->id}}"
       data-parent="{{$category->parent_id}}"
       class="list-group-item list-group-item-action js_catalog_category @if($category->parent_id) d-none @endif"
    >{!! str_repeat('&nbsp;&nbsp;&nbsp;|', $level) !!}
    @if($level)->@endif
    @if($subCategories->count())<span class="js_catalog_toggle_subs" data-type='show' data-id="{{$category->id}}">+</span>@endif
    {{$category->aVar('name')}}</a>
    @includeWhen( $subCategories->count(), 'admin/orders_phone/orders_phone_catalog_categories', [
        'categories' => $subCategories,
        'categoriesSubQry' => $categoriesSubQry,
        'level' => $level+1
    ])
@endforeach
