<?php

namespace App\Http\Controllers;

use App\Classes\CarParkManagement;
use App\Classes\OrderManagement;
use App\Enums\CarEnum;
use App\Enums\RentalStatusEnum;
use App\Models\RentalLine;
use App\Models\Product;
use App\Traits\RentalTrait;
use Illuminate\Http\Request;
use App\Classes\PromotionManagement;
use App\Classes\ProductManagement;
use App\Models\Rental;
use App\Models\DrivingJob;
use App\Models\Location;
use Artisaninweb\SoapWrapper\SoapWrapper;
use RicorocksDigitalAgency\Soap\Facades\Soap;
use SoapClient;
use Illuminate\Support\Facades\Http;
use App\Classes\QuickPay;
use App\Classes\RentalCarManagement;
use App\Enums\RentalTypeEnum;
use App\Models\Car;
use App\Models\CarClass;
use App\Models\CarColor;
use App\Models\CarRentalCategory;
use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\Storage;
use App\Classes\Sap\SapProcess;
use App\Enums\PaymentGatewayEnum;
use App\Enums\PaymentMethodEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NotificationCustom;
use App\Models\User;
use App\Events\NotificationSend;
use App\Classes\NotificationManagement;
use App\Enums\NotificationScopeEnum;
use App\View\Components\NotificationNavbar;
use App\Classes\GPSService;
use App\Models\PurchaseOrder;
use App\Models\CarParkTransfer;
use App\Models\LongTermRental;
use App\Models\Quotation;
use App\Models\InspectionJob;
use App\Models\BillSlip;
use App\Models\Branch;

class DebugController extends Controller
{
    function promotions()
    {
        $service_type_id = '976b0c65-9ace-42e2-9b1c-85ac683d246f';
        //$service_type_id = '976d8a69-cf23-4166-9fd5-38c3640f8a86';
        $pm = new ProductManagement($service_type_id);
        $pm->setBranchId('976b0c02-d440-4932-bc8d-3c92b4e7d803');
        //$pm->setBranchId('976b0c02-d561-4518-a68d-ec11a08932f5');
        //$pm->setIsApplication(true);
        $products = $pm->getAvailableProducts();

        $product_id = '976d5786-68b1-4fc6-bb13-7566409411e0';
        $product = $pm->find($product_id);
        $pm->setDates('2022-10-01 08:00:00', '2022-10-03 16:00:00');

        dd($products, $product);
    }

    function soap()
    {
        /* $this->soapWrapper = new SoapWrapper();
        $this->soapWrapper->add('Currency', function ($service) {
        $service
        ->wsdl('http://sapdbdv1.portal.truemove.th:8015/sap/bc/srt/wsdl/bndg_5BF231B02D231122E1000000AC13086A/wsdl11/allinone/ws_policy/document?sap-client=220')
        ->options([
        'login' => 'SMARTCAR',
        'password' => 'P@sswd01'
        ])
        ->trace(true);
        });
        $response = $this->soapWrapper->call('Currency.Read_Detail_Fund_Code', [
        'CurrencyFrom' => 'USD',
        'CurrencyTo'   => 'EUR',
        'RateDate'     => '2014-06-05',
        'Amount'       => '1000',
        ]); */
        /* $headers = [
        'Authorization' => 'Basic U01BUlRDQVI6UEBzc3dkMDE=',
        'Cookie' => 'sap-usercontext=sap-client=220'
        ];
        $response = Soap::to('https://carfly-dev.truecorp.co.th/sap/bc/srt/wsdl/bndg_5BF231B02D231122E1000000AC13086A/wsdl11/allinone/standard/document?sap-client=220')
        ->withHeaders($headers)
        ->call('ZfmFundcodeGetDetail', [
        'IFikrs' => '1000',
        'IFincode' => 'A011466ZZZ',
        ]);
        ;
        dd($response->ZfmFundcodeGetDetail()); */

        $client = new SoapClient("https://carfly-dev.truecorp.co.th/sap/bc/srt/wsdl/bndg_5BF231B02D231122E1000000AC13086A/wsdl11/allinone/standard/document?sap-client=220", [
            'login' => "SMARTCAR",
            'password' => "P@sswd01",
            'soap_version' => SOAP_1_1,
            'trace' => true,
            'location' => 'https://carfly-dev.truecorp.co.th/sap/bc/srt/rfc/sap/zws_fm_fundcode_get_detail/220/zws_fm_fundcode_get_detail/zws_fm_fundcode_get_detail',
            //'uri' => 'https://carfly-dev.truecorp.co.th'
        ]);
        //dd($client);
        //$client->__setLocation('https://carfly-dev.truecorp.co.th/sap/bc/srt/rfc/sap/zws_fm_fundcode_get_detail/220/zws_fm_fundcode_get_detail/zws_fm_fundcode_get_detail');
        /* dd($client->__getFunctions()); */
        dd($client->ZfmFundcodeGetDetail([
            'IFikrs' => '1000',
            'IFincode' => 'A011466ZZZ',
        ]));
    }

