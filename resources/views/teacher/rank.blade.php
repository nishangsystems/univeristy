@extends('teacher.layout')
@section('section')
    <form id="ranks" class="col-sm-12">
        @csrf
        <p class="text-muted">
            <h4 id="title" class="mb-4"> Student</h4>
        </p>
        <div id="section">
            <div class="form-group">
                <div>
                    <div class="input-group input-group-merge d-flex flex-nowrap border">
                        <select class="w-100 border-0 section" id="sequence" name="sequence">
                            <option selected disabled>Select Sequence</option>
                            @forelse(\App\Models\Sequence::all() as $seq)
                                <option value="{{$seq->id}}">{{$seq->name}}</option>
                            @empty
                                <option>No Sequence Created</option>
                            @endforelse
                        </select>
                        <button type="button" onclick="getStudent($(this))" class="border-0" >GET</button>
                    </div>

                    <div class="children"></div>
                </div>
            </div>
        </div>

        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Name</th>
                        <th>Matricule</th>
                        <th>Score</th>
                        <th>Average</th>
                    </tr>
                    </thead>
                    <tbody id="content">

                    </tbody>
                </table>
            </div>
        </div>
    </form>
@endsection
@section('script')
    <script>


        function getStudent(div){
            if(div.siblings('select').val() != null){
                url = "{{route('student_rank')}}";
                $(".pre-loader").css("display", "block");
                $.ajax({
                    type: "GET",
                    url: url,
                    data:{
                        "sequence":div.siblings('select').val(),
                        'class':'{{$class->id}}',
                        'year':'{{$year}}',
                    },
                    success: function (data) {
                        let html = "";
                        amount = ($('#amount').val() == '')?0:($('#amount').val());
                        $('.table , .adv-table table').dataTable().fnDestroy();
                        for (i = 0; i < data.students.length; i++) {
                            html += '<tr>' +
                                '    <td>'+(i+1)+'</td>' +
                                '    <td>'+data.students[i].name+'</td>' +
                                '    <td>'+data.students[i].matricule+'</td>' +
                                '    <td>'+data.students[i].total+'</td>' +
                                '    <td>'+data.students[i].average+'</td>' +
                                '<input type="hidden" name="students[]" value="'+data.students[i].id+'">';
                                '</tr>';
                        }

                        $('#content').html(html)
                        $('#title').html(data.title)


                        var t = $('.table , .adv-table table').DataTable( {
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
                                                    '{{ $title ?? '' }}',
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
                            paging:false,
                            searching: false,
                            order: [ [ 3, 'desc' ]],
                            "columns":[
                                {
                                    "sortable": false
                                },
                                {
                                    "sortable": false
                                },
                                {
                                    "sortable": false
                                },
                                {
                                    "sortable": true
                                },
                                {
                                    "sortable": false
                                },
                            ]
                        } );

                        var PageInfo = $('.table , .adv-table table').DataTable().page.info();
                        t.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                            cell.innerHTML = i + 1 + PageInfo.start;
                        } );

                        $.ajax({
                            type: "POST",
                            url: '{{route('student_rank')}}',
                            data: $('#ranks').serialize(),
                            success: function (data) {
                                $(".pre-loader").css("display", "none");
                            }, error: function (e) {
                                $(".pre-loader").css("display", "none");
                            }
                        });


                    }, error: function (e) {
                        $(".pre-loader").css("display", "none");
                    }
                });
            }
        }
    </script>
@endsection
