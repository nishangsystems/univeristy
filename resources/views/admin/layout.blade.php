<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>{{$title ?? ''}} | {{__('text.app_name')}}</title>

    <meta name="description" content="overview &amp; stats" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="{{url('/')}}/public/assets/css/*.css" />

    <link rel="stylesheet" href="{{asset('css/app.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
    <!-- <link rel="stylesheet" href="{{asset('assets/css/*.css')}}" /> -->
    <link rel="stylesheet" href="{{asset('assets/font-awesome/4.5.0/css/font-awesome.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/fonts.googleapis.com.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/ace.min.css')}}" class="ace-main-stylesheet" id="main-ace-style" />
    <link rel="stylesheet" href="{{asset('assets/css/ace-part2.min.css')}}" class="ace-main-stylesheet" />
    <![endif]-->
    <link rel="stylesheet" href="{{asset('assets/css/ace-skins.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/ace-rtl.min.css')}}"/>
    <script src="{{asset('assets/js/ace-extra.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('assets/css/custom.css')}}" class="ace-main-stylesheet"
          id="main-ace-style"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('libs')}}/datatables.net-bs4/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('libs')}}/datatables.net-bs4/css/responsive.dataTables.min.css">

    <!-- <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-show-password/1.0.3/bootstrap-show-password.min.js"></script> -->

    <STYLE>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }
        .input-group {
            position: relative;
            display: flex;
            flex-wrap: nowrap;
            align-items: stretch;
            width: 100%;
        }
        .dt-button{
            background-image: none!important;
            border: 1px solid #FFF;
            border-radius: 0;
            padding: 5px 20px;
            border-radius: 5px;
            box-shadow: none!important;
            -webkit-transition: background-color .15s,border-color .15s,opacity .15s;
            -o-transition: background-color .15s,border-color .15s,opacity .15s;
            transition: background-color .15s,border-color .15s,opacity .15s;
            vertical-align: middle;
            margin: 0;
            position: relative;
        }
        table{padding: 0px !important}
        table th, table td{
            padding: 10px;
        }
        .table td{
            border-bottom: 1px  solid  #f1f1f1 !important;
        }
        .nav li {
            display: block;
            width: 100% !important;
        }
        .dropdown-toggle:after {
            display: none;
        }
    </STYLE>
</head>

<body class="no-skin">
<div class="pre-loader">
    <div class="sk-fading-circle">
        <div class="sk-circle1 sk-circle"></div>
        <div class="sk-circle2 sk-circle"></div>
        <div class="sk-circle3 sk-circle"></div>
        <div class="sk-circle4 sk-circle"></div>
        <div class="sk-circle5 sk-circle"></div>
        <div class="sk-circle6 sk-circle"></div>
        <div class="sk-circle7 sk-circle"></div>
        <div class="sk-circle8 sk-circle"></div>
        <div class="sk-circle9 sk-circle"></div>
        <div class="sk-circle10 sk-circle"></div>
        <div class="sk-circle11 sk-circle"></div>
        <div class="sk-circle12 sk-circle"></div>
    </div>
</div>

