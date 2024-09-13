<div class="my-2">
    <div class="my-2">
        <input type="text" class="form-control" id="search_field" placeholder="search by name or matricule" oninput="loadInstances(event)">
    </div>
    <div class="pt-4">
        <table>
            <thead class="text-capitalize bg-secondary text-light" >
            <th>#</th>
            <th>{{__('text.word_name')}}</th>
            <th>{{__('text.word_matricule')}}</th>
            <th>{{__('text.academic_year')}}</th>
            <th>{{__('text.word_class')}}</th>
            <th>{{__('text.word_action')}}</th>
            </thead>
            <tbody id="result_instances"></tbody>
        </table>
    </div>

</div>
@section('script')
    <script>
        function loadInstances(event) {
            value = event.target.value;
            url = "{{route('admin.result.individual.instances')}}";
            $.ajax({
                method : 'GET',
                url : url,
                data:{
                    searchValue: value
                },
                success : function(data) {
                    console.log(data);
                    tbody_content ='';
                    let k = 1;
                    var link = "{{route('admin.transcript.results', "VALUE")}}";
                    var hnd = "{{route('admin.hnd.results', "VALUE")}}";
                    var courses = "{{route('admin.courses.unvalidated', "VALUE")}}";
                    data.forEach(element => {
                        tbody_content += `<tr class="border-top border-bottom border-secondary">
                            <td class="border-left boder-right border-secondary">${k++}</td>
                            <td class="border-left boder-right border-secondary">${element.name}</td>
                            <td class="border-left boder-right border-secondary">${element.matric}</td>;
                            <td class="border-left boder-right border-secondary">${element.year}</td>
                            <td class="border-left boder-right border-secondary">${element.class}</td>
                            <td class="border-left boder-right border-secondary">
                                <a class="btn btn-xs btn-primary rounded mb-1 mr-1" href="${hnd.replace("VALUE", element.id)}">{{__('text.hnd_results')}}</a>
                                <a class="btn btn-xs btn-primary rounded mb-1 mr-1" target="_blank" href="${link.replace("VALUE", element.id)}">{{__('text.print_transcript')}}</a>
                                <a class="btn btn-xs btn-primary rounded mb-1 mr-1" target="_blank" href="${courses.replace("VALUE", element.id)}">{{__('text.unvalidated_courses')}}</a>
                            </td>
                        </tr>`;
                    });
                    $('#result_instances').html(tbody_content);
                }
            })
        }
    </script>
@endsection