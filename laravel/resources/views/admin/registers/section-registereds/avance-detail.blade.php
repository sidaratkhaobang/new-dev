<div class="block {{ __('block.styles') }}">
    {{-- @section('block_options_list')
    <div class="block-options">
    </div>
@endsection --}}

    @include('admin.components.block-header', [
        'text' => __('registers.avance'),
        'block_option_id' => '_list',
        'is_toggle' => true,
    ])
    <div class="block-content">
        <table class="table table-striped table-vcenter">
            <thead class="bg-body-dark">
                <tr>
                    <th> {{__('registers.memo_no')}}</th>
                    <th>{{__('registers.receipt_avance') }}</th>
                    <th>{{ __('registers.operation_fee_avance') }}</th>
                    <th>{{ __('registers.total_avance') }}</th>
                </tr>
            </thead>
            <tbody>
              
                    <tr>
                        <td>
                            <input type="text" class="form-control" id="memo_no" name="memo_no"  value="{{$d->memo_no}}">
                        </td>
                        <td>
                            <input type="text" class="form-control number-format" id="receipt_avance" name="receipt_avance"  value="{{$d->receipt_avance}}">
                        </td>
                        <td>
                            <input type="text" class="form-control number-format" id="operation_fee_avance" name="operation_fee_avance"  value="{{$d->operation_fee_avance}}">
                        </td>
                        <td>
                            <input type="text" class="form-control number-format" id="total_avance" name="total_avance"  value="{{$d->total_avance}}" disabled>
                        </td>


                    </tr>
            </tbody>
        </table>
    </div>
</div>
