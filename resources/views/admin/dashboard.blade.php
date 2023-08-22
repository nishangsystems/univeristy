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
<script type="text/javascript">
    if('ontouchstart' in document.documentElement) document.write("<script src=`{{ asset('assets/js/jquery.mobile.custom.min.js') }}`>"+"<"+"/script>");
</script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

<!-- page specific plugin scripts -->

<!--[if lte IE 8]>
    <script src="assets/js/excanvas.min.js"></script>
<![endif]-->
<script src="{{ asset('assets/js/jquery-ui.custom.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.ui.touch-punch.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.easypiechart.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.sparkline.index.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.flot.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.flot.pie.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.flot.resize.min.js') }}"></script>

<!-- ace scripts -->
<script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
<script src="{{ asset('assets/js/ace.min.js') }}"></script>

<!-- inline scripts related to this page -->
<script type="text/javascript">
    jQuery(function($) {
        $('.easy-pie-chart.percentage').each(function(){
            var $box = $(this).closest('.infobox');
            var barColor = $(this).data('color') || (!$box.hasClass('infobox-dark') ? $box.css('color') : 'rgba(255,255,255,0.95)');
            var trackColor = barColor == 'rgba(255,255,255,0.95)' ? 'rgba(255,255,255,0.25)' : '#E2E2E2';
            var size = parseInt($(this).data('size')) || 50;
            $(this).easyPieChart({
                barColor: barColor,
                trackColor: trackColor,
                scaleColor: false,
                lineCap: 'butt',
                lineWidth: parseInt(size/10),
                animate: ace.vars['old_ie'] ? false : 1000,
                size: size
            });
        })
    
        $('.sparkline').each(function(){
            var $box = $(this).closest('.infobox');
            var barColor = !$box.hasClass('infobox-dark') ? $box.css('color') : '#FFF';
            $(this).sparkline('html',
                                {
                                tagValuesAttribute:'data-values',
                                type: 'bar',
                                barColor: barColor ,
                                chartRangeMin:$(this).data('min') || 0
                                });
        });
    
    
        //flot chart resize plugin, somehow manipulates default browser resize event to optimize it!
        //but sometimes it brings up errors with normal resize event handlers
        $.resize.throttleWindow = false;
    
        var placeholder = $('#piechart-placeholder').css({'width':'90%' , 'min-height':'150px'});
        var data = [
        { label: "social networks",  data: 38.7, color: "#68BC31"},
        { label: "search engines",  data: 24.5, color: "#2091CF"},
        { label: "ad campaigns",  data: 8.2, color: "#AF4E96"},
        { label: "direct traffic",  data: 18.6, color: "#DA5430"},
        { label: "other",  data: 10, color: "#FEE074"}
        ]
        function drawPieChart(placeholder, data, position) {
            $.plot(placeholder, data, {
            series: {
                pie: {
                    show: true,
                    tilt:0.8,
                    highlight: {
                        opacity: 0.25
                    },
                    stroke: {
                        color: '#fff',
                        width: 2
                    },
                    startAngle: 2
                }
            },
            legend: {
                show: true,
                position: position || "ne", 
                labelBoxBorderColor: null,
                margin:[-30,15]
            }
            ,
            grid: {
                hoverable: true,
                clickable: true
            }
            })
        }
        drawPieChart(placeholder, data);
    
        /**
        we saved the drawing function and the data to redraw with different position later when switching to RTL mode dynamically
        so that's not needed actually.
        */
        placeholder.data('chart', data);
        placeholder.data('draw', drawPieChart);
    
    
        //pie chart tooltip example
        var $tooltip = $("<div class='tooltip top in'><div class='tooltip-inner'></div></div>").hide().appendTo('body');
        var previousPoint = null;
    
        placeholder.on('plothover', function (event, pos, item) {
        if(item) {
            if (previousPoint != item.seriesIndex) {
                previousPoint = item.seriesIndex;
                var tip = item.series['label'] + " : " + item.series['percent']+'%';
                $tooltip.show().children(0).text(tip);
            }
            $tooltip.css({top:pos.pageY + 10, left:pos.pageX + 10});
        } else {
            $tooltip.hide();
            previousPoint = null;
        }
        
        });
    
        /////////////////////////////////////
        $(document).one('ajaxloadstart.page', function(e) {
            $tooltip.remove();
        });
    
    
    
    
        var d1 = [];
        for (var i = 0; i < Math.PI * 2; i += 0.5) {
            d1.push([i, Math.sin(i)]);
        }
    
        var d2 = [];
        for (var i = 0; i < Math.PI * 2; i += 0.5) {
            d2.push([i, Math.cos(i)]);
        }
    
        var d3 = [];
        for (var i = 0; i < Math.PI * 2; i += 0.2) {
            d3.push([i, Math.tan(i)]);
        }
        
    
        var sales_charts = $('#sales-charts').css({'width':'100%' , 'height':'220px'});
        $.plot("#sales-charts", [
            { label: "Domains", data: d1 },
            { label: "Hosting", data: d2 },
            { label: "Services", data: d3 }
        ], {
            hoverable: true,
            shadowSize: 0,
            series: {
                lines: { show: true },
                points: { show: true }
            },
            xaxis: {
                tickLength: 0
            },
            yaxis: {
                ticks: 10,
                min: -2,
                max: 2,
                tickDecimals: 3
            },
            grid: {
                backgroundColor: { colors: [ "#fff", "#fff" ] },
                borderWidth: 1,
                borderColor:'#555'
            }
        });
    
    
        $('#recent-box [data-rel="tooltip"]').tooltip({placement: tooltip_placement});
        function tooltip_placement(context, source) {
            var $source = $(source);
            var $parent = $source.closest('.tab-content')
            var off1 = $parent.offset();
            var w1 = $parent.width();
    
            var off2 = $source.offset();
            //var w2 = $source.width();
    
            if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
            return 'left';
        }
    
    
        $('.dialogs,.comments').ace_scroll({
            size: 300
        });
        
        
        //Android's default browser somehow is confused when tapping on label which will lead to dragging the task
        //so disable dragging when clicking on label
        var agent = navigator.userAgent.toLowerCase();
        if(ace.vars['touch'] && ace.vars['android']) {
            $('#tasks').on('touchstart', function(e){
            var li = $(e.target).closest('#tasks li');
            if(li.length == 0)return;
            var label = li.find('label.inline').get(0);
            if(label == e.target || $.contains(label, e.target)) e.stopImmediatePropagation() ;
            });
        }
    
        $('#tasks').sortable({
            opacity:0.8,
            revert:true,
            forceHelperSize:true,
            placeholder: 'draggable-placeholder',
            forcePlaceholderSize:true,
            tolerance:'pointer',
            stop: function( event, ui ) {
                //just for Chrome!!!! so that dropdowns on items don't appear below other items after being moved
                $(ui.item).css('z-index', 'auto');
            }
            }
        );
        $('#tasks').disableSelection();
        $('#tasks input:checkbox').removeAttr('checked').on('click', function(){
            if(this.checked) $(this).closest('li').addClass('selected');
            else $(this).closest('li').removeClass('selected');
        });
    
    
        //show the dropdowns on top or bottom depending on window height and menu position
        $('#task-tab .dropdown-hover').on('mouseenter', function(e) {
            var offset = $(this).offset();
    
            var $w = $(window)
            if (offset.top > $w.scrollTop() + $w.innerHeight() - 100) 
                $(this).addClass('dropup');
            else $(this).removeClass('dropup');
        });
    
    })
</script>
@endsection