    function soap2()
    {
        $client = new SoapClient("https://carfly-dev.truecorp.co.th/sap/bc/srt/wsdl/bndg_15101B6125E8F1B2E1000000AC13086A/wsdl11/allinone/standard/document?sap-client=220", [
            'login' => "SMARTCAR",
            'password' => "P@sswd01",
            'soap_version' => SOAP_1_1,
            'trace' => true,
            'location' => 'https://carfly-dev.truecorp.co.th/sap/bc/srt/rfc/sap/zws_ar_bapi_acc_document_post/220/zws_ar_bapi_acc_document_post/zws_ar_bapi_acc_document_post',
            //'uri' => 'https://carfly-dev.truecorp.co.th'
        ]);
        /* dd($client->__getFunctions()); */
        dd($client->ZarBapiAccDocumentPost([
            'Bktxt' => 'TEST',
            'Blart' => 'D1',
            'Bldat' => '2021-08-24',
            'Brnch' => '0001',
            'Budat' => '2021-08-24',
            'Bukrs' => '1001',
            'ExReturn' => '',
            'ImArAccDocPost' => [
                [
                    'Itm' => '0001',
                    'ActNo' => '0102104080',
                    'Menge' => '0.000',
                    'Meins' => '',
                    'Wrbtr' => '500.00',
                    'AmtBase' => '0.00',
                    'TaxCode' => '',
                    'Zuonr' => 'ASSIGNMENT 1',
                    'Sgtxt' => 'บันทึกยอดเงินตามยอดในSTATEMENT 1',
                    'Zterm' => '',
                    'Zfbdt' => '',
                    'Zlsch' => '',
                    'Bupla' => '0001',
                    'Kostl' => '',
                    'Fistl' => '',
                    'Geber' => '',
                    'Fipex' => '',
                    'Fipos' => '',
                    'AcctType' => 'S',
                    'Stcd3' => '',
                    'J1kftbus' => '',
                    'Name' => '',
                ],
                [
                    'Itm' => '0002',
                    'ActNo' => '0102104080',
                    'Menge' => '0.000',
                    'Meins' => '',
                    'Wrbtr' => '700.00',
                    'AmtBase' => '0.00',
                    'TaxCode' => '',
                    'Zuonr' => 'ASSIGNMENT 2',
                    'Sgtxt' => 'บันทึกยอดเงินตามยอดในSTATEMENT 2',
                    'Zterm' => '',
                    'Zfbdt' => '',
                    'Zlsch' => '',
                    'Bupla' => '0001',
                    'Kostl' => '',
                    'Fistl' => '',
                    'Geber' => '',
                    'Fipex' => '',
                    'Fipos' => '',
                    'AcctType' => 'S',
                    'Stcd3' => '',
                    'J1kftbus' => '',
                    'Name' => '',
                ],
                [
                    'Itm' => '0003',
                    'ActNo' => '0003000120',
                    'Menge' => '0.000',
                    'Meins' => '',
                    'Wrbtr' => '500.00',
                    'AmtBase' => '0.00',
                    'TaxCode' => '',
                    'Zuonr' => 'ASSIGNMENT 3',
                    'Sgtxt' => 'บันทึกยอดเงินตามยอดในSTATEMENT 3',
                    'Zterm' => '',
                    'Zfbdt' => '',
                    'Zlsch' => '',
                    'Bupla' => '0001',
                    'Kostl' => '',
                    'Fistl' => '',
                    'Geber' => '',
                    'Fipex' => '',
                    'Fipos' => '',
                    'AcctType' => 'D',
                    'Stcd3' => '',
                    'J1kftbus' => '',
                    'Name' => '',
                ],
                [
                    'Itm' => '0004',
                    'ActNo' => '0003000120',
                    'Menge' => '0.000',
                    'Meins' => '',
                    'Wrbtr' => '700.00',
                    'AmtBase' => '0.00',
                    'TaxCode' => '',
                    'Zuonr' => 'ASSIGNMENT 4',
                    'Sgtxt' => 'บันทึกยอดเงินตามยอดในSTATEMENT 4',
                    'Zterm' => 'C030',
                    'Zfbdt' => '',
                    'Zlsch' => '',
                    'Bupla' => '0001',
                    'Kostl' => '',
                    'Fistl' => '',
                    'Geber' => '',
                    'Fipex' => '',
                    'Fipos' => '',
                    'AcctType' => 'D',
                    'Stcd3' => '',
                    'J1kftbus' => '',
                    'Name' => '',
                ],
                [
                    'Itm' => '0005',
                    'ActNo' => '0101300010',
                    'Menge' => '0.000',
                    'Meins' => '',
                    'Wrbtr' => '100.00-',
                    'AmtBase' => '0.00',
                    'TaxCode' => '',
                    'Zuonr' => 'ASSIGNMENT 3',
                    'Sgtxt' => 'บันทึกยอดเงินตามยอดในSTATEMENT 3',
                    'Zterm' => 'C030',
                    'Zfbdt' => '',
                    'Zlsch' => '',
                    'Bupla' => '0001',
                    'Kostl' => '',
                    'Fistl' => '',
                    'Geber' => '',
                    'Fipex' => '',
                    'Fipos' => '',
                    'AcctType' => 'S',
                    'Stcd3' => '',
                    'J1kftbus' => '',
                    'Name' => '',
                ],
                [
                    'Itm' => '0006',
                    'ActNo' => '0102104250',
                    'Menge' => '0.000',
                    'Meins' => '',
                    'Wrbtr' => '2300.00-',
                    'AmtBase' => '0.00',
                    'TaxCode' => '',
                    'Zuonr' => 'ASSIGNMENT 4',
                    'Sgtxt' => 'บันทึกยอดเงินตามยอดในSTATEMENT 4',
                    'Zterm' => '',
                    'Zfbdt' => '',
                    'Zlsch' => '',
                    'Bupla' => '0001',
                    'Kostl' => '',
                    'Fistl' => '',
                    'Geber' => '',
                    'Fipex' => '',
                    'Fipos' => '',
                    'AcctType' => 'S',
                    'Stcd3' => '',
                    'J1kftbus' => '',
                    'Name' => '',
                ],
            ],
            'Kursf' => '',
            'Waers' => 'THB',
            'Wwert' => '2021-08-24',
        ]));
    }

