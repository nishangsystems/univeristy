@extends('admin.layout')

@section('section')
    <!-- page start-->

    <div class="col-sm-12">
        <div class="content-panel">
            <div class="py-4 my-3 form-panel">
                <form class="form-horizontal" role="form" action="{{route('admin.campuses.update', request('id'))}}" method="post" >
                    @csrf
                    <div class="form-group @error('school_id') has-error @enderror my-2">
                        <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_school')}} </label>
                        <div class="col-lg-10">
                            <select class=" form-control" name="school_id" required>
                                <option value="">select school</option>
                                @foreach(\App\Models\School::all() as $school)
                                    <option value="{{$school->id}}" {{ $school->id == $campus->school_id ? 'selected' : ''}}>{{$school->name}}</option>
                                @endforeach
                            </select>
                            @error('school_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group @error('name') has-error @enderror my-2">
                        <label for="cname" class="control-label col-lg-2 text-capitalize">{{__('text.word_name')}} </label>
                        <div class="col-lg-10">
                            <input class=" form-control" name="name" value="{{$campus->name}}" type="text"  />
                            @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group @error('address') has-error @enderror my-2">
                        <label for="caddress" class="control-label col-lg-2 text-capitalize">{{__('text.word_address')}} </label>
                        <div class="col-lg-10">
                            <input class=" form-control" name="address" value="{{$campus->address}}" type="text"  />
                            @error('address')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group @error('telephone') has-error @enderror my-2">
                        <label for="caddress" class="control-label col-lg-2 text-capitalize">{{__('text.word_contact')}} </label>
                        <div class="col-lg-10">
                            <input class=" form-control" name="telephone" value="{{$campus->telephone}}" type="tel"  />
                            @error('telephone')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="d-flex justify-content-end col-lg-12">
                            <button id="save" class="btn btn-xs btn-primary mx-3" style="display: block" type="submit">{{__('text.word_update')}}</button>
                            <a class="btn btn-xs btn-danger" href="{{route('admin.campuses.index')}}" type="button">{{__('text.word_cancel')}}</a>
                        </div>
                    </div>
                </form>
            </div>
            <hr>
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th class="text-capitalize">{{__('text.word_school')}}</th>
                        <th class="text-capitalize">{{__('text.word_name')}}</th>
                        <th class="text-capitalize">{{__('text.word_address')}}</th>
                        <th class="text-capitalize">{{__('text.word_contact')}}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    @php($k = 0)
                    @foreach($campuses as $cps)
                        <tr>
                            <td>{{ ++$k }}</td>
                            <td>{{\App\Models\School::find($cps->school_id)->name}}</td>
                            <td>{{ $cps->name }}</td>
                            <td>{{ $cps->address }}</td>
                            <td>{{ $cps->telephone }}</td>
                            <td>
                               @if($cps->students()->count() > 0)
                                    <a href="{{route('admin.campuses.edit', $cps->id)}}" class="btn btn-sm btn-success text-capitalize">{{__('text.word_edit')}}</a>
                                    @else
                                    <a href="{{route('admin.campuses.edit', $cps->id)}}" class="btn btn-sm btn-success text-capitalize">{{__('text.word_edit')}}</a>
                                    <a href="{{route('admin.campuses.delete', $cps->id)}}" class="btn btn-sm btn-danger text-capitalize">{{__('text.word_delete')}}</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