<div id="navbar" class="navbar navbar-default  ace-save-state">
    <div class="navbar-container w-100 ace-save-state" id="navbar-container">
        <button type="button" class="navbar-toggle menu-toggler pull-left display" id="menu-toggler"
                data-target="#sidebar">
            <span class="sr-only">Toggle sidebar</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>

        <div class="navbar-header pull-left">
            <a class="navbar-brand">
                <small>
                    <i class="fa fa-leaf"></i>
                    {{config('app-name') ?? __('text.app_name')}}
                </small>
            </a>
        </div>

        <div class="navbar-buttons navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav d-flex flex-nowrap" style="">
                <li class="grenn dropdown-modal">
                    <a data-toggle="dropdown" class="dropdown-toggle text-white font-weight-bold text-capitalize">
                      {{__('text.word_batch')}} : {{\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name}}
                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>

                    <ul class="dropdown-menu">
                       @foreach(\App\Models\Batch::all() as $batch)
                            <li>
                                <a href="{{ route('mode',$batch->id) }}">{{$batch->name}}</a>
                            </li>
                       @endforeach
                    </ul>
                </li>
                <li class="light-blue dropdown-modal">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <img class="nav-user-photo" src="{{asset('assets/images/avatars/user.jpg')}}"
                             alt="Jason's Photo"/>
                        <span>
						<small class="text-capitalize">{{__('text.word_welcome')}}</small>
                         {{\Auth::user()->name}}
						</span>

                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>

                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">

                        <li>
                            @if(\Auth::guard('student')->user() == null)
                                @if(\Auth::user()->isHod || \Auth::user()->isTeacher)
                                    <a href="{{route('user.home')}}" class="text-capitalize"><i
                                            class="ace-icon fa fa-user"></i>__('text.word_profile')</a>
                                @elseif(\Auth::user()->isAdmin)
                                    <a href="{{route('admin.home')}}" class="text-capitalize"><i
                                            class="ace-icon fa fa-user"></i>__('text.word_profile')</a>
                                @endif
                            @else
                                <a href="{{route('student.home')}}" class="text-capitalize"><i
                                        class="ace-icon fa fa-user"></i>__('text.word_profile')</a>
                            @endif
                        </li>
                        <li>
            <!--                 <a href="{{ route('logout') }}" onclick="event.preventDefault();-->
												<!--document.getElementById('logout-form').submit();" class="text-capitalize">-->
            <!--                    <i class="ace-icon fa fa-power-off"></i>-->
            <!--                    __('text.word_logout')-->
            <!--                </a>-->
                            <a href="{{ route('logout') }}" onclick="event.preventDefault();
												document.getElementById('logout-form').submit();" class="text-capitalize">
                                <i class="ace-icon fa fa-power-off"></i>
                               logout
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div><!-- /.navbar-container -->
</div>
<div class="main-container ace-save-state" id="main-container">

    <div id="sidebar" class="sidebar                  responsive                    ace-save-state">
        <script type="text/javascript">
            try {
                ace.settings.loadState('sidebar')
            } catch (e) {
            }
        </script>

        <div class="sidebar-shortcuts" id="sidebar-shortcuts" style="background-color: #dbfce1; color: #007139;">
            <div>
                <h5>{{\App\Models\Batch::find(\App\Helpers\Helpers::instance()->getCurrentAccademicYear())->name}}</h5>
            </div>
        </div><!-- /.sidebar-shortcuts -->
        <ul class="nav nav-list">
            <li>
                <a
                    href="{{route('admin.home')}}">
                    <i class="menu-icon text-primary fa fa-dashboard"></i>
                    <span class="menu-text text-capitalize">{{__('text.word_dashboard')}}</span>
                </a>
                <b class="arrow"></b>
            </li>
            @if (\Auth::user()->hasPermissionTo('manage_student'))
                <li>
                    <a href="#" class="dropdown-toggle">
                        <i class="menu-icon  text-primary fa fa-graduation-cap"></i>
                        <span class="menu-text text-capitalize"> {{__('text.student_management')}}t</span>

                        <b class="arrow fa fa-angle-down"></b>
                    </a>

                    <b class="arrow"></b>

                    <ul class="submenu">
                        <li>
                            <a href="{{route('admin.student.create')}}" class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.enroll_student')}}
                            </a>
                            <b class="arrow"></b>
                        </li>
                        <li>
                            <a href="{{route('admin.student.index')}}" class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.enrolled_students')}}
                            </a>
                            <b class="arrow"></b>
                        </li>
                        
                        <li>
                            <a href="{{route('admin.class.list')}}?action=class_list"  class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.class_list')}}
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li>
                            <a href="{{route('admin.students.matricule')}}"  class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{__('text.generate_matricule')}}
                            </a>
                            <b class="arrow"></b>
                        </li>

                        <li>
                            <a href="{{route('admin.students.import')}}"  class="text-capitalize">
                                <i class="menu-icon fa fa-caret-right"></i>
                                {{trans_choice('text.import_student', 2)}}
                            </a>
                            <b class="arrow"></b>
                        </li>

                    </ul>
                </li>
            @endif
            
            @if (\Auth::user()->hasPermissionTo('promote_students'))
                <li>
                    <a href="{{route('admin.students.init_promotion')}}"  class="text-capitalize">
                        <strong class=" menu-icon text-primary ">&leftrightharpoons;</strong>
                        {{__('text.promote_students')}}
                    </a>
                    <b class="arrow"></b>
                </li>
            @endif
            @if (\Auth::user()->hasPermissionTo('approve_promotion'))
                <li>
                    <a href="{{route('admin.students.trigger_approval')}}" class="text-capitalize">
                        <strong class="menu-icon text-primary">&Rrightarrow;</strong>
                        {{__('text.validate_promotion')}}
                    </a>
                    <b class="arrow"></b>
                </li>
            @endif
            @if (\Auth::user()->hasPermissionTo('manage_setting'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                    <i class="menu-icon  text-primary fa fa-cog"></i>
                    <span class="menu-text"> {{__('text.settings_zone')}}</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.setayear')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.set_school_year')}}
                        </a>

                        <b class="arrow"></b>
                    </li>

                     <li>
                        <a href="{{route('admin.boarding_fee.index')}}"  class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.set_boarding_fee')}}
                        </a>

                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.sections')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.manage_sections')}}
                        </a>

                        <b class="arrow"></b>
                    </li>
                    
                    <li>
                        <a href="{{route('admin.programs.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.manage_programs')}}
                        </a>

                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.result_release.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                           {{__('text.add_result_release')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    
                    <li>
                        <a href="{{route('admin.schools.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                           {{__('text.manage_school')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li>
                        <a href="{{route('admin.campuses.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                           {{__('text.manage_campuses')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif

            @if (\Auth::user()->hasPermissionTo('manage_setting'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                    <i class="menu-icon  text-primary fa fa-signal"></i>
                    <span class="menu-text"> {{__('text.statistics_zone')}}</span>

                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <b class="arrow"></b>

                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.stats.students')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.student_statistics')}}
                        </a>

                        <b class="arrow"></b>
                    </li>

                     <li>
                        <a href="{{route('admin.stats.fees')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.fee_statistics')}}
                        </a>

                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.stats.results')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.results_statistics')}}
                        </a>

                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.stats.income')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                           {{__('text.income_statistics')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.stats.expenditure')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                           {{__('text.expenditure_statistics')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif

            @if (\Auth::user()->hasPermissionTo('manage_subject'))
            <li>
                <a href="" class="dropdown-toggle text-capitalize">
                    <i class="menu-icon  text-primary fa fa-book"></i>
                    <span class="menu-text">
							{{__('text.course_zone')}}
					</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.subjects.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.all_courses')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li>
                        <a href="{{route('admin.subjects.create')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.add_course')}}
                        </a>
                        <b class="arrow"></b>
                    </li>


                </ul>
            </li>
            @endif
            
            @if (\Auth::user()->hasPermissionTo('manage_user'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                    <i class="menu-icon  text-primary fa fa-users"></i>
                    <span class="menu-text">
						{{__('text.user_accounts')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>

                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.users.index')}}?type=admin" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{trans_choice('text.add_admin', 2)}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.users.index')}}?type=teacher" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{trans_choice('text.word_teacher', 2)}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.users.classmaster')}}?type=teacher" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                           {{trans_choice('text.class_master', 2)}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.roles.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                           {{trans_choice('text.role', 2)}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif

            @if (\Auth::user()->hasPermissionTo('manage_fee'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                    <i class="menu-icon  text-primary fa fa-eur"></i>
                    <span class="menu-text">
						{{__('text.fee_managenent')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>


                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.fee.collect')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.collect_fee')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                      <li>
                        <a href="{{route('admin.collect_boarding_fee.create')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.collect_boarding_fee')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.print_fee')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.print_reciept')}}
                        </a>
                        <b class="arrow"></b>
                    </li>


                    <li>
                        <a href="{{route('admin.fee.daily_report')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.daily_reports')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                     <li>
                        <a href="{{route('admin.collect_boarding_fee.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.boarding_fees')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                    <li>
                        <a href="{{route('admin.fee')}}?type=completed" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.completed_fees')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.fee')}}?type=uncompleted" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.uncompleted_fees')}}
                        </a>
                        <b class="arrow"></b>
                    </li>


                    <li>
                        <a href="{{route('admin.fee.drive')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.fee_drive')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.fee.classes')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.set_class_fee')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif

            @if (\Auth::user()->hasPermissionTo('manage_result'))
            <li>
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon  text-primary fa fa-rocket"></i>
                    <span class="menu-text text-capitalize">
						{{__('text.results')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>


                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.result.import')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.import_results')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                    <li>
                        <a href="{{route('admin.result.export')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                           {{__('text.export_results')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif

            @if (\Auth::user()->hasPermissionTo('manage_scholarship'))
            <li>
                <a href="#" class="dropdown-toggle">
                <i class="menu-icon text-primary fa fa-graduation-cap"></i>
                    <span class="menu-text text-capitalize">
						{{trans_choice('text.scholarship', 2)}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>


                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.scholarship.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{trans_choice('text.scholarship', 2)}}
                        </a>
                        <b class="arrow"></b>
                    </li>


                    <li>
                        <a href="{{route('admin.scholarship.eligible')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.award_scholarship')}}
                        </a>
                        <b class="arrow"></b>
                    </li>

                     <li>
                        <a href="{{route('admin.scholarship.awarded_students')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                           {{trans_choice('text.our_scholar', 2)}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif

            @if (\Auth::user()->hasPermissionTo('manage_incomes'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                <i class="menu-icon text-primary fa fa-money"></i>
                    <span class="menu-text">
						{{__('text.other_incomes')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>


                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.income.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.income_list')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                     <li>
                        <a href="{{route('admin.income.pay_income.create')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.collect_income')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                     <li>
                        <a href="{{route('admin.pay_income.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.paid_incomes')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif


            @if (\Auth::user()->hasPermissionTo('manage_expenses'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                <i class="menu-icon text-primary fa fa-spinner"></i>
                    <span class="menu-text">
						{{__('text.school_expenses')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>


                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.expense.index')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.expense_list')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif

            @if (\Auth::user()->hasPermissionTo('manage_school_debts'))
            <li>
                <a href="#" class="dropdown-toggle text-capitalize">
                <i class="menu-icon fa fa-money"></i>
                    <span class="menu-text">
						{{__('text.school_debts')}}
						</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="{{route('admin.debts.schoolDebts')}}" class="text-capitalize">
                            <i class="menu-icon fa fa-caret-right"></i>
                            {{__('text.manage_school_debts')}}
                        </a>
                        <b class="arrow"></b>
                    </li>
                </ul>
            </li>
            @endif

            <li>
                <a onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   href="{{route('logout')}}" class="text-capitalize">
                    <i class="menu-icon text-primary fa fa-lock"></i>
                    <span class="menu-text">	{{__('text.word_logout')}}</span>
                </a>
                <b class="arrow"></b>
            </li>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>


        </ul><!-- /.nav-list -->


        <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
            <i id="sidebar-toggle-icon" class="ace-icon f ace-save-state"></i>
        </div>
    </div>
    <div class="main-content">
        <div class="main-content-inner">

            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                <ul class="breadcrumb text-capitalize">
                    <li>
                        <i class="ace-icon fa fa-home home-icon"></i>
                        <a href="#">{{__('text.word_home')}}</a>
                    </li>
                    <li class="active">{{__('text.word_dashboard')}}</li>
                    <li class="active"> {{__('text.fullname')}} : <b style="color: #e30000">{{\Auth::user()->name}}</b></li>

                </ul><!-- /.breadcrumb -->
            </div>

            <div class="m-5">
                @if(Session::has('success'))
                    <div class="alert alert-success fade in">
                        <strong>Success!</strong> {{Session::get('success')}}
                    </div>
                @endif

                @if(Session::has('error'))
                    <div class="alert alert-danger fade in">
                        <strong>Error!</strong> {{Session::get('error')}}
                    </div>
                @endif


                <div class="mb-4 mx-3">
                    <h4 id="title" class="font-weight-bold text-capitalize">{{ $title ?? '' }}</h4>
                </div>
                @yield('section')
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <div class="footer-inner">
        <div class="footer-content" style="background:#fff">
            <span class="bigger-120">
               &copy; {{__('text.copyright')}}
            </span>
            &nbsp; &nbsp;
            <span class="action-buttons">
                <a href="#">
                    <i class="ace-icon fa fa-twitter-square light-blue bigger-150"></i>
                </a>

                <a href="#">
                    <i class="ace-icon fa fa-facebook-square text-primary bigger-150"></i>
                </a>

                <a href="#">
                    <i class="ace-icon fa fa-rss-square orange bigger-150"></i>
                </a>
            </span>
        </div>
    </div>
</div>

<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
    <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
</a>
</div>
<script src="{{asset('assets/js/jquery-2.1.4.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
<script src="{{ asset('assets/vendor/toastr/toastr.min.js') }}"></script>
<script src="{{asset('assets/js/ace.min.js')}}"></script>
<script src="{{ asset('libs')}}/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('libs')}}/datatables.net-bs4/js/dataTables.responsive.min.js"></script>


<script>
    $(function () {
        $('.table , .adv-table table').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel',
                {
                    text: 'Download PDF',
                    extend: 'pdfHtml5',
                    message: '',
                    orientation: 'portrait',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (doc) {
                        doc.pageMargins = [10,10,10,10];
                        doc.defaultStyle.fontSize = 7;
                        doc.styles.tableHeader.fontSize = 7;
                        doc.styles.title.fontSize = 9;
                        doc.content[0].text = doc.content[0].text.trim();

                        doc['footer']=(function(page, pages) {
                            return {
                                columns: [
                                    "{{ $title ?? '' }}",
                                    {
                                        // This is the right column
                                        alignment: 'right',
                                        text: ['page ', { text: page.toString() },  ' of ', { text: pages.toString() }]
                                    }
                                ],
                                margin: [10, 0]
                            }
                        });
                        // Styling the table: create style object
                        var objLayout = {};
                        // Horizontal line thickness
                        objLayout['hLineWidth'] = function(i) { return .5; };
                        // Vertikal line thickness
                        objLayout['vLineWidth'] = function(i) { return .5; };
                        // Horizontal line color
                        objLayout['hLineColor'] = function(i) { return '#aaa'; };
                        // Vertical line color
                        objLayout['vLineColor'] = function(i) { return '#aaa'; };
                        // Left padding of the cell
                        objLayout['paddingLeft'] = function(i) { return 4; };
                        // Right padding of the cell
                        objLayout['paddingRight'] = function(i) { return 4; };
                        // Inject the object in the document
                        doc.content[1].layout = objLayout;
                    }
                }

            ],
            info:     false,
            searching: true,
            // order: [
            //     [1, 'asc']
            // ],
        });

    });


</script>

<script src="{{ asset('libs')}}/datatables.net/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('libs')}}/datatables.net/js/jszip.min.js"></script>
<script src="{{ asset('libs')}}/datatables.net/js/pdfmake.min.js"></script>
<script src="{{ asset('libs')}}/datatables.net/js/vfs_fonts.js"></script>
<script src="{{ asset('libs')}}/datatables.net/js/buttons.html5.min.js"></script>

<script>
    (function($){
        'use strict';
        $(window).on('load', function () {
            if ($(".pre-loader").length > 0)
            {
                $(".pre-loader").fadeOut("slow");
            }
        });
    })(jQuery)
</script>
@yield('script')
</body>
</html>
