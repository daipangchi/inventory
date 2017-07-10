<div class="main-nav">
    <div class="container">
        <ul class="nav nav-inline">
            <li class="nav-item">
                <a class="nav-link" href="/">Home</a>
            </li>
            @if(auth()->user()->is_admin)
                <li class="nav-item">
                    <a class="nav-link" href="/merchants">Merchants</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/categories">Categories</a>
                </li>
            @endif
            @if(! auth()->user()->is_admin && (boolean)session('impersonated_by'))
                <li class="nav-item">
                    <a class="nav-link" href="/schedules">Schedules</a>
                </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" href="/inventory">Inventory</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/orders">Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/shipments">Shipments</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/reports">Reports</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/settings">Settings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/logout">Logout</a>
            </li>
        </ul>
    </div>
</div>
