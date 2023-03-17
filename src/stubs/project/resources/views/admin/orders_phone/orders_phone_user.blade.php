@pushonce('above_css')
<!-- JQUERY UI -->
<link href="{{ asset('admin/vendor/jquery-ui-1.12.1/jquery-ui.min.css') }}" rel="stylesheet" type="text/css" />
@endpushonce

@pushonce('below_js')
<script language="javascript"
        type="text/javascript"
        src="{{ asset('admin/vendor/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>
@endpushonce

@pushonceOnReady('below_js_on_ready')
<script>
    $inputUser = $('input[name="{{$inputBag}}\\[user_id\\]"]');
    $autocompleteUsers = $('#autocomplete_user');
    $autocompleteUsers.autocomplete({
        source: function( request, response ) {
            $.getJSON( $autocompleteUsers.attr('data-src'), {
                search: request.term,
            }, response );
        },
        change: function( event, ui ) {
            if(this.value.length) return;
            $inputUser.val("");
            if($('#autocomplete_user_url').length)
            $('#autocomplete_user_url').first().attr('href', "javascript:void(0)").removeAttr('target');
        },


        search: function() {
            $inputUser.val("");
            if($('#autocomplete_user_url').length)
            $('#autocomplete_user_url').first().attr('href', "javascript:void(0)").removeAttr('target');;
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
            $inputUser.val( ui.item.value );
            $(document).trigger('user_changed', [ui.item]);
            this.value = ui.item.label;
            // setTimeout(function () {
            //     $(event.target).blur();
            // });
            return false;
        }
    });
</script>
@endpushonceOnReady

<div class="form-group row">
    <label for="autocomplete_user"
       class="col-lg-2 col-form-label"
    >User:</label>
    <div class="col-lg-10">
        <input type="hidden"
               name="{{$inputBag}}[user_id]"
               value='{{ old("{$inputBag}.user_id")}}'
           />
        <div class="input-group">
            <input type="text"
                   class="form-control @if($errors->$inputBag->has('user_id')) is-invalid @endif"
                   onkeyup="this.classList.remove('is-invalid')"
                   id="autocomplete_user"
                   name="autocomplete_user"
                   value="{{ old("{$inputBag}.autocomplete_user")}}"
                   data-src="{{route("{$route_namespace}.orders_phone.autocomplete", ['users'])}}"
            />
            @can('view', \App\Models\User::class)
                <div class="input-group-append" >
                    <a href="javascript:void(0)"
                       class="btn btn-primary"
                       id="autocomplete_user_url">Check user</a>
                </div>
            @endcan

        </div>

    </div>
</div>
