<h4>{{ __('purchase_orders.purchase_requisition_car_detail') }}</h4>
<hr>
<div class="row push mb-3">

    <div class="col-sm-3">
        <div class="has-search input-icons">
{{--            <span class="fa fa-search has-search form-control-feedback icon"></span>--}}
            <input class="form-control input-field" type="text" id="searchbar" onkeyup="search_import_car()"
                placeholder="ระบุข้อมูลที่ต้องการค้นหา">
        </div>
    </div>
    @if (empty($view))
        <div class="col-sm-9 text-end">
            <a class="btn btn-outline-primary"
                href="{{ route('import-car-dealers.export-template', ['import_car_dealer' => $import_car->id]) }}"><i
                    class="fa fa-arrow-down"></i>&nbsp;
                {{ __('import_cars.download_template') }}</a>
            <div class="file btn btn-primary">
                <i class="fa fa-arrow-up-from-bracket"></i>&nbsp;
                {{ __('import_cars.upload') }}
                <input id="upload" type=file name="file[]" />
            </div>
        </div>
    @endif

</div>
<div id="import-cars" v-cloak>
    <div class="mb-5">
        <div class="table-responsive db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <th style="width: 3%"></th>
                    <th style="width: 37%">{{ __('purchase_orders.model') }}</th>
                    <th style="width: 30%">{{ __('purchase_orders.color') }}</th>
                    <th style="width: 30%">{{ __('purchase_orders.amount') }}</th>
                </thead>
                <tbody>
                    @php
                        $total_cars = 0;
                        $sum_total_price = 0;
                        $total_price = 0;

                    @endphp
                    @if (sizeof($purchase_requisition_cars) > 0)
                        @foreach ($purchase_requisition_cars as $index => $purchase_requisition_car)
                            @php
                                $total_cars += $purchase_requisition_car->amount;
                                $total_price = $purchase_requisition_car->subtotal * $purchase_requisition_car->amount;
                                $sum_total_price += $total_price;
                            @endphp
                            <tr class="car_class_name2">
                                <td><i class="fas fa-angle-right" aria-hidden="true" id="arrow-{{ $index }}"></i>
                                </td>
                                <td class="car_class_name">{{ $purchase_requisition_car->class_name }} -
                                    {{ $purchase_requisition_car->name }}</td>
                                <td>{{ $purchase_requisition_car->color_name }}</td>
                                <td>{{ $purchase_requisition_car->amount }} {{ __('purchase_orders.car_unit') }}</td>
                            </tr>

                            @php
                                $loop2 = [];
                                for ($i = 0; $i < $purchase_requisition_car->amount; $i++) {
                                    array_push($loop2, $i);
                                }

                                $loop2 = json_encode($loop2);
                            @endphp

                            <tr id="group-of-rows-2" class="car_class_name3">
                                <td></td>
                                <td colspan="3">
                                    <div class="table-wrap">

                                        <table class="table table-bordered hidden db-scroll"
                                            id="sub-table-{{ $index }}">
                                            <thead class="bg-body-dark">
                                                <th>#</th>
                                                <th>{{ __('import_cars.engine_no') }}</th>
                                                <th>{{ __('import_cars.chassis_no') }}</th>
                                                <th>{{ __('import_cars.delivery_ready_date') }}</th>
                                                <th>{{ __('import_cars.status') }}</th>
                                                <th></th>
                                                <th>{{ __('import_cars.delivery_date') }}</th>
                                                <th>{{ __('import_cars.delivery_place') }}</th>
                                                <th>{{ __('import_cars.car_entry') }}</th>
                                                <th>{{ __('import_cars.car_inspection') }}</th>
                                                <th>{{ __('import_cars.status') }}</th>
                                                <th style="width: 1%"></th>

                                            </thead>
                                            <tbody>

                                                <template>
                                                    <?php

                                                    $i = 0;
                                                    ?>

                                                    <tr v-for="(index) in {{ $loop2 }}" :key="index">
                                                        <td>@{{ index + 1 }}</td>

                                                        <template
                                                            v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == '{{ \App\Enums\ImportCarLineStatusEnum::CONFIRM_DATA }}' || dataset['{{ $purchase_requisition_car->id }}'][index].status_draft == '{{ \App\Enums\ImportCarLineStatusEnum::CONFIRM_DATA }}'  ">
                                                            <td><input type="text"
                                                                    class="form-control engine is-valid"
                                                                    :id="'engine_no-' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    :name="'engine_no' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].engine_no"
                                                                    :label="null" disabled /></td>
                                                            <td><input type="text"
                                                                    class="form-control chassis is-valid"
                                                                    v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].chassis_no"
                                                                    :label="null"
                                                                    :id="'chassis_no-' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    :name="'chassis_no' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    disabled />
                                                            </td>
                                                            <td>
                                                                <div class="input-group is-valid"><input
                                                                        class="form-control js-flatpickr flatpickr-input border-date"
                                                                        style="border-color:#6f9c40;"
                                                                        :id="'installation_completed_date-' +
                                                                        '{{ $purchase_requisition_car->id }}' + '-' +
                                                                        index"
                                                                        :name="'installation_completed_date' +
                                                                        '{{ $purchase_requisition_car->id }}' + '-' +
                                                                        index"
                                                                        v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].installation_completed_date"
                                                                        :label="null" data-date-format="d-m-Y"
                                                                        disabled /><span
                                                                        class="input-group-text border-date" style="border-color:#6f9c40;">
                                                                        <i class="far fa-calendar-check"></i>
                                                                    </span></div>
                                                            </td>
                                                        </template>
                                                        <template
                                                            v-else-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == '{{ \App\Enums\ImportCarLineStatusEnum::VENDOR_CONFIRM_DATA }}' || dataset['{{ $purchase_requisition_car->id }}'][index].status_draft == '{{ \App\Enums\ImportCarLineStatusEnum::VENDOR_CONFIRM_DATA }}'">
                                                            <td><input type="text"
                                                                    class="form-control engine is-valid"
                                                                    :id="'engine_no-' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    :name="'engine_no' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].engine_no"
                                                                    :label="null" @guest disabled @endguest />
                                                            </td>
                                                            <td><input type="text"
                                                                    class="form-control chassis is-valid"
                                                                    v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].chassis_no"
                                                                    :label="null"
                                                                    :id="'chassis_no-' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    :name="'chassis_no' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    @guest disabled @endguest />
                                                            </td>
                                                            <td>
                                                                <div class="input-group is-valid"><input
                                                                        class="form-control js-flatpickr flatpickr-input border-date"
                                                                        style="border-color:#6f9c40;"
                                                                        :id="'installation_completed_date-' +
                                                                        '{{ $purchase_requisition_car->id }}' + '-' +
                                                                        index"
                                                                        :name="'installation_completed_date' +
                                                                        '{{ $purchase_requisition_car->id }}' + '-' +
                                                                        index"
                                                                        v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].installation_completed_date"
                                                                        :label="null" data-date-format="d-m-Y"
                                                                        @guest disabled @endguest /><span
                                                                        class="input-group-text border-date" style="border-color:#6f9c40;">
                                                                        <i class="far fa-calendar-check"></i>
                                                                    </span></div>
                                                            </td>
                                                        </template>
                                                        <template
                                                            v-else-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == '{{ \App\Enums\ImportCarLineStatusEnum::REJECT_DATA }}'">
                                                            <td><input type="text"
                                                                    class="form-control engine is-invalid"
                                                                    :id="'engine_no-' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    :name="'engine_no' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].engine_no"
                                                                    :label="null" /></td>
                                                            <td><input type="text"
                                                                    class="form-control chassis is-invalid"
                                                                    v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].chassis_no"
                                                                    :label="null"
                                                                    :id="'chassis_no-' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    :name="'chassis_no' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index" />
                                                            </td>
                                                            <td>
                                                                <div class="input-group border-date-invalid"><input
                                                                        class="form-control installation_completed_date border-date-invalid js-flatpickr flatpickr-input"
                                                                        style="border-color:#e04f1a;"
                                                                        :id="'installation_completed_date-' +
                                                                        '{{ $purchase_requisition_car->id }}' + '-' +
                                                                        index"
                                                                        :name="'installation_completed_date' +
                                                                        '{{ $purchase_requisition_car->id }}' + '-' +
                                                                        index"
                                                                        v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].installation_completed_date"
                                                                        :label="null"
                                                                        data-date-format="d-m-Y" /><span
                                                                        class="input-group-text border-date-invalid" style="border-color:#e04f1a;">
                                                                        <i class="far fa-calendar-check"></i>
                                                                    </span></div>
                                                            </td>
                                                        </template>
                                                        <template v-else>
                                                            <td><input type="text" class="form-control engine"
                                                                    :id="'engine_no-' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    :name="'engine_no' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].engine_no"
                                                                    :label="null" /></td>
                                                            <td><input type="text" class="form-control chassis"
                                                                    v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].chassis_no"
                                                                    :label="null"
                                                                    :id="'chassis_no-' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    :name="'chassis_no' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index" />
                                                            </td>
                                                            <td>
                                                                <div class="input-group"><input
                                                                        class="form-control installation_completed_date js-flatpickr flatpickr-input "
                                                                        :id="'installation_completed_date-' +
                                                                        '{{ $purchase_requisition_car->id }}' + '-' +
                                                                        index"
                                                                        :name="'installation_completed_date' +
                                                                        '{{ $purchase_requisition_car->id }}' + '-' +
                                                                        index"
                                                                        v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].installation_completed_date"
                                                                        :label="null"
                                                                        data-date-format="d-m-Y" /><span
                                                                        class="input-group-text">
                                                                        <i class="far fa-calendar-check"></i>
                                                                    </span></div>

                                                            </td>
                                                        </template>
                                                        <td
                                                            v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == '{{ \App\Enums\ImportCarLineStatusEnum::PENDING }}'">
                                                            {!! badge_render(
                                                                'warning',
                                                                __('import_car_lines.status_' . \App\Enums\ImportCarLineStatusEnum::PENDING),
                                                                'w-100',
                                                            ) !!}
                                                        </td>
                                                        <td
                                                            v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == '{{ \App\Enums\ImportCarLineStatusEnum::PENDING_REVIEW }}' && dataset['{{ $purchase_requisition_car->id }}'][index].status_draft == '{{ \App\Enums\ImportCarLineStatusEnum::PENDING_REVIEW }}'">
                                                            {!! badge_render(
                                                                'primary',
                                                                __('import_car_lines.status_' . \App\Enums\ImportCarLineStatusEnum::PENDING_REVIEW),
                                                                'w-100',
                                                            ) !!}
                                                        </td>
                                                        <td
                                                            v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == '{{ \App\Enums\ImportCarLineStatusEnum::PENDING_REVIEW }}' && dataset['{{ $purchase_requisition_car->id }}'][index].status_draft == '{{ \App\Enums\ImportCarLineStatusEnum::CONFIRM_DATA }}'">
                                                            {!! badge_render(
                                                                'success',
                                                                __('import_car_lines.status_' . \App\Enums\ImportCarLineStatusEnum::CONFIRM_DATA),
                                                                'w-100',
                                                            ) !!}
                                                        </td>
                                                        <td
                                                            v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == 'PENDING_REVIEW' && dataset['{{ $purchase_requisition_car->id }}'][index].status_draft == '{{ \App\Enums\ImportCarLineStatusEnum::VENDOR_CONFIRM_DATA }}'">
                                                            {!! badge_render(
                                                                'success',
                                                                __('import_car_lines.status_' . \App\Enums\ImportCarLineStatusEnum::VENDOR_CONFIRM_DATA),
                                                                'w-100',
                                                            ) !!}
                                                        </td>
                                                        <td
                                                            v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == '{{ \App\Enums\ImportCarLineStatusEnum::CONFIRM_DATA }}'">
                                                            {!! badge_render(
                                                                'success',
                                                                __('import_car_lines.status_' . \App\Enums\ImportCarLineStatusEnum::CONFIRM_DATA),
                                                                'w-100',
                                                            ) !!}
                                                        </td>
                                                        <td
                                                            v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == '{{ \App\Enums\ImportCarLineStatusEnum::VENDOR_CONFIRM_DATA }}'">
                                                            {!! badge_render(
                                                                'success',
                                                                __('import_car_lines.status_' . \App\Enums\ImportCarLineStatusEnum::VENDOR_CONFIRM_DATA),
                                                                'w-100',
                                                            ) !!}
                                                        </td>

                                                        <td
                                                            v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == '{{ \App\Enums\ImportCarLineStatusEnum::REJECT_DATA }}'">
                                                            {!! badge_render(
                                                                'danger',
                                                                __('import_car_lines.status_' . \App\Enums\ImportCarLineStatusEnum::REJECT_DATA),
                                                                'w-100',
                                                            ) !!}
                                                        </td>


                                                        <td class="text-center">
                                                            <div class="btn-group">
                                                                <div class="col-sm-12">
                                                                    <div class="dropdown dropleft">
                                                                        <button type="button"
                                                                            style="border-color:#e04f1a;"
                                                                            v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == '{{ \App\Enums\ImportCarLineStatusEnum::REJECT_DATA }}' || dataset['{{ $purchase_requisition_car->id }}'][index].status_draft == '{{ \App\Enums\ImportCarLineStatusEnum::REJECT_DATA }}'"
                                                                            class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                                            id="dropdown-dropleft-dark"
                                                                            data-bs-toggle="dropdown"
                                                                            aria-haspopup="true"
                                                                            aria-expanded="false">
                                                                            <i class="fa fa-ellipsis-vertical"></i>
                                                                        </button>
                                                                        <button type="button" v-else
                                                                            class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                                            id="dropdown-dropleft-dark"
                                                                            data-bs-toggle="dropdown"
                                                                            aria-haspopup="true"
                                                                            aria-expanded="false">
                                                                            <i class="fa fa-ellipsis-vertical"></i>
                                                                        </button>
                                                                        <div class="dropdown-menu"
                                                                            aria-labelledby="dropdown-dropleft-dark">
                                                                            <a class="dropdown-item"
                                                                                v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == '{{ \App\Enums\ImportCarLineStatusEnum::REJECT_DATA }}' || dataset['{{ $purchase_requisition_car->id }}'][index].status_draft == '{{ \App\Enums\ImportCarLineStatusEnum::REJECT_DATA }}'"
                                                                                v-on:click="display('{{ $purchase_requisition_car->id }}',index)"
                                                                                style="color:#e04f1a;"><i
                                                                                    class="fa fa-eye me-1"
                                                                                    style="color:#e04f1a;"></i>
                                                                                ดูข้อมูล</a>
                                                                            <a class="dropdown-item" v-else
                                                                                v-on:click="display('{{ $purchase_requisition_car->id }}',index)"><i
                                                                                    class="fa fa-eye me-1"></i>
                                                                                ดูข้อมูล</a>
                                                                            @if (empty($view))
                                                                                <a class="dropdown-item"
                                                                                    v-on:click="edit('{{ $purchase_requisition_car->id }}',index)"
                                                                                    v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == '{{ \App\Enums\ImportCarLineStatusEnum::PENDING }}'"><i
                                                                                        class="far fa-edit me-1"></i>
                                                                                    แก้ไข</a>

                                                                                <a class="dropdown-item"
                                                                                    v-on:click="confirmStatus('{{ $purchase_requisition_car->id }}',index)"
                                                                                    v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == '{{ \App\Enums\ImportCarLineStatusEnum::PENDING_REVIEW }}'"><i
                                                                                        class="far fa-check-circle me-1"></i>
                                                                                    ยืนยันข้อมูลถูกต้อง</a>
                                                                                @auth
                                                                                    <a class="dropdown-item"
                                                                                        v-on:click="confirmStatus('{{ $purchase_requisition_car->id }}',index)"
                                                                                        v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == '{{ \App\Enums\ImportCarLineStatusEnum::VENDOR_CONFIRM_DATA }}'"><i
                                                                                            class="far fa-check-circle me-1"></i>
                                                                                        ยืนยันข้อมูลถูกต้อง</a>
                                                                                    <a class="dropdown-item"
                                                                                        v-on:click="rejectDisplay('{{ $purchase_requisition_car->id }}',index)"
                                                                                        v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == '{{ \App\Enums\ImportCarLineStatusEnum::VENDOR_CONFIRM_DATA }}'"><i
                                                                                            class="far fa-times-circle me-1"></i>
                                                                                        แจ้งแก้ไขข้อมูล</a>
                                                                                @endauth
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>

                                                        <template
                                                            v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == '{{ \App\Enums\ImportCarLineStatusEnum::CONFIRM_DATA }}'
                                                            && (dataset['{{ $purchase_requisition_car->id }}'][index].status_delivery != '{{ \App\Enums\ImportCarLineStatusEnum::PENDING_DELIVERY }}' && dataset['{{ $purchase_requisition_car->id }}'][index].status_delivery != '{{ \App\Enums\ImportCarLineStatusEnum::SUCCESS_DELIVERY }}')">
                                                            <td>
                                                                <div class="input-group"><input
                                                                        class="form-control  js-flatpickr delivery_date flatpickr-input"
                                                                        :id="'delivery_date-' +
                                                                        '{{ $purchase_requisition_car->id }}' + '-' +
                                                                        index"
                                                                        :name="'delivery_date' +
                                                                        '{{ $purchase_requisition_car->id }}' + '-' +
                                                                        index"
                                                                        v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].delivery_date"
                                                                        :label="null"
                                                                        data-date-format="d-m-Y"
                                                                        @guest disabled @endguest /><span
                                                                        class="input-group-text">
                                                                        <i class="far fa-calendar-check"></i>
                                                                    </span></div>
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                    class="form-control delivery_place"
                                                                    :id="'delivery_place-' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    :name="'delivery_place' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].delivery_place"
                                                                    :label="null" @guest disabled @endguest />
                                                            </td>
                                                        </template>

                                                        <template
                                                            v-else-if="dataset['{{ $purchase_requisition_car->id }}'][index].status_delivery == '{{ \App\Enums\ImportCarLineStatusEnum::PENDING_DELIVERY }}'">
                                                            <td>
                                                                <div class="input-group is-valid"><input
                                                                        class="form-control is-valid"
                                                                        :id="'delivery_date-' +
                                                                        '{{ $purchase_requisition_car->id }}' + '-' +
                                                                        index"
                                                                        :name="'delivery_date' +
                                                                        '{{ $purchase_requisition_car->id }}' + '-' +
                                                                        index"
                                                                        v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].delivery_date"
                                                                        :label="null"
                                                                        data-date-format="d-m-Y" disabled /><span
                                                                        class="input-group-text border-date" style="border-color: #419E6A;">
                                                                        <i class="far fa-calendar-check"></i>
                                                                    </span></div>
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                    class="form-control delivery_place is-valid"
                                                                    :id="'delivery_place-' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    :name="'delivery_place' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].delivery_place"
                                                                    :label="null" disabled />
                                                            </td>
                                                        </template>
                                                        <template
                                                            v-else-if="dataset['{{ $purchase_requisition_car->id }}'][index].status_delivery == '{{ \App\Enums\ImportCarLineStatusEnum::SUCCESS_DELIVERY }}'">
                                                            <td>
                                                                <div class="input-group is-valid"><input
                                                                        class="form-control is-valid"
                                                                        :id="'delivery_date-' +
                                                                        '{{ $purchase_requisition_car->id }}' + '-' +
                                                                        index"
                                                                        :name="'delivery_date' +
                                                                        '{{ $purchase_requisition_car->id }}' + '-' +
                                                                        index"
                                                                        v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].delivery_date"
                                                                        :label="null"
                                                                        data-date-format="d-m-Y" disabled /><span
                                                                        class="input-group-text border-date" style="border-color: #419E6A;">
                                                                        <i class="far fa-calendar-check"></i>
                                                                    </span></div>
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                    class="form-control delivery_place is-valid"
                                                                    :id="'delivery_place-' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    :name="'delivery_place' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].delivery_place"
                                                                    :label="null" disabled />
                                                            </td>
                                                        </template>

                                                        <template v-else>
                                                            <td>
                                                                <div class="input-group"><input class="form-control"
                                                                        :id="'delivery_date-' +
                                                                        '{{ $purchase_requisition_car->id }}' + '-' +
                                                                        index"
                                                                        :name="'delivery_date' +
                                                                        '{{ $purchase_requisition_car->id }}' + '-' +
                                                                        index"
                                                                        v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].delivery_date"
                                                                        :label="null"
                                                                        data-date-format="d-m-Y" disabled /><span
                                                                        class="input-group-text">
                                                                        <i class="far fa-calendar-check"></i>
                                                                    </span></div>
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                    class="form-control delivery_place"
                                                                    :id="'delivery_place-' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    :name="'delivery_place' +
                                                                    '{{ $purchase_requisition_car->id }}' +
                                                                    '-' + index"
                                                                    v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].delivery_place"
                                                                    :label="null" disabled />
                                                            </td>
                                                        </template>
                                                        {{--   <td>
                                                            <input type="text" class="form-control delivery_place"
                                                            :id="'delivery_place-' +
                                                            '{{ $purchase_requisition_car->id }}' +
                                                            '-' + index"
                                                            :name="'delivery_place' +
                                                            '{{ $purchase_requisition_car->id }}' +
                                                            '-' + index"
                                                            v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].delivery_place"
                                                            :label="null" />
                                                        </td> --}}


                                                        {{-- <td>
                                                            <input type="text" class="form-control delivery_place"
                                                            :id="'delivery_place-' +
                                                            '{{ $purchase_requisition_car->id }}' +
                                                            '-' + index"
                                                            :name="'delivery_place' +
                                                            '{{ $purchase_requisition_car->id }}' +
                                                            '-' + index"
                                                            v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].delivery_place"
                                                            :label="null" disabled />
                                                        </td> --}}
                                                        <td>
                                                            <template
                                                                v-for="(item_car, index_car) in car_park_transfer">
                                                                <div v-if="dataset['{{ $purchase_requisition_car->id }}'][index].id === item_car.car_id">
                                                                    <a :href="redirectPage(item_car.id, 'car_park_transfer')"
                                                                        target="_blank">@{{ item_car.worksheet_no }} </a>
                                                                </div>
                                                            </template>
                                                            <br>
                                                        </td>
                                                        <td>
                                                            <template v-for="(item_job, index_job) in inspection_job">
                                                                <div v-if="dataset['{{ $purchase_requisition_car->id }}'][index].id === item_job.car_id">
                                                                    <a  :href="redirectPage(item_job.id, 'inspection_job')"
                                                                        target="_blank">@{{ item_job.worksheet_no }} </a>
                                                                </div>
                                                            </template>
                                                            <br>
                                                        </td>
                                                        <td
                                                            v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status_delivery == '{{ \App\Enums\ImportCarLineStatusEnum::PENDING }}'">
                                                            {!! badge_render(
                                                                'warning',
                                                                __('import_car_lines.status_' . \App\Enums\ImportCarLineStatusEnum::PENDING),
                                                                'w-100',
                                                            ) !!}
                                                        </td>
                                                        <td
                                                            v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status_delivery == '{{ \App\Enums\ImportCarLineStatusEnum::PENDING_DELIVERY }}'">
                                                            {!! badge_render(
                                                                'primary',
                                                                __('import_car_lines.status_' . \App\Enums\ImportCarLineStatusEnum::PENDING_DELIVERY),
                                                                'w-100',
                                                            ) !!}
                                                        </td>
                                                        <td
                                                        v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status_delivery == '{{ \App\Enums\ImportCarLineStatusEnum::SUCCESS_DELIVERY }}'">
                                                        {!! badge_render(
                                                            'success',
                                                            __('import_car_lines.status_' . \App\Enums\ImportCarLineStatusEnum::SUCCESS_DELIVERY),
                                                            'w-100',
                                                        ) !!}
                                                    </td>




                                                        <td class=" text-center">
                                                            <div class="btn-group">
                                                                <div class="col-sm-12">
                                                                    <div class="dropdown dropleft">
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                                            id="dropdown-dropleft-dark"
                                                                            data-bs-toggle="dropdown"
                                                                            aria-haspopup="true"
                                                                            aria-expanded="false">
                                                                            <i class="fa fa-ellipsis-vertical"></i>
                                                                        </button>
                                                                        <div class="dropdown-menu"
                                                                            aria-labelledby="dropdown-dropleft-dark">
                                                                            <a class="dropdown-item"
                                                                                v-on:click="display('{{ $purchase_requisition_car->id }}',index)"><i
                                                                                    class="fa fa-eye me-1"></i>
                                                                                ดูข้อมูล</a>
                                                                            @if (empty($view))
                                                                                <a class="dropdown-item"
                                                                                    v-on:click="editRemark('{{ $purchase_requisition_car->id }}',index)"
                                                                                    v-if="dataset['{{ $purchase_requisition_car->id }}'][index].status == '{{ \App\Enums\ImportCarLineStatusEnum::CONFIRM_DATA }}' && dataset['{{ $purchase_requisition_car->id }}'][index].status_delivery == '{{ \App\Enums\ImportCarLineStatusEnum::PENDING_DELIVERY }}'"><i
                                                                                        class="far fa-edit me-1"></i>
                                                                                    แก้ไข</a>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>

                                                        <?php $i++; ?>
                                                        <input type="hidden"
                                                            v-bind:name="'reject_reason_text'+'[{{ $purchase_requisition_car->id }}]['+ index+ ']'"
                                                            v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].reject_reason">
                                                        <input type="hidden"
                                                            v-bind:name="'engine_no'+'[{{ $purchase_requisition_car->id }}]['+ index+ ']'"
                                                            v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].engine_no">
                                                        <input type="hidden"
                                                            v-bind:name="'chassis_no'+'[{{ $purchase_requisition_car->id }}]['+ index+ ']'"
                                                            v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].chassis_no">
                                                        <input type="hidden"
                                                            v-bind:name="'delivery_date_request'+'[{{ $purchase_requisition_car->id }}]['+ index+ ']'"
                                                            v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].delivery_date">
                                                        <input type="hidden"
                                                            v-bind:name="'delivery_place'+'[{{ $purchase_requisition_car->id }}]['+ index+ ']'"
                                                            v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].delivery_place">
                                                        <input type="hidden"
                                                            v-bind:name="'installation_completed_date'+'[{{ $purchase_requisition_car->id }}]['+ index+ ']'"
                                                            v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].installation_completed_date">
                                                        <input type="hidden"
                                                            v-bind:name="'id'+'[{{ $purchase_requisition_car->id }}]['+ index+ ']'"
                                                            v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].id">
                                                        <input type="hidden"
                                                            v-bind:name="'status_car_line'+'[{{ $purchase_requisition_car->id }}]['+ index+ ']'"
                                                            v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].status">
                                                        <input type="hidden"
                                                            v-bind:name="'status_draft'+'[{{ $purchase_requisition_car->id }}]['+ index+ ']'"
                                                            v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].status_draft">
                                                        <input type="hidden"
                                                            v-bind:name="'remark_line'+'[{{ $purchase_requisition_car->id }}]['+ index+ ']'"
                                                            v-model:value="dataset['{{ $purchase_requisition_car->id }}'][index].remark_line">
                                                        <input type="hidden" name="car_id"
                                                            value="{{ $import_car->id }}">
                                                    </tr>

                                                </template>

                                                {{-- </div> --}}


                                                <?php
                                                // }
                                                ?>

                                            </tbody>
                                        </table>
                                    </div>

                            </tr>
                        @endforeach
                    @else
                        <tr class="table-empty">
                            <td class="text-center" colspan="4">“
                                {{ __('lang.no_list') . __('purchase_orders.purchase_requisition_car_detail') }} “
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

        </div>
        @include('admin.import-cars.modals.edit-purchase')
        @include('admin.import-cars.modals.reject')
    </div>
