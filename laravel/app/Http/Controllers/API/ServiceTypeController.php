<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceType;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\TransportationTypeEnum;

class ServiceTypeController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $list = ServiceType::select('service_types.id', 'service_types.name', 'service_types.service_type')
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('service_types.name', 'like', '%' . $s . '%');
                    // $q->orWhere('service_types.transportation_type', 'like', '%' . $s . '%');
                    // $q->orWhere('service_types.can_rental_over_days', 'like', '%' . $s . '%');
                    // $q->orWhere('service_types.can_add_stopover', 'like', '%' . $s . '%');
                });
            })
            ->paginate(PER_PAGE);
        foreach ($list as $key => $item) {
            $medias = $item->getMedia('service_images');
            $files = get_medias_detail($medias);
            $item->image_url = null;
            if (isset($files[0]['url'])) {
                $item->image_url = $files[0]['url'];
            }
        }
        return response()->json($list, 200);
    }


    public function read(Request $request)
    {
        $data = ServiceType::select('service_types.id', 'service_types.name', 'service_types.transportation_type')
            ->where('service_types.id', $request->id)
            ->first();

        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        $medias = $data->getMedia('service_images');
        $files = get_medias_detail($medias);
        $data->image_url = null;
        if (isset($files[0]['url'])) {
            $data->image_url = $files[0]['url'];
        }

        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }
}
