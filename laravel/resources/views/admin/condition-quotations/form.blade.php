@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <h4>{{ __('condition_quotations.condition_table') }}</h4>

                <div class="mb-3" data-detail-uri="" data-title="">
                    <div class="col-sm-4 mb-4">
                        <x-forms.select-option id="condition_type" :value="$d->condition_type" :list="$list_type" :label="__('driver_wages.type')" :optionals="['required' => true]" />
                    </div>
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
                                <th style="width: 3%"></th>
                                <th style="width: 5%">ลำดับหัวข้อ</th>
                                <th style="width: 85%">หัวข้อเงื่อนไข</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="form-check d-inline-block">
                                            <input class="form-check-input form-check-input-each" type="checkbox"
                                                name="status" id="status" value="{{ $d->status }}"
                                                @if ($d->status === STATUS_ACTIVE) checked @endif>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="seq" id="seq"
                                            value="{{ $d->seq }}" min="0">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="name" id="name"
                                            value="{{ $d->name }}">
                                    </td>
                                </tr>
                                <tr id="sub-condition">
                                    <td></td>
                                    <td colspan="3">
                                        <div class="table-wrap">
                                            <table class="table table-striped">
                                                <thead class="bg-body-dark">
                                                    <th style="width: 5%"></th>
                                                    <th style="width: 5%">ลำดับรายการ</th>
                                                    <th style="width: 75%">รายการ</th>
                                                    <th class="sticky-col text-center"></th>
                                                </thead>
                                                <tbody v-if="sub_condition_checklist.length > 0">
                                                    <tr v-for="(item_checklist,k) in sub_condition_checklist">
                                                        <td>
                                                            <div class="form-check d-inline-block">
                                                                <input class="form-check-input form-check-input-each"
                                                                    type="checkbox" v-model="item_checklist.status">
                                                                <input type="hidden"
                                                                    v-bind:name="'sub_condition_checklist['+ k+ '][status]'"
                                                                    id="status" v-model="item_checklist.status">
                                                                <label class="form-check-label"></label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control"
                                                                v-model="item_checklist.seq" min="0" required>
                                                            <input type="hidden"
                                                                v-bind:name="'sub_condition_checklist['+ k+ '][seq]'"
                                                                id="seq" v-model="item_checklist.seq">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                v-model="item_checklist.name" required>
                                                            <input type="hidden"
                                                                v-bind:name="'sub_condition_checklist['+ k+ '][name]'"
                                                                id="name" v-model="item_checklist.name">
                                                            <input type="hidden"
                                                                v-bind:name="'sub_condition_checklist['+ k+ '][id]'"
                                                                id="id" v-model="item_checklist.id">
                                                        </td>
                                                        <td>
                                                            @if (empty($view))
                                                                <a class="btn btn-light" v-on:click="removeCheckList(k)"><i
                                                                        class="fa-solid fa-trash-can"
                                                                        style="color:red"></i></a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>

                                                <tbody v-else>
                                                    <tr class="table-empty" id='empty-data'>
                                                        <td class="text-center" colspan="7">" {{ __('lang.no_list') }} "
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        @if (empty($view))
                                            <div class="col-md-12 text-end">
                                                <button type="button" class="btn btn-primary"
                                                        v-on:click="addSubConditionChecklist()">{{ __('lang.add') }}</button>
                                            </div>
                                        @endif
                                    </td>
                                    <template v-for="(id,k) in del_checklist_id">
                                        <input type="hidden" v-bind:name="'del_checklist[]'" id="del_checklist_id"
                                            v-bind:value="id">
                                    </template>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <x-forms.hidden id="id" :value="$d->id" />
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-secondary"
                            href="{{ $redirect_route }}">{{ __('lang.back') }}</a>
                        @if (empty($view))
                            @can($manage_permission)
                                <button type="button" class="btn btn-info btn-save-form">{{ __('lang.save') }}</button>
                            @endcan
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => $store_route,
])

@include('admin.condition-quotations.scripts.sub-condition-script')

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        if ($status) {
            $('.form-control').prop('disabled', true);
            $("input[type=checkbox]").attr('disabled', true);
            $("input[type='radio']").attr('disabled', true);
        }
    </script>
@endpush