</div>


<script>
    var ExcelToJSON = function() {

        this.parseExcel = function(file) {
            var reader = new FileReader();

            reader.onload = function(e) {
                var data = e.target.result;
                var workbook = XLSX.read(data, {
                    type: 'binary',
                    blankrows: false
                });
                var json_object2 = ['<?= json_encode($arr_ob_2) ?>'];
                var json_object = [];
                workbook.SheetNames.forEach(function(sheetName) {
                    var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[
                        sheetName]);
                    json_object = JSON.stringify(XL_row_object);
                })

                var loggedIn = {!! json_encode(Auth::check()) !!};
                if (loggedIn) {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('admin.import-cars.edit', $import_car->id) }}",
                        data: {
                            json_object: JSON.parse(json_object),
                        },
                        success: function(data) {
                            addImportCarVue.test(data.success);
                        }
                    });
                } else {
                    $.ajax({
                        type: 'GET',
                        url: "{{ route('import-car-dealers.edit', $import_car->id) }}",
                        data: {
                            json_object: JSON.parse(json_object)
                        },
                        success: function(data) {
                            addImportCarVue.test(data.success);
                        }
                    });
                }
            };

            reader.onerror = function(ex) {
                console.log(ex);
            };

            reader.readAsBinaryString(file);
        };
    };

    function handleFileSelect(evt) {

        var files = evt.target.files;
        var xl2json = new ExcelToJSON();
        xl2json.parseExcel(files[0]);
    }

    document.getElementById('upload').addEventListener('change', handleFileSelect, false);


    function search_import_car() {
        let input = document.getElementById('searchbar').value
        input = input.toLowerCase();
        let class_name_car = document.getElementsByClassName('car_class_name');
        let class_name_detail = document.getElementsByClassName('car_class_name2');
        let car_class_detail = document.getElementsByClassName('car_class_name3');

        for (i = 0; i < class_name_car.length; i++) {
            if (!class_name_car[i].innerHTML.toLowerCase().includes(input)) {
                class_name_detail[i].style.display = "none";
                car_class_detail[i].style.display = "none";
            } else {
                class_name_detail[i].style.display = "";
                car_class_detail[i].style.display = "";
            }
        }
    }
</script>
