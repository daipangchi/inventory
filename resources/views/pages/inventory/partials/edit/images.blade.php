<div class="row">
    <div class="col-sm-12">
        <fieldset>
            <legend>Upload Images</legend>

            <div class="images-validation-container"></div>

            <input id="fileupload" type="file" name="files[]" multiple data-csrf-token="{{ csrf_token() }}" data-product-id="{{ $product->id }}">

            <div id="files" class="row m-t-2"></div>

            <div class="text-right">
                <button id="fileupload-button" class="btn btn-primary" style="display: none;">Upload</button>
            </div>
        </fieldset>
    </div>
</div>

<div class="m-t-1 uploaded-images-container">
    @foreach(array_chunk($product->images->toArray(), 4) as $row)
        <div class="row">
            @foreach($row as $image)
                <div class="col-sm-3">
                    <img class="img-thumbnail" src="{{ $image['url'] }}" alt="alt">
                    <p class="text-xs-center">
                        <a href data-image-id="{{ $image['id'] }}" data-product-id="{{ $product['id'] }}" class="lnk-delete-image">
                            <i class="glyphicon glyphicon-remove"></i>
                            Remove
                        </a>
                    </p>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
