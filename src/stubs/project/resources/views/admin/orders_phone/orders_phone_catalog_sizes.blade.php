<div class="list-group" data-spy="scroll"  data-offset="0" style="height: 244px; overflow-y: scroll;">
    @foreach($sizes as $size)
        <a href="#" class="list-group-item list-group-item-action js_catalog_size"
           data-id="{{$size->id}}"
        >{{$size->aVar('name')}}</a>
    @endforeach
</div>
