@extends('admin.layout')
@section('section')

    @if($data['fee_cleared'])
        <div class="my-4 d-flex justify-content-end py-2">

            <button class="btn btn-primary rounded btn-sm text-uppercase" onclick="printClearance()">print</button>
        </div>
        <div class=" px-4 border watermark-bg" style="font-size: larger !important; line-height:2.7rem;" id="clearance_panel">
            <div class="light-white">
                {{-- <div class="text-center h3 font-weight-bold my-4 text-underline">{{ $data['institution']->name }}</div> --}}
                <div class="d-flex justify-content-center py-1 my-1">
                    <img src="{{ $helpers->getHeader() }}" alt="" srcset="" style="width: 100%; height: auto;">
                </div>
                <div class="py-1">
                    <div class="font-weight-bold h6">@lang('text.department_of_finance')</div>
                    <small class="text-capitalize">@lang('text.our_reference') : <span class="font-weight-semibold text-underline">{{$data['ref']}}</span></small>
                </div>
                <div class="text-center h4 font-weight-bold my-5 pt-5 text-uppercase text-underline">@lang('text.to_whom_it_may_concern')</div>
                <div class="h6 font-weight-bold mb-4 text-uppercase text-underline">@lang('text.fees_clearance')</div>
                @if ($data['fee_cleared'])
                    <div class="mb-5">{!! __('text.clearance_text', ['name'=>$data['student']->name, 'matric'=>$data['student']->matric, 'program'=>$data['degree']->deg_name." IN ".$data['program']->parent->name, 'school'=>$data['school']->name, 'adm_year'=>optional($data['clearance']->first())->_year??'', 'fin_year'=>optional($data['clearance']->last())->_year??'']) !!}</div>
                @else
                    <div class="mb-5">{!! __('text.clearance_text_debt', ['name'=>$data['student']->name, 'matric'=>$data['student']->matric, 'program'=>$data['degree']->deg_name." IN ".$data['program']->parent->name, 'school'=>$data['school']->name, 'adm_year'=>$data['clearance']->first()->_year, 'fin_year'=>$data['clearance']->last()->_year, 'debt'=>$data['debt']]) !!}</div>
                @endif
                <table class="my-5 border">
                    <thead class="text-uppercase border-top border-bottom">
                        <th class="border-left border-right p-2">@lang('text.word_year')/@lang('text.word_level')</th>
                        <th class="border-left border-right p-2">@lang('text.tution_fees_paid')</th>
                        <th class="border-left border-right p-2">@lang('text.word_scholarship')</th>
                        <th class="border-left border-right p-2">@lang('text.registration_fees_paid')</th>
                    </thead>
                    <tbody>
                        @foreach($data['clearance'] as $key => $value)
                            <tr class="border-top border-bottom">
                                <td class="border-left border-right p-2">{{ $value->_year }}</td>
                                <td class="border-left border-right p-2">{{ $value->paid }}</td>
                                <td class="border-left border-right p-2">{{ $value->scholarship }}</td>
                                <td class="border-left border-right p-2">{{ $value->reg??0 }}</td>
                            </tr>
                        @endforeach
                        <tr class="border-top border-bottom">
                            <th class="border-left border-right p-2 text-uppercase">@lang('text.word_total')</th>
                            <th class="border-left border-right p-2">{{ $data['clearance']->sum('paid') }}</th>
                            <th class="border-left border-right p-2">{{ $data['clearance']->sum('scholarship') }}</th>
                            <th class="border-left border-right p-2">{{ $data['total_reg_paid'] }}</th>
                        </tr>
                    </tbody>
                </table>
                <div class="my-5 py-5"></div>
                <div class="my-5 pt-5 row">
                    <div class="col-sm-7 my-1 py-2 d-flex">
                        <span class="text-uppercase font-weight-bold">checked by:</span>
                        <div style="flex:auto;" class="border-bottom py-2"></div>
                    </div>
                    <div class="col-sm-5 my-1 py-2 d-flex">
                        <span class="text-uppercase font-weight-bold">signature:</span>
                        <div style="flex:auto;" class="border-bottom py-2"></div>
                    </div>
                </div>
                <div class="my-5 pt-5 row">
                    <div class="col-6 my-1 py-2 d-flex">
                        <span class="text-uppercase font-weight-bold">approved by:</span>
                        <div style="flex:auto;" class="border-bottom py-2"></div>
                    </div>
                    <div class="col-4 my-1 py-2 d-flex">
                        <span class="text-uppercase font-weight-bold">signature:</span>
                        <div style="flex:auto;" class="border-bottom py-2"></div>
                    </div>
                    <div class="col-2 my-1 py-2 d-flex">
                        <span class="text-uppercase font-weight-bold">on the:</span>
                        <div style="flex:auto;" class="border-bottom py-2"></div>
                    </div>
                </div>
                <div class="pt-5 mt-5 d-flex justify-content-center">
                    <span class="text-uppercase font-weight-bold">this clearance is issued once</span>
                </div>
                <div class="py-5 mt-5 d-flex justify-content-center">
                    <span class="text-uppercase font-weight-bold">{!! $data['qrcode'] !!}</span>
                </div>
            </div>
        </div>
    @endif
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
            let url = "{{ Request::url() }}";
            let token = "{{ csrf_token() }}";
            $({
                method: 'POST', url: url, data: {'_token': token},
                success: function(data){
                    console.log($data);
                    alert(data.message);
                },
                error: function(error){
                    console.log(error);
                    alert(err)
                }
            });

        }
    </script>
@endsection