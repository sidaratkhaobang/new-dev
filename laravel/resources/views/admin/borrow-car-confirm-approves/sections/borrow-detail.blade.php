{{-- Detail --}}
<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="d-md-flex justify-content-md-between align-items-md-center">
            <div>
                <h4>{{ __('borrow_cars.borrow_detail') }}</h4>
            </div>
            <div>
                @if (in_array($d->status, [BorrowCarEnum::PENDING_DELIVERY, BorrowCarEnum::IN_PROCESS, BorrowCarEnum::SUCCESS]))
                    <a target="_blank" href="{{ route('admin.borrow-cars.print-pdf', ['borrow_car_id' => $d->id]) }}"
                        class="btn btn-primary">
                        {{ __('borrow_cars.worksheet_print') }}
                    </a>
                @endif
            </div>
        </div>
        <hr>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="borrow_id" :value="$d->borrow_type" :list="$borrow_type_list" :label="__('borrow_cars.borrow_type')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="borrow_reason" :value="$d->purpose" :label="__('borrow_cars.borrow_reason')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="borrow_branch_id" :value="$d->borrow_branch_id" :list="$branch_list" :label="__('transfer_cars.branch')" :optionals="['required' => true]"/>
            </div>
        </div>

        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="start_date" name="start_date" :value="get_thai_date_format($d->start_date, 'd/m/Y')" :label="__('borrow_cars.start_date')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                @if (Route::is('*.edit') && strcmp($d->borrow_type, BorrowTypeEnum::BORROW_OTHER) === 0)
                    <x-forms.date-input id="end_date" name="end_date" :value="$d->end_date" :label="__('borrow_cars.end_date')"
                        :optionals="['required' => true, 'date_enable_time' => true]" />
                @else
                    <x-forms.input-new-line id="end_date" name="end_date" :value="get_thai_date_format($d->end_date, 'd/m/Y')" :label="__('borrow_cars.end_date')"
                        :optionals="['required' => true]" />
                @endif
            </div>

            <div class="col-sm-6">
                <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('borrow_cars.remark')" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.view-image :id="'optional_borrow_files'" :label="__('transfer_cars.optional_file')" :list="$optional_borrow_files" />
            </div>
        </div>
    </div>
</div>
