<div class="block {{ __('block.styles') }}">
    {{-- @section('block_options_list')
    <div class="block-options">
    </div>
@endsection --}}

    @include('admin.components.block-header', [
        'text' => __('tax_renewals.send_tax_detail'),
        'block_option_id' => '_list',
        'is_toggle' => true,
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="send_tax_date" :value="$d->send_tax_date" :label="__('tax_renewals.send_tax_date')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="ems" :value="$d->ems" :label="__('tax_renewals.ems')" :optionals="['required' => true]" />
            </div>
            {{-- <div class="col-sm-3">
                <x-forms.date-input id="amount_day_wait_cmi" :value="$d->amount_day_wait_cmi" :label="__('tax_renewals.amount_day_wait_cmi')" />
            </div> --}}
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="recipient_name" :value="$d->recipient_name" :label="__('tax_renewals.recipient_name')" :optionals="['required' => true]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="tel" :value="$d->tel" :label="__('tax_renewals.tel')" :optionals="['required' => true,'input_class' => 'number-format']"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-12">
                <label class="text-start col-form-label"> {{ __('tax_renewals.place') }}</label><span class="text-danger"> *</span>
                <textarea class="form-control" id="contact" name="contact" placeholder="" :value="$d->contact"
                    :label="__('tax_renewals.place')">{{$d->contact}}</textarea>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-sm-12">
                <p>ประวัติที่อยู่การส่งข้อมูล</p>
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th> {{ __('tax_renewals.seq') }}</th>
                            <th>{{ __('tax_renewals.year') }}</th>
                            <th>{{ __('tax_renewals.visitor') }}</th>
                            <th>{{ __('tax_renewals.tel') }}</th>
                            <th>{{ __('tax_renewals.place') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($tax_history->count() > 0)
                        @foreach($tax_history as $index => $history)
                        <tr>
                            <td>
                                {{$index+1}}
                            </td>
                            <td>
                                {{get_thai_date_format($history->receive_tax_label_date, 'Y')}}
                            </td>
                            <td>
                                {{$history->recipient_name}}
                            </td>
                            <td>
                                {{$history->tel}}
                            </td>
                            <td>
                                {{$history->contact}}
                            </td>

                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td class="text-center" colspan="5">" {{ __('lang.no_list') }} "</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>