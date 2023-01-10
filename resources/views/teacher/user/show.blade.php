@extends('teacher.layout')

@section('section')

<div>
    <div id="user-profile-1" class="user-profile row">
        <div class=" col-md-6 center">
            <div>
                <span class="profile-picture">
                    <img width="200px" height="" id="avatar" class="editable img-responsive"
                    alt="Alex's Avatar" src="{{url('assets/images/avatars/profile-pic.jpg')}}"/>
                </span>
                
                    <div class="space-4"></div>

                    <div class="width-80 label label-info label-xlg arrowed-in arrowed-in-right">
                        <div class="inline position-relative">
                            <a href="#" class="user-title-label dropdown-toggle" data-toggle="dropdown">
                                <i class="ace-icon fa fa-circle light-green"></i>
                                &nbsp
                                <span
                                    class="white">{{$user->name}}</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="space-6"></div>

                <div class="profile-contact-info"></div>

                <div class="hr hr12 dotted"></div>

                <div class="hr hr16 dotted"></div>
            </div>

            <div class="col-md-6">
                <div class="space-12"></div>

                <h3>
                    <b>{{$user->name}}</b>
                </h3>


                <div class="profile-user-info profile-user-info-striped">

                    <div class="profile-info-row">
                        <div class="profile-info-name"> Gender</div>

                        <div class="profile-info-value">
                            <span class="editable"
                                  id="username"> {{$user->gender}}</span>
                        </div>
                    </div>

                    <div class="profile-info-row">
                        <div class="profile-info-name"> Email</div>

                        <div class="profile-info-value">
                            <span class="editable"
                                  id="username"> {{$user->email}}</span>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div class="profile-info-name"> Address</div>

                        <div class="profile-info-value">
                            <span class="editable"
                                  id="username"> {{$user->address}}</span>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div class="profile-info-name"> Contact</div>

                        <div class="profile-info-value">
                            <span class="editable" id="username"> {{$user->phone}}</span>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div class="profile-info-name"> Type</div>

                        <div class="profile-info-value">
                            <span class="editable" id="username"> {{$user->type}}</span>
                        </div>
                    </div>
                    <div class="profile-info-row">
                        <div class="profile-info-name"></div>
                    </div>
                </div>
                <div class="space-20"></div>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <p class="text-muted">
            <a href="{{route('user.teacher.subjects.add', $user->id)}}" class="btn btn-info btn-xs text-capitalize">{{__('text.assign_course')}}</a>
        </p>

        <div class="content-panel">
            <div class="adv-table table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table" id="hidden-table-info">
                    <thead>
                        <tr class="text-capitalize">
                            <th>#</th>
                            <th>{{__('text.course_code')}}</th>
                            <th>{{__('text.word_name')}}</th>
                            <th>{{__('text.word_class')}}</th>
                            <th>{{__('text.word_campus')}}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @php($c = 1)
                    @foreach($courses as $subject)
                        <tr>
                            @php($value = \App\Models\ProgramLevel::find($subject->class))
                            <td>{{$c++}}</td>
                            <td>{{$subject->code}}</td>
                            <td>{{$subject->name}}</td>
                            <td>{{$value->program()->first()->name.': LEVEL '.$value->level()->first()->level}}</td>
                            <td>{{\App\Models\Campus::find($subject->campus_id)->name ?? '----'}}</td>
                            <td style="float: right;">
                                <a onclick="event.preventDefault();
                                            document.getElementById('delete{{$subject->id}}').submit();" class=" btn btn-danger btn-xs m-2">DROP</a>
                                <form id="delete{{$subject->id}}" action="{{route('user.teacher.subjects.drop',$subject->ts_id)}}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
