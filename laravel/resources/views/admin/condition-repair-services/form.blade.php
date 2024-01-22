@extends('admin.layouts.layout')

@section('page_title', $page_title)

@push('custom_styles')
    <style>
        .hidden {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="col-sm-12  mb-2">
                    <div class="row">
                        <div class="col-md-9 text-left">
                            <span>{{ __('condition_quotations.condition_table') }}</span>
                        </div>
                        @if ((!empty($create)))
                            <div class="col-md-3 text-end">
                                <button type="button" class="btn btn-primary"
                                    onclick="add()"><i class="fa fa-plus-circle"></i> {{ __('lang.add_data') }}</button>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="mb-3" id="condition-repair-service" v-cloak data-detail-uri="" data-title="">
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
                                <th style="width: 5%"></th>
                                <th style="width: 10%">{{ __('condition_quotations.condition_seq') }}</th>
                                <th>{{ __('condition_quotations.condition_name') }}</th>
                                <th class="sticky-col"></th>
                            </thead>
                            <template v-if="condition_service.length > 0">
                                <tbody v-for="(input,k) in condition_service">
                                    <tr>
                                        <td>
                                            <div class="form-check d-inline-block">
                                                <label class="form-check-label"></label>
                                                <i class="fas fa-angle-right" aria-hidden="true" v-on:click="hide(k)"
                                                    :id="'arrow-' + k"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" min="0"
                                                v-model="condition_service[k].seq">
                                            <input type="hidden" v-bind:name="'data_repair_service['+ k+ '][seq]'"
                                                id="seq" v-model="condition_service[k].seq">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" v-model="condition_service[k].name">
                                            <input type="hidden" v-bind:name="'data_repair_service['+ k+ '][name]'"
                                                id="name" v-model="condition_service[k].name">
                                        </td>
                                        <td>
                                            @if (empty($view))
                                                <a class="btn btn-light" v-on:click="remove(k)"
                                                    v-show="k || ( !k && condition_service.length > 1)"><i
                                                        class="fa-solid fa-trash-can" style="color:red"></i></a>
                                            @endif
                                        </td>
                                        <input type="hidden" v-bind:name="'data_repair_service['+ k+ '][id]'"
                                            id="id" v-model="condition_service[k].id">
                                    </tr>
                                    <tr :id="'sub-service' + k" class="hidden hd">
                                        <td></td>
                                        <td colspan="3">
                                            <div class="row mb-3">
                                                <div class="col-md-9 text-left">
                                                    <span>{{ __('condition_quotations.checklist_table') }}</span>
                                                </div>
                                                @if (empty($view))
                                                    <div class="col-md-3 text-end">
                                                        <button type="button" class="btn btn-primary"
                                                            v-on:click="addSub(k)"><i class="fa fa-plus-circle"></i> {{ __('lang.add_data') }}</button>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="table-wrap">
                                                <table class="table table-striped" :id="'sub-table-' + k">
                                                    <thead class="bg-body-dark">
                                                        <th style="width: 10%">{{ __('condition_quotations.checklist_seq') }}</th>
                                                        <th>{{ __('condition_quotations.checklist_name') }}</th>
                                                        <th class="sticky-col text-center"></th>
                                                    </thead>
                                                    <tbody v-if="condition_service[k].sub_condition_service.length > 0">
                                                        <tr
                                                            v-for="(input2,k2) in condition_service[k].sub_condition_service">
                                                            <td>
                                                                <input type="number" class="form-control" min="0"
                                                                    v-model="input2.seq">
                                                                <input type="hidden"
                                                                    v-bind:name="'data_repair_service['+ k+ '][sub_condition_service]['+ k2+ '][seq]'"
                                                                    id="seq" v-model="input2.seq">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    v-model="input2.name">
                                                                <input type="hidden"
                                                                    v-bind:name="'data_repair_service['+ k+ '][sub_condition_service]['+ k2+ '][name]'"
                                                                    id="name" v-model="input2.name">
                                                                <input type="hidden"
                                                                    v-bind:name="'data_repair_service['+ k+ '][sub_condition_service]['+ k2+ '][id]'"
                                                                    id="id" v-model="input2.id">
                                                            </td>
                                                            <td>
                                                                @if (empty($view))
                                                                    <a class="btn btn-light" v-on:click="removeList(k,k2)"
                                                                        v-show="condition_service[k].sub_condition_service.length > 0">
                                                                        <i class="fa-solid fa-trash-can"
                                                                            style="color:red"></i></a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tbody v-else>
                                                        <tr class="table-empty" id='empty-data'>
                                                            <td class="text-center" colspan="4">"
                                                                {{ __('lang.no_list') }} "</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </template>
                            <template v-else>
                                <tbody>
                                    <tr class="table-empty" id='empty-data'>
                                        <td class="text-center" colspan="5">" {{ __('lang.no_list') }} "</td>
                                    </tr>
                                </tbody>
                            </template>
                            <template v-for="(input,k) in del_input_id">
                                <input type="hidden" v-bind:name="'del_section[]'" id="del_input_id"
                                    v-bind:value="input">
                            </template>

                            <template v-for="(input2,k) in del_input_sub_id">
                                <input type="hidden" v-bind:name="'del_checklist[]'" id="del_input_sub_id"
                                    v-bind:value="input2">
                            </template>
                        </table>
                    </div>
                </div>

                <x-forms.hidden id="id" :value="$d->id" />
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-secondary"
                            href="{{ route('admin.condition-repair-services.index') }}">{{ __('lang.back') }}</a>
                        @if (empty($view))
                            <button type="button" class="btn btn-info btn-save-form">{{ __('lang.save') }}</button>
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
    'store_uri' => route('admin.condition-repair-services.store'),
])

@include('admin.condition-repair-services.scripts.sub-service-script')

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
