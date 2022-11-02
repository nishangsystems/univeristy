@extends('admin.layout')

@section('section')
    <div class="col-sm-12">

        
        
        <div class="" id="printable">
            <div class="">
                <table cellpadding="0" cellspacing="0" border="0" class="" id="hidden-table-info">
                    <thead>
                        <div id="letter-head">
                            <img src="{{asset('assets/images/header.jpg')}}" alt="" class="w-100 img">
                        </div>
                        <tr class="text-capitalize bg-light">
                            <th>#</th>
                            <th>{{__('text.word_name')}}</th>
                            <th>{{__('text.word_class')}}</th>
                            <th>{{__('text.word_amount')}}</th>
                        </tr>
                    </thead>
                    @php($k = 1)
                    <tbody id="content">
                        @foreach($students ?? [] as $student)
                            <tr class="border-bottom">
                                <td class="border-left border-right">{{$k++}}</td>
                                <td class="border-left border-right">{{$student['name']}}</td>
                                <td class="border-left border-right">{{$student['class']}}</td>
                                <td class="border-left border-right">{{$student['total']}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    // $(document).ready(function(){
    //     let printable = $('#printable').html();
    //     let body = document.body.innerHTML;
    //     document.body.innerHTML = printable;
    // })
        // $(document).html(body);


</script>
@endsection
