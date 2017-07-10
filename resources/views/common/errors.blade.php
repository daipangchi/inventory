@if(count($errors))
    <div class="alert alert-danger form-error-text-block" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="z-index: 100;"><span aria-hidden="true">×</span></button>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
