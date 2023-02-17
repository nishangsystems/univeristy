@extends('admin.layout')

@section('section')
    <div class="col-sm-12">

        <form action="{{Request::url().'/listing'}}" method="get" target="_new">
            <div id="section">

                <input type="hidden" name="type" id="" value="uncompleted">
                <div class="form-group">
                    <div class="col-lg-12 mb-4">
                        <input class="form-control" type="number" id="amount" name="amount" placeholder="{{__('text.phrase3')}}"  type="text" required/>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-lg-12 mb-4">
                        <div class="input-group input-group-merge border">
                            <select class="w-100   section form-control" id="section0" name="class" required>
                                <option selected class="text-capitalize">{{__('text.select_class')}}</option>
                                @forelse(\App\Http\Controllers\Controller::sorted_program_levels() as $pl)
                                    <option value="{{$pl['id']}}">{{$pl['name']}}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="border-0 text-uppercase" >{{__('text.word_get')}}</button>
                        </div>
                        <div class="children"></div>
                    </div>
                </div>
            </div>
        </form>


        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                    <tr class="text-capitalize">
                        <th>#</th>
                        <th>{{__('text.word_name')}}</th>
                        <th>{{__('text.word_class')}}</th>
                        <th>{{__('text.amount_paid')}}</th>
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
        
        function getStudent(div){
            if($('#section0').val() == null){
                alert("Invalid Class or Section")
            }

            url = "{{route('student-fee-search')}}";
            $(".pre-loader").css("display", "block");
            $.ajax({
                type: "GET",
                url: url,
                data:{
                    'class':$('#section0').val(),
                    'bal':$('#amount').val(),
                    'type':'{{request('type', 'uncompleted')}}',
                },
                success: function (data) {
                    let html = "";
                    amount = ($('#amount').val() == '')?0:($('#amount').val());
                   var j= 0
                    for (const i in data.students) {
                       if(data.students[i].total < amount){
                           html += '<tr>' +
                               '    <td>'+(j+1)+'</td>' +
                               '    <td>'+data.students[i].name+'</td>' +
                               '    <td>'+data.students[i].class+'</td>' +
                               '    <td>'+data.students[i].total+'</td>' +
                               '@if(request("type","completed") != "completed")    <td class="d-flex justify-content-between align-items-center">' +
                               '        <a class="btn btn-xs btn-primary text-capitalize" href="'+data.students[i].link+'"> {{__("text.fee_collections")}}</a>' +
                               '    </td> @endif'+
                                   '</tr>';
                                   j++;
                       }
                    }
                    $('#content').html(html)
                    $('#title').html(data.title)
                    $(".pre-loader").css("display", "none");
                },
                error: function (e) {
                    $(".pre-loader").css("display", "none");
                }
            });
        }
    </script>
@endsection
