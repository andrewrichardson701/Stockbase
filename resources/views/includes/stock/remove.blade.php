<!-- Remove Stock -->
<div class="container well-nopad theme-divBg">
    <input id="hidden-page-number" type="hidden" value="{{ $params['page'] }}'" />
    <pre id="hidden-sql" hidden></pre>
@if($params['stock_id'] == 0)
    <!-- /stock/0/remove -->
    <!-- search for item -->
    @include('includes.stock.remove.search')
@else
    <!-- /stock/#/remove -->
    <!-- remove existing quantity -->
    @include('includes.stock.remove.existing')
@endif
    <!-- Add the JS for the file -->
    <script src="{{ asset('js/stock-remove.js') }}"></script>
</div>


