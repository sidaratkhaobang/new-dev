{{-- <div class="row push mb-4">
    <div class="col-sm-8">
        <x-forms.input-new-line id="name" :value="null" :label="__('import_cars.name')" :optionals="['placeholder' => 'ระบุข้อมูล']"/>
    </div>
    <div class="col-sm-4">
      <x-forms.input-new-line id="name2" :value="null" :label="__('import_cars.name')" :optionals="['placeholder' => 'ระบุข้อมูล']"/>
  </div>
</div> --}}
<div class="row push mb-4">
  <div class="col-sm-4">
    <label class="text-start col-form-label">{{__('import_cars.engine_no')}}</label>
      <input type="text" id="engine_no" class="form-control"/>
  </div>
  <div class="col-sm-4">
    <label class="text-start col-form-label">{{__('import_cars.chassis_no')}}</label>
    <input type="text" id="chassis_no" class="form-control"/>
</div>

<div class="col-sm-4">

<label class="text-start col-form-label">{{__('import_cars.installation_completed_date')}}</label>
<div class="input-group">
  <input type="date" class="form-control js-flatpickr form-control flatpickr-input" id="installation_completed_date"
      name="setup_date" placeholder="" data-date-format="d-m-Y">
  <span class="input-group-text">
      <i class="far fa-calendar-check"></i>
  </span>
</div>

</div>

</div>

<p id="verification_date"></p>
<p id="reason"></p>