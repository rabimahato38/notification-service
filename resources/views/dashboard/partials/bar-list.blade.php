@if ($rows->isEmpty())
    <p class="muted small">No traffic in the last 7 days.</p>
@else
    @php $top = max(1, $rows->max('total')); @endphp
    <ul class="bar-list">
        @foreach ($rows as $row)
            <li>
                <div class="bar-meta">
                    <a class="row-link {{ $filter === 'template' ? 'mono' : '' }}" href="{{ route('logs.index', [$filter === 'template' ? 'template' : 'service_name' => $row->label]) }}">{{ $row->label }}</a>
                    <span class="muted nowrap">{{ number_format($row->total) }}@if ($row->failed > 0) · <span style="color:#fca5a5">{{ $row->failed }} failed</span>@endif</span>
                </div>
                <div class="bar-track"><div class="bar-fill" style="width: {{ round($row->total / $top * 100) }}%"></div></div>
            </li>
        @endforeach
    </ul>
@endif
