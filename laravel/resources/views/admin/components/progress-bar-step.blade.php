@if (sizeof($step_flow) > 0)
    <div class="row mb-3">
        <div class="col-sm-12 float-center">
            <div class="d-flex justify-content-center">
                @foreach ($step_flow as $key => $item)
                    @php
                        if ($step == $key) {
                            $class = 'primary';
                        } elseif ($step > $key) {
                            $class = 'success';
                        } else {
                            $class = 'light text-gray';
                        }
                    @endphp
                    <button type="button" class="btn badge-bg-{{ $class }} pe-none">
                        {{ $item }}
                    </button>
                    @if (!$loop->last)
                        <hr class="ms-1 me-1 my-auto" style="width: 30px;">
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endif
