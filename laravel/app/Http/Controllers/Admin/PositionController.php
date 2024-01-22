<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Position;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Position);
        $list = Position::select('id', 'name', 'status')
            ->sortable('name')
            ->search($request->s)
            ->paginate(PER_PAGE);
        return view('admin.positions.index', [
            'list' => $list,
            's' => $request->s,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::Position);
        $d = new Position();
        $d->status = STATUS_ACTIVE;
        $listStatus = $this->getListStatus();
        $page_title = __('lang.create') . __('positions.page_title');
        return view('admin.positions.form', [
            'd' => $d,
            'page_title' => $page_title,
            'listStatus' => $listStatus,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Position);
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('positions', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'status' => [
                'required'
            ],
        ], [], [
            'name' => __('positions.name'),
            'status' => __('positions.status'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $positions = Position::firstOrNew(['id' => $request->id]);
        $positions->name = $request->name;
        $positions->status = $request->status;
        $positions->save();

        $redirect_route = route('admin.positions.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Position $position)
    {
        $this->authorize(Actions::View . '_' . Resources::Position);
        $page_title = __('lang.view') . __('positions.page_title');
        $listStatus = $this->getListStatus();
        $view = true;
        return view('admin.positions.form', [
            'd' => $position,
            'view' => $view,
            'page_title' => $page_title,
            'listStatus' => $listStatus,

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Position $position)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Position);
        $page_title = __('lang.edit') . __('positions.page_title');
        $listStatus = $this->getListStatus();
        return view('admin.positions.form', [
            'd' => $position,
            'page_title' => $page_title,
            'listStatus' => $listStatus,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Position);
        $positions = Position::find($id);
        $positions->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
