@if (isset($transactions) && $transactions['count'] > 0)

    <table class="table table-dark theme-table centertable" id="transactions">
        <thead>
            <tr style="white-space: nowrap;" class="theme-tableOuter">
                <th>ID</th>
                <th>Table Name</th>
                <th>Item ID</th>
                <th>Type</th>
                <th>Date</th>
                <th>Time</th>
                <th class="viewport-mid-large">Site</th>
                <th class="viewport-mid-large">Username</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>

        @foreach ($transactions['rows'] as $transaction)
            <tr class="{{ $transaction['class'] }}">
                <td id="t_id_{{ $transaction['id'] }}">{{ $transaction['id'] }}</td>
                <td id="t_table_name_{{ $transaction['id'] }}">{{ $transaction['table_name'] }}</td>
                <td id="t_item_id_{{ $transaction['id'] }}">{{ $transaction['item_id'] }}</td>
                <td id="t_type_{{ $transaction['id'] }}">{{ ucwords($transaction['type']) }}</td>
                <td id="t_date_{{ $transaction['id'] }}" style="white-space: nowrap;">{{ $transaction['date'] }}</td>
                <td id="t_time_{{ $transaction['id'] }}" style="white-space: nowrap;">{{ $transaction['time'] }}</td>
                <td id="s_name_{{ $transaction['id'] }}" class="viewport-mid-large">{{ $transaction['site_name'] }}</td>
                <td id="t_username_{{ $transaction['id'] }}" class="viewport-mid-large">{{ $transaction['username'] }}</td>
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
                @if (isset($transactions['view']) && $transactions['view'] !== 'transactions')
                    &nbsp;&nbsp;<or class="specialColor clickable" onclick="navPage('{{ url('transactions') }}/{{ $params['item_id'] }}')">view all</or>
                @endif
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
                            @if (isset($transactions['view']) && $transactions['view'] !== 'transactions')
                            <td><or class="specialColor clickable" onclick="navPage('{{ url('transactions') }}/optics/{{ $params['item_id'] }}')">view all</or></td>
                            @endif
                        <tr>
                    </tbody>
                </table>        
            </form>
        @endif
    </div>
@else 
    No Transactions
@endif
