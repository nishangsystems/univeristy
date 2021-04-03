@extends('layout.base')


@section('section')
    <!-- FORM VALIDATION -->
    <div class="row mt">
        <div class="col-lg-12">
            <div class="form-panel">
                <div class=" form">
                    <form class="cmxform form-horizontal style-form" method="post" action="{{route('admin.units.update', $id)}}">
                        {{csrf_field()}}
                        <input type="hidden" name="parent" value="{{($unit != null)?$unit->parent:''}}">
                        <input type="hidden" name="_method" value="put">
                        <div class="form-group has-error">
                            <label for="cname" class="control-label col-lg-2" >Name (required)</label>
                            <div class="col-lg-10">
                                <input class=" form-control" name="name" value="{{($unit != null)?$unit->name:''}}" type="text" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button class="btn btn-xs btn-theme" type="submit">Save</button>
                                <a class="btn btn-xs btn-theme04" href="{{route('admin.units.index',[$parent_id, $flag])}}" type="button">Cancel</a>
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
