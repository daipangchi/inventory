<p>Your {{ ucfirst($log->channel) }} integration has been completed.</p>

<ul>
    <li>Products created: {{ $log->products_created ?: '0' }}</li>
    <li>Products updated: {{ $log->products_updated ?: '0' }}</li>
    <li>Products removed: {{ $log->products_removed ?: '0' }}</li>
</ul>
