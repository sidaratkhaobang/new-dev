<div id="repair_bill_list" class="block {{ __('block.styles') }}">
    @section('block_options_add_repair_data')
        @if(!isset($view))
            <button type="button" class="btn btn-primary" @click="addRepairData">
                <i class="icon-add-circle"></i>
                {{__('lang.add_data')}}
            </button>
        @endif
    @endsection
    @include('admin.components.block-header', [
            'text' => __('repair_bills.bill_list'),
            'block_icon_class' => 'icon-document',
            'block_option_id' => '_add_repair_data',
        ])
    <div class="block-content">
        <table class="table table-striped table-vcenter">
            <thead class="bg-body-dark">
            <tr>
                <th>#</th>
                <th>{{__('repair_bills.search_bill_no')}}</th>
                <th>{{__('repair_bills.total_document')}}</th>
                <th>{{__('repair_bills.repair_bill_price')}}</th>
                <th>{{__('lang.remark')}}</th>
                <th></th>
            </tr>
            </thead>
            <tbody v-if="repair_bill_list.length > 0">
            <tr v-for="(item, index) in repair_bill_list">
                <td>
                    @{{ ++index }}
                </td>
                <td>
                    <input class="form-control worksheet_no" :value="item.billing_slip_no" id="worksheet_no"
                           :name="'repair_bill_data['+index+'][worksheet_no]'">
                </td>
                <td>
                    <input type="number" class="form-control number-format total_document" :value="item.amount_document"
                           id="total_document"
                           :name="'repair_bill_data['+index+'][total_document]'">
                </td>
                <td>
                    <input type="number" class="form-control repair_bill_price number-format" :value="item.amount"
                           id="repair_bill_price"
                           :name="'repair_bill_data['+index+'][repair_bill_price]'">
                </td>
                <td>
                    <input class="form-control remark" :value="item.remark" id="remark"
                           :name="'repair_bill_data['+index+'][remark]'">
                </td>
                <td>
                    @if(!isset($view))
                        <template>
                            <a class="dropdown-item btn-delete-row" href="javascript:void(0)"
                               v-on:click="remove(index)"><i
                                    class="fa fa-trash-alt me-1"></i></a>
                        </template>
                    @endif
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
