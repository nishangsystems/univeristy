@extends('teacher.layout')
@section('section')
    <div class="col-sm-12">
        <p class="text-muted">
            <h4 id="title" class="mb-4"> Student</h4>
        </p>
        <div id="section">
            <div class="form-group">
                <div>
                    <div class="input-group input-group-merge d-flex flex-nowrap border">
                        <select class="w-100 border-0 section" id="section0">
                            <option selected disabled>Select Sequence</option>
                            @forelse(\App\Models\Sequence::all() as $seq)
                                <option value="{{$seq->id}}">{{$seq->name}}</option>
                            @empty
                                <option>No Sequence Created</option>
                            @endforelse
                        </select>
                        <button type="submit" onclick="getStudent($(this))" class="border-0" >GET</button>
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
    </div>
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

                        for (i = 0; i < data.students.length; i++) {
                            html += '<tr>' +
                                '    <td>'+(i+1)+'</td>' +
                                '    <td>'+data.students[i].name+'</td>' +
                                '    <td>'+data.students[i].matricule+'</td>' +
                                '    <td>'+data.students[i].total+'</td>' +
                                '    <td>'+data.students[i].average+'</td>' +
                                '</tr>';
                        }
                        $('#content').html(html)
                        $('#title').html(data.title)
                        $(".pre-loader").css("display", "none");
                    }, error: function (e) {
                        $(".pre-loader").css("display", "none");
                    }
                });
            }
        }
    </script>
@endsection
