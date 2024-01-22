<h4>{{ __('short_term_rentals.car_info') }}</h4>
<hr>
<div class="form-group row push mb-5 mt-3" id="car-select">
    <template v-for="(item, index) in cars">
        <div class="col-sm-4">
            <div class="block block-rounded block-link-shadow block-car" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex justify-content-around block-car-content">
                    <div class="item item-block">
                        <p class="fs-base mb-5 text-dark">
                            @{{ truncateString(item.class_full_name, 30) }}
                        </p>
                        <img v-if="item.image.length > 0 && item.image[0]['url']" class="img-block img-fluid"
                            :src="item.image[0]['url']" alt="">
                        <img v-else class="img-block img-fluid"
                            src="{{ asset('images/car-sample/car-placeholder.png') }}" alt="">
                    </div>
                    <div class="ps-3 text-start text-block">
                        <p class="fs-lg fw-semibold mb-0 text-primary">
                            @{{ item.license_plate }}
                        </p>
                        <p class="fs-sm text-muted mb-0">
                            <i class="far fa-calendar-check pe-1"></i>
                            {{ $rental->pickup_date ? get_date_time_by_format($rental->pickup_date, 'd-m-Y') : null }}
                            ถึง
                            {{ $rental->return_date ? get_date_time_by_format($rental->return_date, 'd-m-Y') : null }}
                        </p>
                        <p class="fs-sm text-muted mb-3">
                            <i class="far fa-clock pe-1"></i>
                            <span class="pe-3">รับ
                                {{ $rental->pickup_date ? get_date_time_by_format($rental->pickup_date, 'H:i') : null }}
                                น.</span>
                            <i class="far fa-clock pe-1"></i>
                            <span>คืน
                                {{ $rental->return_date ? get_date_time_by_format($rental->return_date, 'H:i') : null }}
                                น.</span>
                        </p>
                        @if (!isset($view))
                            <template v-if='rental_status === status_change'>
                                <button :disabled="item.is_new_car || (item.is_replace == 1)" type="button"
                                    class="btn btn-sm rounded-pill btn-primary btn-ssm"
                                    v-on:click="openCarChart(item.id)">
                                    <template v-if="item.is_new_car">รถใหม่</template>
                                    <template v-else-if="item.is_replace">รถคันเดิม</template>
                                    <template v-else><i class="far fa-edit m-sm-1"></i></template>
                                </button>
                                <button v-if="item.is_new_car" type="button"
                                    class="btn btn-sm rounded-pill btn-danger btn-ssm"
                                    v-on:click="removeNewCar(item.id)">
                                    <i class="fa fa-fw fa-times"></i>
                                </button>
                            </template>
                        @endif
                        <p class="fs-sm text-muted mb-0">
                            <template v-if='item.status_arr.length > 0'>
                                <template v-for="(item_status, index2) in item.status_arr">
                                    <div v-if="item_status.remark_reason">
                                        <span class="badge larger-badge badge-pill text-white"
                                            :class="item_status.status_inspection_class">
                                            @{{ item_status.transfer_type_text }} : @{{ item_status.status_inspection_text }}(@{{ item_status.remark_reason_text }})
                                        </span>
                                    </div>
                                    <div v-else>
                                        <span class="badge larger-badge badge-pill text-white"
                                            :class="item_status.status_inspection_class">
                                            @{{ item_status.transfer_type_text }} : @{{ item_status.status_inspection_text }}
                                        </span>
                                    </div>
                                </template>
                            </template>
                        </p>
                    </div>
                    <template v-if='item.is_new_car'>
                        <input type="hidden" v-bind:name="'new_cars['+ index +'][id]'" id="new_car_id"
                            v-bind:value="item.id">
                        <input type="hidden" v-bind:name="'new_cars['+ index +'][former_car_id]'"
                            id="new_car_former_car_id" v-bind:value="item.former_car">
                    </template>
                </div>
            </div>
        </div>
    </template>
    @include('admin.short-term-rental-alter-driver.modals.car-select')
</div>
