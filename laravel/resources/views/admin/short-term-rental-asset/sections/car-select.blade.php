<h4>{{ __('short_term_rentals.car_select') }}</h4>
<hr>
<div class="form-group mb-2">
    <div class="d-flex wrap flex-column flex-sm-row justify-content-sm-between align-items-sm-center">

        <div class="form-group row push mb-4">
            <div class="col-sm-12 d-flex justify-content-start">
                <div>
                    <label for="search" class="text-start col-form-label">ค้นหาทะเบียนรถ</label>
                    {{-- <input type="text" id="search" name="search" class="form-control"  v-model="search"
                        placeholder="{{ __('lang.search_placeholder') }}"> --}}
                    <input type="text" class="form-control" v-model="search" @keyup="filterCarList"
                           placeholder="{{ __('lang.search_placeholder') }}">
                </div>

            </div>
        </div>
        <p class="flex-grow-1 my-2 my-sm-3 d-flex justify-content-end">รถที่เลือก @{{ count_select_car }}/1</p>
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
        <div class="col-3 px-0 head-title day-header-border-top day-header-border-bottom day-header-border-left">
            <div class="">ข้อมูลรถ</div>
        </div>
        <div class="col-9 px-0">
            <div class="gantt" :style="{ 'grid-template-columns': grid_column }">
                <div class="head" v-for=" i in daysInMonth">
                    @{{ daysName[i-1] }}
                    @{{ i }}
                </div>
            </div>
        </div>
    </div>
    <div v-if="car_list.length > 0">
        <template v-for="(car, car_index) in car_list_temp.slice(pageStart, pageStart + countOfPage) ">
            {{-- car_list_temp.slice(pageStart, pageStart + countOfPage) --}}
            {{-- <template v-if="car.can_rent == true"> --}}
            <div class="row form-check form-block gantt-card mb-3 ms-0 me-0" :dusk="'item-' + (car_index + pageStart)"
                 v-on:click="car.can_rent == true ? select(car_index + pageStart) : null"
                 :class="{ 'high-light': car.checked, 'bg-disable': car.can_rent == false }">

                <template v-if="select_multiple">
                    <input type="checkbox" class="form-check-input" :id="'select-car-' + (car_index + pageStart)"
                           name="select-car">
                    <input v-if='car.checked' type="hidden" id="car_id" name="cars[]" v-model="car.id">
                </template>
                <template v-else>
                    <input type="radio" class="form-check-input" :id="'select-car-' + (car_index + pageStart)"
                           name="select-car">
                    <input v-if='car.checked' type="hidden" id="car_id" name="cars[]" v-model="car.id">
                </template>
                <div class="col-3 px-0">
                    <div class="chart-card d-flex">
                        <div class="chart-card-content w-100">
                            <div class="row">
                                <div class="col-2 d-flex justify-content-center align-items-center">
                                    <input type="checkbox" class="form-check-input pe-none check-box-car-select" :checked="car.checked" style="opacity: unset;position: relative;width: 24px;height: 24px;left: 20px;">
                                </div>
                                <div class="col-10">
                                    <div>
                                        <h6 class="card-title mb-4">
                                            <template
                                                v-if="car.can_rent == false"><span
                                                    class="text-danger reserve">(จองแล้ว)</span></template>
                                        </h6>
                                        <div class="card-block text-center">
                                            <div class="row">
                                                <div class="col-6">
                                                    {{-- <img v-if="car.image.length > 0" v-bind:src="car.image[0].url"
                                                         alt=""
                                                         class="card-image text-center car-select-img"
                                                         :class="{ 'img-disable': car.can_rent == false }">
                                                    <img v-else
                                                         src="{{ asset('images/car-sample/car-placeholder.png') }}"
                                                         alt=""
                                                         class="card-image text-center car-select-img"
                                                         :class="{ 'img-disable': car.can_rent == false }"> --}}
                                                </div>
                                                <div class="col-6" style="margin: auto">
                                                    <div class="me-2">
                                                        <span class="car-select-name">@{{ car.class_name }}</span>
                                                        <div class="card-link text-center mt-2 car-select-plate">@{{
                                                            car.license_plate }}
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
                        <template v-for="(timeline, timeline_index) in car.timelines">
                            <div class="gantt-timeline gantt-border"
                                 :class="'timeline-color-' + (car.can_rent == false ? 'disable' : timeline.status)"
                                 :style="'grid-row: ' + (timeline_index % 3) + 1 + '; grid-column: ' + timeline
                                    .pickup_index + ' / span ' + timeline.count_index + ''">
                                 @{{ timeline.pickup_hours + ' - ' + timeline.return_hours }}
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
                <li class="page-item" v-if="currPage-1 > 0" @click.prevent="setPage(currPage-1)"><a class="page-link"
                                                                                                    href="">@{{
                        currPage-1 }}</a></li>
                <li class="page-item" v-bind:class="'active'" @click.prevent="setPage(n)"><a class="page-link" href="">@{{
                        currPage }}</a></li>
                <li class="page-item" v-if="currPage+1 < totalPage" @click.prevent="setPage(currPage+1)"><a
                        class="page-link" href="">@{{ currPage+1 }}</a></li>
                {{-- <li class="page-item" v-if="currPage+2 < totalPage"  @click.prevent="setPage(currPage+2)"><a class="page-link" href="">@{{ currPage+2 }}</a></li> --}}
                <li class="page-item" v-bind:class="{'disabled': (currPage === totalPage)}"
                    @click.prevent="setPage(currPage+1)"><a class="page-link" href="">{{ __('lang.next') }}</a>
                </li>
            </ul>
        </template>
    </div>
    <div v-else class="row form-check form-block mb-7 ms-0 me-0 text-center">
        <p class="text-muted">" {{ __('lang.no_data') }} "</p>
    </div>
</div>
