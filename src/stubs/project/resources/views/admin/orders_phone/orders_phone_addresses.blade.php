@pushonceOnReady('below_js_on_ready')
<script>
    var fieldsToFields = ['fname', 'lname', 'email', 'phone', 'postcode', 'city', 'street', 'country', 'company', 'orgnum'];
    $(document).on('user_changed', function(event, userData) {
        for(var i=0;i<fieldsToFields.length; i++) {
            $("input[name='{{$inputBag}}\\[facAddr\\]\\[" + fieldsToFields[i] + "\\]']")
                .val( userData.addr[ fieldsToFields[i] ] )
                .trigger('change');
        }
        if(userData.url && $('#autocomplete_user_url').length) {
            $('#autocomplete_user_url').first().attr('href', userData.url).attr('target','_blank');;
        } else {
            $('#autocomplete_user_url').first().attr('href', "javascript:void(0)").removeAttr('target');;
        }
    });
    var changeHandler = function() {
        if(!$('#no_delivery_addr').prop('checked')) return;
        var $this = $(this);
        var name = $this.attr('name').replace('facAddr', 'delAddr');
        $( 'input[name="' + $.escapeSelector(name) + '"]' ).val( $(this).val() );
    };
    $(document).on('click', '#no_delivery_addr', function() {
        var $this = $(this);
        var facAddr;
        for(var i=0;i<fieldsToFields.length; i++) {
            facAddr = "input[name='{{$inputBag}}\\[facAddr\\]\\[" + fieldsToFields[i] + "\\]']";
            if($this.prop('checked')) {
                $("input[name='{{$inputBag}}\\[delAddr\\]\\[" + fieldsToFields[i] + "\\]']").val( $( facAddr).val() );
                $(document).on('change', facAddr, changeHandler);
                continue;
            }
            $(document).off('change', facAddr, changeHandler);
        }
    });
</script>
@endpushonceOnReady

