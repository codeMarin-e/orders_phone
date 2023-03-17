<div class="form-group row">
    <div class="col-lg-6">
        <label for="{{$inputBag}}[add][comments]"
               class="col-form-label @if($errors->$inputBag->has('add.comments')) text-danger @endif"
        >@lang('admin/orders_phone/orders_phone.comments'):</label>
        <textarea
            name="{{$inputBag}}[add][comments]"
            id="{{$inputBag}}[add][comments]"
            class="form-control"
            rows="5"
        >{{old("{$inputBag}.add.comments")}}</textarea>
    </div>
</div>
{{-- @HOOK_AFTER_COMMENTS --}}
