<?php

namespace App\Traits;

use App\Classes\NotificationManagement;
use App\Enums\DepartmentEnum;
use App\Enums\NotificationScopeEnum;
use App\Models\Approve;
use App\Models\PurchaseOrder;
use App\Models\Role;
use App\Models\User;
use App\Models\Department;

trait NotificationTrait
{
    public static function getRoleId($dataRoleName)
    {
        $idRole = [];
        if (!empty($dataRoleName)) {
            $idRole = Role::wherein('name', $dataRoleName)
                ->pluck('id')
                ->toArray();
        }
        return $idRole;
    }

    public static function getUserId($notiDepartmentId = null, $notiRole = null)
    {
        $notiUserId = User::when(!empty($notiDepartmentId), function ($querySearch) use ($notiDepartmentId) {
            $querySearch->whereIn('user_department_id', $notiDepartmentId);
        })
            ->when(!empty($notiRole), function ($querySearch) use ($notiRole) {
                $querySearch->wherein('role_id', $notiRole);
            })
            ->pluck('id')
            ->toArray();

        return $notiUserId;
    }

    public static function sendNotificationSpecAccessoryApprove($IdLongTermRental, $modelLongtermRental, $dataWorkSheetNo)
    {

        if (!empty($IdLongTermRental)) {
            $ModelApprove = Approve::leftJoin(
                'approve_lines',
                'approve_lines.approve_id',
                'approves.id'
            )
                ->where('job_id', $IdLongTermRental)
                ->wherenull('is_pass')
                ->orderBy('seq', 'ASC')
                ->limit(1)
                ->pluck('user_id')
                ->toArray();
            if (!empty($ModelApprove)) {
                $notiUserId = $ModelApprove;
                $url = route('admin.long-term-rental.specs-approve.show', ['rental' => $modelLongtermRental]);
                $notiTypeChange = new NotificationManagement('อนุมัติสเปครถและอุปกรณ์', 'ใบขอเช่ายาว ' . $dataWorkSheetNo . ' พิจารณาอนุมัติสเปครถและอุปกรณ์', $url, NotificationScopeEnum::USER, $notiUserId, [], 'success');
                $notiTypeChange->send();
            } else {
                $dataDepartment = [
                    DepartmentEnum::PCD_PURCHASE,
                ];
                $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
                $url = route('admin.long-term-rental.compare-price.edit', ['rental' => $modelLongtermRental]);
                $notiTypeChange = new NotificationManagement('อนุมัติสเปครถและอุปกรณ์', 'ใบขอเช่ายาว ' . $dataWorkSheetNo . ' กรุณาเปรียบเทียบราคา', $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, []);
                $notiTypeChange->send();
            }
        }
    }

    public static function getDepartmentId($dataDeaprtmentName)
    {
        $idDepartment = [];
        if (!empty($dataDeaprtmentName)) {
            $idDepartment = Department::wherein('code', $dataDeaprtmentName)
                ->pluck('id')
                ->toArray();
        }
        return $idDepartment;
    }

    public static function sendNotificationRentalPriceApprove($IdQuotation, $modelQuotation, $dataWorkSheetNo)
    {

        if (!empty($IdQuotation)) {
            $ModelApprove = Approve::leftJoin(
                'approve_lines',
                'approve_lines.approve_id',
                'approves.id'
            )
                ->where('job_id', $IdQuotation)
                ->wherenull('is_pass')
                ->orderBy('seq', 'ASC')
                ->limit(1)
                ->pluck('user_id')
                ->toArray();
            if (!empty($ModelApprove)) {
                $notiUserId = $ModelApprove;
                $url = route('admin.quotation-approves.show', ['quotation_approve' => $modelQuotation]);
                $notiTypeChange = new NotificationManagement('อนุมัติใบเสนอราคาเช่ายาว', 'ใบเสนอราคา ' . $dataWorkSheetNo . ' พิจารณาอนุมัติ', $url, NotificationScopeEnum::USER, $notiUserId, [], 'success');
                $notiTypeChange->send();
            } else {
                $dataDepartment = [
                    DepartmentEnum::AMO_SALE_ADMIN,
                ];
                $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
                $url = route('admin.quotations.show', ['quotation' => $modelQuotation]);
                $notiTypeChange = new NotificationManagement('อนุมัติใบเสนอราคาเช่ายาว', 'ใบเสนอราคา ' . $dataWorkSheetNo . ' ได้รับการอนุมัติ ', $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, [], 'success');
                $notiTypeChange->send();
            }
        }
    }

    public static function sendNotificationPrApprove($IdPurchaseRequisition, $modelPurchaseRequisition, $dataPrNo,$type = null)
    {
        if (!empty($IdPurchaseRequisition)) {
            $ModelApprove = Approve::leftJoin(
                'approve_lines',
                'approve_lines.approve_id',
                'approves.id'
            )
                ->where('job_id', $IdPurchaseRequisition)
                ->wherenull('is_pass')
                ->orderBy('seq', 'ASC')
                ->limit(1)
                ->pluck('user_id')
                ->toArray();
            if (!empty($ModelApprove)) {
                $notiUserId = $ModelApprove;
                $url = route('admin.purchase-requisition-approve.show', ['purchase_requisition_approve' => $modelPurchaseRequisition]);
                $notiTypeChange = new NotificationManagement('อนุมัติใบขอซื้อ', 'ใบขอซื้อ ' . $dataPrNo . ' พิจารณาอนุมัติ', $url, NotificationScopeEnum::USER, $notiUserId, [], $type);
                $notiTypeChange->send();
            } else {
                $dataDepartment = [
                    DepartmentEnum::PCD_PURCHASE,
                ];
                $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
                $url = route('admin.purchase-order-open.show', ['purchase_order_open' => $modelPurchaseRequisition]);
                $notiTypeChange = new NotificationManagement('เปิดใบสั่งซื้อ', 'ใบขอซื้อ ' . $dataPrNo . ' กรุณาเปิดใบสั่งซื้อ ', $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, []);
                $notiTypeChange->send();
            }
        }
    }

    public static function sendNotificationPrOpenOrder($IdPurchaseOrder, $modelPurchaseOrder, $dataPrOrderNo,$type = null){
        if (!empty($IdPurchaseOrder)) {
            $ModelApprove = Approve::leftJoin(
                'approve_lines',
                'approve_lines.approve_id',
                'approves.id'
            )
                ->where('job_id', $IdPurchaseOrder)
                ->wherenull('is_pass')
                ->orderBy('seq', 'ASC')
                ->limit(1)
                ->pluck('user_id')
                ->toArray();
            if (!empty($ModelApprove)) {

                $notiUserId = $ModelApprove;
                $url = route('admin.purchase-order-approve.show', ['purchase_order_approve' => $modelPurchaseOrder]);
                $notiTypeChange = new NotificationManagement('อนุมัติใบสั่งซื้อ', 'ใบขอซื้อ ' . $dataPrOrderNo . ' พิจารณาอนุมัติ', $url, NotificationScopeEnum::USER, $notiUserId, [], $type);
                $notiTypeChange->send();
            } else {

                $dataDepartment = [
                    DepartmentEnum::AMO_SALE_ADMIN,
                ];
                $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
                $url = route('admin.purchase-order-approve.show', ['purchase_order_approve' => $modelPurchaseOrder]);
                $notiTypeChange = new NotificationManagement('อนุมัติใบสั่งซื้อ', 'ใบขอซื้อ ' . $dataPrOrderNo . ' ได้รับการอนุมัติ ', $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, [],'success');
                $notiTypeChange->send();
            }
        }

    }


}
