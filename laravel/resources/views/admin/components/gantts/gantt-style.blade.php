@push('styles')
    <style>
        .gantt {
            display: grid;
            /* grid-template-columns: repeat(30, minmax(0, 1fr)); */
            border-radius: 4px;
            background: repeating-linear-gradient(to right, #f2f2f2, #ddd 2px, #fff 2px, #fff 14.25%);
            background: repeating-linear-gradient(to right, #f2f2f2, #ddd 2px, #fff 2px, #fff 14.25%);
        }

        .gantt.card-content {
            display: grid;
            border-radius: 0px;
            background: transparent;
            row-gap: 0.5px;
        }

        .gantt div {
            padding: 8px;
        }

        .gantt .head {
            font-size: 14px;
            text-align: center;
            font-weight: 700;
            background: #EDF0F7;
            color: #4D4D4D;
        }

        .head-title {
            text-align: center;
            font-weight: 700;
            background: #EDF0F7;
            color: #4D4D4D;
            display: flex;
            -webkit-flex-align: center;
            -ms-flex-align: center;
            -webkit-align-items: center;
            align-items: center;
            justify-content: center;
        }

        .gantt-card {
            box-shadow: 0px 4px 10px rgba(163, 163, 163, 0.25);
            border-radius: 4px;
            border: 3px solid transparent;
        }

        .chart-card {
            padding: 1rem;
        }

        .chart-card-content {
            display: block;
            border-right: 1px solid #F0F0F0;
        }

        .card-image {
            width: 60%;
            height: 100%;
            object-fit: contain;
            display: block;
            margin: auto;
        }

        .gantt-timeline-section {
            position: absolute;
            display: grid;
            width: inherit;
            align-self: center;
        }

        .gantt-timeline {
            /* border: 1px solid #63a4ee; */
            background-color: #ffffff;
            border-radius: 4px;
            /* padding: 4px !important; */
            text-align: center;
            color: #fff;
            height: 25px;
            overflow: hidden;
        }

        .timeline-color-success {
            border: 2px solid #419E6A !important;
            background-color: #419E6A;
        }
    
        .timeline-color-primary {
            border: 2px solid #0665d0 !important;
            background-color: #0665d0;
        }

        .timeline-color-dark {
            border: 2px solid #475569 !important;
            background-color: #475569;
        }

        .timeline-color-danger {
            border: 2px solid #e04f1a !important;
            background-color: #e04f1a;
        }

        .timeline-color-warning {
            border: 2px solid #EFB008 !important;
            background-color: #EFB008;
        }

        .timeline-color-disable {
            border: 2px solid #DBDEE3 !important;
            background-color: #DBDEE3 !important;
        }

        .box-wrapper .form-check {
            display: flex !important;
        }

        .gantt-card>input {
            visibility: hidden;
            position: absolute;
        }

        .gantt-card {
            cursor: pointer;
            overflow: hidden;
            /* border: 2px solid #ff6600; */
        }

        .high-light {
            border: 3px solid #157CF2;
            background-color: #fefeff;
        }

        .text-btn-group {
            text-align: center;
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #4D4D4D !important;
        }

        .btn-img {
            height: 220px;
            width: 250px;
            background: #FFFFFF;
            box-shadow: 0px 4px 10px rgba(163, 163, 163, 0.25);
            border-radius: 0.5em;
            cursor: pointer;
        }

        .btn-img:hover {
            border: solid #a4c1e2;
            box-shadow: 0px 4px 10px rgba(163, 163, 163, 0.25);
            border-radius: 0.5em;
        }

        .scroll-items {
            width: 100%;
            white-space: nowrap;
            padding-bottom: 50px;
            overflow-x: autscrollo;
            overflow-y: hidden;
            appearance: none;
        }

        .bg-disable {
            background-color: #EDF0F7;
        }

        .img-disable {
            filter: grayscale(100%);
        }

        .reserve {
            font-size: 20px;
        }

        .radio-item-select {
            width: 24px !important;
            height: 24px !important;
        }

        .item-select-img {
            max-width: 75px;
            width: 100%;
        }

        .item-select-name {
            color: var(--black, #000);

            /* Bold - 14 - Sarabun */
            font-family: Sarabun;
            font-size: 14px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            text-transform: capitalize;
        }

        .item-select-sub-name {
            color: var(--black, #000);

            /* Reg - 12 - Sarabun */
            font-family: Sarabun;
            font-size: 12px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            text-transform: uppercase;
        }

        .gantt-border {
            border-radius: 100px;
            border: 2px solid rgba(77, 130, 243, 0.30);
        }

        .day-header-border-top {
            border-top: 1px solid var(--neutral-borders-01, #CBD4E1);
            background: var(--genaral-white, #FFF);
        }

        .day-header-border-bottom {
            border-bottom: 1px solid var(--neutral-borders-01, #CBD4E1);
            background: var(--genaral-white, #FFF);
        }

        .day-header-border-left {
            border-left: 1px solid var(--neutral-borders-01, #CBD4E1);
            background: var(--genaral-white, #FFF);
        }

        .head:nth-child(even) {

            border-top: 1px solid var(--neutral-borders-01, #CBD4E1);
            border-bottom: 1px solid var(--neutral-borders-01, #CBD4E1);
            background: white;

        }

        /* Apply styles to odd elements with class "head" */
        .head:nth-child(odd) {
            border-top: 1px solid var(--neutral-borders-01, #CBD4E1);
            border-bottom: 1px solid var(--neutral-borders-01, #CBD4E1);
            background: var(--neutral-bg-01, #F6F8FC);
            /*color: darkgray;*/
        }

        .head:last-child {
            border-right: 1px solid var(--neutral-borders-01, #CBD4E1);
        }

        .rounded-pill {
            border: 1px solid #F6F8FC;
            background-color: #F6F8FC;
            border-radius: 99px;
            padding: 0.25rem 1rem;
            font-size: 14px;
        }

        .seperator {
            border: 1px solid #CBD4E1;
            width: 1px;
            height: 40px;
        }

        .highlight-date {
            background-color: #E5EDFE !important;
            color: #4D82F3 !important;
        }

        .highlight-today {
            background-color: #fee5e5 !important;
            color: #f34d4d !important;
        }

        .card-content-block {
            display: grid;
        }

        .card-content-block .hide {
            visibility: hidden;
        }

        .card-content-block .head {
            border-top: 0px;
            border-bottom: 0px;
        }

        .card-content-block .head:last-child {
            border-right: 0px;
        }

        .timeline-label {
            font-size: 0.7rem;
            vertical-align: center !important;
            margin: 0 !important;
        }

        .box {
            display: block;
            position: relative;
            overflow: hidden;
        }

        /* common */
        .ribbon {
            width: 150px;
            height: 150px;
            overflow: hidden;
            position: absolute !important;
            z-index: 99;
        }

        .ribbon::before,
        .ribbon::after {
            position: absolute;
            z-index: -1;
            content: '';
            display: block;
            border: 5px solid #fdd6d6;
        }

        .ribbon span {
            position: absolute;
            display: block;
            width: 180px;
            padding: 10px 0;
            background-color: #fdd6d6;
            /* box-shadow: 0 5px 10px rgba(0, 0, 0, .1); */
            color: #D83232;
            font-size: 14px;
            text-align: center;
        }

        /* top left*/
        .ribbon-top-left {
            top: -20px;
            left: -50px;
        }

        .ribbon-top-left::before,
        .ribbon-top-left::after {
            border-top-color: transparent;
            border-left-color: transparent;
        }

        .ribbon-top-left::before {
            top: 0;
            right: 0;
        }

        .ribbon-top-left::after {
            bottom: 0;
            left: 0;
        }

        .ribbon-top-left span {
            right: -25px;
            top: 35px;
            transform: rotate(-45deg);
        }

        .box-disable {
            opacity: 80%;
        }

        .box-wrapper {
            max-height: 600px;
            overflow-y: scroll;
        }

        .gantt-card-disable {
            cursor: default;
        }
    </style>
@endpush