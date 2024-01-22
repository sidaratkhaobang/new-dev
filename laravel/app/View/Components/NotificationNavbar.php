<?php

namespace App\View\Components;

use Illuminate\View\Component;

class NotificationNavbar extends Component
{
    public $id;
    public $title;
    public $description;
    public $url;
    public $type;
    public $datetime;
    public $readat;
    public $timeago;
    public $icon;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id, $title, $description, $url, $type, $datetime, $readat, $icon = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->url = $url;
        $this->type = $type;
        $this->icon = $icon;
        $this->datetime = $datetime;
        $this->readat = $readat;

        $this->timeago = timeago($this->datetime);

        if (empty($this->icon)) {
            switch ($this->type) {
                case 'info':
                    $this->icon = 'fa fa-info-circle text-info';
                    break;
                case 'success':
                    $this->icon = 'fa fa-check-circle text-success';
                    break;
                case 'warning':
                    $this->icon = 'fa fa-exclamation-circle text-warning';
                    break;
                case 'danger':
                    $this->icon = 'fa fa-times-circle text-danger';
                    break;
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
        return view('admin.components.notification-navbar', [
            'id' => $this->id,
            'icon' => $this->icon,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'timeago' => $this->timeago,
            'readat' => $this->readat,
        ]);
    }

    public function getIcon()
    {
        return $this->icon;
    }
}
