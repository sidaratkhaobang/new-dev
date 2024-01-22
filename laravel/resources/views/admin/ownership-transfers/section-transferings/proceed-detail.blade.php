<div class="block {{ __('block.styles') }}">
    {{-- @section('block_options_list')
    <div class="block-options">
    </div>
@endsection --}}

    @include('admin.components.block-header', [
        'text' => __('registers.proceed_detail'),
        'block_option_id' => '_list',
        'is_toggle' => true,
    ])
    <div class="block-content">
        <table class="table table-striped table-vcenter">
            <thead class="bg-body-dark">
                <tr>
                    <th> {{__('registers.receipt_date')}}</th>
                    <th>{{__('registers.receipt_no') }}</th>
                    <th>{{ __('registers.tax') }}</th>
                    <th>{{ __('registers.service_fee') }}</th>
                    <th>{{ __('registers.total') }}</th>
                </tr>
            </thead>
            <tbody>
              
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control js-flatpickr flatpickr-input input-with-right-border" id="receipt_date"
                                    name="receipt_date" data-date-format="Y-m-d" value="{{$d->receipt_date ?? null}}" style="border-right: 1px solid #d1d8ea ;background-color: #fff !important; ">
                                <span class="input-group-text js-flatpickr-icon" style="background-color: #fff !important">
                                    <i class="icon-calendar"></i>
                                </span>
                            </div>
                        </td>
                        <td>
                            <input type="text" class="form-control" id="receipt_no" name="receipt_no"  value="{{$d->receipt_no}}">
                        </td>
                        <td>
                            <input type="text" class="form-control number-format" id="tax" name="tax"  value="{{$d->tax}}">
                        </td>
                        <td>
                            <input type="text" class="form-control number-format" id="service_fee" name="service_fee"  value="{{$d->service_fee}}">
                        </td>
                        <td>
                            <input type="text" class="form-control number-format" id="total" name="total"  value="{{$d->total}}" readonly>
                        </td>


                    </tr>
            </tbody>
        </table>
    </div>
</div>
