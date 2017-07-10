<fieldset class="m-t-1">
    <legend>Other Information</legend>

    <div class="form-group row">
        <label for="tax_identification_number" class="col-sm-2 form-control-label">Tax ID Number</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <input type="text" id="tax_identification_number" class="form-control" name="tax_identification_number"
                       placeholder="Tax ID Number"
                       value="{{ old('tax_identification_number') ?: auth()->user()->tax_id_number }}">
            </p>
        </div>
    </div>
</fieldset>

<div class="text-right m-t-2">
    {{--<a href="/" role="button" class="btn btn-default m-r-3">Skip</a>--}}
    <button type="submit" class="btn btn-primary">Save</button>
</div>

{!! csrf_field().method_field('patch') !!}
