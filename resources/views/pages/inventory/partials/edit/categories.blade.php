<h3>Categories</h3>
<form action="/inventory/{{$product->id}}?_type=categories" method="post" id="product-categories-form">

    <div id="product-categories" style="display: none;">
        <ul>
            @foreach($categories as $category)
                {!! $category->toHtml($product->categories) !!}
            @endforeach
        </ul>
    </div>

    <hr>

    <div class="text-right">
        <button class="btn btn-primary">Update Categories</button>
    </div>
</form>