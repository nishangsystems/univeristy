@extends('admin.layout')
@section('section')
    <div class="my-4 d-flex justify-content-end py-2">
        <button class="btn btn-primary rounded btn-sm text-uppercase" onclick="printClearance()">print</button>
    </div>
    <div class="py-3 px-4 border" id="clearance_panel">
        {{-- <div class="text-center h3 font-weight-bold my-4 text-underline">{{ $data['institution']->name }}</div> --}}
        <div class="d-flex justify-content-center py-2 my-4">
            <img src="{{ $helpers->getHeader() }}" alt="" srcset="" style="width: 100%; height: auto;">
        </div>
        <div class="py-2">
            <div class="font-weight-bold h6">@lang('text.department_of_finance')</div>
            <small class="text-capitalize">@lang('text.our_reference') : <span class="font-weight-semibold text-underline">21/24/2931</span></small>
        </div>
        <div class="text-center h4 font-weight-bold my-4 text-uppercase text-underline">@lang('text.to_whom_it_may_concern')</div>
        <div class="h6 font-weight-bold my-2 text-uppercase text-underline">@lang('text.fees_clearance')</div>
        @if ($data['fee_cleared'])
            <div>{!! __('text.clearance_text', ['name'=>$data['student']->name, 'matric'=>$data['student']->matric, 'program'=>$data['degree']->deg_name." IN ".$data['program']->parent->name, 'school'=>$data['school']->name, 'adm_year'=>$data['clearance']->first()->_year, 'fin_year'=>$data['clearance']->last()->_year]) !!}</div>
        @else
            <div>{!! __('text.clearance_text_debt', ['name'=>$data['student']->name, 'matric'=>$data['student']->matric, 'program'=>$data['degree']->deg_name." IN ".$data['program']->parent->name, 'school'=>$data['school']->name, 'adm_year'=>$data['clearance']->first()->_year, 'fin_year'=>$data['clearance']->last()->_year, 'debt'=>$data['debt']]) !!}</div>
        @endif
        <table class="my-5 border">
            <thead class="text-uppercase border-top border-bottom">
                <th class="border-left border-right">@lang('text.word_year')/@lang('text.word_level')</th>
                <th class="border-left border-right">@lang('text.tution_fees_paid')</th>
                <th class="border-left border-right">@lang('text.word_scholarship')</th>
                <th class="border-left border-right">@lang('text.registration_fees_paid')</th>
            </thead>
            <tbody>
                @foreach($data['clearance'] as $key => $value)
                    <tr class="border-top border-bottom">
                        <td class="border-left border-right">{{ $value->_year }}</td>
                        <td class="border-left border-right">{{ $value->paid }}</td>
                        <td class="border-left border-right">{{ $value->scholarship }}</td>
                        <td class="border-left border-right">{{ $value->reg??0 }}</td>
                    </tr>
                @endforeach
                <tr class="border-top border-bottom">
                    <th class="border-left border-right text-uppercase">@lang('text.word_total')</th>
                    <th class="border-left border-right">{{ $data['clearance']->sum('paid') }}</th>
                    <th class="border-left border-right">{{ $data['clearance']->sum('scholarship') }}</th>
                    <th class="border-left border-right">{{ $data['total_reg_paid'] }}</th>
                </tr>
            </tbody>
        </table>

        <div class="margin-top-5 padding-top-5 row">
            <div class="col-sm-7 my-1 py-2 d-flex">
                <span class="text-uppercase font-weight-bold">checked by:</span>
                <div style="flex:auto;" class="border-bottom py-2"></div>
            </div>
            <div class="col-sm-5 my-1 py-2 d-flex">
                <span class="text-uppercase font-weight-bold">signature:</span>
                <div style="flex:auto;" class="border-bottom py-2"></div>
            </div>
        </div>
        <div class="margin-top-2 padding-top-2 d-flex justify-content-center">
            <div class="col-sm-7 my-1 py-2 d-flex">
                <span class="text-uppercase font-weight-bold">approved by:</span>
                <div style="flex:auto;" class="border-bottom py-2"></div>
            </div>
        </div>
        <div class="margin-top-2 padding-top-2 row">
            <div class="col-sm-5 my-1 py-2 d-flex">
                <span class="text-uppercase font-weight-bold">signature:</span>
                <div style="flex:auto;" class="border-bottom py-2"></div>
            </div>
            <div class="col-sm-4 my-1 py-2 d-flex">
                <span class="text-uppercase font-weight-bold">on the:</span>
                <div style="flex:auto;" class="border-bottom py-2"></div>
            </div>
        </div>
        <div class="margin-top-5 padding-top-5 d-flex justify-content-center">
                <span class="text-uppercase font-weight-bold">this clearance is issued once</span>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        let printClearance = function(){

            let printable = $('#clearance_panel');
            let docBody = $(document.body).html();
            $(document.body).html(printable);
            window.print();
            $(document.body).html(docBody);
            
            // save clearance record to database
            // let url = "{{ Request::url() }}";
            // let token = "{{ csrf_token() }}";
            // $({
            //     method: 'POST', url: url, data: {'_token': token},
            //     success: function(data){
            //         console.log($data);
            //         alert(data.message);
            //     },
            //     error: function(error){
            //         console.log(error);
            //         alert(err)
            //     }
            // });

        }
    </script>
@endsection