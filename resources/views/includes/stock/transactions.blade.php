<div class="container well-nopad theme-divBg viewport-large-empty" style="margin-top:5px">
@if (isset($transactions) && $transactions['count'] > 0)
    <h2 style="font-size:22px">Transactions</h2>
    <table class="table table-dark theme-table centertable" id="transactions">
        <thead>
            <tr style="white-space: nowrap;" class="theme-tableOuter">
                <th hidden>ID</th>
                <th hidden>Stock ID</th>
                <th hidden>Item ID</th>
                <th>Type</th>
                <th>Date</th>
                <th>Time</th>
                <th>Location</th>
                <th class="viewport-mid-large">Shelf</th>
                <th class="viewport-mid-large">Username</th>
                <th>Quantity</th>
                <th class="viewport-large-empty" @if ($head_data['config_compare']['cost_enable_normal'] == 0) hidden @endif>Price</th>
                <th class="viewport-large-empty">Serial Number</th>
                <th hidden>Comments</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>

        @foreach ($transactions['rows'] as $transaction)
            <tr class="{{ $transaction['class'] }}">
                <td id="t_id_{{ $transaction['id'] }}" hidden>{{ $transaction['id'] }}</td>
                <td id="t_stock_id_{{ $transaction['id'] }}" hidden>{{ $transaction['stock_id'] }}</td>
                <td id="t_item_id_{{ $transaction['id'] }}" hidden>{{ $transaction['item_id'] }}</td>
                <td id="t_type_{{ $transaction['id'] }}">{{ ucwords($transaction['type']) }}</td>
                <td id="t_date_{{ $transaction['id'] }}" style="white-space: nowrap;">{{ $transaction['date'] }}</td>
                <td id="t_time_{{ $transaction['id'] }}" style="white-space: nowrap;">{{ $transaction['time'] }}</td>
                <td id="a_name_{{ $transaction['id'] }}">{{ $transaction['area_name'] }}</td>
                <td id="s_name_{{ $transaction['id'] }}" class="viewport-mid-large">{{ $transaction['shelf_name'] }}</td>
                <td id="t_username_{{ $transaction['id'] }}" class="viewport-mid-large">{{ $transaction['username'] }}</td>
                <td id="t_quantity_{{ $transaction['id'] }}">{{ $transaction['quantity'] }}</td>
                <td id="t_price_{{ $transaction['id'] }}" class="viewport-large-empty" @if ($head_data['config_compare']['cost_enable_normal'] == 0) hidden @endif>{{ $head_data['config_compare']['currency'].$transaction['price'] }}</td>
                <td id="t_serial_number_{{ $transaction['id'] }}" class="viewport-large-empty">{{ $transaction['serial_number'] }}</td>
                <td id="t_comments_{{ $transaction['id'] }}" hidden>{{ $transaction['comments'] }}</td>
                <td id="t_reason_{{ $transaction['id'] }}">{{ $transaction['reason'] }}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
    <div class="container" style="text-align: center;">
        @if ($transactions['pages'] > 1 && $transactions['pages'] <=15)
            @if ($transactions['page'] > 1)
                <or class="gold clickable" style="padding-right:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $transactions['page']-1 }}') + '')"><</or>
            @endif
            @if ($transactions['pages'] > 5)
                @for ($i = 1; $i <= $transactions['pages']; $i++)
                    @if ($i == $transactions['page'])
                        <span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">{{ $i }}</span>
                    @elseif ($i == 1 && $transactions['page'] > 5)
                        <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or><or style="padding-left:5px;padding-right:5px">...</or>
                    @elseif ($i < $transactions['page'] && $i >= $transactions['page']-2)
                        <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                    @elseif ($i > $transactions['page'] && $i <= $transactions['page']+2)
                        <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                    @elseif ($i == $transactions['pages'])
                        <or style="padding-left:5px;padding-right:5px">...</or><or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                    @endif
                @endfor
            @else
                @for ($i = 1; $i <= $transactions['pages']; $i++)
                    @if ($i == $transactions['page'])
                        <span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">{{ $i }}</span>
                    @else
                        <or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $i }}') + '')">{{ $i }}</or>
                    @endif
                @endfor
            @endif

            @if ($transactions['page'] < $transactions['pages'])
                <or class="gold clickable" style="padding-left:2px" onclick="navPage(updateQueryParameter('', 'page', '{{ $transactions['page'] + 1}}') + '')">></or>
            @endif
                &nbsp;&nbsp;<or class="specialColor clickable" onclick="navPage('{{ url('transactions') }}/{{ $params['stock_id'] }}')">view all</or>
        @else 
            <form style="margin-bottom:0px">
                <table class="centertable">
                    <tbody>
                        <tr>
                            <td style="padding-right:10px">Page:</td>
                            <td style="padding-right:10px">
                                <select id="page-select" class="form-control row-dropdown" style="width:50px;height:25px; padding:0px" onchange="navPage(updateQueryParameter('', 'page', document.getElementById('page-select').value + '#transactions'))" name="page">
                                @for ($i = 1; $i <= $transactions['pages']; $i++) 
                                    <option value="{{ $i }}" @if ($i == $transactions['page']) selected @endif>{{ $i }}</option>
                                @endfor
                                </select>
                            </td>
                            <td><or class="specialColor clickable" onclick="navPage('{{ url('transactions') }}/{{ $params['stock_id'] }}')">view all</or></td>
                        <tr>
                    </tbody>
                </table>        
            </form>
        @endif
    </div>
@else 
    No Transactions
@endif
</div>
<div class="container well-nopad theme-divBg viewport-small-empty text-center" style="margin-top:5px">
    <or class="specialColor clickable" style="font-size:12px" onclick="navPage('{{ url('transactions') }}/{{ $params['stock_id'] }}')">View Transactions</or>
</div>