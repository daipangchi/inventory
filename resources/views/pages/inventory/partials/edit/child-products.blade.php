@foreach($product->children as $child)
    <ul class="list-group">
        <li class="list-group-item">
            <p><a href="/inventory/{{$child->id}}/edit">{{ $child->sku }}</a></p>

            <div class="row">
                <div class="col-sm-6">
                    <table class="table table-sm">
                        <colgroup>
                            <col width="30%">
                            <col width="70%">
                        </colgroup>
                        @foreach($child->attributes ?: [] as $attribute => $value)
                            <tr>
                                <td>{{ $attribute}}</td>
                                <td>{{ $value }}</td>
                            </tr>
                        @endforeach
                    </table>

                </div>
            </div>
        </li>
    </ul>
@endforeach
