<?php

namespace App\Http\Controllers\API;

use App\Enums\PettyCashTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\ExpensePettyCash;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ExpenseCashController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $reference_id = $request->reference_id;
        $reference_type = $request->reference_type;
        $list = ExpensePettyCash::select(
            'id',
            'petty_cash_type',
            'reference_type',
            'reference_id',
            'expense_type_id',
            'name',
            'subtotal',
            'vat',
            'total',
            'remark',
        )
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('name', 'like', '%' . $s . '%');
                });
            })
            ->when(!empty($reference_id), function ($query) use ($reference_id) {
                return $query->where(function ($q) use ($reference_id) {
                    $q->where('reference_id', $reference_id);
                });
            })
            ->when(!empty($reference_type), function ($query) use ($reference_type) {
                return $query->where(function ($q) use ($reference_type) {
                    $q->where('reference_type', $reference_type);
                });
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $data = ExpensePettyCash::select(
            'id',
            'petty_cash_type',
            'reference_type',
            'reference_id',
            'expense_type_id',
            'name',
            'subtotal',
            'vat',
            'total',
            'remark',
        )
            ->where('id', $request->id)
            ->first();

        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        $medias = $data->getMedia('expense_petty_cash_files');
        $files = get_medias_detail($medias);

        $data->files = $files;
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'petty_cash_type' => ['nullable', Rule::in([PettyCashTypeEnum::DRIVING_JOB])],
            'reference_type' => ['required'],
            'reference_id' => ['required', 'exists:driving_jobs,id'],
            'expense_type_id' => ['required', 'exists:expense_types,id'],
            'name' => ['nullable', 'string', 'max:100'],
            'subtotal' => ['required', 'numeric', 'gte:0', 'max:999999999.99'],
            'remark' => ['nullable'],
        ], [], [
            'petty_cash_type' => __('expense_petty_cashes.petty_cash_type'),
            'reference_type' => __('expense_petty_cashes.reference_type'),
            'reference_id' => __('expense_petty_cashes.reference_id'),
            'expense_type_id' => __('expense_petty_cashes.expense_type'),
            'name' => __('expense_petty_cashes.name'),
            'subtotal' => __('expense_petty_cashes.subtotal'),
            'remark' => __('expense_petty_cashes.remark'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $expense_type = ExpenseType::find($request->expense_type_id);
        $expense = new ExpensePettyCash();
        $expense->petty_cash_type = $request->petty_cash_type ? $request->petty_cash_type : $expense_type->petty_cash_type;
        $expense->reference_type = $request->reference_type;
        $expense->reference_id = $request->reference_id;
        $expense->expense_type_id = $request->expense_type_id;
        $expense->name = $request->name ? $request->name : $expense_type->name;
        $expense->subtotal = $request->subtotal;
        $expense->vat = 0;
        $expense->total = $request->subtotal;
        $expense->remark = $request->remark;
        $expense->save();

        if (!empty($request->expense_petty_cash_file)) {
            if ($request->expense_petty_cash_file->isValid()) {
                $expense->clearMediaCollection('expense_petty_cash_files');
                $expense->addMedia($request->expense_petty_cash_file)->toMediaCollection('expense_petty_cash_files');
            }
        }

        return $this->responseWithCode(true, DATA_SUCCESS, $expense->id, 201);
    }

    public function getExpenseTypeList(Request $request)
    {
        $s = $request->s;
        $list = ExpenseType::select(
            'id',
            'petty_cash_type',
            'name',
        )
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('name', 'like', '%' . $s . '%');
                });
            })
            ->get();
        return response()->json($list, 200);
    }

    public function destroy(Request $request)
    {
        $expense_cash = ExpensePettyCash::where('id', $request->id)->first();
        if (empty($expense_cash)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        $expense_cash->delete();
        return $this->responseWithCode(true, DATA_SUCCESS, $expense_cash->id, 200);
    }
}
