<div class="row">
    <div class="col-sm-12">
        <div class="btn-group" role="group" aria-label="Horizontal Outline Primary">
            @if (strcmp($state, CarStateEnum::CREATE_DETAIL) === 0)         
                <a type="button" href="{{ route('admin.cars.create') }}"
                    class="btn btn-outline-primary btn-tab-page page-1 active" data-page="1">รายละเอียดรถยนต์</a>
            @elseif (strcmp($state, CarStateEnum::EDIT_DETAIL) === 0)    
                <a type="button" href="{{ route('admin.cars.edit', ['car' => $d->id]) }}"
                    class="btn btn-outline-primary btn-tab-page page-1 active" data-page="1">รายละเอียดรถยนต์</a>
            @else
                <a type="button" href="{{ route('admin.cars.show', ['car' => $d->id]) }}"
                    class="btn btn-outline-primary btn-tab-page page-1 active" data-page="1">รายละเอียดรถยนต์</a>
            @endif
            @if (Route::is('*.edit') || Route::is('*.show'))
                <a type="button" href="#" class="btn btn-outline-primary btn-tab-page page-2" data-page="2">
                    {{ __('พ.ร.บ / ประกันภัย / ภาษี / ทะเบียน') }}
                </a>
                <a type="button" href="#" class="btn btn-outline-primary btn-tab-page page-3" data-page="3">
                    {{ __('เอกสารสัญญาเช่า') }}
                </a>
                <a type="button" href="#" class="btn btn-outline-primary btn-tab-page page-4" data-page="4">
                    {{ __('ประวัติการติดตั้งอุปกรณ์') }}
                </a>
                <a type="button" href="#" class="btn btn-outline-primary btn-tab-page page-5" data-page="5">
                    {{ __('ประวัติอุบัติเหตุ/การซ่อมบำรุง') }}
                </a>
            @endif
        </div>
    </div>
</div>
