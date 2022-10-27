@extends('admin.layout')

@section('section')
<div>
    <div id="user-profile-1" class="user-profile row">
        <div class=" col-md-6 center">
            <div>
                <span class="profile-picture">
                    <img width="200px" height="" id="avatar" class="editable img-responsive" alt="Alex's Avatar" src="{{url('assets/images/avatars/profile-pic.jpg')}}" />
                </span>

                <div class="space-4"></div>

                <div class="width-80 label label-info label-xlg arrowed-in arrowed-in-right">
                    <div class="inline position-relative">
                        <a href="#" class="user-title-label dropdown-toggle" data-toggle="dropdown">
                            <i class="ace-icon fa fa-circle light-green"></i>
                            &nbsp
                            <span class="white">{{$user->name}}</span>
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
            <h3 class="mb-4">
                <b>{{$user->name}}</b>
            </h3>


            <div class="profile-user-info profile-user-info-striped mx-0">

                <div class="profile-info-row">
                    <div class="profile-info-name text-capitalize"> {{__('text.word_gender')}}</div>

                    <div class="profile-info-value">
                        <span class="editable" id="username"> {{$user->gender}}</span>
                    </div>
                </div>

                <div class="profile-info-row">
                    <div class="profile-info-name text-capitalize"> {{__('text.word_email')}}</div>

                    <div class="profile-info-value">
                        <span class="editable" id="username"> {{$user->email}}</span>
                    </div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name text-capitalize"> {{__('text.word_address')}}</div>

                    <div class="profile-info-value">
                        <span class="editable" id="username"> {{$user->address}}</span>
                    </div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name text-capitalize">{{__('text.date_of_birth')}}</div>

                    <div class="profile-info-value">
                        <span class="editable" id="username"> {{$user->dob}}</span>
                    </div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name text-capitalize"> {{__('text.place_of_birth')}}</div>

                    <div class="profile-info-value">
                        <span class="editable" id="username"> {{$user->pob}}</span>
                    </div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name text-capitalize"> {{__('text.word_contact')}}</div>

                    <div class="profile-info-value">
                        <span class="editable" id="username"> {{$user->phone}}</span>
                    </div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name text-capitalize"> {{__('text.phrase_10')}}</div>

                    <div class="profile-info-value">
                        <span class="editable" id="username"> {{$user->parent_name}}</span>
                    </div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name text-capitalize">{{__('text.phrase_8')}}</div>

                    <div class="profile-info-value">
                        <span class="editable" id="username"> {{$user->parent_phone_number}}</span>
                    </div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name text-capitalize"> {{__('text.word_class')}}</div>

                    <div class="profile-info-value">
                        <span class="editable" id="username"> {{$user->class(Session::get('mode', \App\Helpers\Helpers::instance()->getCurrentAccademicYear()))->name}}</span>
                    </div>
                </div>

                <div class="profile-info-row">
                    <div class="profile-info-name text-capitalize"> {{__('text.word_section')}}</div>

                    <div class="profile-info-value text-capitalize">
                        @if($user->type == 'day')
                        <span class="editable" id="username"> {{__('text.day_section')}}</span>
                        @else
                        <span class="editable" id="username"> {{__('text.boarding_section')}}</span>
                        @endif
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
@endsection