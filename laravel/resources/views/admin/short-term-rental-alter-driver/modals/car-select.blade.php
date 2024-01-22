<div class="modal fade" id="modal-car-select" tabindex="-1" aria-labelledby="modal-car-select" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout modal-dialog-scrollable"
        style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="car-select-label">เพิ่มข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>{{ __('short_term_rentals.car_select') }}</h4>
                <hr>
                <div class="form-group mb-2">
                    <div class="d-flex wrap flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                        <p class="flex-grow-1 my-2 my-sm-3">รถที่เลือก @{{ count_select_car }}/1</p>
                        <div class="form-group row push mb-4">
                            <div class="col-sm-12">
                                <label for="search" class="text-start col-form-label">ค้นหาทะเบียนรถ</label>
                                    <input type="text" class="form-control" v-model="search" @keyup="filterCarList"  placeholder="{{ __('lang.search_placeholder') }}">
                            </div>
                        </div>
                        <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="pe-4 text-color-1"><i class="fa fa-square me-1"></i>
                                    {{ __('short_term_rentals.status_' . RentalStatusEnum::DRAFT) }}</li>
                                <li class="pe-4 text-warning"><i class="fa fa-square me-1"></i>
                                    {{ __('short_term_rentals.status_' . RentalStatusEnum::PENDING) }}</li>
                                <li class="pe-4 text-success"><i class="fa fa-square me-1"></i>
                                    {{ __('short_term_rentals.status_' . RentalStatusEnum::PAID) }}</li>
                                <li class="pe-4 text-primary"><i class="fa fa-square me-1"></i>
                                    {{ __('short_term_rentals.status_' . RentalStatusEnum::SUCCESS) }}</li>
                            </ol>
                        </nav>
                    </div>

                    <div class="d-flex wrap flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                        <div class="text-muted" v-text="currentMonthName"></div>
                        <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3">
                            <div class="btn-group" role="group" aria-label="Horizontal Alternate Dark">
                                <button @click="prevMonth" class="btn btn-alt-secondary text-btn-group">
                                    <i class="fa fa-angle-left"></i> ก่อนหน้า
                                </button>
                                <button @click="nextMonth" class="btn btn-alt-secondary text-btn-group">
                                    ถัดไป <i class="fa fa-angle-right"></i>
                                </button>
                            </div>
                        </nav>
                    </div>
                </div>
                <div>
                    <div class="row mb-3 ms-0 me-0">
                        <div class="col-2 px-0 head-title">
                            <div class="">ข้อมูลรถ</div>
                        </div>
                        <div class="col-10 px-0">
                            <div class="gantt" :style="{ 'grid-template-columns': grid_column }">
                                <div class="head" v-for=" i in daysInMonth">
                                    @{{ i }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="car_list.length > 0">
                        <template v-for="(car, car_index) in car_list_temp.slice(pageStart, pageStart + countOfPage)">
                            <div class="row form-check form-block gantt-card mb-3 ms-0 me-0"
                                v-on:click="car.can_rent == true ? select(car_index + pageStart) : null"
                                :class="{ 'high-light': car.checked, 'bg-disable': car.can_rent == false }">
                                <template v-if="select_multiple">
                                    <input type="checkbox" class="form-check-input" :id="'select-car-' + (car_index + pageStart)"
                                        name="select-car">
                                    <input v-if='car.checked' type="hidden" id="car_id" name="cars[]"
                                        v-model="car.id">
                                </template>
                                <template v-else>
                                    <input type="radio" class="form-check-input" :id="'select-car-' + (car_index + pageStart)"
                                        name="select-car">
                                    <input v-if='car.checked' type="hidden" id="car_id" name="cars[]"
                                        v-model="car.id">
                                </template>
                                <div class="col-2 px-0">
                                    <div class="chart-card">
                                        <div class="chart-card-content">
                                            <h6 class="card-title mb-4">@{{ car.class_name }}</h6>
                                            <div class="card-block text-center">
                                                <img v-if="car.image.length > 0" v-bind:src="car.image[0].url"
                                                    alt="" class="card-image text-center"
                                                    :class="{ 'img-disable': car.can_rent == false }">
                                                <img v-else src="{{ asset('images/car-sample/car-placeholder.png') }}"
                                                    alt="" class="card-image text-center"
                                                    :class="{ 'img-disable': car.can_rent == false }">
                                                <div class="card-link text-center mt-2">@{{ car.license_plate }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-10 card-content-block px-0">
                                    <div class="gantt card-content" :style="{ 'grid-template-columns': grid_column }">
                                        <template v-for="(timeline, timeline_index) in car.timelines">
                                            <div class="gantt-timeline"
                                                :class="'timeline-color-' + (car.can_rent == false ? 'disable' : timeline
                                                    .status)"
                                                :style="'grid-row: ' + (timeline_index % 3) + 1 + '; grid-column: ' + timeline
                                                    .pickup_index + ' / span ' + timeline.count_index + ''">
                                                {{-- @{{ timeline.pickup_index + ' ' + timeline.return_index }} --}}
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template>
                            <ul class="pagination">
                                <li class="page-item" v-bind:class="{'disabled': (currPage === 1)}"
                                @click.prevent="setPage(currPage-1)"><a class="page-link"
                                    href="">{{ __('lang.previous') }}</a></li>
                            {{-- <li class="page-item" v-if="currPage-2 > 0"  @click.prevent="setPage(currPage-2)"><a class="page-link" href="">@{{ currPage-2 }}</a></li> --}}
                            <li class="page-item" v-if="currPage-1 > 0"  @click.prevent="setPage(currPage-1)"><a class="page-link" href="">@{{ currPage-1 }}</a></li>
                            <li class="page-item" v-bind:class="'active'" @click.prevent="setPage(n)"><a class="page-link"href="">@{{ currPage }}</a></li>
                            <li class="page-item" v-if="currPage+1 < totalPage"  @click.prevent="setPage(currPage+1)"><a class="page-link" href="">@{{ currPage+1 }}</a></li>
                            {{-- <li class="page-item" v-if="currPage+2 < totalPage"  @click.prevent="setPage(currPage+2)"><a class="page-link" href="">@{{ currPage+2 }}</a></li> --}}
                            <li class="page-item" v-bind:class="{'disabled': (currPage === totalPage)}"
                                @click.prevent="setPage(currPage+1)"><a class="page-link"href="">{{ __('lang.next') }}</a>
                            </li>
                            </ul>
                        </template>
                    </div>
                    <div v-else class="row form-check form-block mb-7 ms-0 me-0 text-center">
                        <p class="text-muted">" {{ __('lang.no_data') }} "</p>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary"
                    v-on:click="selectNewCar()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
