@extends('admin.layout')

@section('section')
    <div class="col-sm-12">

        <div id="section">

            <div class="form-group">
                <div class="col-lg-12 mb-4">
                    <input class="form-control" type="number" id="amount" name="amount" placeholder="{{__('text.phrase3')}}"  type="text" required/>
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-12 mb-4">
                    <div class="input-group input-group-merge border">
                        <select class="w-100 border-0 section" id="section0">
                            <option selected disabled class="text-capitalize">{{__('text.select_section')}}</option>
                            @forelse(\App\Models\SchoolUnits::where('parent_id',0)->get() as $section)
                                <option value="{{$section->id}}">{{$section->name}}</option>
                            @empty
                                <option class="text-capitalize">{{__('text.no_sections_created')}}</option>
                            @endforelse
                        </select>
                        <button type="submit" onclick="getStudent($(this))" class="border-0 text-uppercase" >{{__('text.word_get')}}</button>
                    </div>
                    <div class="children"></div>
                </div>
            </div>
        </div>


        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_class')}}</th>
                        <th>{{__('text.word_amount')}}</th>
                        @if(request('type','completed') != 'completed') <th></th> @endif
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
        $('.section').on('change', function () {
            refresh($(this));
        })
        function refresh(div) {
            $(".pre-loader").css("display", "block");
            url = "{{route('section-children', "VALUE")}}";
            url = url.replace('VALUE', div.val());
            $.ajax({
                type: "GET",
                url: url,
                success: function (data) {
                    $(".pre-loader").css("display", "none");
                    let html = "";
                    if(data.valid == 1){
                        $('#save').css("display", "block");
                    }else{
                        $('#save').css("display", "none");
                    }
                    if (data.array.length > 0) {
                        html += '<div class="mt-3"> <div class="input-group input-group-merge border">' +
                            '                        <select onchange="refresh($(this))" class="w-100 border-0 section" name="'+data.name+'" id="section0">';
                        html += '<option selected > Select ' + data.name + '</option>';
                        for (i = 0; i < data.array.length; i++) {
                            html += '<option value="' + data.array[i].id + '">' + data.array[i].name + '</option>';
                        }
                        html += '</select>  <button type="submit" onclick="getStudent($(this))" class="border-0 text-uppercase" >{{__("text.word_get")}}</button>' +
                            '                    </div>' +
                            '<div class="children"></div></div>';
                    }
                    div.parent().parent().find('.children').html(html)
                }, error: function (e) {
                    $(".pre-loader").css("display", "none");
                }
            });
        }

        function getStudent(div){
            if(div.siblings('select').val() == null){
                alert("Invalid Class or Section")
            }

            url = "{{route('student-fee-search')}}";
            $(".pre-loader").css("display", "block");
            $.ajax({
                type: "GET",
                url: url,
                data:{
                    "section":div.siblings('select').val(),
                    'bal':$('#amount').val(),
                    'type':'{{request('type', 'uncompleted')}}',
                },
                success: function (data) {
                    let html = "";
                    amount = ($('#amount').val() == '')?0:($('#amount').val());
                   var j= 0
                    for (i = 0; i < data.students.length; i++) {
                       if(data.students[i].total > amount){
                           html += '<tr>' +
                               '    <td>'+(j+1)+'</td>' +
                               '    <td>'+data.students[i].name+'</td>' +
                               '    <td>'+data.students[i].class+'</td>' +
                               '    <td>'+data.students[i].total+'</td>' +
                               @if(request('type','completed') != 'completed') '    <td class="d-flex justify-content-between align-items-center">' +
                               '        <a class="btn btn-xs btn-primary text-capitalize" href="'+data.students[i].link+'"> {{__("text.fee_collections")}}</a>' +
                               '    </td>' +@endif
                                   '</tr>';
                                   j++;
                       }
                    }
                    $('#content').html(html)
                    $('#title').html(data.title)
                    $(".pre-loader").css("display", "none");
                }, error: function (e) {
                    $(".pre-loader").css("display", "none");
                }
            });
        }
    </script>
@endsection
