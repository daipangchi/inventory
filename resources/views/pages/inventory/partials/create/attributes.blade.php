<fieldset class="row col-sm-10 col-sm-offset-2" id="product-variations-input-container"
          style="display: {{ old('variations_enabled') && !old('variations') ? 'block' : 'none' }};">
    <legend>Variations</legend>

    <div class="variations-container col-sm-12">
        @if(old('variations_enabled'))
            @foreach(old('attributes') as $index => $attribute)
                <div class="row attribute">
                    <div class="col-sm-2">
                        <input type="text" name="attributes[{{ $index }}][key]" value="{{ $attribute['key'] }}"
                               class="form-control form-control-sm attribute-key"
                               placeholder="Attribute" aria-label="Attribute">
                    </div>
                    <div class="col-sm-10">
                        <select multiple name="attributes[{{ $index }}][values][]" data-role="tagsinput"
                                class="attribute-values" placeholder="Values" aria-label="values">
                            @if(isset($attribute['values']))
                                @foreach($attribute['values'] as $value)
                                    <option value="{{ $value }}" selected>{{ $value }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            @endforeach
        @else
            <div class="row attribute">
                <div class="col-sm-2">
                    <input type="text" name="attributes[0][key]" aria-label="Attribute"
                           class="form-control form-control-sm attribute-key" placeholder="Attribute">
                </div>
                <div class="col-sm-10">
                    <select multiple name="attributes[0][values][]" data-role="tagsinput"
                            class="attribute-values" placeholder="Values" aria-label="values"></select>
                </div>
            </div>
        @endif
    </div>

    <div class="col-sm-12">
        <button type="button" class="btn btn-default m-r-2" id="add-new-attribute">Add New</button>
        <button type="button" class="btn btn-primary" id="generate-variations">Generate</button>
    </div>
</fieldset>
