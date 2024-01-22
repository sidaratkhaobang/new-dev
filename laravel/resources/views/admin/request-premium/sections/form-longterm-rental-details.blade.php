<div class="block {{ __('block.styles') }}">
    <div class="block-content box-padding-bottom">
        @include('admin.components.block-header',[
     'text' => __('request_premium.longterm_rental_detail') ,
    'block_icon_class' => 'icon-document'
])
        <div class="row">
            <div class="col-sm-3">
                <x-forms.input-new-line id="job_type" :value="$d?->getLongTermRental?->rentalType?->name"
                                        :label="__('request_premium.job_type')"
                                        :optionals="['required' => false]"/>
                <x-forms.hidden id="job_type" :value="null"/>
            </div>
            <div class="col-sm-3">
                <label class="text-start col-form-label" for="month">
                    {{__('request_premium.rental_duration')}}
                </label>
                <input type="text" name="month" id="month" class="form-control col-sm-4" value="{{$d?->month}}"
                       data-role="tagsinput"/>
            </div>
            @if(!empty($d?->tor_file))
                <div class="col-sm-3">
                    <label class="text-start col-form-label">
                        {{__('request_premium.tor_document')}}
                    </label>
                    <a target="_blank" href="{{$d?->tor_file}}">

                        <button class="btn-tor">
                            <i class="icon-document-download"></i>
                            {{$d?->tor_file_name}}
                        </button>
                    </a>
                </div>
            @endif


        </div>
    </div>
</div>


