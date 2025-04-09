<!-- ADD STOCK -->
<div class="container well-nopad theme-divBg">
    <input id="hidden-page-number" type="hidden" value="{{ $params['page'] }}'" />
    <pre id="hidden-sql" hidden></pre>
@if ($params['stock_id'] == 0 && $params['add_new'] == 'new')
    <!-- /stock/0/add/new -->
    <!-- add new stock item -->
    @include('includes/stock/add/new')
@elseif ($params['stock_id'] == 0 && $params['add_new'] == null)
    <!-- /stock/0/add -->
    <!-- search for stock -->
    @include('includes/stock/add/search')
@else
    <!-- /stock/#/add -->
    <!-- existing stock, add more quantity -->
    @include('includes/stock/add/existing')
@endif
    @include('includes.stock.new-properties')
    <!-- Add the JS for the file -->
    <script src="{{ asset('js/stock-add.js') }}"></script>
</div>