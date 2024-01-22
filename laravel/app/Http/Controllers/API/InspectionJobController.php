<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\InspectionJob;
use App\Enums\TransferTypeEnum;

class InspectionJobController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;

        $list = InspectionJob::select(
            'id',
            'worksheet_no',
            'transfer_type',
            'inspection_type',
            'inspection_status',
            'open_date',
            'inspection_must_date',
            'inspection_date',
            'car_id'
        )
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('worksheet_no', 'like', '%' . $s . '%');
                });
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $data = InspectionJob::select(
            'id',
            'worksheet_no',
            'transfer_type',
            'inspection_type',
            'inspection_status',
            'open_date',
            'inspection_must_date',
            'inspection_date',
            'remark',
            'car_id',
            'is_need_customer_sign_in',
            'is_need_customer_sign_out'
        )
            ->where('id', $request->id)
            ->first();

        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        $medias = $data->getMedia('signature');
        $files = get_medias_detail($medias);

        $data->image_customer_sign_in = null;
        $data->image_customer_sign_out = null;
        if ((strcmp($data->transfer_type, TransferTypeEnum::IN) == 0) && (strcmp($data->is_need_customer_sign_in, BOOL_TRUE) == 0)) {
            $data->is_need_customer_sign_out = BOOL_FALSE;
            if (isset($files[0]['url'])) {
                $data->image_customer_sign_in = $files[0]['url'];
            }
        } else if ((strcmp($data->transfer_type, TransferTypeEnum::OUT) == 0) && (strcmp($data->is_need_customer_sign_out, BOOL_TRUE) == 0)) {
            $data->is_need_customer_sign_in = BOOL_FALSE;
            if (isset($files[0]['url'])) {
                $data->image_customer_sign_out = $files[0]['url'];
            }
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }

    public function sign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required'],
            'signature' => ['required'],
        ], [], [
            'id' => __('inspection_cars.id'),
            'signature' => __('inspection_cars.signature_customer'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $inspection_job = InspectionJob::find($request->id);
        if (empty($inspection_job)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        // $inspection_job->fill($request->all());
        // $inspection_job->save();

        if (!empty($request->signature)) {
            if ($request->signature->isValid()) {
                $inspection_job->clearMediaCollection('signature');
                $inspection_job->addMedia($request->signature)->toMediaCollection('signature');
            }
        }

        return $this->responseWithCode(true, DATA_SUCCESS, $inspection_job->id, 200);
    }
}

