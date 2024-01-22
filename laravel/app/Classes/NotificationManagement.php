<?php

namespace App\Classes;

use App\Classes\NotificationObject;
use App\Enums\NotificationScopeEnum;
use App\Models\User;
use App\Notifications\NotificationCustom;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

class NotificationManagement
{
    public $title;
    public $description;
    public $url;
    public $scope;
    public $reference_id;
    public $type;
    public $branch_id;
    public $permissions;

    public $via_database;
    public $via_broadcast;
    public $via_mail;

    public function __construct($title, $description, $url, $scope, $reference_id, $permissions = [], $type = 'info')
    {
        $this->title = $title;
        $this->description = $description;
        $this->url = $url;
        $this->scope = $scope;
        $this->reference_id = $reference_id;
        $this->type = $type;
        $this->permissions = $permissions;
        if (!is_array($this->permissions)) {
            $this->permissions = [];
        }

        $user = Auth::user();
        $this->setBranchId($user->branch_id);

        $this->via_database = true;
        $this->via_broadcast = true;
        $this->via_mail = false;
    }

    function send()
    {
        try {
            $notificationObj = new NotificationObject($this->title, $this->description, $this->url, $this->type);
            switch ($this->scope) {
                case NotificationScopeEnum::USER:
                    $this->sendByUser($this->reference_id, $notificationObj);
                    break;
                case NotificationScopeEnum::ROLE:
                    $this->sendByRole($this->reference_id, $notificationObj);
                    break;
                case NotificationScopeEnum::DEPARTMENT:
                    $this->sendByDepartment($this->reference_id, $notificationObj);
                    break;
                case NotificationScopeEnum::SECTION:
                    $this->sendBySection($this->reference_id, $notificationObj);
                    break;
            }
        } catch (Exception $e) {
            Log::error('NotificationManagement: ' . $e->getMessage());
        }
    }

    private function sendByUser($reference_id, $notificationObj)
    {
        $query = $this->getUserQuery();
        $user = $query->find($reference_id);
        if (empty($user)) {
            return false;
        }
        $this->sendNotifications($user, $notificationObj);
    }

    private function sendByRole($reference_id, $notificationObj)
    {
        $query = $this->getUserQuery();
        $users = $query->where('role_id', $reference_id)->get();
        if (sizeof($users) <= 0) {
            return false;
        }
        $this->sendNotifications($users, $notificationObj);
    }

    private function sendByDepartment($reference_id, $notificationObj)
    {
        $query = $this->getUserQuery();
        $users = $query->where('department_id', $reference_id)->get();
        if (sizeof($users) <= 0) {
            return false;
        }
        $this->sendNotifications($users, $notificationObj);
    }

    private function sendBySection($reference_id, $notificationObj)
    {
        $query = $this->getUserQuery();
        $users = $query->where('section_id', $reference_id)->get();
        if (sizeof($users) <= 0) {
            return false;
        }
        $this->sendNotifications($users, $notificationObj);
    }

    private function sendNotifications($users, $notificationObj)
    {
        $via = $this->getVia();
        Notification::send($users, new NotificationCustom($notificationObj, $via));
    }

    private function getUserQuery()
    {
        return User::select('id', 'role_id', 'department_id', 'section_id', 'email')->where('branch_id', $this->branch_id);
    }

    function setBranchId($branch_id)
    {
        $this->branch_id = $branch_id;
    }

    function setViaDatabase(bool $bool)
    {
        $this->via_database = $bool;
    }

    function setViaBroadcast(bool $bool)
    {
        $this->via_broadcast = $bool;
    }

    function setViaEmail(bool $bool)
    {
        $this->via_mail = $bool;
    }

    function setType(bool $type)
    {
        $this->type = $type;
    }

    private function getVia()
    {
        $via = collect([
            'database' => $this->via_database,
            'broadcast' => $this->via_broadcast,
            'mail' => $this->via_mail,
        ])->filter(function ($value) {
            return $value;
        })->toArray();
        $via = array_keys($via);
        return $via;
    }
}
