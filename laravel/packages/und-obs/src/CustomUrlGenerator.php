<?php

namespace UndObs;

use DateTimeInterface;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Support\UrlGenerator\BaseUrlGenerator;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;
use Illuminate\Support\Facades\Storage;

class CustomUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        return $this->getDisk2()->url($this->getPathRelativeToRoot());
    }

    public function getPath(): string
    {
        return $this->getRootOfDisk() . $this->getPathRelativeToRoot();
    }

    protected function getRootOfDisk(): string
    {
        return $this->getDisk2()->path('/');
    }

    protected function getDisk2()
    {
        return Storage::disk($this->getDiskName());
    }
}
