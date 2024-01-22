@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option :value="$d->car_brand_id" id="car_brand_id" :list="null" :label="__('car_classes.car_brand')"
                            :optionals="['ajax' => true, 'default_option_label' => $car_brand_name, 'required' => true]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.select-option :value="$d->car_class_id" id="car_class_id" :list="null" :label="__('car_classes.class')"
                            :optionals="[
                                'ajax' => true,
                                'default_option_label' => $car_class_name,
                                'required' => true,
                            ]" />
                    </div>
                </div>
                @include('admin.check-distances.sections.distance-line')
                <x-forms.hidden id="id" :value="$d->id" />
                @if (isset($edit))
                    <x-forms.hidden id="car_brand_id" :value="$d->car_brand_id" />
                    <x-forms.hidden id="car_class_id" :value="$d->car_class_id" />
                @endif
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        @auth
                            <a class="btn btn-secondary"
                                href="{{ route('admin.check-distances.index') }}">{{ __('lang.back') }}</a>
                        @endauth
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
    'store_uri' => route('admin.check-distances.store'),
])
@include('admin.check-distances.scripts.distance-line-script')

@include('admin.components.select2-ajax', [
    'id' => 'car_brand_id',
    'url' => route('admin.util.select2.car-brand'),
])
@include('admin.components.select2-ajax', [
    'id' => 'car_class_id',
    'url' => route('admin.util.select2.car-class-by-car-brand'),
    'parent_id' => 'car_brand_id',
])

@push('scripts')
    <script>
        $('.toggle-table').click(function() {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
                'fa fa-angle-right text-muted');
        });

        $edit = '{{ isset($edit) }}';
        $view = '{{ isset($view) }}';
        if ($edit) {
            $('#car_brand_id').prop('disabled', true);
            $('#car_class_id').prop('disabled', true);
        }
        if ($view) {
            $('.form-control').prop('disabled', true);
        }

        async function getCarDetail(car_id) {
            // try {
            //     const response = await axios.get("{{ route('admin.install-equipments.car-detail') }}", {
            //         params: {
            //             car_id: car_id
            //         }
            //     });
            //     return response.data;
            // } catch (error) {
            //     return null;
            // }
        }

        function assignOptions(car, except_id) {
            if (except_id != 'car_brand_id') {
                option_text = car.car_brand_id ?? "{{ __('lang.no_data') }}";
                var tempCodeOption = new Option(option_text, car.id, true, true);
                $("#car_brand_id").append(tempCodeOption).trigger('change');
            }

            if (except_id != 'car_class_id') {
                option_text = car.car_class_id ?? "{{ __('lang.no_data') }}";
                var tempLicensePlateOption = new Option(option_text, car.id, true, true);
                $("#car_class_id").append(tempLicensePlateOption).trigger('change');
            }
        }

        function clearOptions() {
            $("#car_brand_id").val(null).trigger('change');
            $("#car_class_id").val(null).trigger('change');
        }

        var car_select2_arr = ['car_brand_id', 'car_class_id'];
       

        car_select2_arr.forEach(element => {
            $("#" + element).on('select2:select', async function(e) {
                var data = e.params.data;
                console.log(data);
                var car = await getCarDetail(data.id);
                if (car) {
                    assignOptions(car, element);
                }
            });

            $("#" + element).on('select2:unselect', function(e) {
                clearOptions();
            });
        });
    </script>
@endpush
