@push('styles')
    <style>
        .form-progress-bar {
            color: #888888;
            padding: 30px;
            /* margin-left: auto; 
            margin-right:auto; */
        }

        .form-progress-bar .form-progress-bar-header {
            text-align: center;
            margin-left: 35%; 
        }


        .form-progress-bar .form-progress-bar-steps {
            margin: 30px 0 10px 0;
        }

        .form-progress-bar .form-progress-bar-steps li,
        .form-progress-bar .form-progress-bar-labels li {
            width: 16.6%;
            float: left;
            position: relative;
        }

        .form-progress-bar .form-progress-bar-steps li::after {
            background-color: #f3f3f3;
            content: "";
            height: 2px;
            left: 0;
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            border-bottom: 1px solid #dddddd;
            border-top: 1px solid #dddddd;
        }

        .form-progress-bar .form-progress-bar-steps li span {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
            z-index: 1;
        }

        .form-progress-bar .form-progress-bar-labels li span {
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            z-index: 1;
            font-weight: 500;
        }

        .form-progress-bar .form-progress-bar-steps li:last-child::after {
            width: 50%;
        }

        .form-progress-bar .form-progress-bar-steps li.active span,
        .form-progress-bar .form-progress-bar-steps li.activated span {
            background-color: #157CF2;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-labels li.active span,
        .form-progress-bar .form-progress-bar-labels li.activated span {
            color: #3C5768;
        }

        .form-progress-bar .form-progress-bar-steps li.active::after,
        .form-progress-bar .form-progress-bar-steps li.activated::after {
            background-color: #157CF2;
            left: 50%;
            width: 50%;
            border-color: #157CF2;
        }

        .form-progress-bar .form-progress-bar-steps li.activated::after {
            width: 100%;
            border-color: #157CF2;
        }

        .form-progress-bar .form-progress-bar-steps li:last-child::after {
            left: 0;
        }

        .form-progress-bar .form-progress-bar-labels li::after {
            background-color: #f3f3f3;
            height: 2px;
            left: 0;
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
        }
    </style>
@endpush
<div class="row no-gutters">
    <div class="col-12">
        <div class="form-progress-bar">
            <div class="form-progress-bar-header">
                <ul class="list-unstyled form-progress-bar-steps clearfix">
                    @foreach ($active as $index => $item)
                        <li class="{{ $item ? 'activated' : '' }}">
                            @if ($item)
                                <span><i class="fa fa-check"></i></span>
                            @else
                                <span><i class="fa fa-circle"></i></span>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <ul class="list-unstyled form-progress-bar-labels clearfix">
                    <li class="{{ $active[0] ? 'activated' : '' }}"><span>รายละเอียดใบงาน</span></li>
                    <li class="{{ $active[1] ? 'activated' : '' }}"><span>ตรวจอุปกรณ์/คุณภาพ</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
