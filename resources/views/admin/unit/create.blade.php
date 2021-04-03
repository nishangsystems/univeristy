@extends('layout.base')

@section('section')
    <!-- FORM VALIDATION -->
    <div class="row mt">
        <div class="col-lg-12">
            <p class="text-muted">
                Select language to update content in :
                @foreach($languages as $language)
                    <a href="{{url()->current()}}?lang={{$language->code}}" class="btn {!! $lang== $language->code ? 'btn-success' : 'btn-default' !!} btn-xs">{{$language->name}}</a>
                @endforeach
                <a href="{{route('admin.languages.create')}}" class="btn btn-primary btn-xs">New language</a>

            </p>
            <div class="form-panel">
                <div class=" form">
                    <form class="cmxform form-horizontal style-form" method="post" action="{{route('admin.units.store')}}">
                        {{csrf_field()}}

                        <div class="form-group ">
                            <label for="cname" class="control-label col-lg-2">Unit type (required)</label>
                            <div class="col-lg-10">
                                <select class="form-control" name="type" required>
                                    <option selected disabled>Select Unit Type</option>
                                    @foreach(\App\Units::get() as $type)
                                        <option value="{{$type->id}}">{{$type->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group ">
                            <input type="hidden" name="parent_id" value="{{$parent_id}}">
                            <input type="hidden" name="flag" value="{{$flag}}">
                            <input type="hidden" name="lang" value="{{$lang}}">
                            <label for="cname" class="control-label col-lg-2">Name (required)</label>
                            <div class="col-lg-10">
                                <input class=" form-control" name="name" type="text" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button class="btn btn-xs btn-primary" type="submit">Save</button>
                                <a class="btn btn-xs btn-danger" href="{{route('admin.units.index',[$parent_id,0])}}" type="button">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /form-panel -->
        </div>
        <!-- /col-lg-12 -->
    </div>
    <!-- /row -->
@stop
