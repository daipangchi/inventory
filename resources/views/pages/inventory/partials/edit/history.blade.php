<h3>{{ $product->name }}</h3>

<ul class="list-group">
    @foreach($product->changeLogs as $log)
        <li class="list-group-item">
            <div class="row">
                <div class="col-sm-3">
                    <p>{{ $log->created_at->format('F j, Y, g:i a') }}</p>
                    <p>{{ $log->created_at->diffForHumans() }}</p>
                </div>
                <div class="col-sm-9">
                    @if($log->action == 'created')
                        <p>Product was <b>{{ $log->action }}</b> from <b>{{ ucfirst($log->channel) }}</b>.</p>

                        <table class="table table-sm">
                            <tr>
                                <td>Price:</td>
                                <td>{{ money_format('$%i', $log->data['price']) }}</td>
                            </tr>
                            <tr>
                                <td>Quantity:</td>
                                <td>{{ $log->data['quantity'] }}</td>
                            </tr>
                        </table>
                    @elseif($log->action == 'updated' && $log->data['entity'] == 'price')
                        <table class="table table-sm">
                            <tr>
                                <td>Reason</td>
                                <td>{{ $log->data['reason'] }}</td>
                            </tr>
                            <tr>
                                <td>Price before:</td>
                                <td>{{ money_format('$%i', $log->data['before']) }}</td>
                            </tr>
                            <tr>
                                <td>Price after:</td>
                                <td>{{ money_format('$%i', $log->data['after']) }}</td>
                            </tr>
                            <tr>
                                <td>Price change:</td>
                                <td>{{ ($log->data['changed'] < 0 ? '- ' : ''). money_format('$%i', abs($log->data['changed'])) }}</td>
                            </tr>
                        </table>
                    @elseif($log->action == 'updated' && $log->data['entity'] == 'quantity')
                        <table class="table table-sm">
                            <tr>
                                <td>Reason</td>
                                <td>{{ $log->data['reason'] }}</td>
                            </tr>
                            <tr>
                                <td>Quantity before:</td>
                                <td>{{ $log->data['before'] }}</td>
                            </tr>
                            <tr>
                                <td>Quantity after:</td>
                                <td>{{ $log->data['after'] }}</td>
                            </tr>
                            <tr>
                                <td>Quantity change:</td>
                                <td>{{ $log->data['changed'] }}</td>
                            </tr>
                        </table>
                    @endif
                </div>
            </div>
        </li>
    @endforeach
</ul>
