@extends('admin.layout')

@section('title', 'Our Scholars')
@section('section')
<div class="col-sm-12">
    <div class="mb-5">
        <p>
        <h3 class="font-weight-bold">Our Scholars</h3>
        </p>
    </div>
    <div class="form-panel mb-5 ml-2">
        <form class="form-horizontal" role="form" method="POST" action="{{route('admin.scholarship.awarded_students')}}">
            <div class="form-group @error('year') has-error @enderror ">
                <div class="col-md-8">
                    <select class="form-control" name="year">
                        <option value="">Select year</option>
                        @foreach($years as $key => $year)
                        <option value="{{$year->id}}">{{$year->name}}</option>
                        @endforeach
                    </select>
                    @error('year')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <label class="control-label col-md-1"> </label>
                <label class="control-label col-md-3 mb-2"> <button class="btn btn-md btn-primary" type="submit">Get Scholars</button></label>
            </div>
            @csrf
        </form>
    </div>
    <div class="content-panel">
        <div class="adv-table table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Gender</th>
                        <th>Scholarship Type</th>
                        <th>Scholarship Amount </th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $k=>$student)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$student->name}}</td>
                        <td>{{$student->email}}</td>
                        <td>{{$student->phone}}</td>
                        <td>{{$student->address}}</td>
                        <td>{{$student->gender}}</td>
                        <td>{{$student->type}}</td>
                        <td>{{$student->amount}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end">
                {{$students->links()}}
            </div>
        </div>
    </div>
</div>
@endsection