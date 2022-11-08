@extends('admin.layout')

@section('section')
    <div class="col-sm-12">

        <div id="section">
            <div class="form-group">
                <div>
                    <form method="get" action="{{Request::url().'/list'}}" target="_new">
                        <div class="input-group input-group-merge border">
                            <select class="w-100 border-0 section form-control" id="year" name="year" required>
                                <option selected class="text-capitalize">{{__('text.select_year')}}</option>
                                @forelse(\App\Models\Batch::all() as $batch)
                                    <option value="{{$batch->id}}">{{$batch->name}}</option>
                                @endforeach
                            </select>
                            <select class="w-100 border-0  section form-control" name="class" id="class" required>
                                <option selected class="text-capitalize">{{__('text.select_class')}}</option>
                                @forelse(\App\Http\Controllers\Controller::sorted_program_levels() as $pl)
                                    <option value="{{$pl['id']}}">{{$pl['name']}}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="border-0 text-uppercase" >{{__('text.word_get')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Amount</th>
                        @if(request('type','completed') != 'completed') <th></th> @endif
                    </tr>
                    </thead>
                    <tbody id="content">
                        @foreach($students ?? [] as $student)
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>

        function getStudents(){

            year = $("#year_id").val();
            clss = $("#class_id").val();
// console.log(year +' : '+ clss);
            url = "{{route('student-fee-search')}}";
            $(".pre-loader").css("display", "block");
            $.ajax({
                type: "GET",
                url: url,
                data:{
                    'year': year,
                    'class':clss,
                    'type':'{{request("type", "completed")}}',
                },
                success: function (resp) {
                    let html = "";
                    let students = resp.students;
                    let i = 1
                    for (const key in students) {
                        console.log(typeof(students));
                        html += '<tr>'+
                            '<td>'+(i++)+'</td>'+
                            '<td>'+students[key].name+'</td>'+
                            '<td>'+students[key].class+'</td>'+
                            '<td>'+students[key].total+'</td>'+
                            '@if(request("type","completed") != "completed")'+
                                '<td class="d-flex justify-content-between align-items-center">'+
                                    '<a class="btn btn-xs btn-primary text-capitalize" href="'+students[key].link+'"> {{__("text.fee_collections")}}</a>'+
                                '</td>'+ 
                            '@endif'+
                        '</tr>';
                    }
                    $('#content').html(html)
                    $('title').html(resp.title)
                    $(".pre-loader").css("display", "none");
                }, 
                error: function (e) {
                    $(".pre-loader").css("display", "none");
                }
            });
        }
    </script>
@endsection
