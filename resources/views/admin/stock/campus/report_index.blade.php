@extends('admin.layout')

@section('section')
<div class="col-sm-12">
    
    <form action="{{Request::url()}}" method="get" target="_new">
        <div id="section">
            <div class="form-group">
                <div class="col-lg-12 mb-4">
                    <div class="input-group input-group-merge border">
                        <select class="w-100   section form-control" name="year_id" required>
                            <option selected class="text-capitalize">{{__('text.academic_year')}}</option>
                            @forelse(\App\Models\Batch::all() as $yr)
                                <option value="{{$yr->id}}">{{$yr->name}}</option>
                            @endforeach
                        </select>
                        <select class="w-100   section form-control" name="class_id" required>
                            <option selected class="text-capitalize">{{__('text.select_class')}}</option>
                            @forelse(\App\Http\Controllers\Controller::sorted_program_levels() as $pl)
                                <option value="{{$pl['id']}}">{{$pl['name']}}</option>
                            @endforeach
                        </select>
                        <select class="w-100   section form-control" name="item_id" required>
                            <option selected class="text-capitalize">{{__('text.word_item')}}</option>
                            @forelse(\App\Models\Stock::where(['type'=>request('type')])->orderBy('name')->get() as $item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="border-0 text-uppercase" >{{__('text.word_get')}}</button>
                    </div>
                    <div class="children"></div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
@section('script')
<script>
    
</script>
@endsection
