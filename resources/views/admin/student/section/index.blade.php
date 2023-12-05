@extends('admin.layout')
@section('section')
    <div>
        <div class="my-3 py-2 d-flex input-group">
            <input class="form-control rounded" placeholder="search student by name or matricule" id="search_field" type="search">
        </div> 
        <div class="">
            <div class=" ">
                <table cellpadding="0" cellspacing="0" border="0" class="table table-stripped" id="hidden-table-info">
                    <thead>
                        <tr class="text-capitalize">
                            <th>#</th>
                            <th>{{__('text.word_name')}}</th>
                            <th>{{__('text.word_matricule')}}</th>
                            <th>{{__('text.word_campus')}}</th>
                            <th>{{__('text.word_class')}}</th>
                            <th></th>

                        </tr>
                    </thead>
                    <tbody id="table_body">
                        
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">

                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $('#search_field').on('keyup', function() {
            let value = $(this).val();
            url = '{{ route("search_students") }}';
            // console.log(url);
            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    'key': value
                },
                success: function(response) {
                    let html = '';
                    let k = 1;
                    // console.log(response);
                    response.forEach(element => {
                        // console.log(element);
                        html += `
                        <tr>
                            <td>${k++}</td>
                            <td>${element.name}</td>
                            <td>${element.matric}</td>
                            <td>${element.campus_name}</td>
                            <td>${element.class_name}</td>
                            <td class="d-flex justify-content-end  align-items-start text-capitalize">
                                <a class="btn btn-sm btn-primary m-1" href="{{ route('admin.student.section.change', '__SID__') }}"><i class="fa fa-info-circle"> {{__('text.change_section')}}</i></a>
                            </td>
                        </tr>
                        `.replace('__SID__', element.id);
                    });
                    $('#table_body').html(html);
                },
                error: function(e) {
                    console.log(e)
                }
            })
        })

    </script>
@endsection