    function soap3()
    {
        $client = new SoapClient("https://carfly-dev.truecorp.co.th/sap/bc/srt/wsdl/bndg_5BF231B02D231122E1000000AC13086A/wsdl11/allinone/standard/document?sap-client=220", [
            'login' => "SMARTCAR",
            'password' => "P@sswd01",
            'soap_version' => SOAP_1_1,
        ]);
        $old_location = $client->__setLocation();
        dd($old_location);
    }

    function quickpay()
    {
        $generate_link = [
            'url' => 'xxx',
            'orderIdPrefix' => 'aaa'
        ];
        $update_result = null;
        $inquiry_result = null;
        $delete_result = null;
        $inquiry_result2 = null;

        $qp = new QuickPay();
        $qp->amount = 99;
        $qp->description = 'description';
        $qp->rental_id = "";
        $qp->quotation_id = "";
        $qp->class_name = "";
        $generate_link = $qp->generateLink();

        $qp2 = new QuickPay();
        $qp2->amount = 158;
        $qp2->qp_id = $generate_link['qpID'];
        $qp2->description = 'description';
        $qp2->rental_id = "";
        $qp2->quotation_id = "";
        $qp2->class_name = "";
        $update_result = $qp2->update();

        $qp3 = new QuickPay();
        $qp3->qp_id = $generate_link['qpID'];
        $inquiry_result = $qp3->query();

        $qp4 = new QuickPay();
        $qp4->qp_id = $generate_link['qpID'];
        $delete_result = $qp4->delete();

        $inquiry_result2 = $qp3->query();

        dd($generate_link, $generate_link['url'], $generate_link['orderIdPrefix'], $update_result, $inquiry_result, $delete_result, $inquiry_result2);
    }

