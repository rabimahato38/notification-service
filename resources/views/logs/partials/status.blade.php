@if ($log->status === 'success')
    <span class="badge badge-ok">Delivered</span>
@else
    <span class="badge badge-bad" title="HTTP {{ $log->status_code }}">Failed · {{ $log->status_code }}</span>
@endif
