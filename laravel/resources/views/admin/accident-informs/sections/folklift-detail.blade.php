<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('accident_informs.forklift_detail'),
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.radio-inline id="first_lifting" :value="$d->first_lifting" :list="$status_list" :label="__('accident_informs.first_lifting')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3" id="first_lifter_id"
                @if ($d->first_lifting) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.input-new-line id="first_lifter" :value="$d->first_lifter" :label="__('accident_informs.first_lifter')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3" id="first_lift_date_id"
                @if ($d->first_lifting) style="display: block;" @else  style="display: none;" @endif>
                @if (Route::is('*.edit') || Route::is('*.create'))
                    <x-forms.date-input id="first_lift_date" name="first_lift_date" :value="$d->first_lift_date" :label="__('accident_informs.first_lift_date')"
                        :optionals="['required' => true, 'date_enable_time' => true]" />
                @else
                    <x-forms.input-new-line id="first_lift_date" name="first_lift_date" :value="get_thai_date_format($d->accident_date, 'd/m/Y')"
                        :label="__('accident_informs.first_lift_date')" />
                @endif
            </div>

            <div class="col-sm-3" id="first_lift_price_id"
                @if ($d->first_lifting) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.input-new-line id="first_lift_price" :value="$d->first_lift_price" :label="__('accident_informs.first_lift_price')"
                    :optionals="['required' => true, 'input_class' => 'number-format col-sm-4', 'oninput' => 'true']" />
            </div>
        </div>
        <div class="row push mb-4" id="first_lift_tel_id"
            @if ($d->first_lifting) style="display: block;" @else  style="display: none;" @endif>
            <div class="col-sm-3">
                <x-forms.input-new-line id="first_lift_tel" :value="$d->first_lift_tel" :label="__('accident_informs.lift_tel')" :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.radio-inline id="need_folklift" :value="$d->need_folklift" :list="$status_list" :label="__('accident_informs.need_folklift')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3" id="lift_date_id"
                @if ($d->need_folklift) style="display: block;" @else  style="display: none;" @endif>
                @if (Route::is('*.edit') || Route::is('*.create'))
                    <x-forms.date-input id="lift_date" name="lift_date" :value="$d->lift_date" :label="__('accident_informs.lift_date')"
                        :optionals="['required' => true, 'date_enable_time' => true]" />
                @else
                    <x-forms.input-new-line id="lift_date" name="lift_date" :value="get_thai_date_format($d->lift_date, 'd/m/Y')" :label="__('accident_informs.accident_date')" />
                @endif
            </div>
            <div class="col-sm-3" id="lift_from_id"
                @if ($d->need_folklift) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.input-new-line id="lift_from" :value="$d->lift_from" :label="__('accident_informs.lift_from')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3" id="lift_to_id"
                @if ($d->need_folklift) style="display: block;" @else  style="display: none;" @endif>
                <x-forms.input-new-line id="lift_to" :value="$d->lift_to" :label="__('accident_informs.lift_to')" :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row push mb-4" id="lift_price_id"
            @if ($d->need_folklift) style="display: block;" @else  style="display: none;" @endif>
            <div class="col-sm-3">
                <x-forms.input-new-line id="lift_price" :value="$d->lift_price" :label="__('accident_informs.lift_price')" :optionals="['required' => true, 'input_class' => 'number-format col-sm-4', 'oninput' => 'true']" />
            </div>
        </div>
    </div>
</div>

{{-- <div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('accident_informs.folklift_detail'),
    ])
    <div class="block-content">
        <div id="folklift-inform" v-cloak data-detail-uri="" data-title="">
            <div class="table-wrap">
                <table class="table table-striped">
                    <thead class="bg-body-dark"> --}}
                        {{-- <th style="width: 2px;">ครั้งที่</th>
                        <th>{{ __('accident_informs.lift_date') }}</th>
                        <th>{{ __('accident_informs.lifter') }}</th>
                        <th>{{ __('accident_informs.lift_from') }}</th>
                        <th>{{ __('accident_informs.lift_to') }}</th>
                        <th>{{ __('accident_informs.folklift_price') }}</th>
                        <th>{{ __('accident_informs.optional_file') }}</th>
                        @if (!isset($view))
                            <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                        @endif --}}
                        {{-- <th style="width: 2px;">ครั้งที่</th>
                        <th>{{ __('accident_informs.slide_type') }}</th>
                        <th>{{ __('accident_informs.lifter') }}</th>
                        <th>{{ __('accident_informs.lift_from') }}</th>
                        <th>{{ __('accident_informs.lift_to') }}</th>
                        <th>{{ __('accident_informs.folklift_price') }}</th>
                        <th>{{ __('accident_informs.slide_file') }}</th>
                        @if (!isset($view))
                            <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                        @endif
                    </thead>
                    <tbody v-if="folklift_list.length > 0">
                        <tr v-for="(item, index) in folklift_list">
                            <td>@{{ index + 1 }}</td>
                            <td>@{{ format_date(item.lift_date) }}</td>
                            <td>@{{ item.slide_driver }}</td>
                            <td>@{{ item.lift_from }}</td>
                            <td>@{{ item.lift_to }}</td>
                            <td>@{{ getNumberWithCommas(item.lift_price) }}</td>
                            <td>
                                <div v-if="getFilesPendingCount(item.slide_files) > 0">
                                    <p class="m-0">{{ __('drivers.pending_file') }} : @{{ getFilesPendingCount(item.slide_files) }}
                                        {{ __('lang.file') }}</p>
                                </div>
                                <div v-if="item.slide_files">
                                    <div v-for="(slide_file, index) in item.slide_files">
                                        <div v-if="slide_file.saved">
                                            <a target="_blank" v-bind:href="slide_file.url"><i
                                                    class="fa fa-download text-primary"></i>
                                                @{{ slide_file.name }}</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            @if (!isset($view))
                                <td class="sticky-col text-center">
                                    <div class="btn-group">
                                        <div class="col-sm-12">
                                            <div class="dropdown dropleft">
                                                <button type="button"
                                                    class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                    id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                    <a class="dropdown-item" v-on:click="editAccident(index)"><i
                                                            class="far fa-edit me-1"></i> แก้ไข</a>
                                                    <a class="dropdown-item btn-delete-row"
                                                        v-on:click="removeAccident(index)"><i
                                                            class="fa fa-trash-alt me-1"></i> ลบ</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            @endif
                            <input type="hidden" v-bind:name="'slide['+ index+ '][id]'" v-bind:value="item.id">
                            <input type="hidden" v-bind:name="'slide['+ index+ '][lift_date]'"
                                v-bind:value="item.lift_date">
                            <input type="hidden" v-bind:name="'slide['+ index+ '][slide_driver]'"
                                v-bind:value="item.slide_driver">
                            <input type="hidden" v-bind:name="'slide['+ index+ '][lift_from]'"
                                v-bind:value="item.lift_from">
                            <input type="hidden" v-bind:name="'slide['+ index+ '][lift_to]'"
                                v-bind:value="item.lift_to">
                            <input type="hidden" v-bind:name="'slide['+ index+ '][lift_price]'"
                                v-bind:value="item.lift_price">
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr class="table-empty">
                            <td class="text-center" colspan="8">“
                                {{ __('lang.no_list') . __('accident_informs.folklift_detail') }} “</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if (!isset($view))
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button type="button" class="btn btn-primary" onclick="addSlide()"
                            id="openModal">{{ __('lang.add') }}</button>
                    </div>
                </div>
            @endif
        </div>
        <br>
    </div>
</div>--}}
{{-- @include('admin.accident-informs.modals.folklift-modal')  --}}

