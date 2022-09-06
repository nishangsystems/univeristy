@extends('admin.layout')

@section('section')
    <div class="mx-3">
        <div class="form-panel">
            <form class="form-horizontal" role="form" method="POST" action="{{route('admin.users.subjects.save', $user->id)}}">
                @csrf
                <h5 class="mt-5 mb-4 font-weight-bold">Class Details</h5>

                <div id="section">
                    <div class="form-group">
                        <label for="cname" class="control-label col-lg-2">Section</label>
                        <div class="col-lg-10">
                            <div>
                                <select class="form-control section" name="section" id="section0">
                                    <option selected disabled>Select Section</option>
                                    @foreach($classes as $key => $section)
                                        <option value="{{$key}}">{{$section}}</option>
                                    @endforeach
                                </select>
                                <div class="children"></div>
                            </div>
                        </div>
                    </div>



                    <div class="form-group mt-5">
                        <label for="cname" class="control-label col-lg-2">Subjects</label>
                        <div class="col-lg-10">
                            <div>
                                <div class="subjects">
                                    <select class="form-control" id="subjects" name="subject">
                                        <option selected disabled>Select Subjects, if the list is empty, select a class, or add subject to the class you have selected</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="d-flex justify-content-end col-lg-12">
                        <button id="save" class="btn btn-xs btn-primary mx-3" style="display: none" type="submit">Save</button>
                        <a class="btn btn-xs btn-danger" href="{{route('admin.users.show', $user->id)}}" type="button">Cancel</a>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('.section').on('change', function () {
            refresh($(this));
        })

        $('#subjects').on('change', function (){
            console.log($(this).val());
            if($(this).val() != ""){
                $('#save').css("display", "block");
            }else{
                $('#save').css("display", "none");
            }
        })

        function refresh(div) {

            
            let subject_url = "{{route('section-subjects', "VALUE")}}";
                subject_url = subject_url.replace('VALUE', div.val());
                $.ajax({
                    type: "GET",
                    url: subject_url,
                    success: function (data) {
                        $(".pre-loader").css("display", "none");
                        let html = "";
                        if (data.array.length > 0) {
                            html += '<option selected value="" > Select Subjects </option>';
                            for (i = 0; i < data.array.length; i++) {
                                html += '<option value="' + data.array[i].id + '">' + data.array[i].subject.name + '</option>';
                            }
                        }else{
                            html += ' <option selected disabled>Select Subjects, if the list is empty, select a class, or add subject to the class you have selected</option>';
                        }
                        $('#subjects').html(html)
                    }, error: function (e) {
                        $(".pre-loader").css("display", "none");
                    }
                });
            


            // $(".pre-loader").css("display", "block");
            // url = "{{route('section-children', "VALUE")}}";
            // url = url.replace('VALUE', div.val());
            // $.ajax({
            //     type: "GET",
            //     url: url,
            //     success: function (data) {

            //         let html = "";

            //         if (data.array.length > 0) {
            //             html += '<div class="mt-3"><select onchange="refresh($(this))" class="form-control section" name="'+data.name+'">';
            //             html += '<option selected > Select ' + data.name + '</option>';
            //             for (i = 0; i < data.array.length; i++) {
            //                 html += '<option value="' + data.array[i].id + '">' + data.array[i].name + '</option>';
            //             }
            //             html += '</select>' +
            //                 '<div class="children"></div></div>';
            //         }else{}
            //         $(".pre-loader").css("display", "none");
            //         if(data.valid == 1){
            //         }else{
            //             $('#save').css("display", "none");
            //         }
            //         div.parent().find('.children').html(html)
            //     }, error: function (e) {
            //         $(".pre-loader").css("display", "none");
            //     }
            // });
        }
    </script>
@endsection
