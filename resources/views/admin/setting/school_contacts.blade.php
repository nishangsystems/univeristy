@extends('admin.layout')
@section('section')
    <div class="py-3">
        <form class="d-flex flex-wrap w-100 w-md-75 mx-auto border rounded-md form-group" method="POST" style="font-size: large;"> @csrf
            <input class="border-collapse d-inlineblock border-0 py-2 px-3" style="width: 30% !important; min-width: 15rem;" value="{{ $_contact->name??null }}" placeholder="{{ __('text.word_name') }}" name="name">
            <input class="border-collapse d-inlineblock border-0 py-2 px-3" style="width: 30% !important; min-width: 15rem;" value="{{ $_contact->title??null }}" placeholder="{{ __('text.word_title') }}" name="title">
            <input class="border-collapse d-inlineblock border-0 py-2 px-3" style="width: 30% !important; min-width: 15rem;" value="{{ $_contact->contact??null }}" placeholder="{{ __('text.word_contact') }}" name="contact">
            <input class="border-collapse  btn d-inlineblock border-0 py-2" style="width: 10% !important; min-width: fit-content;" type="submit" value="{{ __('text.word_save') }}">
        </form>
        <div class="py-3">
            <table class="table table-light table-stripped">
                <thead class="text-capitalize">
                    <th>{{ __('text.word_name') }}</th>
                    <th>{{ __('text.word_title') }}</th>
                    <th>{{ __('text.word_contact') }}</th>
                    <th></th>
                </thead>
                <tbody>
                    @foreach ($contacts as $contact)
                        <tr>
                            <td>{{ $contact->name }}</td>
                            <td>{{ $contact->title }}</td>
                            <td>{{ $contact->contact }}</td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="{{ route('admin.setcontacts', $contact->id) }}">{{ __('text.word_edit') }}</a>
                                <a class="btn btn-sm btn-danger" href="{{ route('admin.dropcontacts', $contact->id) }}">{{ __('text.word_delete') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection