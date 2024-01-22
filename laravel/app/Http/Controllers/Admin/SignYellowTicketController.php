<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\ChangeRegistrationTypeEnum;
use App\Enums\Resources;
use App\Enums\ResponsibleSignYellowTicketEnum;
use App\Enums\ServiceTypeEnum;
use App\Enums\SignYellowTicketStatusEnum;
use App\Enums\TaxRenewalStatusEnum;
use App\Enums\TrainingSignYellowTicketEnum;
use App\Http\Controllers\Controller;
use App\Models\AccidentRepairOrder;
use App\Models\Branch;
use App\Models\Car;
use App\Models\CarClass;
use App\Models\ContractLines;
use App\Models\Contracts;
use App\Models\Cradle;
use App\Models\Driver;
use App\Models\DrivingJob;
use App\Models\LongTermRental;
use App\Models\Province;
use App\Models\Rental;
use App\Models\RentalDriver;
use App\Models\RentalLine;
use App\Models\Repair;
use App\Models\SignYellowTicket;
use App\Models\SignYellowTicketLine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Glide\Manipulators\Contrast;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Validator;

class SignYellowTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::SignYellowTicket);
        $status_list = $this->getStatus();
        $responsible_list = $this->getResponsibleList();
        $responsible = $request->responsible;
        $status = $request->status;
        $created_at = $request->created_at;
        $car_class = $request->car_class;
        $car_class_model = CarClass::find($car_class);
        $car_class_text = $car_class_model && $car_class_model->full_name ? $car_class_model->full_name : null;
        $car_id = $request->car_id;
        $car_text = null;
        $car = null;
        if ($car_id) {
            $car = Car::find($car_id);
            if ($car) {
                if ($car->license_plate) {
                    $car_text = $car->license_plate;
                } else if ($car->engine_no) {
                    $car_text = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
                } else if ($car->chassis_no) {
                    $car_text = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
                }
            }
        }


        $worksheet_no = $request->worksheet_no;
        $list = SignYellowTicket::select('sign_yellow_tickets.*')
            ->sortable(['created_at' => 'desc'])
            // ->distinct('sign_yellow_ticket_lines.sign_yellow_ticket_id')
            ->search($request)
            ->paginate(PER_PAGE);

        $list->map(function ($item) {
            $list_lawsuits = SignYellowTicketLine::where('sign_yellow_ticket_id', $item->id)->get();
            $item->lawsuit_count = count($list_lawsuits);
            $lawsuit_array = [];
            $total_amount = 0;
            $total_pay_dlt = 0;
            foreach ($list_lawsuits as $list) {
                $lawsuit = [];
                $lawsuit['incident_date'] = ($list) ? $list->incident_date : '';
                $province = null;
                if ($list->location_id) {
                    $province = Province::find($list->location_id);
                }
                $lawsuit['case'] = ($list->case) ? $list->case : '';
                $lawsuit['location'] = ($province) ? $province->name_th : '';
                $lawsuit['is_mistake'] = ($list->is_mistake) ? '/' : 'X';
                $lawsuit['institution'] = ($list->institution) ?  __('sign_yellow_tickets.responsible_' . $list->institution) : '';
                $lawsuit['amount'] = ($list->amount) ? $list->amount : '';
                $total_amount += $list->amount;
                $total_pay_dlt += $this->totalPayDLT($list->amount, $list->is_payment_fine);
                $lawsuit['is_payment_fine'] = ($list->is_payment_fine) ? '/' : 'X';
                $lawsuit['payment_date'] = ($list->payment_date) ? $list->payment_date : '';
                $lawsuit_array[] = $lawsuit;
            }
            $item->total_pay_dlt = $total_pay_dlt;
            $item->total_amount = $total_amount;
            $item->lawsuits = $lawsuit_array;
            return $item;
        });
        return view('admin.sign-yellow-tickets.index', [
            'lists' => $list,
            's' => $request->s,
            'status_list' => $status_list,
            'car_text' => $car_text,
            'status' => $status,
            'worksheet_no' => $worksheet_no,
            'car' => $car,
            'created_at' => $created_at,
            'responsible_list' => $responsible_list,
            'responsible' => $responsible,
            'car_class' => $car_class_model,
            'car_class_text' => $car_class_text,
        ]);
    }

    private function totalPayDLT($amount, $is_payment_fine)
    {
        if ($is_payment_fine) {
            return $amount;
        }
        return 0;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::SignYellowTicket);
        $d = new SignYellowTicket();
        // $d->status = STATUS_ACTIVE;
        $responsible_list = $this->getResponsibleList();
        $training_list = $this->getTrainingList();
        // $car_list = Car::select('id', 'license_plate as name')->get();
        $page_title = __('lang.create') . __('sign_yellow_tickets.page_title');
        $url = 'admin.sign-yellow-tickets.index';
        return view('admin.sign-yellow-tickets.form', [
            'd' => $d,
            'page_title' => $page_title,
            // 'car_list' => $car_list,
            'responsible_list' => $responsible_list,
            'training_list' => $training_list,
            'url' => $url,
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
        
        if ($request->status) {
            $validator = Validator::make($request->all(), [
                'receive_find_date' => [
                    'required',
                ],
                'car_id_hidden' => [
                    'required',
                ],
                'lawsuit_data' => [
                    'required',
                ],

            ], [], [
                'receive_find_date' => __('sign_yellow_tickets.receive_find_date'),
                'car_id_hidden' => __('cars.license_plate'),
                'lawsuit_data' => __('sign_yellow_tickets.validate_case'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }
        $sign_yellow_ticket = SignYellowTicket::firstOrNew(['id' => $request->id]);
        $sign_yellow_ticket->receive_document_date = $request->receive_find_date;
        $sign_yellow_ticket->car_id = $request->car_id_hidden;
        if ($request->status) {
            $sign_yellow_ticket->status = $request->status;
        }

        $sign_yellow_ticket->save();

        if ($sign_yellow_ticket) {
            if ($request->lawsuit_data) {
                foreach ($request->lawsuit_data as $lawsuit_data) {
                    $sign_yellow_ticket_line = SignYellowTicketLine::firstOrNew(['id' => $lawsuit_data['id']]);
                    $sign_yellow_ticket_line->sign_yellow_ticket_id = $sign_yellow_ticket->id;
                    $sign_yellow_ticket_line->incident_date = $lawsuit_data['incident_date'];
                    $sign_yellow_ticket_line->case = $lawsuit_data['lawsuit_detail'];
                    $sign_yellow_ticket_line->location_id = $lawsuit_data['province_id'];
                    $sign_yellow_ticket_line->institution = $lawsuit_data['responsible_id'];
                    $sign_yellow_ticket_line->is_train = $lawsuit_data['training_id'];
                    // $sign_yellow_ticket_line->amount = $request->amount;
                    $amount = $lawsuit_data['amount'] ? str_replace(',', '', $lawsuit_data['amount']) : null;
                    $sign_yellow_ticket_line->amount = $amount ? $amount : null;
                    $sign_yellow_ticket_line->driver_name = $lawsuit_data['driver'];
                    $sign_yellow_ticket_line->tel = $lawsuit_data['tel'];
                    $sign_yellow_ticket_line->save();
                }
            }
        }

        $redirect_route = route('admin.sign-yellow-tickets.index');

        return $this->responseValidateSuccess($redirect_route);
    }

    public function storeMistake(Request $request)
    {
        if ($request->status) {
            $validator = Validator::make($request->all(), [
                'lawsuit_data.*.mistake_id' => [
                    'required',
                ],
                'lawsuit_data.*.notification_date' => [
                    'required',
                ],

            ], [], [
                'lawsuit_data.*.mistake_id' => __('sign_yellow_tickets.is_wrong'),
                'lawsuit_data.*.notification_date' => __('sign_yellow_tickets.announ_pay_find_date'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        $sign_yellow_ticket = SignYellowTicket::find($request->id);
        if ($request->status) {
            $sign_yellow_ticket->status = $request->status;
        }
        // else {
        //     $sign_yellow_ticket->status = SignYellowTicketStatusEnum::WAITING_WRONG;
        // }
        $sign_yellow_ticket->save();
        if ($request->lawsuit_data) {
            foreach ($request->lawsuit_data as $lawsuit_data) {
                $sign_yellow_ticket_line = SignYellowTicketLine::find($lawsuit_data['id']);
                if ($sign_yellow_ticket_line) {
                    $sign_yellow_ticket_line->is_mistake = $lawsuit_data['mistake_id'];
                    $sign_yellow_ticket_line->notification_date = $lawsuit_data['notification_date'];
                    $sign_yellow_ticket_line->save();
                }
            }
        }

        $redirect_route = route('admin.sign-yellow-tickets.index');

        return $this->responseValidateSuccess($redirect_route);
    }


    public function storePaid(Request $request)
    {
        if ($request->status) {
            $validator = Validator::make($request->all(), [
                'lawsuit_data.*.receipt_no' => [
                    'required',
                ],
                'lawsuit_data.*.payment_fine_date' => [
                    'required',
                ],
                'lawsuit_data.*.amount_total' => [
                    'required',
                ],

            ], [], [
                'lawsuit_data.*.receipt_no' => __('sign_yellow_tickets.receipt_no'),
                'lawsuit_data.*.payment_fine_date' => __('sign_yellow_tickets.payment_fine_date'),
                'lawsuit_data.*.amount_total' => __('sign_yellow_tickets.amount_total'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }
        $sign_yellow_ticket = SignYellowTicket::find($request->id);
        if ($request->status) {
            $sign_yellow_ticket->status = $request->status;
        }
        // else {
        //     $sign_yellow_ticket->status = SignYellowTicketStatusEnum::WAITING_PAY_FINE;
        // }
        $sign_yellow_ticket->save();

        if ($request->lawsuit_data) {
            foreach ($request->lawsuit_data as $index => $lawsuit_data) {
                $sign_yellow_ticket_line = SignYellowTicketLine::find($lawsuit_data['id']);
                if ($sign_yellow_ticket_line) {
                    $sign_yellow_ticket_line->payment_fine_date = $lawsuit_data['payment_fine_date'];
                    $sign_yellow_ticket_line->receipt_no = $lawsuit_data['receipt_no'];
                    $amount = $lawsuit_data['amount_total'] ? str_replace(',', '', $lawsuit_data['amount_total']) : null;
                    if ($amount) {
                        $sign_yellow_ticket_line->amount = $amount;
                    }
                    $sign_yellow_ticket_line->save();
                }
                // $this->deleteReceiptMedias($request);

                if ((!empty($request->receipt_files)) && (sizeof($request->receipt_files) > 0)) {
                    $all_receipt_files = $request->receipt_files;
                    if (isset($all_receipt_files[$index])) {
                        $receipt_files = $all_receipt_files[$index];
                        foreach ($receipt_files as $receipt_file) {

                            if ($receipt_file) {
                                $sign_yellow_ticket_line->addMedia($receipt_file)->toMediaCollection('receipt_file');
                            }
                        }
                    }
                }
            }
        }

        $redirect_route = route('admin.sign-yellow-tickets.index');

        return $this->responseValidateSuccess($redirect_route);
    }

    public function storePaidFine(Request $request)
    {
        if ($request->status) {
            $validator = Validator::make($request->all(), [
                'lawsuit_data.*.payment_date' => [
                    'required',
                ],
                'lawsuit_data.*.is_payment_fine_id' => [
                    'required',
                ],

            ], [], [
                'lawsuit_data.*.payment_date' => __('sign_yellow_tickets.paid_date'),
                'lawsuit_data.*.is_payment_fine' => __('sign_yellow_tickets.payment'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        $sign_yellow_ticket = SignYellowTicket::find($request->id);

        // else {
        //     $sign_yellow_ticket->status = SignYellowTicketStatusEnum::WAITING_WRONG;
        // }
        $sign_yellow_ticket->save();
        if ($request->lawsuit_data) {
            $count_paid = 0;
            foreach ($request->lawsuit_data as $lawsuit_data) {
                $sign_yellow_ticket_line = SignYellowTicketLine::find($lawsuit_data['id']);
                if ($sign_yellow_ticket_line) {
                    $sign_yellow_ticket_line->payment_date = $lawsuit_data['payment_date'];
                    $sign_yellow_ticket_line->is_payment_fine = $lawsuit_data['is_payment_fine_id'];
                    $count_paid = $lawsuit_data['is_payment_fine_id'] ? $count_paid + 1 : $count_paid;
                    $sign_yellow_ticket_line->save();
                }
            }
            if (count($request->lawsuit_data) == $count_paid) {
                if ($request->status) {
                    $sign_yellow_ticket->status = $request->status;
                }
                $sign_yellow_ticket->save();
            }
        }

        $redirect_route = route('admin.sign-yellow-tickets.index');

        return $this->responseValidateSuccess($redirect_route);
    }

    private function deleteReceiptMedias($request)
    {

        $pending_delete_receipt_files = $request->receipt_files__pending_delete_ids;
        if ((!empty($pending_delete_receipt_files)) && (sizeof($pending_delete_receipt_files) > 0)) {
            foreach ($pending_delete_receipt_files as $dealer_media_id) {
                $dealer_media = Media::find($dealer_media_id);
                if ($dealer_media && $dealer_media->model_id) {
                    $sign_yelllow_ticket_line = SignYellowTicketLine::find($dealer_media->model_id);
                    $sign_yelllow_ticket_line->deleteMedia($dealer_media->id);
                }
            }
        }
        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(SignYellowTicket $sign_yellow_ticket)
    {
        $this->authorize(Actions::View . '_' . Resources::SignYellowTicket);
        $law_suit_list = SignYellowTicketLine::where('sign_yellow_ticket_id', $sign_yellow_ticket->id)->get();
        foreach ($law_suit_list as $index => $law_suit) {
            $law_suit->id = $law_suit->id;
            $law_suit->incident_date = $law_suit->incident_date;
            $law_suit->lawsuit_detail = $law_suit->case;
            $province = Province::find($law_suit->location_id);
            if ($province) {
                $law_suit->province_id = $province->id;
                $law_suit->province_text = $province->name_th;
            }
            if ($law_suit->institution) {
                $law_suit->responsible_text = __('sign_yellow_tickets.responsible_'  . $law_suit->institution);
                $law_suit->responsible_id = $law_suit->institution;
            }

            if (in_array($law_suit->is_train, [STATUS_ACTIVE, STATUS_DEFAULT])) {
                $law_suit->training_text =  __('sign_yellow_tickets.training_'  . $law_suit->is_train);
                $law_suit->training_id = $law_suit->is_train;
            }

            if (in_array($law_suit->is_payment_fine, [STATUS_ACTIVE, STATUS_DEFAULT])) {
                $law_suit->is_payment_fine_text =  __('sign_yellow_tickets.payment_'  . $law_suit->is_payment_fine);
                $law_suit->is_payment_fine_id = $law_suit->is_payment_fine;
            }


            $law_suit->driver = $law_suit->driver_name;
            $law_suit->tel = $law_suit->tel;

            $incident_date = $law_suit->incident_date;
            $car_id = $sign_yellow_ticket->car_id;
            $driving_job = $this->getDrivingJobEdit($incident_date, $car_id);
            if ($driving_job) {
                $law_suit->driver = $driving_job['name'] ?? null;
                $law_suit->tel = $driving_job['tel'] ?? null;
                $law_suit->driver_type = $driving_job['type'] ?? null;
            }

            $medias = $law_suit->getMedia('receipt_file');
            $receipt_file = get_medias_detail($medias);
            $receipt_file = collect($receipt_file)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $law_suit->receipt_files = $receipt_file;
        }
        $car_detail = Car::find($sign_yellow_ticket->car_id);
        if ($car_detail) {
            $sign_yellow_ticket->car_license_plate = $car_detail->license_plate;
        }

        $training_list = $this->getTrainingList();
        $mistake_list = $this->getMistakeList();
        $page_title = __('lang.view') . __('sign_yellow_tickets.page_title');
        $url = 'admin.sign-yellow-tickets.index';
        $sign_yellow_ticket->step = $this->setProgressStep($sign_yellow_ticket->status);
        return view('admin.sign-yellow-tickets.form', [
            'd' => $sign_yellow_ticket,
            'page_title' => $page_title,
            'lawsuit_list' => $law_suit_list,
            // 'car_list' => $car_list,
            'training_list' => $training_list,
            'mistake_list' => $mistake_list,
            'url' => $url,
            'view' => true,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(SignYellowTicket $sign_yellow_ticket)
    {
        $this->authorize(Actions::Manage . '_' . Resources::SignYellowTicket);
        $law_suit_list = SignYellowTicketLine::where('sign_yellow_ticket_id', $sign_yellow_ticket->id)->get();
        foreach ($law_suit_list as $index => $law_suit) {
            $law_suit->id = $law_suit->id;
            $law_suit->incident_date = $law_suit->incident_date;
            $law_suit->lawsuit_detail = $law_suit->case;
            $province = Province::find($law_suit->location_id);
            if ($province) {
                $law_suit->province_id = $province->id;
                $law_suit->province_text = $province->name_th;
            }
            if ($law_suit->institution) {
                $law_suit->responsible_text = __('sign_yellow_tickets.responsible_'  . $law_suit->institution);
                $law_suit->responsible_id = $law_suit->institution;
            }

            if (in_array($law_suit->is_train, [STATUS_ACTIVE, STATUS_DEFAULT])) {
                $law_suit->training_text =  __('sign_yellow_tickets.training_'  . $law_suit->is_train);
                $law_suit->training_id = $law_suit->is_train;
            }

            if (in_array($law_suit->is_mistake, [STATUS_ACTIVE, STATUS_DEFAULT])) {
                $law_suit->mistake_text =  __('sign_yellow_tickets.mistake_'  . $law_suit->is_mistake);
                $law_suit->mistake_id = $law_suit->is_mistake;
            }

            if (in_array($law_suit->is_payment_fine, [STATUS_ACTIVE, STATUS_DEFAULT])) {
                $law_suit->is_payment_fine_text =  __('sign_yellow_tickets.payment_'  . $law_suit->is_payment_fine);
                $law_suit->is_payment_fine_id = $law_suit->is_payment_fine;
            }


            $law_suit->driver = $law_suit->driver_name;
            $law_suit->tel = $law_suit->tel;

            $law_suit->amount_total = $law_suit->amount;

            $incident_date = $law_suit->incident_date;
            $car_id = $sign_yellow_ticket->car_id;
            $driving_job = $this->getDrivingJobEdit($incident_date, $car_id);
            if ($driving_job) {
                // dd($driving_job);
                $law_suit->driver = $driving_job['name'] ?? null;
                $law_suit->tel = $driving_job['tel'] ?? null;
                $law_suit->driver_type = $driving_job['type'] ?? null;
            }

            $medias = $law_suit->getMedia('receipt_file');
            $receipt_file = get_medias_detail($medias);
            $receipt_file = collect($receipt_file)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $law_suit->receipt_files = $receipt_file;
        }
        $car_detail = Car::find($sign_yellow_ticket->car_id);
        if ($car_detail) {
            $sign_yellow_ticket->car_license_plate = $car_detail->license_plate;
        }
        // dd($car_detail);
        $training_list = $this->getTrainingList();
        $mistake_list = $this->getMistakeList();
        $page_title = __('lang.edit') . __('sign_yellow_tickets.page_title');
        $url = 'admin.sign-yellow-tickets.index';
        $sign_yellow_ticket->step = $this->setProgressStep($sign_yellow_ticket->status);
        return view('admin.sign-yellow-tickets.form', [
            'd' => $sign_yellow_ticket,
            'page_title' => $page_title,
            'lawsuit_list' => $law_suit_list,
            // 'car_list' => $car_list,
            'training_list' => $training_list,
            'mistake_list' => $mistake_list,
            'url' => $url,
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
        //
    }

    public static function getStatus()
    {
        $status = collect([
            (object) [
                'id' => SignYellowTicketStatusEnum::DRAFT,
                'name' => __('sign_yellow_tickets.status_' . SignYellowTicketStatusEnum::DRAFT . '_text'),
                'value' => SignYellowTicketStatusEnum::DRAFT,
            ],
            (object) [
                'id' => SignYellowTicketStatusEnum::WAITING_WRONG,
                'name' => __('sign_yellow_tickets.status_' . SignYellowTicketStatusEnum::WAITING_WRONG . '_text'),
                'value' => SignYellowTicketStatusEnum::WAITING_WRONG,
            ],
            (object) [
                'id' => SignYellowTicketStatusEnum::WAITING_PAY_DLT,
                'name' => __('sign_yellow_tickets.status_' . SignYellowTicketStatusEnum::WAITING_PAY_DLT . '_text'),
                'value' => SignYellowTicketStatusEnum::WAITING_PAY_DLT,
            ],
            (object) [
                'id' => SignYellowTicketStatusEnum::WAITING_PAY_FINE,
                'name' => __('sign_yellow_tickets.status_' . SignYellowTicketStatusEnum::WAITING_PAY_FINE . '_text'),
                'value' => SignYellowTicketStatusEnum::WAITING_PAY_FINE,
            ],
            (object) [
                'id' => SignYellowTicketStatusEnum::SUCCESS,
                'name' => __('sign_yellow_tickets.status_' . SignYellowTicketStatusEnum::SUCCESS . '_text'),
                'value' => SignYellowTicketStatusEnum::SUCCESS,
            ],
        ]);
        return $status;
    }


    public static function getResponsibleList()
    {
        $status = collect([
            (object) [
                'id' => ResponsibleSignYellowTicketEnum::DRIVER,
                'name' => __('sign_yellow_tickets.responsible_' . ResponsibleSignYellowTicketEnum::DRIVER),
                'value' => ResponsibleSignYellowTicketEnum::DRIVER,
            ],
            (object) [
                'id' => ResponsibleSignYellowTicketEnum::LADKRABANG,
                'name' => __('sign_yellow_tickets.responsible_' . ResponsibleSignYellowTicketEnum::LADKRABANG),
                'value' => ResponsibleSignYellowTicketEnum::LADKRABANG,
            ],
            (object) [
                'id' => ResponsibleSignYellowTicketEnum::PHUKET,
                'name' => __('sign_yellow_tickets.responsible_' . ResponsibleSignYellowTicketEnum::PHUKET),
                'value' => ResponsibleSignYellowTicketEnum::PHUKET,
            ],
            (object) [
                'id' => ResponsibleSignYellowTicketEnum::CHAINGRAI,
                'name' => __('sign_yellow_tickets.responsible_' . ResponsibleSignYellowTicketEnum::CHAINGRAI),
                'value' => ResponsibleSignYellowTicketEnum::CHAINGRAI,
            ],
            (object) [
                'id' => ResponsibleSignYellowTicketEnum::CHAINGMAI,
                'name' => __('sign_yellow_tickets.responsible_' . ResponsibleSignYellowTicketEnum::CHAINGMAI),
                'value' => ResponsibleSignYellowTicketEnum::CHAINGMAI,
            ],
            (object) [
                'id' => ResponsibleSignYellowTicketEnum::KRABI,
                'name' => __('sign_yellow_tickets.responsible_' . ResponsibleSignYellowTicketEnum::KRABI),
                'value' => ResponsibleSignYellowTicketEnum::KRABI,
            ],
            (object) [
                'id' => ResponsibleSignYellowTicketEnum::PATTAYA,
                'name' => __('sign_yellow_tickets.responsible_' . ResponsibleSignYellowTicketEnum::PATTAYA),
                'value' => ResponsibleSignYellowTicketEnum::PATTAYA,
            ],
            (object) [
                'id' => ResponsibleSignYellowTicketEnum::PRAPADAENG,
                'name' => __('sign_yellow_tickets.responsible_' . ResponsibleSignYellowTicketEnum::PRAPADAENG),
                'value' => ResponsibleSignYellowTicketEnum::PRAPADAENG,
            ],

        ]);
        return $status;
    }

    public static function getTrainingList()
    {
        $status = collect([
            (object) [
                'id' => STATUS_ACTIVE,
                'name' => __('sign_yellow_tickets.training_' . STATUS_ACTIVE),
                'value' => STATUS_ACTIVE,
            ],
            (object) [
                'id' => STATUS_DEFAULT,
                'name' => __('sign_yellow_tickets.training_' . STATUS_DEFAULT),
                'value' => STATUS_DEFAULT,
            ],

        ]);
        return $status;
    }

    public static function getMistakeList()
    {
        $status = collect([
            (object) [
                'id' => STATUS_ACTIVE,
                'name' => __('sign_yellow_tickets.mistake_' . STATUS_ACTIVE),
                'value' => STATUS_ACTIVE,
            ],
            (object) [
                'id' => STATUS_DEFAULT,
                'name' => __('sign_yellow_tickets.mistake_' . STATUS_DEFAULT),
                'value' => STATUS_DEFAULT,
            ],

        ]);
        return $status;
    }

    public function getDefaultDataCar(Request $request)
    {
        $car_id = $request->car_id;
        $data = [];
        $car = Car::find($car_id);
        $data['branch'] = null;
        if ($car) {
            $branch = Branch::find($car->branch_id);
            $data['branch'] = ($branch) ? $branch->name : null;
        }
        $data['engine_no'] = ($car) ? $car->engine_no : null;
        $data['chassis_no'] = ($car) ? $car->chassis_no : null;;
        $data['car_class'] = ($car && $car->carClass) ? $car->carClass->full_name : null;
        $data['car_color'] = ($car && $car->carColor) ? $car->carColor->name : null;
        $data['status'] = ($car && $car->status) ? __('cars.status_' . $car->status) : null;
        return [
            'success' => true,
            'data' => $data,
        ];
    }

    public function getDrivingJobEdit($incident_date, $car_id)
    {
        $driver_type = null;
        $driver_names = null;
        $driver_tels = null;
        $driving_job = DrivingJob::where('actual_start_date', '<=', $incident_date)->where('actual_end_date', '>=', $incident_date)->where('car_id', $car_id)->first();
        if ($driving_job) {
            $driver = Driver::find($driving_job->driver_id);
            if ($driver) {
                $driver_type = 'พนักงานขับรถ';
                // $data['info'] = $driving_job;
                $driver_names = $driver->name;
                $driver_tels = $driver->tel;
            }
        } else {
            $contract_line = ContractLines::where('pick_up_date', '<=', $incident_date)->where('return_date', '>=', $incident_date)->where('car_id', $car_id)->first();
            if ($contract_line) {
                $contract = Contracts::find($contract_line->contract_id);
                if ($contract->job_type == Rental::class) {
                    $rental = Rental::find($contract->job_id);
                    if ($rental) {
                        $rental_driver = RentalDriver::where('rental_id', $rental->id)->get();
                        if ($rental_driver) {
                            $driver_type = 'ลูกค้า';
                            $driver_names = $rental_driver->pluck('name')->implode(', ');
                            $driver_tels = $rental_driver->pluck('tel')->implode(', ');
                        } else {
                            $driver_type = 'ลูกค้า';
                            $driver_names = $contract_line->car_user;
                            $driver_tels = $contract_line->tell;
                        }
                    }
                } else {
                    $data['is_driver'] = false;
                    $data['info'] = $contract_line;
                }

                $repair = Repair::whereDate('in_center_date', '=', $incident_date)->where('car_id', $car_id)->first();
                if ($repair) {
                    $driver_type = 'ฝ่ายซ่อมบำรุง';
                    $driver_names = $repair->contact;
                    $driver_tels = $repair->tel;
                }

                $accident = AccidentRepairOrder::leftJoin('accidents', 'accidents.id', '=', 'accident_repair_orders.accident_id')
                    ->where('accidents.car_id', $car_id)
                    ->whereDate('accident_repair_orders.repair_date', '<=', $incident_date)
                    ->whereDate(
                        DB::raw('DATE_ADD(accident_repair_orders.repair_date, INTERVAL accident_repair_orders.amount_completed DAY)'),
                        '>=',
                        $incident_date
                    )
                    ->select('accident_repair_orders.*')
                    ->first();
                if ($accident) {
                    $cradle = Cradle::find($accident->cradle_id);
                    if ($cradle) {
                        $driver_type = 'อู่';
                        $driver_names = $cradle->name;
                        $driver_tels = $cradle->cradle_tel;
                    }
                }
            } else {
                $driver_type = null;
                $driver_names = null;
                $driver_tels = null;
            }
        }
        return  [
            'name' => $driver_names,
            'tel' => $driver_tels,
            'type' => $driver_type,
        ];
    }

    public function getDrivingJob(Request $request)
    {
        $incident_date = $request->incident_date;
        $data = [];
        $driver_type = null;
        $driver_names = null;
        $driver_tels = null;
        $driving_job = DrivingJob::where('actual_start_date', '<=', $incident_date)->where('actual_end_date', '>=', $incident_date)->where('car_id', $request->car_id)->first();
        if ($driving_job) {
            $driver = Driver::find($driving_job->driver_id);
            if ($driver) {
                $driver_type = 'พนักงานขับรถ';
                // $data['info'] = $driving_job;
                $driver_names = $driver->name;
                $driver_tels = $driver->tel;
            }
        } else {
            $contract_line = ContractLines::where('pick_up_date', '<=', $incident_date)->where('return_date', '>=', $incident_date)->where('car_id', $request->car_id)->first();
            if ($contract_line) {
                $contract = Contracts::find($contract_line->contract_id);
                if ($contract->job_type == Rental::class) {
                    $rental = Rental::find($contract->job_id);
                    if ($rental) {
                        $rental_driver = RentalDriver::where('rental_id', $rental->id)->get();
                        $driver_type = 'ลูกค้า';
                        $driver_names = $rental_driver->pluck('name')->implode(', ');
                        $driver_tels = $rental_driver->pluck('tel')->implode(', ');
                    }
                } else if ($contract->job_type == Rental::class) {
                    $driver_type = 'ลูกค้า';
                    $driver_names = $contract_line->car_user;
                    $driver_tels = $contract_line->tell;
                } else {
                    $data['is_driver'] = false;
                    $data['info'] = $contract_line;
                }

                $repair = Repair::whereDate('in_center_date', '=', $incident_date)->where('car_id', $request->car_id)->first();
                if ($repair) {
                    $driver_type = 'ฝ่ายซ่อมบำรุง';
                    $driver_names = $repair->contact;
                    $driver_tels = $repair->tel;
                }

                $accident = AccidentRepairOrder::leftJoin('accidents', 'accidents.id', '=', 'accident_repair_orders.accident_id')
                    ->where('accidents.car_id', $request->car_id)
                    ->whereDate('accident_repair_orders.repair_date', '<=', $incident_date)
                    ->whereDate(
                        DB::raw('DATE_ADD(accident_repair_orders.repair_date, INTERVAL accident_repair_orders.amount_completed DAY)'),
                        '>=',
                        $incident_date
                    )
                    ->select('accident_repair_orders.*')
                    ->first();
                if ($accident) {
                    $cradle = Cradle::find($accident->cradle_id);
                    if ($cradle) {
                        $driver_type = 'อู่';
                        $driver_names = $cradle->name;
                        $driver_tels = $cradle->cradle_tel;
                    }
                }
                // dd($accident);


            } else {
                $driver_type = null;
                $driver_names = null;
                $driver_tels = null;
            }
        }

        return [
            'success' => true,
            'name' => $driver_names,
            'tel' => $driver_tels,
            'type' => $driver_type,
        ];
    }

    private function setProgressStep($status)
    {
        $step = 0;
        if (in_array($status, [SignYellowTicketStatusEnum::DRAFT,SignYellowTicketStatusEnum::WAITING_WRONG])) {
            $step = 0;
        } elseif (in_array($status, [SignYellowTicketStatusEnum::WAITING_PAY_DLT])) {
            $step = 1;
        }elseif (in_array($status, [SignYellowTicketStatusEnum::WAITING_PAY_FINE])) {
            $step = 2;
        }elseif (in_array($status, [SignYellowTicketStatusEnum::SUCCESS])) {
            $step = 3;
        }
        return $step;
    }
}
