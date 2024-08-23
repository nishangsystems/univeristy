<div>

    <div class="py-1 form-floating">
        <select name="school" id="" class="form-control" placeholder="Select School" onchange="school_selected(this)">
            <option selected>choose a school</option>
            @foreach ($schools as $school)
                <option value="{{$school->id}}">{{$school->name??''}}</option>
            @endforeach
        </select>
        <label for="school">@lang('text.word_school')</label>
    </div>
    <div class="py-1 form-floating">
        <select name="department" id="departments" class="form-control" placeholder="Select Department" onchange="department_selected(this)">
            
        </select>
        <label for="department">@lang('text.word_department')</label>
    </div>
    <div class="py-1 form-floating">
        <select name="{{$field_name??'program_level'}}" id="program_levels" class="form-control" placeholder="Select Department" onchange="class_changed(this)">
            
        </select>
        <label for="class">@lang('text.word_class')</label>
    </div>

    <script>
        let school_selected = function(element){
            // Load departments and classes
            let val = $(element).val();
            let _url = "{{route('departments', '__UID__')}}".replace('__UID__', val);
            $.ajax({
                method: "GET", url: _url, success: function(data){
                    console.log(data);
                    let options = "<option>----</option>";
                    data.data.forEach(elem => {
                        options += "<option value='"+elem.id+"'>"+elem.name+"</option>";
                    });
                    $('#departments').html(options);
                }
            })

            let _url2 = "{{route('unit_classes', '__UID__')}}".replace('__UID__', val);
            $.ajax({
                method: "GET", url: _url2, success: function(data){
                    console.log(data);
                    let html = "<option>----</option>";
                    data.data.forEach(elm => {
                        html += "<option value='"+elm.id+"'>"+elm.name+"</option>";
                    });
                    $('#program_levels').html(html);
                }
            })
        }
        let department_selected = function(element){
            let val = $(element).val();
            let _url2 = "{{route('unit_classes', '__UID__')}}".replace('__UID__', val);
            $.ajax({
                method: "GET", url: _url2, success: function(data){
                    console.log(data);
                    let html = "<option>----</option>";
                    data.data.forEach(elm => {
                        html += "<option value='"+elm.id+"'>"+elm.name+"</option>";
                    });
                    $('#program_levels').html(html);
                }
            })
        }
    </script>
</div>