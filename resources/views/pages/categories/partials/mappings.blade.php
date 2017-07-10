@if($category->level <= 3)
    <h3>Amazon Category Mappings</h3>

    <p>Select third-party categories to map to <strong>{{ $category->name }}</strong>.</p>

    <div class="row">


        <div class="col-sm-6">
            <select class="form-control category-mappings" name="category_mappings[]" id="amazon_category_mappings" multiple>
                @foreach($amazonCategories as $cat)
                    <option value="{{ $cat->node_id }}" data-category-level="{{ $cat->node_level }}" selected>
                        {{ $cat->path_by_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <hr>

    <h3>eBay Category Mappings</h3>

    <p>Select third-party categories to map to <strong>{{ $category->name }}</strong>.</p>

    <div class="row">
        <div class="col-sm-6">
            <select class="form-control category-mappings" name="category_mappings[]" id="ebay_category_mappings" multiple>
                @foreach($ebayCategories as $cat)
                    <option value="{{ $cat->ebay_category_id }}" data-category-level="{{ $cat->ebay_category_name }}" selected>
                        {{ $cat->ebay_category_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
@endif