    function quickpayCheck()
    {
        $qp = new QuickPay();
        $generate_link = $qp->inquiry();
        dd($generate_link, $generate_link['url']);
    }

    function upload()
    {
        return view('debug.upload');
    }

    function uploadCheck(Request $request)
    {
        $id = '976b0c65-9ace-42e2-9b1c-85ac683d246f';
        $purchase_requisitions = PurchaseRequisition::firstOrNew(['id' => $id]);
        $purchase_requisitions->save();
        if ($request->hasFile('file')) {
            $file = $request->file;
            if ($file->isValid()) {
                $purchase_requisitions->addMedia($file)->toMediaCollection('testabc');
            }
        }
        dd(1);

        /* $result = Storage::put('testxyz/filename.jpg', $request->file);
        dd($result); */

        /* $exists = Storage::has('testxyz/filename.jpg/Tk8XZEYhPXsWVmWUzFtQcLW4FlvXUIGzqbuAKw5P.jpg');
        dd($exists); */

        /* $contents = Storage::get('testxyz/filename.jpg/Tk8XZEYhPXsWVmWUzFtQcLW4FlvXUIGzqbuAKw5P.jpg');
        dd($contents); */
    }

    function uploadTest()
    {
        /* $contents = Storage::url('testxyz/filename.jpg/Tk8XZEYhPXsWVmWUzFtQcLW4FlvXUIGzqbuAKw5P.jpg');
        dd($contents); */

        $id = '976b0c65-9ace-42e2-9b1c-85ac683d246f';
        $purchase_requisitions = PurchaseRequisition::firstOrNew(['id' => $id]);
        $medias = $purchase_requisitions->getMedia('default');
        dd($medias[0]->getUrl());
    }

    function generateRandomString($length = 10, $th = false, $number = false)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($th) {
            $characters = 'ฟหกดทมสวนยรบลพชขจภถ';
        }
        if ($number) {
            $characters = '0123456789';
        }

        $charactersLength = mb_strlen($characters);
        if ($th) {
            $characters = $this->getMBStrSplit($characters);
        }
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    // Convert a string to an array with multibyte string
    function getMBStrSplit($string, $split_length = 1)
    {
        mb_internal_encoding('UTF-8');
        mb_regex_encoding('UTF-8');

        $split_length = ($split_length <= 0) ? 1 : $split_length;
        $mb_strlen = mb_strlen($string, 'utf-8');
        $array = array();
        $i = 0;

        while ($i < $mb_strlen) {
            $array[] = mb_substr($string, $i, $split_length);
            $i = $i + $split_length;
        }

        return $array;
    }

    function getFakeCars()
    {
        for ($i = 0; $i < 5; $i++) {
            $rental_type = [RentalTypeEnum::SHORT, RentalTypeEnum::LONG];
            $car = new Car();
            $car->code = $this->generateRandomString(5);
            $car->license_plate = $this->generateRandomString(2, true) . ' ' . $this->generateRandomString(4, false, true);
            $car->engine_no = $this->generateRandomString(2) . '-' . $this->generateRandomString(6);
            $car->chassis_no = $this->generateRandomString(2) . '-' . $this->generateRandomString(6);
            $car->rental_type = $rental_type[array_rand($rental_type)];
            $car->car_class_id = CarClass::inRandomOrder()->first()->id;
            $car->car_color_id = CarColor::inRandomOrder()->first()->id;
            $car->save();
        }
        dd(true);
    }

    function getFakeReplacementCars()
    {
        for ($i = 0; $i < 5; $i++) {
            $rand_num = rand(0, 9999);
            $replacement_car = new Car();
            $replacement_car->code = 'RAND1' . $rand_num;
            $replacement_car->license_plate = 'ทด ' . $rand_num;
            $replacement_car->engine_no = 'RC-E' . $rand_num;
            $replacement_car->chassis_no = 'RC-C' . $rand_num;
            $replacement_car->status = CarEnum::READY_TO_USE;
            $replacement_car->rental_type = RentalTypeEnum::REPLACEMENT;
            $replacement_car->car_class_id = CarClass::inRandomOrder()->first()->id;
            $replacement_car->car_color_id = CarColor::inRandomOrder()->first()->id;
            $replacement_car->save();
        }
        dd(true);
    }

