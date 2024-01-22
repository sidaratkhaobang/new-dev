@switch($d->job_type)
    @case(\App\Models\Rental::class)
        @include('admin.driving-jobs.job-parents.short-term-rental')
        @break
    @default
        @include('admin.driving-jobs.job-parents.other')
@endswitch