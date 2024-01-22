<?php

namespace App\Classes;

class NotificationObject
{
    public $title;
    public $description;
    public $url;
    public $type;
    public $optionals;

    public function __construct($title, $description, $url, $type = 'info', $optionals = [])
    {
        $this->title = $title;
        $this->description = $description;
        $this->url = $url;
        $this->type = $type;

        // check type
        if (!in_array($this->type, ['info', 'success', 'warning', 'danger'])) {
            $this->type = 'info';
        }
    }

    function toArray()
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'type' => $this->type,
        ];
    }
}