    function scbPayment()
    {
        $TaxId = '1111';
        $Suffix = '05';
        $Ref1 = 'ABC123456';
        $Ref2 = '1234567989';
        $newBarcode = "|" . $TaxId . $Suffix . "\r" . $Ref1 . "\r" . $Ref2 . "\r" . "10000";
        return view('debug.test-qrcode', [
            'newBarcode' => $newBarcode
        ]);
    }

    function carPark(Request $request)
    {
        $car_id = Car::first()->id;
        $branch_id = Branch::first()->id;
        $cpm = new CarParkManagement($car_id, $branch_id);
        $is_car_in_carpark = $cpm->isActivated();

        $cpm->activate();

        $is_car_in_carpark2 = $cpm->isActivated();

        dd([
            'is_car_in_carpark' => $is_car_in_carpark,
            'is_car_in_carpark2' => $is_car_in_carpark2
        ]);
    }

    function sap()
    {
        $rental = Rental::find('97da62e5-2a16-4727-b2de-b48fad661f51');
        $rental->payment_gateway = PaymentGatewayEnum::SCB_BILL_PAY;
        $rental->is_paid = true;
        $rental->payment_date = date('Y-m-d', strtotime('2022-10-15'));
        $rental->save();
        $sap = new SapProcess();
        $sap->afterPaymentBeforeService($rental->id, Rental::class);
    }

    function sap2()
    {
        $rental = Rental::find('97da62e5-2a16-4727-b2de-b48fad661f51');
        $driving_job = DrivingJob::find('97dde27d-9df3-4e3c-8140-f9f6781a5cfc');
        $sap = new SapProcess();
        $sap->startService($rental->id, $driving_job->id);
    }

    function date()
    {
        $rental = new RentalCarManagement('97c7935b-9c82-4f47-85e1-97b8c8537321');
        $dates = $rental->getAvailablePickupDates();

        $rental2 = new RentalCarManagement('97c7935b-9c82-4f47-85e1-97b8c8537321');
        $dates2 = $rental2->getAvailablePickupTimes('2023-01-20');

        $rental3 = new RentalCarManagement('97c7935b-9c82-4f47-85e1-97b8c8537321');
        $dates3 = $rental3->getAvailableReturnDates('2023-01-20', '12:00');

        $rental4 = new RentalCarManagement('97c7935b-9c82-4f47-85e1-97b8c8537321');
        $dates4 = $rental4->getAvailableReturnTimes('2023-01-21');

        $rental5 = new RentalCarManagement('97c7935b-9c82-4f47-85e1-97b8c8537321');
        $cars5 = $rental5->getAvailableCars('2023-01-29', '12:00', '2023-01-31', '10:00');

        //dd($dates, $dates2, $dates3, $dates4);
        dd($cars5);
    }

    function exportDataDictionary()
    {
        $tables = DB::select('SHOW TABLES');
        $fields = DB::table('information_schema.COLUMNS')->select('table_name', 'column_name', 'column_type', 'is_nullable', 'extra', 'column_comment')
            ->where('table_schema', 'newcarfly')
            ->orderBy('table_name')
            ->get();
        return view('debug.export-data-dictionary', [
            'tables' => $tables,
            'fields' => $fields
        ]);
    }

    function designSystem()
    {
        $select_option = collect([
            (object) [
                'id' => 1,
                'value' => 1,
                'name' => 'ลูกค้า',
            ],
            (object) [
                'id' => 0,
                'value' => 0,
                'name' => 'TLS',
            ],
        ]);
        $days = getDayCollection();
        $booking_day_arr = [];
        foreach ($days as $day) {
            $booking_day = STATUS_ACTIVE;
            if ($booking_day == STATUS_ACTIVE) {
                array_push($booking_day_arr, $day['value']);
            }
        }

        return view(
            'debug.design-system',
            [
                'select_option' => $select_option,
                'days' => $days,
                'booking_day_arr' => $booking_day_arr,
            ]
        );
    }

