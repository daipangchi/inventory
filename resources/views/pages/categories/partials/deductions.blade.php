<div class="row">
    <h2 class="col-sm-12">Deductions</h2>
</div>

<div class="deductions">
    <div class="row">
        <div class="col-sm-3">
            <fieldset class="form-group">
                <label class="form-label semibold" for="deduction-amazon">Amazon</label>
                <div class="input-group input-group-sm">
                    <input type="number" min="0" class="form-control" id="deduction-amazon" name="deduction[amazon_deduction]"
                           placeholder="00" value="{{ $deduction['amazon_deduction'] ?? '' }}" step="any">
                    <span class="input-group-addon">%</span>
                </div>
            </fieldset>
        </div>
        <div class="col-sm-3">
            <fieldset class="form-group">
                <label class="form-label semibold" for="deduction-ebay">eBay</label>
                <div class="input-group input-group-sm">
                    <input type="number" min="0" class="form-control" id="deduction-ebay" name="deduction[ebay_deduction]"
                           placeholder="00" value="{{ $deduction['ebay_deduction'] ?? '' }}" step="any">
                    <span class="input-group-addon">%</span>
                </div>
            </fieldset>
        </div>
    </div>
</div>
