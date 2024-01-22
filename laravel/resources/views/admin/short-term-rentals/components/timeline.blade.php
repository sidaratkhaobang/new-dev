@if ($data)
    @foreach ($data as $index => $timeline)
        <div class="gantt-timeline gantt-border mb-2 timeline-color-{{ $timeline->bg_color }}"
            style="grid-row: {{ ($index % 3) + 1 }}; grid-column:  {{ $timeline->start_point }}  / span {{ $timeline->total_point}} ">
            <p class="timeline-label">{{ $timeline->text }}</p>
        </div>
    @endforeach
@endif