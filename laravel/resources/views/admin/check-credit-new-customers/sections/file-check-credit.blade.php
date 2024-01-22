@section('block_options_doc')
    @if((Route::is('*.edit') || Route::is('*.create')) && $d->status != \App\Enums\CheckCreditStatusEnum::CONFIRM)
    <button class="btn btn-primary btn-custom-size btn-open-modal-upload" type="button">
        <i class="fa fa-plus-circle"></i> {{ __('check_credit.form.section_table.btn-add-file') }}
    </button>
    @endif
@endsection

<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        // 'block_header_class' => 'justify-content-start',
        // 'block_title_class' => 'flex-grow-0',
        'text' => __('check_credit.form.section_table.title'),
        'block_option_id' => '_doc'
    ])
    <div class="block-content">
        <div id="table-file-upload" v-cloak data-detail-uri="" data-title="">
            <div class="table-wrap mb-4">
                <table class="table table-striped">
                    <thead class="bg-body-dark">
                    <th style="width: 45%">{{ __('check_credit.form.section_table.file_name') }}</th>
                    <th style="width: 45%">{{ __('check_credit.form.section_table.extension_name') }}</th>
                    <th style="width: 10%" class="sticky-col text-center"></th>
                    </thead>
                    <tbody v-if="data_list.length > 0">
                    <tr v-for="(item, index) in data_list">
                        <td>@{{ item.name }}</td>
                        <td>@{{ item.mime_type }}</td>
                        <td class="sticky-col text-center">
                            <a v-if="item.saved" v-bind:href="item.url"><i class="fa fa-file-arrow-down">&nbsp;&nbsp;&nbsp;</i></a><i class="far fa-trash-alt" style="cursor: pointer" v-on:click="removeFile(index)"></i>
                        </td>
                    </tr>
                    </tbody>
                    <tbody v-else>
                    <tr class="table-empty">
                        <td class="text-center" colspan="6">"
                            {{ __('lang.no_list') . __('check_credit.form.section_table.title') }} "</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <x-forms.hidden id="id" :value="$d->id" />
        @if(in_array($d->status, [CheckCreditStatusEnum::CONFIRM, CheckCreditStatusEnum::REJECT]))
            <div class="row mb-4">
                <x-forms.radio-inline id="approve_status"
                    :value="$d->status"
                    :list="$listApproveStatus"
                    :label="__('check_credit.form.result_check_credit')"
                    :optionals="['required' => true ,'input_class' => 'col-sm-6 input-pd']" />
            </div>
            <div class="row" id="display-approve" style="display: none">
                <div class="col-sm-3 mb-2">
                    <x-forms.input-new-line id="approved_amount" :value="$d->approved_amount" :label="__('check_credit.form.approved_amount')" 
                        :optionals="['placeholder' => __('lang.input.placeholder') ,  'input_class' => 'number-format']"/>
                </div>
                <div class="col-sm-3 mb-2">
                    <x-forms.input-new-line id="approved_days" :value="$d->approved_days" :label="__('check_credit.form.approved_days')" :optionals="['type' => 'number']"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.view-image id="approve_other_file" :label="__('เอกสารเพิ่มเติม')" :list="isset($check_credit_approve_file) ? $check_credit_approve_file : []" />
                </div>
            </div>
            <div class="row push" id="display-non-approve" style="display: none">
                <div class="col-sm-6 mb-2">
                    <x-forms.input-new-line id="reason" :value="$d->reason" :label="__('check_credit.form.reason')" />
                </div>
            </div>
        @endif
    </div>
</div>
