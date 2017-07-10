<div id="categories" style="display: none;">
    <ul>
        @foreach($categories as $category)
            {!! $category->toHtml() !!}
        @endforeach
    </ul>
</div>

<div class="m-t-2">
    <button type="button" id="edit-selected-button" class="btn btn-primary-outline btn-block btn-sm" style="display: none;">
        Edit Selected
    </button>
</div>
