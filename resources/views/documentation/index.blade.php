@extends('documentation.layout')
@section('section')
    <div class="d-flex justify-content-end py-4"><a class="btn btn-xs btn-primary rounded" href="{{route('documentation.create')}}">{{__('text.new_document')}}</a></div>
    <div class="py-5  border border-dark shadow-md my-4 rounded-lg bg-light">
        <table class="table-stripped">
            <thead class="text-capitalize">
                <th></th>
                <th>{{__('text.word_document')}}</th>
                <th>{{__('text.word_description')}}</th>
                <th></th>
            </thead>
            <tbody>
                @php($k = 1)
                @foreach (\App\Models\Documentation::where('parent_id', 0)->get() as $doc)
                    <tr class="border-bottom border-light">
                        <td>{{$k++}}</td>
                        <td>{{$doc->title}}</td>
                        <td>{{$doc->content}}</td>
                        <td>
                            <a class="btn btn-sm btn-primary text-capitalize" href="{{route('documentation.show', [$doc->id])}}">{{__('text.get_started')}}</a>|
                            <a class="btn btn-sm btn-success text-capitalize" href="{{route('documentation.create', [$doc->id])}}">{{__('text.add_child')}}</a>
                            @if ($doc->children->count() == 0)
                                <a class="btn btn-sm btn-danger text-capitalize" onclick="confirm(`You are about to delete {{$doc->title}}`) ? window.location = `{{route('documentation.destroy', [$doc->id])}}` : null">{{__('text.word_delete')}}</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection