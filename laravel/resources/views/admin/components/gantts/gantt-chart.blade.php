<div id="{{ $id }}" v-cloak>
    <div class="form-group mb-2">
        <div class="d-flex wrap justify-content-sm-between align-items-sm-center">
            @includeWhen($searchable, 'admin.components.gantts.search', ['search_text' => $search_text])
            <div class="d-flex align-items-center">
                @if ($show_count)
                    <div class="rounded-pill me-3">
                        เลือกแล้ว @{{ select_item_count }} {{ $count_unit }}
                    </div>
                @endif
                @if ($show_count && sizeof($status_list) > 0)
                    <div class="seperator me-3"></div>
                @endif
                @includeWhen(sizeof($status_list) > 0, 'admin.components.gantts.status-list', [
                    'status_list' => $status_list,
                ])
            </div>
        </div>

        <div class="d-flex wrap flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
            <div class="text-muted" v-text="current_month_name"></div>
            <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3">
                @includeWhen($show_navigate_btn, 'admin.components.gantts.navigate-btn')
            </nav>
        </div>
    </div>
    <div>
        <div class="row mb-3 m-0">
            <div class="col-3 px-0 head-title day-header-border-top day-header-border-bottom day-header-border-left">
                {{ $table_header ?? '' }}
            </div>
            <div class="col-9 px-0">
                <div class="gantt gantt-header" :style="{ 'grid-template-columns': grid_column }">
                    <template v-for=" i in days_in_month">
                        <div class="head"
                            :class="{
                                'highlight-date': checkhighlightDate(year + month + i),
                                'highlight-today': highlightToday(
                                    year + month + i)
                            }">
                            @{{ days_name[i - 1] }}<br>
                            @{{ i }}
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <div class="box-wrapper" v-if="item_list_temp.length > 0">
            <template v-for="(item, item_index) in item_list_temp">
                <div class="box" :class="{ 'box-disable': !isAvailable(item.id) }"
                    :id="'box-' + item.id + year + month">
                    @if ($can_select)
                        <div v-if="!isAvailable(item.id)" class="ribbon ribbon-top-left"><span>จองแล้ว</span></div>
                    @endif
                    <div class="row form-check form-block gantt-card mb-3 ms-0 me-0 {{ $can_select ? '' : 'gantt-card-disable'}}" :dusk="'item-' + item_index"
                        v-on:click="isAvailable(item.id) ? select(item_index) : null"
                        :class="{ 'high-light': item.checked }">
                        @if ($can_select)
                            <template v-if="select_multiple">
                                <input type="checkbox" class="form-check-input" :id="'select-item-' + item_index"
                                    name="select-item">
                                <input v-if='item.checked' type="hidden" id="item_id" name="items[]" v-model="item.id">
                            </template>
                            <template v-else>
                                <input type="radio" class="form-check-input" :id="'select-item-' + item_index"
                                    name="select-item">
                                <input v-if='item.checked' type="hidden" id="item_id" name="items[]" v-model="item.id">
                            </template>
                        @endif
                        <div class="col-3 px-0 align-self-center">
                            <div class="chart-card d-flex">
                                <div class="chart-card-content w-100">
                                    <div class="row">
                                        <div class="col-2 d-flex justify-content-center align-items-center">
                                            @if ($can_select)
                                            <input type="checkbox"
                                                class="form-check-input pe-none check-box-item-select"
                                                :checked="item.checked"
                                                style="opacity: unset;position: relative;width: 24px;height: 24px;left: 20px;">
                                            @endif
                                        </div>
                                        <div class="col-10">
                                            <div>
                                                <div class="card-block ">
                                                    <div class="row">
                                                        <div class="col-4">
                                                            <img v-if="item.image" v-bind:src="item.image.url"
                                                                alt="img"
                                                                class="card-image text-center item-select-img"
                                                                :class="{ 'img-disable': item.can_select == false }">
                                                            <img v-else
                                                                src="{{ asset('images/car-sample/car-placeholder.png') }}"
                                                                alt="placeholder"
                                                                class="card-image text-center item-select-img"
                                                                :class="{ 'img-disable': item.can_select == false }">
                                                        </div>
                                                        <div class="col-8" style="margin: auto">
                                                            <div class="me-2">
                                                                <span
                                                                    class="item-select-name">@{{ item.name }}</span>
                                                                <div class="card-link mt-2 item-select-sub-name">
                                                                    @{{ item.sub_name }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-9 card-content-block px-0">
                            <div class="gantt" :style="{ 'grid-template-columns': grid_column }">
                                <template v-for=" i in days_in_month">
                                    <div class="head"
                                        :class="{
                                            'highlight-date': checkhighlightDate(year + month +
                                                i),
                                            'highlight-today': highlightToday(year + month + i)
                                        }">
                                    </div>
                                </template>
                            </div>
                            <div class="gantt-timeline-section" :style="{ 'grid-template-columns': grid_column }"
                                :id="'timeline-container-' + item.id"></div>
                            @{{ getTimeLines(item.id, month, year) }}
                        </div>
                    </div>
                </div>

            </template>
        </div>
        <div v-else class="text-center">
            <p class="text-muted">" {{ __('lang.no_data') }} "</p>
        </div>
    </div>
</div>

@include('admin.components.gantts.gantt-style')
@include('admin.components.gantts.gantt-script')
