@extends('admin.layout')
@section('section')
    <div class="my-2">
        <div class="input-group-merge d-flex border rounded border-dark col-sm-8 my-2">
            <input type="text" class="form-control border-0" id="search_field" placeholder="search by name or matricule" oninput="loadInstances(event)">
        </div>
        <div class="pt-4">
            <table>
                <thead class="text-capitalize bg-secondary text-light">
                    <th>#</th>
                    <th>{{__('text.word_name')}}</th>
                    <th>{{__('text.word_matricule')}}</th>
                    <th>{{__('text.academic_year')}}</th>
                    <th>{{__('text.word_class')}}</th>
                    <th>{{__('text.word_action')}}</th>
                </thead>
                <tbody class="result_instances"></tbody>
            </table>
        </div>
    </div>
@endsection
@section('script')
    <script>

         function loadInstances(event) {
            value = event.target.value;
            url = "{{route('admin.result.individual.instances', '__V__')}}";
            url = url.replace('__V__', value);
            $.ajax({
                method : 'GET',
                url : url,
                success : function(data) {
                    console.log(data);
                    tbody_content ='';
                    data.forEach(element => {
                        tbody_content += `<tr class="border-top border-bottom border-secondary">
                            <td class="border-left boder-right border-secondary">`+element.name+`</td>
                            <td class="border-left boder-right border-secondary">`+element.matric+`</td>;
                            <td class="border-left boder-right border-secondary">`+element.year+`</td>
                            <td class="border-left boder-right border-secondary">`+element.class+`</td>
                            <td class="border-left boder-right border-secondary">
                            </td>
                        </tr>`;
                    });
                }
            })
         }
    </script>
@endsection