    function notification()
    {
        /* $user_notifications = get_unread_user_notification();
        dd($user_notifications); */
        $url = 'https://google.co.th';
        $user = Auth::user();
        $user_id = $user->id;
        $role_id = $user->role_id;
        $department_id = $user->user_department_id;
        $branch_id = $user->branch_id;

        $noti1 = new NotificationManagement('เปลี่ยนสถานะรถ', 'รถทะเบียน กด3868  ได้เปลี่ยนประเภทรถจาก รถเช่ายาว เป็น รถทดแทน', $url, NotificationScopeEnum::USER, $user_id, []);
        $noti2 = new NotificationManagement('อนุมัติใบเสนอราคาเช่ายาว', 'ใบเสนอราคา QT2023/20/9999 ได้รับการอนุมัติแล้ว', $url, NotificationScopeEnum::USER, $user_id, [], 'success');
        $noti3 = new NotificationManagement('ตีสเปครถ', 'การตลาดได้เปิดใบขอเช่ายาว 20230900002 กรุณาตีสเปครถ', $url, NotificationScopeEnum::USER, $user_id, [], 'warning');
        $noti4 = new NotificationManagement('ไม่อนุมัติใบเสนอราคาเช่ายาว', 'ใบเสนอราคา 2023/20/9999 ไม่ได้รับอนุมัติ', $url, NotificationScopeEnum::USER, $user_id, [], 'danger');
        $noti5 = new NotificationManagement('test other type', 'test description', $url, NotificationScopeEnum::USER, $user_id, [], 'abc');
        $noti5->setViaEmail(true);

        $noti6 = new NotificationManagement('test role', 'test description', $url, NotificationScopeEnum::ROLE, $role_id, []);
        $noti7 = new NotificationManagement('test department', 'test description', $url, NotificationScopeEnum::DEPARTMENT, $department_id, []);
        $noti8 = new NotificationManagement('test branch', 'test description', $url, NotificationScopeEnum::USER, $user_id, []);
        $noti8->setBranchId($branch_id);

        $noti1->send();
        $noti2->send();
        $noti3->send();
        $noti4->send();
        $noti5->send();

        /* $noti6->send();
        $noti7->send();
        $noti8->send(); */

        dd(1);
    }

    function notificationAlert()
    {
        return view('debug.notification');
    }

    function env()
    {
        dd([
            'LOG_CHANNEL' => env('LOG_CHANNEL'),
            'LOG_DEPRECATIONS_CHANNEL' => env('LOG_DEPRECATIONS_CHANNEL'),
            'LOG_LEVEL' => env('LOG_LEVEL'),
            'SENTRY_TRACES_SAMPLE_RATE' => env('SENTRY_TRACES_SAMPLE_RATE'),
        ], [
            'LOG_CHANNEL' => config('logging.default'),
            'LOG_DEPRECATIONS_CHANNEL' => config('logging.deprecations'),
            'stack' => config('logging.channels.stack'),
            'single' => config('logging.channels.single'),
            'daily' => config('logging.channels.daily'),
            'sentry' => config('logging.channels.sentry'),
            'SENTRY_TRACES_SAMPLE_RATE' => config('sentry.traces_sample_rate'),
        ]);
    }

    function abort403()
    {
        return abort(403);
        //return abort(403, 'test abort 403');
    }

    function abort404()
    {
        return abort(404, 'test abort 404');
    }

    function abort500()
    {
        return abort(500, 'test abort 500');
    }

    function getImage()
    {
        $contents = Storage::disk('public')->get('file.jpg');
        //dd($contents);
        return response($contents)
            ->header('Cache-Control', 'no-cache private')
            ->header('Content-Description', 'File Transfer')
            ->header('Content-Type', 'image/jpg')
            ->header('Content-length', strlen($contents))
            ->header('Content-Transfer-Encoding', 'binary');
    }

    function apiGPS()
    {
        $gpsService = new GPSService();

        $start_date_time = date('Y-m-d H:i:s', strtotime('-1 hours'));
        $end_date_time = date('Y-m-d H:i:s');
        $vehicle_list = [
            "2004210529",
            "2004213337",
        ];

        $res1 = $gpsService->getMasterDatas();
        $res2 = $gpsService->getVehEvents($start_date_time, $end_date_time);
        $res3 = $gpsService->getVehLastLocations($vehicle_list);
        dd([
            'start_date_time' => $start_date_time,
            'end_date_time' => $end_date_time,
            'vehicle_list' => $vehicle_list,
            'res1' => $res1,
            'res2' => $res2,
            'res3' => $res3,

            'res11' => $res1['data'][0],
            'res22' => $res2['data'][0],
            'res33' => $res3['data'][0],
        ]);
    }

