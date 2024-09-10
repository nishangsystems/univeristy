@extends('admin.layout')
@section('section')

<div class="py-3">
    <div class="container row shadow py-5 px-3 mb-4 rounded">
        <div class="col-lg-9">
            <div class="container-fluid position-relative">
                <div>
                    <select class="chosen-select form-control" name="class_id" id="form-field-select-3" data-placeholder="search class by name...">
                        <option selected class="text-capitalize">{{__('text.select_class')}}</option>
                        @forelse(\App\Http\Controllers\Controller::sorted_program_levels() as $pl)
                            <option value="{{$pl['id']}}">{{$pl['name']}}</option>
                        @endforeach
                    </select>
                </div>
                
            </div>
        </div>
        
        <div class="col-lg-3">
            <button class="rounded btn btn-primary px-4" onclick="submitClass(this)">@lang('text.word_next')</button>
        </div>
    </div>
    <div class="d-flex justify-content-end my-4">
        <button class="btn btn-primary btn-lg rounded px-4 text-uppercase" onclick="printList()">@lang('text.word_print')</button>
    </div>

    @isset($report)
        {{-- @dd($report) --}}
        <div class="py-2">
            <table class="table">
                <thead class="text-capitalize">
                    <tr><th colspan="8" class="header text-center text-dark text-uppercase">{{ $title }}</th></tr>
                    <tr>
                        <th>@lang('text.sn')</th>
                        <th>@lang('text.word_matricule')</th>
                        <th>@lang('text.word_name')</th>
                        <th>@lang('text.word_total')</th>
                        <th>@lang('text.unit_cost')</th>
                        <th>@lang('text.amount_expected')</th>
                        <th>@lang('text.amount_paid')</th>
                        <th>@lang('text.amount_owing')</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $k = 1;
                    @endphp
                    @foreach ($report as $rpt)
                        <tr>
                            <td>{{ $k++ }}</td>
                            <td>{{ $rpt->student->matric??'' }}</td>
                            <td>{{ $rpt->student->name??'' }}</td>
                            <td>{{ $rpt->n_courses??'' }}</td>
                            <td>{{ $resit_unit_cost??'' }}</td>
                            <td>{{ intVal($rpt->n_courses??0)*intVal($resit_unit_cost??0) }}</td>
                            <td>{{ intVal($rpt->n_paid??0)*intVal($resit_unit_cost??0) }}</td>
                            <td>{{ intVal($rpt->n_unpaid??0)*intVal($resit_unit_cost??0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endisset
</div>
@endsection
@section('script')
    <script>
        let submitClass = (element)=>{
            let _class = $('#form-field-select-3').val();
            let _url = "{{ route('admin.resits.class_report', ['resit_id'=>$resit->id, 'class_id'=>'__CLID__']) }}".replace('__CLID__', _class);
            window.location = _url;
        }

        let printList = ()=>{
            let printable = $('.table');
            let doc = $(document.body).html();
            $(document.body).html(printable);
            window.print();
            $(document.body).html(doc);
        }

        $('input.searchable').on('input', function(){
            let val = $(this).val();
            let dropdown = $(this).nextAll('div.searchable-dropdown');
            let data = JSON.parse($(dropdown).attr('data-collection'));
            // filter @data with @val
            let selection = data.filter((element)=>{
                return element.name.toLowerCase().indexOf(val.toLowerCase()) > 0;
            });
            // console.log(data);
            let html = ``;
            selection.forEach(element => {
                html += `<div class="alert-success p-2 clickable  border-top border-bottom my-1" onclick="pickItem('${element.name}', '${element.id}')">
                        <small class="text-uppercase text-secondary">${element.name}</small>
                    </div>`
            });
            $(dropdown).html(html);
        });

        let pickItem = function(name, id){
            $('input.searchable').val(name);
            $('input.searchable').nextAll('input.value_field').val(id);
            $('input.searchable').nextAll('div.searchable-dropdown').html(null);
        }
    </script>
@endsection