<div class="container" id="alerts-container">
    <div class="row">
        @if(session()->get('success'))
            <div class="alert alert-success">
                <h1> {{ session()->get('success') }}</h1>

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        @endif
        @if(session()->get('info'))
            <div class="alert alert-info">
                <h1> {{ session()->get('info') }}</h1>

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        @endif
        @if(session()->get('error'))
            <div class="alert alert-danger">
                <h1> {{ session()->get('error') }}</h1>

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        @endif
        @if(session()->get('warning'))
            <div class="alert alert-warning">
                <h1> {{ session()->get('warning') }}</h1>

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        @endif
    </div>
</div>
