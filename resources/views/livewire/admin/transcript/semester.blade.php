<div style="flex-grow: 1;" class="{{$class}}">
    <div style="display: flex;">
        <div class="text-center title">
            Course<br/>
            Code
        </div>
        <div style="flex-grow: 8" class="score text-left">
            Course Title
        </div>
        <div class="text-center score">
            Type
        </div>
        <div class="text-center score">
            Credit<br/>
            Value
        </div>
        <div class="text-center score">
            Grade
        </div>
        <div class="text-center score">
            Credits<br/>
            Earned
        </div>
        <div class="text-center score border-none">
            Grade<br/>
            Point
        </div>
    </div>

    @php
        $creditAttempted = 0;
        $creditEarned = 0;
        $gpa = 0;
    @endphp

    @if(isset($results))

        <div class="bold" style="margin:5px 10px; ">
            {{$results[0]['semester_name']}} - {{$results[0]['year_name']}}
        </div>



        @foreach($results as $result)
            <div style="display: flex;">
                <div class="title">
                    {{$result['code']}}
                </div>
                <div style="flex-grow: 1;  padding:2px 5px; display: flex;   align-items: start;  justify-content: start; width: 35px;  font-size: 10px;   border-right: 1px solid #000;">
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
                $creditAttempted += $result['cv'];
                $creditEarned += $result['ce'];
            @endphp
        @endforeach
    @endif

    <div>

    </div>

    <div style="display: flex; padding: 5px">
        <div style="flex-grow: 1">
            TOTAL CREDITS ATTEMPTED : {{$creditAttempted}}<br/>
            GPA CREDITS ATTEMPTED:  {{$creditAttempted}}
        </div>

        <div style="flex-grow: 1">
            TOTAL CREDITS EARNED : {{$creditEarned}}<br/>
            GPA CREDITS EARNED: {{$creditEarned}}
        </div>
    </div>

    <div style=" padding: 5px">
        SEMESTER GPA = {{$gpa}}
    </div>


    @if($isLast)
        <div style="display: flex;">
            <div style="font-weight: bold; flex-grow: 1; padding:5px;">
                TOTAL CREDITS ATTEMPTED: {{$tca}}<br/>
                CUMMULATIVE TOTAL CREDIT EARNED: {{$tce}}
            </div>

            <div style="font-weight: bold; flex-grow: 1; padding: 5px;">
                GPA CREDITS ATTEMTED: {{$tca}}<br/>
                CUM GPA CREDITS EARNED: {{$tce}}
            </div>
        </div>

        <div style="font-weight: bold; padding: 5px">
            CUMMULATIVE GPA: {{$tgpa}}
        </div>
    @endif
</div>