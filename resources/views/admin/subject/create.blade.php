@extends('admin.layout')

@section('section')
    <!-- FORM VALIDATION -->
    <div class="mx-3">
        <div class="form-panel">
            <form class="cmxform form-horizontal style-form" method="post" action="{{route('admin.subjects.store')}}">
                {{csrf_field()}}
                <div class="form-group @error('name') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2">Name (required)</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="name" value="{{old('name')}}" type="text" required />
                    </div>
                </div>

                <div class="form-group @error('coef') has-error @enderror">
                    <label for="cname" class="control-label col-lg-2">Coefficient (required)</label>
                    <div class="col-lg-10">
                        <input class=" form-control" name="coef" value="{{old('coef')}}" type="number" required />
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10">
                        <button class="btn btn-xs btn-primary" type="submit">Save</button>
                        <a class="btn btn-xs btn-danger" href="{{route('admin.subjects.index')}}" type="button">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
