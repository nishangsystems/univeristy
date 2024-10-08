@php
    $creditAttempted = 0;
    $creditEarned = 0;

    $gpaCreditAttempted = 0;
    $gpaCreditEarned = 0;
@endphp

<div class="bold" style="margin:2px 10px; ">
    {{$results[0]['semester_name']}} @if($results[0]['code'] != "HND") - {{$results[0]['year_name']}}  @endif
</div>

@foreach($results as $result)
    <div style="display: flex;">
        <div class="title">
            {{$result['code']}}
        </div>
        <div style="flex-grow: 1;  padding:2px 5px; display: flex;   align-items: center;  justify-content: start; width: 35px;  font-size: 9px;   border-right: 1px solid #888888;">
            {{$result['name']}}
        </div>
        <div class="score">
            {{$result['type']}}
        </div>
        <div class="score">
            {{$result['cv']}}
        </div>
        <div class="score">
            {{$result['grade']}}
        </div>
        <div class="score">
            {{$result['ce']}}
        </div>
        <div class="score border-none">
            {{$result['gp']}}
        </div>
    </div>

    @php
        $creditAttempted += intval($result['cv']);
        $creditEarned += intval($result['ce']);
    @endphp


    @php
        if($result['passed']){
            $gpaCreditAttempted += intval($result['cv']);
            $gpaCreditEarned += intval($result['ce']);
        }
    @endphp
@endforeach

@if($isseminster)
    <div style="display: flex; padding: 2px 5px">
        <div style="flex-grow: 1">
            TOTAL CREDITS ATTEMPTED : {{$creditAttempted}}<br/>
            GPA CREDITS ATTEMPTED: {{$creditAttempted}}
        </div>

        <div style="flex-grow: 1">
            TOTAL CREDITS EARNED : {{$gpaCreditEarned}}<br/>
            GPA CREDITS EARNED: {{$gpaCreditEarned}}
        </div>
    </div>

    <div style=" padding: 0px 5px; font-weight: bold;">
        SEMESTER GPA = {{ \App\Helpers\Helpers::getGPA($results) }}
    </div>
@endif