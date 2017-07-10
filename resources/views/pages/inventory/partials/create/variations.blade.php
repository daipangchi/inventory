<fieldset class="col-sm-10 col-sm-offset-2" id="product-variations-container" style="display: {{ old('variations_enabled') && old('variations') ? 'block' : 'none' }};">
    <legend>Variations</legend>

    @if(old('variations_enabled') && !empty(old('variations')))
        <table class="table">
            <thead>
            <tr>
                @foreach(array_keys(old('variations')[0]) as $column)
                    <th>{{ $column }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach(old('variations') as $index => $variation)
                <tr>
                    @foreach($variation as $key => $value)
                        <td>
                            <input class="form-control form-control-sm" type="text" value="{{ $value }}" name="{{  "variations[$index][$key]" }}">
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</fieldset>
