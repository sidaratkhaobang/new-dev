<?php

namespace App\View\Components\ShortTermRental;

use Illuminate\View\Component;
use App\Models\Rental;

class StepService extends Component
{
    public $success;
    public $service_type_name;
    public $service_type_url;
    public $show;
    public $istoggle;
    public $showstep;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($rentalid, $success, $show = false, $istoggle = false, $showstep = true)
    {
        $this->success = $success;
        $this->istoggle = $istoggle;
        $this->show = $this->istoggle ? $show : false;
        $this->showstep = $showstep;
        $this->service_type_name = '-';
        $this->service_type_url = asset('images/place_holder_150.png');
        if ($success) {
            $rental = Rental::find($rentalid);
            if ($rental) {
                $serviceType = $rental->serviceType;
                $this->service_type_name = $serviceType ? $serviceType->name : '-';
                $medias = $serviceType ? $serviceType->getMedia('service_images') : [];
                $files = get_medias_detail($medias);
                if (sizeof($files) > 0) {
                    $this->service_type_url = $files[0]['url'];
                }
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.short-term-rental.step-service');
    }
}
