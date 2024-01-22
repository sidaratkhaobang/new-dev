@push('custom_styles')
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/admin/dropzone-image.css') }}">
@endpush

<div class="table-wrap db-scroll">
    <table class="table table-striped table-vcenter">
        <thead class="bg-body-dark">
            <tr>
                <th style="width: 10px;">#</th>
                <th style="width: 30%;">@sortablelink('engine_no', __('inspection_cars.designated_image'))</th>
                <th style="width: 70%;">@sortablelink('chassis_no', __('inspection_cars.images'))</th>
                {{-- <th style="width: 35%;">@sortablelink('delivery_date', __('inspection_cars.in_depot'))</th> --}}
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ __('inspection_cars.front_image') }}</td>
                <td> 
                    <div class="text-center" >
                        <x-forms.upload-image-dropzone :id="'front_car_images_out'" :label="null" />                  
                    </div>
                </td>
                    
                {{-- <td>
                    <div class="text-center" >
                        <x-forms.upload-image-dropzone :id="'front_car_images_in'" :label="null" />
                    </div>
                </td> --}}
                
            </tr>
            <tr>
                <td>2</td>
                <td>{{ __('inspection_cars.back_image') }}</td>
                <td> 
                    <div class="text-center" >
                        <x-forms.upload-image-dropzone :id="'back_car_images_out'" :label="null" />                  
                    </div>
                </td>
                    
                {{-- <td style="width:25%;">
                    <div class="text-center" >
                        <x-forms.upload-image-dropzone :id="'back_car_images_in'" :label="null" />
                    </div>
                </td> --}}
            </tr>
            <tr>
                <td>3</td>
                <td>{{ __('inspection_cars.right_image') }}</td>
                <td> 
                    <div class="text-center" >
                        <x-forms.upload-image-dropzone :id="'right_car_images_out'" :label="null" />                  
                    </div>
                </td>
                    
                {{-- <td style="width:25%;" >
                    <div class="text-center" >
                        <x-forms.upload-image-dropzone :id="'right_car_images_in'" :label="null" />
                    </div>
                </td> --}}
            </tr>
            <tr>
                <td>4</td>
                <td>{{ __('inspection_cars.left_image') }}</td>
                <td> 
                    <div class="text-center" >
                        <x-forms.upload-image-dropzone :id="'left_car_images_out'" :label="null" />                  
                    </div>
                </td>
                    
                {{-- <td style="width:25%;" >
                    <div class="text-center" >
                        <x-forms.upload-image-dropzone :id="'left_car_images_in'" :label="null" />
                    </div>
                </td> --}}
            </tr>
            <tr>
                <td>5</td>
                <td>{{ __('inspection_cars.top_image') }}</td>
                <td> 
                    <div class="text-center" >
                        <x-forms.upload-image-dropzone :id="'top_car_images_out'" :label="null" />                  
                    </div>
                </td>
                    
                {{-- <td style="width:25%;" >
                    <div class="text-center" >
                        <x-forms.upload-image-dropzone :id="'top_car_images_in'" :label="null" />
                    </div>
                </td> --}}
            </tr>
        </tbody>
    </table>
</div>

