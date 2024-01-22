<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class CMIImport implements ToCollection, WithHeadingRow
{
    use SkipsFailures;
    public function collection(Collection $rows)
    {

    }

    public function onFailure(Failure ...$failures)
    {
        // Handle the failures how you'd like.
    }
}
