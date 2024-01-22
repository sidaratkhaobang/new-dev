<?php

namespace App\Traits;

use App\Classes\OrderManagement;
use App\Models\CustomerDriver;
use App\Models\ProductAdditional;
use App\Models\Rental;
use App\Models\RentalDriver;
use App\Models\RentalLine;
use App\Models\RentalProductTransport;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait RentalDriverTrait
{
    function deleteRentalDriverFiles($pending_files)
    {
        if ((!empty($pending_files)) && (sizeof($pending_files) > 0)) {
            foreach ($pending_files as $media_id) {
                $media = Media::find($media_id);
                if ($media && $media->model_id) {
                    $model = RentalDriver::find($media->model_id);
                    $model->deleteMedia($media->id);
                }
            }
        }
        return true;
    }

    public function saveRentalDriver($request, $rental_id)
    {
        $delete_driver_ids = $request->delete_driver_ids;
        if ((!empty($delete_driver_ids)) && (is_array($delete_driver_ids))) {
            foreach ($delete_driver_ids as $delete_id) {
                $rental_driver_delete = RentalDriver::find($delete_id);
                $driving_license_medias = $rental_driver_delete->getMedia('rental_driver_license');
                foreach ($driving_license_medias as $driving_license_media) {
                    $driving_license_media->delete();
                }
                $driving_citizen_medias = $rental_driver_delete->getMedia('rental_driver_citizen');
                foreach ($driving_citizen_medias as $driving_citizen_media) {
                    $driving_citizen_media->delete();
                }
                $rental_driver_delete->delete();
            }
        }

        //// create + update rental driver data
        $pending_delete_license_files = $request->pending_delete_license_files;
        $pending_delete_citizen_files = $request->pending_delete_citizen_files;
        if (!empty($request->drivers)) {
            foreach ($request->drivers as $key => $request_rental_driver) {
                $rental_driver = RentalDriver::firstOrNew(['id' => $request_rental_driver['id']]);
                if (!$rental_driver->exists) {
                    //
                }
                $rental_driver->rental_id = $rental_id;
                $rental_driver->name = $request_rental_driver['name'];
                $rental_driver->citizen_id = $request_rental_driver['citizen_id'];
                $rental_driver->email = $request_rental_driver['email'];
                $rental_driver->tel = $request_rental_driver['tel'];
                $rental_driver->is_check_dup = boolval($request_rental_driver['is_check_dup']);
                $rental_driver->license_id = $request_rental_driver['license_id'];
                $rental_driver->license_exp_date = $request_rental_driver['license_exp_date'];
                $rental_driver->save();
                // delete license and delete citizen
                if ((!empty($pending_delete_license_files)) && (sizeof($pending_delete_license_files) > 0)) {
                    foreach ($pending_delete_license_files as $license_media_id) {
                        $license_media = Media::find($license_media_id);
                        if ($license_media && $license_media->model_id) {
                            $license_model = RentalDriver::find($license_media->model_id);
                            $license_model->deleteMedia($license_media->id);
                        }
                    }
                }

                if ((!empty($pending_delete_citizen_files)) && (sizeof($pending_delete_citizen_files) > 0)) {
                    foreach ($pending_delete_citizen_files as $citizen_media_id) {
                        $citizen_media = Media::find($citizen_media_id);
                        if ($citizen_media && $citizen_media->model_id) {
                            $citizen_model = RentalDriver::find($citizen_media->model_id);
                            $citizen_model->deleteMedia($citizen_media->id);
                        }
                    }
                }
                // insert + update rental driver license and citizen
                if ((!empty($request->driver_license_file)) && (sizeof($request->driver_license_file) > 0)) {
                    foreach ($request->driver_license_file as $table_row_index => $driver_license_files) {
                        foreach ($driver_license_files as $driver_license_file) {
                            if ($driver_license_file->isValid() && (strcmp($table_row_index, 'table_row_' . $key) == 0)) {
                                $rental_driver->addMedia($driver_license_file)->toMediaCollection('rental_driver_license');
                            }
                        }
                    }
                }
                if ((!empty($request->driver_citizen_files)) && (sizeof($request->driver_citizen_files) > 0)) {
                    foreach ($request->driver_citizen_files as $table_row_index => $driver_citizen_files) {
                        foreach ($driver_citizen_files as $driver_citizen_file) {
                            if ($driver_citizen_file->isValid() && (strcmp($table_row_index, 'table_row_' . $key) == 0)) {
                                $rental_driver->addMedia($driver_citizen_file)->toMediaCollection('rental_driver_citizen');
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    public function saveRentalProductAdditional($request, $rental_id, $rental)
    {
        /* RentalProductAdditional::where('rental_id', $rental_id)->forceDelete(); */
        /* RentalLine::where('rental_bill_id', $rental_bill_id)
            ->where('item_type', OrderLineTypeEnum::ADDITIONAL_PRODUCT_COST)
            ->forceDelete(); */
        $product_additionals_del = $request->product_additionals_del;
        if (is_array($product_additionals_del) && (sizeof($product_additionals_del) > 0)) {
            RentalLine::where('rental_id', $rental_id)->whereIn('id', $product_additionals_del)->forceDelete();
        }

        //$rental = Rental::find($rental_id);
        $product_additionals = $request->product_additionals;
        $totalCar = [];
        if ($product_additionals && sizeof($product_additionals) > 0) {
            $total = 0;
            $amount = 0;
            foreach ($product_additionals as $key => $item) {
                $rental_line = RentalLine::firstOrNew(['id' => $item['rental_line_id']]);
                $product_additional = ProductAdditional::find($item['product_additional_id']);
                if ($product_additional) {
                    $rental_line->rental_id = $rental_id;
                    $rental_line->item_type = ProductAdditional::class;
                    $rental_line->item_id = $product_additional->id;
                    $rental_line->car_id = $item['car_id'];
                    $rental_line->name = $product_additional->name;
                    $rental_line->unit_price = abs(floatval($product_additional->price));
                    $rental_line->amount = abs(intval($item['amount']));
                    $rental_line->save();
                }
            }

            $om = new OrderManagement($rental);
            $om->calculate();
        }
    }

    public function saveRentalProductTransport($request, $rental_id)
    {
        $rental = Rental::find($rental_id);
        $product_transport = $request->product_transport;
        if ($product_transport && sizeof($product_transport) > 0) {
            foreach ($product_transport as $key => $item) {
                $rental_pt = RentalProductTransport::firstOrNew(['id' => $item['id']]);
                $rental_pt->rental_id = $rental_id;
                $rental_pt->brand_name = $item['brand_name'];
                $rental_pt->product_type = $item['product_type'];
                $rental_pt->class_name = $item['class_name'];
                $rental_pt->color_name = $item['color_name'];
                $rental_pt->license_plate = $item['license_plate'];
                $rental_pt->chassis_no = $item['chassis'];
                $rental_pt->engine_no = $item['engine'];
                $rental_pt->chassis_no = $item['chassis'];
                $rental_pt->remark = $item['remark'];
                $rental_pt->transfer_type = $item['transfer_type'];
                $rental_pt->column_1 = $item['width'];
                $rental_pt->column_2 = $item['long'];
                $rental_pt->column_3 = $item['height'];
                $rental_pt->column_4 = preg_replace("/[^0-9]/", '', $item['weight']);
                $rental_pt->save();

                // product_files__pending_delete_ids
                if ($request->product_files__pending_delete_ids) {
                    $pending_delete_ids = $request->product_files__pending_delete_ids;
                    if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                        foreach ($pending_delete_ids as $media_id) {
                            $media = Media::find($media_id);
                            if ($media && $media->model_id) {
                                if ($request->product_files__no_delete_ids) {
                                    $no_del = array_unique($request->product_files__no_delete_ids);
                                    $search = array_search($media_id, $no_del);
                                    if ($search === false) {
                                        $rental_transport = RentalProductTransport::find($media->model_id);
                                        $rental_transport->deleteMedia($media->id);
                                    }
                                } else {
                                    $rental_transport = RentalProductTransport::find($media->model_id);
                                    $rental_transport->deleteMedia($media->id);
                                }
                            }
                        }
                    }
                }

                $delete_product_file_ids = $request->delete_driver_ids;
                if ((!empty($delete_product_file_ids)) && (is_array($delete_product_file_ids))) {
                    foreach ($delete_product_file_ids as $delete_id) {
                        $rental_product_file_delete = RentalProductTransport::find($delete_id);
                        if ($rental_product_file_delete !== null) {
                            $rental_product_file_delete->delete();
                        }
                    }
                }
                if (!empty($request->product_transport)) {
                    if ((!empty($request->product_files)) && (sizeof($request->product_files) > 0)) {
                        foreach ($request->product_files as $table_row_index => $product_files) {
                            foreach ($product_files as $product_file) {
                                if ($product_file->isValid() && (strcmp($table_row_index, 'table_row_' . $key) == 0)) {
                                    $media = DB::table('media')->where('model_id', $rental_pt->id)->delete();
                                    $rental_pt->addMedia($product_file)->toMediaCollection('product_file');
                                }
                            }
                        }
                    }
                }
            }
        }

        $product_transport_return = $request->product_transport_return;
        if ($product_transport_return && sizeof($product_transport_return) > 0) {
            foreach ($product_transport_return as $key => $item) {
                $rental_ptr = RentalProductTransport::firstOrNew(['id' => $item['id']]);
                $rental_ptr->rental_id = $rental_id;
                $rental_ptr->brand_name = $item['brand_name'];
                $rental_ptr->product_type = $item['product_type'];
                $rental_ptr->class_name = $item['class_name'];
                $rental_ptr->color_name = $item['color_name'];
                $rental_ptr->license_plate = $item['license_plate'];
                $rental_ptr->chassis_no = $item['chassis'];
                $rental_ptr->engine_no = $item['engine'];
                $rental_ptr->chassis_no = $item['chassis'];
                $rental_ptr->remark = $item['remark'];
                $rental_ptr->transfer_type = $item['transfer_type'];
                $rental_ptr->column_1 = $item['width'];
                $rental_ptr->column_2 = $item['long'];
                $rental_ptr->column_3 = $item['height'];
                $rental_ptr->column_4 = preg_replace("/[^0-9]/", '', $item['weight']);
                $rental_ptr->save();
                if ($request->pending_delete_product_files) {
                    foreach ($request->pending_delete_product_files as $key_media => $value_media) {
                        $media = Media::find($value_media);
                        if ($media && $media->model_id) {
                            $rental_transport = RentalProductTransport::find($media->model_id);
                            $rental_transport->deleteMedia($media->id);
                        }
                    }
                }
                if ($request->pending_delete_product_files_return) {
                    foreach ($request->pending_delete_product_files_return as $key_media => $value_media) {
                        $media = Media::find($value_media);
                        if ($media && $media->model_id) {
                            $rental_transport = RentalProductTransport::find($media->model_id);
                            $rental_transport->deleteMedia($media->id);
                        }
                    }
                }
                $delete_product_file_return_ids = $request->delete_driver_ids;
                if ((!empty($delete_product_file_return_ids)) && (is_array($delete_product_file_return_ids))) {
                    foreach ($delete_product_file_return_ids as $delete_id) {
                        $rental_product_file_return_delete = RentalProductTransport::find($delete_id);
                        if ($rental_product_file_return_delete !== null) {
                            $rental_product_file_return_delete->delete();
                        }
                    }
                }
                if (!empty($request->product_transport_return)) {
                    if ((!empty($request->product_files_return)) && (sizeof($request->product_files_return) > 0)) {
                        foreach ($request->product_files_return as $table_row_index => $product_files_return) {
                            foreach ($product_files_return as $product_file_return) {
                                if ($product_file_return->isValid() && (strcmp($table_row_index, 'table_row_' . $key) == 0)) {
                                    $media = DB::table('media')->where('model_id', $rental_ptr->id)->delete();
                                    $rental_ptr->addMedia($product_file_return)->toMediaCollection('product_file_return');
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function copyCustomerDriverToRentalDriver($customer_id, $rental_id)
    {
        $customer_driver_list = $this->getCustomerDriverList($customer_id);
        foreach ($customer_driver_list as $key => $customer_driver) {
            $new_rental_driver = new RentalDriver();
            $new_rental_driver->customer_driver_id = $customer_driver->id;
            $new_rental_driver->rental_id = $rental_id;
            $new_rental_driver->name = $customer_driver->name;
            $new_rental_driver->tel = $customer_driver->tel;
            $new_rental_driver->email = $customer_driver->email;
            $new_rental_driver->citizen_id = $customer_driver->citizen_id;
            $new_rental_driver->save();
            $driver_license_medias = $customer_driver->getMedia('driver_license')->first();
            if ($driver_license_medias) {
                $copy_driver_license_medias = $driver_license_medias->replicate();
                $copy_driver_license_medias->collection_name = 'rental_driver_license';
                $copy_driver_license_medias->model_type = 'App\Models\RentalDriver';
                $copy_driver_license_medias->model_id = $new_rental_driver->id;
                $copy_driver_license_medias->save();
            }
            $driver_citizen_medias = $customer_driver->getMedia('driver_citizen')->first();
            if ($driver_citizen_medias) {
                $copy_driver_citizen_medias = $driver_citizen_medias->replicate();
                $copy_driver_citizen_medias->collection_name = 'rental_driver_citizen';
                $copy_driver_citizen_medias->model_type = 'App\Models\RentalDriver';
                $copy_driver_citizen_medias->model_id = $new_rental_driver->id;
                $copy_driver_citizen_medias->save();
            }
        }
        return true;
    }

    function getCustomerDriverList($customer_id)
    {
        $driver_list = CustomerDriver::where('customer_id', $customer_id)->get();
        $driver_list->map(function ($item) {
            $driver_license_medias = $item->getMedia('driver_license');
            $license_files = get_medias_detail($driver_license_medias);

            $item->license_files = $license_files;
            $item->pending_delete_license_files = [];
            // get driver citizen files
            $driver_citizen_medias = $item->getMedia('driver_citizen');
            $citizen_files = get_medias_detail($driver_citizen_medias);

            $item->citizen_files = $citizen_files;
            $item->pending_delete_citizen_files = [];
            $item->type = 'CUSTOMER_DRIVER';
            return $item;
        });
        return $driver_list;
    }
}