    function ganttChart()
    {
        $car_list = Car::leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->where('cars.rental_type', RentalTypeEnum::SHORT)
            ->whereIn('cars.status', [CarEnum::READY_TO_USE, CarEnum::NEWCAR])
            ->select(
                'cars.id',
                'cars.license_plate',
                'car_classes.name as car_class_name',
                'car_classes.full_name as car_class_full_name',
                DB::raw('false as checked'),
            )
            ->distinct()
            ->get();
        $car_list->map(function ($car) {
            $car->can_select = true;
            $car->checked = false;
            $car->sub_name = $car->license_plate;
            $car->name = $car->car_class_full_name;
            $car_images = $car->getMedia('car_images');
            $car_images = get_medias_detail($car_images);
            $car_image = sizeof($car_images) > 0 ? $car_images[0] : null;
            $car->image = $car_image;
        });

        $status_list = collect([
            (object) [
                'id' => RentalStatusEnum::DRAFT,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::DRAFT),
                'value' => RentalStatusEnum::DRAFT,
                'class' => 'dark-blue',
            ],
            (object) [
                'id' => RentalStatusEnum::PENDING,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::PENDING),
                'value' => RentalStatusEnum::PENDING,
                'class' => 'warning',
            ],
            (object) [
                'id' => RentalStatusEnum::PAID,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::PAID),
                'value' => RentalStatusEnum::PAID,
                'class' => 'success',
            ],
            (object) [
                'id' => RentalStatusEnum::SUCCESS,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::SUCCESS),
                'value' => RentalStatusEnum::SUCCESS,
                'class' => 'primary',
            ],
        ]);
        return view(
            'debug.gantt-chart',
            [
                'car_list' => $car_list,
                'status_list' => $status_list,
            ]
        );
    }

    function worksheetno()
    {
        dd([
            generate_worksheet_no('xxx'),
            generate_worksheet_no(PurchaseRequisition::class, false),
            generate_worksheet_no(PurchaseOrder::class, false),
            generate_worksheet_no(CarParkTransfer::class, true),
            generate_worksheet_no(Rental::class),
            generate_worksheet_no(LongTermRental::class, false),
            generate_worksheet_no(Quotation::class, false),
            //generate_worksheet_no(InspectionJob::class),
            generate_worksheet_no(DrivingJob::class),
            generate_worksheet_no(BillSlip::class, false),
        ]);

        dd(generate_worksheet_no(DrivingJob::class));
    }

    function calculateRental()
    {
        $rental_id = '9a7c08a8-d6bf-4401-9d06-61807943a9ec';
        $rental = Rental::find($rental_id);

        // re-calculate product price
        $price = RentalTrait::findProductPrice($rental);

        $rental_line = RentalLine::where('rental_id', $rental_id)->where('item_type', Product::class)->first();
        if ($rental_line) {
            $rental_line->unit_price = abs(floatval($price));
            $rental_line->save();
        }

        RentalLine::where('rental_id', $rental_id)->update([
            'subtotal' => 0,
            'discount' => 0,
            'vat' => 0,
            'total' => 0,
        ]);
        RentalTrait::clearRentalLines($rental_id, Promotion::class);
        RentalTrait::clearRentalLines($rental_id, PromotionCode::class);
        RentalTrait::clearRentalLines($rental_id, ProductAdditional::class, ['is_from_promotion' => true]);
        RentalTrait::clearRentalLines($rental_id, ProductAdditional::class, ['is_from_coupon' => true]);
        $order = new OrderManagement($rental);
        $order->calculate();
        $summary = $order->getSummary();
        dd([
            'price' => $price,
            'summary' => $summary
        ]);
    }

    function rental(Request $request)
    {
        $id = $request->id;
        $rental = Rental::find($id);
        $quotation = 1;
        if ($rental) {
            $quotation = $rental->quotationPrimary;
        }
        dd($id, $rental, $quotation);
    }
}
