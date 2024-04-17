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


    @if(isset($results))
        @component('livewire.admin.transcript.semester-result',
        ['results'=>$results])
        @endcomponent
    @endif

    @if(isset($resits))

        @component('livewire.admin.transcript.semester-result',
       ['results'=>$resits])
        @endcomponent
    @endif

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