<!-- ADDRESSES -->
<div class="row mb-4">
    <!-- FACTURA ADDRESS -->
    <div class="col-6">
        <div class="card">
            <div class="card-header">@lang('admin/orders_phone/orders_phone.fac_addr_label')</div>
            <div class="card-body">
                @include('admin/orders_phone/orders_phone_user')

                <div class="form-group row">
                    <div class="col-6">
                        <label for="{{$inputBag}}[facAddr][fname]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.fac_addr.fname')</label>
                        <input type="text"
                               name="{{$inputBag}}[facAddr][fname]"
                               id="{{$inputBag}}[facAddr][fname]"
                               value="{{ old("{$inputBag}.facAddr.fname", (isset($chOrderFacturaAddr)? $chOrderFacturaAddr->fname: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('facAddr.fname')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_FACTURA_FNAME --}}
                    <div class="col-6">
                        <label for="{{$inputBag}}[facAddr][lname]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.fac_addr.lname')</label>
                        <input type="text"
                               name="{{$inputBag}}[facAddr][lname]"
                               id="{{$inputBag}}[facAddr][lname]"
                               value="{{ old("{$inputBag}.facAddr.lname", (isset($chOrderFacturaAddr)? $chOrderFacturaAddr->lname: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('facAddr.lname')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_FACTURA_LNAME --}}
                </div>

                <div class="form-group row">
                    <div class="col-6">
                        <label for="{{$inputBag}}[facAddr][email]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.fac_addr.email')</label>
                        <input type="text"
                               name="{{$inputBag}}[facAddr][email]"
                               id="{{$inputBag}}[facAddr][email]"
                               value="{{ old("{$inputBag}.facAddr.email", (isset($chOrderFacturaAddr)? $chOrderFacturaAddr->email: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('facAddr.email')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_FACTURA_EMAIL --}}
                    <div class="col-6">
                        <label for="{{$inputBag}}[facAddr][phone]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.fac_addr.phone')</label>
                        <input type="text"
                               name="{{$inputBag}}[facAddr][phone]"
                               id="{{$inputBag}}[facAddr][phone]"
                               value="{{ old("{$inputBag}.facAddr.phone", (isset($chOrderFacturaAddr)? $chOrderFacturaAddr->phone: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('facAddr.phone')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_FACTURA_PHONE --}}
                </div>

                <div class="form-group row">
                    <div class="col-6">
                        <label for="{{$inputBag}}[facAddr][company]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.fac_addr.company')</label>
                        <input type="text"
                               name="{{$inputBag}}[facAddr][company]"
                               id="{{$inputBag}}[facAddr][company]"
                               value="{{ old("{$inputBag}.facAddr.company", (isset($chOrderFacturaAddr)? $chOrderFacturaAddr->company: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('facAddr.company')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_FACTURA_COMPANY --}}
                    <div class="col-6">
                        <label for="{{$inputBag}}[facAddr][orgnum]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.fac_addr.orgnum')</label>
                        <input type="text"
                               name="{{$inputBag}}[facAddr][orgnum]"
                               id="{{$inputBag}}[facAddr][orgnum]"
                               value="{{ old("{$inputBag}.facAddr.orgnum", (isset($chOrderFacturaAddr)? $chOrderFacturaAddr->orgnum: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('facAddr.orgnum')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_FACTURA_ORGNUM --}}
                </div>

                <div class="form-group row">
                    <div class="col-6">
                        <label for="{{$inputBag}}[facAddr][postcode]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.fac_addr.postcode')</label>
                        <input type="text"
                               name="{{$inputBag}}[facAddr][postcode]"
                               id="{{$inputBag}}[facAddr][postcode]"
                               value="{{ old("{$inputBag}.facAddr.postcode", (isset($chOrderFacturaAddr)? $chOrderFacturaAddr->postcode: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('facAddr.postcode')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_FACTURA_POSTCODE --}}
                    <div class="col-6">
                        <label for="{{$inputBag}}[facAddr][city]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.fac_addr.city')</label>
                        <input type="text"
                               name="{{$inputBag}}[facAddr][city]"
                               id="{{$inputBag}}[facAddr][city]"
                               value="{{ old("{$inputBag}.facAddr.city", (isset($chOrderFacturaAddr)? $chOrderFacturaAddr->city: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('facAddr.city')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_FACTURA_CITY --}}
                </div>

                <div class="form-group row">
                    <div class="col-6">
                        <label for="{{$inputBag}}[facAddr][street]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.fac_addr.street')</label>
                        <input type="text"
                               name="{{$inputBag}}[facAddr][street]"
                               id="{{$inputBag}}[facAddr][street]"
                               value="{{ old("{$inputBag}.facAddr.street", (isset($chOrderFacturaAddr)? $chOrderFacturaAddr->street: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('facAddr.street')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_FACTURA_STREET --}}
                    <div class="col-6">
                        <label for="{{$inputBag}}[facAddr][country]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.fac_addr.country')</label>
                        <input type="text"
                               name="{{$inputBag}}[facAddr][country]"
                               id="{{$inputBag}}[facAddr][country]"
                               value="{{ old("{$inputBag}.facAddr.country", (isset($chOrderFacturaAddr)? $chOrderFacturaAddr->country: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('facAddr.country')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_FACTURA_COUNTRY --}}
                </div>
            </div>
        </div>
    </div>
    {{-- @HOOK_AFTER_FACTURA_ADDRESS --}}
    <!-- DELIVERY ADDRESS -->
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                @lang('admin/orders_phone/orders_phone.del_addr_label')
                <span style="float: right;">
                    <label for="no_delivery_addr">@lang('admin/orders_phone/orders_phone.del_addr_same')</label>
                    <input type="checkbox"
                           name="{{$inputBag}}[no_delivery_addr]"
                           id="no_delivery_addr"
                           value="1"
                           @if(old("{$inputBag}.no_delivery_addr"))checked="checked"@endif
                    />
                </span>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-6">
                        <label for="{{$inputBag}}[delAddr][fname]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.del_addr.fname')</label>
                        <input type="text"
                               name="{{$inputBag}}[delAddr][fname]"
                               id="{{$inputBag}}[delAddr][fname]"
                               value="{{ old("{$inputBag}.delAddr.fname", (isset($chOrderDeliveryAddr)? $chOrderDeliveryAddr->fname: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('delAddr.fname')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_DELIVERY_FNAME --}}
                    <div class="col-6">
                        <label for="{{$inputBag}}[delAddr][lname]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.del_addr.lname')</label>
                        <input type="text"
                               name="{{$inputBag}}[delAddr][lname]"
                               id="{{$inputBag}}[delAddr][lname]"
                               value="{{ old("{$inputBag}.delAddr.lname", (isset($chOrderDeliveryAddr)? $chOrderDeliveryAddr->lname: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('delAddr.lname')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_DELIVERY_LNAME --}}
                </div>

                <div class="form-group row">
                    <div class="col-6">
                        <label for="{{$inputBag}}[delAddr][email]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.del_addr.email')</label>
                        <input type="text"
                               name="{{$inputBag}}[delAddr][email]"
                               id="{{$inputBag}}[delAddr][email]"
                               value="{{ old("{$inputBag}.delAddr.email", (isset($chOrderDeliveryAddr)? $chOrderDeliveryAddr->email: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('delAddr.email')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_DELIVERY_EMAIL --}}
                    <div class="col-6">
                        <label for="{{$inputBag}}[delAddr][phone]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.del_addr.phone')</label>
                        <input type="text"
                               name="{{$inputBag}}[delAddr][phone]"
                               id="{{$inputBag}}[delAddr][phone]"
                               value="{{ old("{$inputBag}.delAddr.phone", (isset($chOrderDeliveryAddr)? $chOrderDeliveryAddr->phone: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('delAddr.phone')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_DELIVERY_PHONE --}}
                </div>

                <div class="form-group row">
                    <div class="col-6">
                        <label for="{{$inputBag}}[delAddr][company]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.del_addr.company')</label>
                        <input type="text"
                               name="{{$inputBag}}[delAddr][company]"
                               id="{{$inputBag}}[delAddr][company]"
                               value="{{ old("{$inputBag}.delAddr.company", (isset($chOrderDeliveryAddr)? $chOrderDeliveryAddr->company: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('delAddr.company')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_DELIVERY_COMPANY--}}
                    <div class="col-6">
                        <label for="{{$inputBag}}[delAddr][orgnum]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.del_addr.orgnum')</label>
                        <input type="text"
                               name="{{$inputBag}}[delAddr][orgnum]"
                               id="{{$inputBag}}[delAddr][orgnum]"
                               value="{{ old("{$inputBag}.delAddr.orgnum", (isset($chOrderDeliveryAddr)? $chOrderDeliveryAddr->orgnum: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('delAddr.orgnum')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_DELIVERY_ORGNUM --}}
                </div>

                <div class="form-group row">
                    <div class="col-6">
                        <label for="{{$inputBag}}[delAddr][postcode]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.del_addr.postcode')</label>
                        <input type="text"
                               name="{{$inputBag}}[delAddr][postcode]"
                               id="{{$inputBag}}[delAddr][postcode]"
                               value="{{ old("{$inputBag}.delAddr.postcode", (isset($chOrderDeliveryAddr)? $chOrderDeliveryAddr->postcode: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('delAddr.postcode')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_DELIVERY_POSTCODE --}}
                    <div class="col-6">
                        <label for="{{$inputBag}}[delAddr][city]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.del_addr.city')</label>
                        <input type="text"
                               name="{{$inputBag}}[delAddr][city]"
                               id="{{$inputBag}}[delAddr][city]"
                               value="{{ old("{$inputBag}.delAddr.city", (isset($chOrderDeliveryAddr)? $chOrderDeliveryAddr->city: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('delAddr.city')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_DELIVERY_CITY --}}
                </div>

                <div class="form-group row">
                    <div class="col-6">
                        <label for="{{$inputBag}}[delAddr][street]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.del_addr.street')</label>
                        <input type="text"
                               name="{{$inputBag}}[delAddr][street]"
                               id="{{$inputBag}}[delAddr][street]"
                               value="{{ old("{$inputBag}.delAddr.street", (isset($chOrderDeliveryAddr)? $chOrderDeliveryAddr->street: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('delAddr.street')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_DELIVERY_STREET --}}
                    <div class="col-6">
                        <label for="{{$inputBag}}[delAddr][country]"
                               class="col-form-label"
                        >@lang('admin/orders_phone/orders_phone.del_addr.country')</label>
                        <input type="text"
                               name="{{$inputBag}}[delAddr][country]"
                               id="{{$inputBag}}[delAddr][country]"
                               value="{{ old("{$inputBag}.delAddr.country", (isset($chOrderDeliveryAddr)? $chOrderDeliveryAddr->country: '')) }}"
                               onkeyup="this.classList.remove('is-invalid')"
                               class="form-control @if($errors->$inputBag->has('delAddr.country')) is-invalid @endif"
                        />
                    </div>
                    {{-- @HOOK_AFTER_DELIVERY_COUNTRY --}}
                </div>
            </div>
        </div>
    </div>
    {{-- @HOOK_AFTER_DELIVERY_ADDRESS --}}
</div>
<!-- END ADDRESSES -->
