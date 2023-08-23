@extends('admin.layout')
@section('section')
@php
$user = \Auth()->user();
$year = \App\Helpers\Helpers::instance()->getCurrentAccademicYear();
$students = \App\Models\StudentClass::where('year_id', $year)->join('students', 'students.id', '=', 'student_classes.student_id')->distinct()->get(['students.id', 'students.active']);
$active_students = $students->where('active', 1);
$inactive_students = $students->where('active', 0);
$n_programs = \App\Models\SchoolUnits::where('unit_id', 4)->count();
$sms_total = \App\Models\Config::where('year_id', $year)->first()->sms_sent;
$n_teachers = \App\Models\User::where('type', 'teacher')->count();
$total_fee_expected = 1;
$total_fee_paid = 1;
$total_fee_owed = 1;
// dd($n_teachers);
@endphp
<div>
    <div class="row">
        {{-- <div class="space-6"></div> --}}

        <div class="col-md-6 col-lg-6 infobox-container text-capitalize">
            <div class="infobox border border-dark mx-1 my-1 rounded infobox-green">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-users"></i>
                </div>

                <div class="infobox-data">
                    <span class="infobox-data-number">{{ count($active_students) }}</span>
                    <div class="infobox-content">{{ __('text.active_students') }}</div>
                </div>

                <div class="stat stat-success">{{ number_format(100*count($active_students)/count($students), 2) }}%</div>
            </div>

            <div class="infobox border border-dark mx-1 my-1 rounded infobox-black">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-users"></i>
                </div>

                <div class="infobox-data">
                    <span class="infobox-data-number">{{ count($inactive_students) }}</span>
                    <div class="infobox-content">{{ __('text.inactive_students') }}</div>
                </div>

                <div class="badge badge-dark">
                    {{ number_format(100*count($inactive_students)/count($students), 2) }}%
                    <i class="ace-icon fa fa-arrow-up"></i>
                </div>
            </div>

            <div class="infobox border border-dark mx-1 my-1 rounded infobox-pink">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-user"></i>
                </div>

                <div class="infobox-data">
                    <span class="infobox-data-number">{{ $n_teachers }}</span>
                    <div class="infobox-content">{{ __('text.word_teachers') }}</div>
                </div>
            </div>

            <div class="infobox border border-dark mx-1 my-1 rounded infobox-red">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-flask"></i>
                </div>

                <div class="infobox-data">
                    <span class="infobox-data-number">{{ $n_programs }}</span>
                    <div class="infobox-content">{{ __('text.word_programs') }}</div>
                </div>
            </div>

            <div class="infobox border border-dark mx-1 my-1 rounded infobox-purple">
                <div class="infobox-icon">
                    <a class="ace-icon fa fa-bell"></a>
                </div>

                <div class="infobox-data">
                    <span class="infobox-data-number">{{ $sms_total }}</span>
                    <div class="infobox-content">{{ __('text.total_sms_sent') }}</div>
                </div>

            </div>

            <div class="infobox border border-dark mx-1 my-1 rounded infobox-blue2">
                <div class="infobox-icon">
                    <a class="ace-icon fa fa-money fa-spin"></a>
                </div>
                <div class="infobox-data">
                    <span class="infobox-text">{{ $total_fee_expected }}</span>

                    <div class="infobox-content">
                        {{ __('text.total_fee_expected') }}
                    </div>
                </div>
            </div>
            <div class="infobox border border-dark mx-1 my-1 rounded infobox-green">

                <div class="infobox-icon">
                    <i class="ace-icon fa fa-money fa-spin"></i>
                </div>

                <div class="infobox-data">
                    <span class="infobox-text">{{ $total_fee_paid }}</span>

                    <div class="infobox-content">
                        {{ __('text.total_fee_paid') }}
                    </div>
                </div>
                <div class="stat stat-success">{{ number_format(100*$total_fee_paid/$total_fee_expected) }}%</div>
            </div>
            <div class="infobox border border-dark mx-1 my-1 rounded infobox-black">
                <div class="infobox-icon">
                    <i class="ace-icon fa fa-money fa-spin"></i>
                </div>

                <div class="infobox-data">
                    <span class="infobox-text">{{ $total_fee_owed }}</span>

                    <div class="infobox-content">
                        {{ __('text.total_fee_owed') }}
                    </div>
                </div>
                <div class="stat stat-dark">{{ number_format(100*$total_fee_paid/$total_fee_expected) }}%</div>
            </div>
        </div>

        {{-- <div class="vspace-12-sm"></div> --}}

        <div class="col-md-6 col-lg-6">
            <div class="widget-box">
                <div class="widget-header widget-header-flat widget-header-small">
                    <h5 class="widget-title">
                        <i class="ace-icon fa fa-signal"></i>
                        Fee Statistics
                    </h5>

                    {{-- <div class="widget-toolbar no-border">
                        <div class="inline dropdown-hover">
                            <button class="btn btn-minier btn-primary">
                                This Week
                                <i class="ace-icon fa fa-angle-down icon-on-right bigger-110"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-right dropdown-125 dropdown-lighter dropdown-close dropdown-caret">
                                <li class="active">
                                    <a href="#" class="blue">
                                        <i class="ace-icon fa fa-caret-right bigger-110">&nbsp;</i>
                                        This Week
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <i class="ace-icon fa fa-caret-right bigger-110 invisible">&nbsp;</i>
                                        Last Week
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <i class="ace-icon fa fa-caret-right bigger-110 invisible">&nbsp;</i>
                                        This Month
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <i class="ace-icon fa fa-caret-right bigger-110 invisible">&nbsp;</i>
                                        Last Month
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div> --}}
                </div>

                <div class="widget-body">
                    <div class="widget-main">
                        <div id="piechart-placeholder"></div>

                        <div class="hr hr8 hr-double"></div>

                        {{-- <div class="clearfix">
                            <div class="grid3">
                                <span class="grey">
                                    <i class="ace-icon fa fa-facebook-square fa-2x blue"></i>
                                    &nbsp; likes
                                </span>
                                <h4 class="bigger pull-right">1,255</h4>
                            </div>

                            <div class="grid3">
                                <span class="grey">
                                    <i class="ace-icon fa fa-twitter-square fa-2x purple"></i>
                                    &nbsp; tweets
                                </span>
                                <h4 class="bigger pull-right">941</h4>
                            </div>

                            <div class="grid3">
                                <span class="grey">
                                    <i class="ace-icon fa fa-pinterest-square fa-2x red"></i>
                                    &nbsp; pins
                                </span>
                                <h4 class="bigger pull-right">1,050</h4>
                            </div>
                        </div> --}}
                    </div><!-- /.widget-main -->
                </div><!-- /.widget-body -->
            </div><!-- /.widget-box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
</div>
@endsection
@section('script')

@endsection
