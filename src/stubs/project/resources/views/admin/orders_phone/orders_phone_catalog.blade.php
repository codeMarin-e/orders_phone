@pushonceOnReady('below_js_on_ready')
<script>
</script>
@endpushonceOnReady

@pushonceOnReady('below_js_on_ready')
<script>
    var showingCatalog = false;
    var oldCatalogHtml = $('#js_catalog').html();
    $(document).on('click', '#js_toggle_catalog', function() {
        if(showingCatalog) return;
        showingCatalog = true;
        var $catalogCon = $('#js_catalog');
        if(!$catalogCon.hasClass('d-none')) {
            $catalogCon.addClass('d-none').html( oldCatalogHtml );
            showingCatalog = false;
            return;
        }
        $catalogCon.removeClass('d-none');
        $.ajax({
            url: $catalogCon.attr('data-src'),
            method: 'GET',
            timeout: 0,
            dataType: 'html',
            error: function(jqXHR, textStatus, errorThrown){
                if(jqXHR.responseJSON && jqXHR.responseJSON.message) alert(jqXHR.responseJSON.message);
                else alert('Error');
                $catalogCon.addClass('d-none')
            },
            success: function(response) {
                showingCatalog = false;
                $catalogCon.html( $(response).filter('#js_catalog').first().html() );
            }
        });
    });

    var showingCatalogProducts = false;
    $(document).on('click', '#js_catalog_categories .js_catalog_category', function(e) {
        e.preventDefault();
        if(showingCatalogProducts) return;
        showingCatalogProducts = true;
        var $this = $(this);
        $('#js_catalog_categories .js_catalog_category').removeClass('active');
        showCategoryBranch( $this );
        $this.addClass('active');
        $('#js_catalog_sizes').html(''); //empty the sizes container
        var $catalogProducts = $('#js_catalog_products');
        $catalogProducts.html( $('#js_loader').html() );
        $.ajax({
            url: $this.attr('data-src'),
            method: 'GET',
            timeout: 0,
            dataType: 'html',
            error: function(jqXHR, textStatus, errorThrown){
                if(jqXHR.responseJSON && jqXHR.responseJSON.message) alert(jqXHR.responseJSON.message);
                else alert('Error');
            },
            success: function(response) {
                showingCatalogProducts = false;
                $catalogProducts.html( response );
            }
        });
    });

    $(document).on('click', '.js_catalog_toggle_subs', function() {
        var $this = $(this);
        var myId = $this.attr('data-id');
        if($this.attr('data-type') == 'hide') {
            $('#js_catalog_categories .js_catalog_category[data-parent="'+ myId +'"] .js_catalog_toggle_subs').each(function(index, el) {
                $(el).attr('data-type', 'hide').html('-').click();
            });
            $this.attr('data-type', 'show').html('+');
            $('#js_catalog_categories .js_catalog_category[data-parent="'+ myId +'"]').addClass('d-none');
            return;
        }
        $this.attr('data-type', 'hide').html('-');;
        $('#js_catalog_categories .js_catalog_category[data-parent="'+ myId +'"]').removeClass('d-none');
        return;
    });

    var showCategoryBranch = function($category) {
        $('#js_catalog_categories .js_catalog_category[data-id="'+ $category.attr('data-parent') +'"]').each(function(index, el) {
            var showBtn = $(el).find('.js_catalog_toggle_subs').first();
            if(showBtn.attr('data-type') == 'hide') return;
            showBtn.click();
            showCategoryBranch( $(el) );
        });
    }

    $(document).on('click', '#js_catalog_products .js_catalog_category', function(e) {
        e.preventDefault();
        $('#js_catalog_categories .js_catalog_category[data-id="'+ $(this).attr('data-id') +'"]').click();
    });

    var showingCatalogSizes = false;
    $(document).on('click', '#js_catalog_products .js_catalog_product', function(e) {
        e.preventDefault();
        if(showingCatalogSizes) return;
        showingCatalogSizes = true;
        var $this = $(this);
        $('#js_catalog_products .js_catalog_product').removeClass('active');
        $this.addClass('active');
        var $catalogSizes = $('#js_catalog_sizes');
        $catalogSizes.html( $('#js_loader').html() );
        $.ajax({
            url: $this.attr('data-src'),
            method: 'GET',
            timeout: 0,
            dataType: 'html',
            error: function(jqXHR, textStatus, errorThrown){
                if(jqXHR.responseJSON && jqXHR.responseJSON.message) alert(jqXHR.responseJSON.message);
                else alert('Error');
            },
            success: function(response) {
                showingCatalogSizes = false;
                $catalogSizes.html( response );
            }
        });
    });

    $(document).on('click', '#js_catalog_sizes .js_catalog_size', function(e) {
        e.preventDefault();
        var $this = $(this);
        $('#js_catalog_sizes .js_catalog_size').removeClass('active');
        $this.addClass('active');
        $('#autocomplete_product').val('');
        $('#add_product_size').attr('data-size_id', $this.attr('data-id')).removeAttr('disabled');
    })
</script>
@endpushonceOnReady

@pushonce('below_templates')

@endpushonce

<div class="form-group row @if(!isset($categories)) d-none @endif" id="js_catalog"
     data-src="{{route("{$route_namespace}.orders_phone.show_catalog")}}">
    @isset($categories)
        <div class="col-lg-4" id="js_catalog_categories">
            <div class="list-group" data-spy="scroll"  data-offset="0" style="height: 244px; overflow-y: scroll;">
                @includeWhen($categories->count(), 'admin/orders_phone/orders_phone_catalog_categories', [
                    'categories' => $categories,
                    'categoriesSubQry' => $categoriesSubQry,
                    'level' => 0
                ])
            </div>
        </div>
        <div class="col-lg-4" id="js_catalog_products">
        </div>
        <div class="col-lg-4" id="js_catalog_sizes">
        </div>
    @else
        <div class="col-lg-12">
            <div class="spinner-border spinner-border-sm text-warning" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    @endisset
</div>

