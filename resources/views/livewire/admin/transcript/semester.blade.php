<div style="flex-grow: 1; display: flex; flex-direction: column; width: 50%" class="{{$class}}">
    <div style="display: flex; border-bottom: 1px solid #888888;">
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
        ['results'=>$results,
           'isseminster'=>true,
        ])
        @endcomponent
    @endif

    @if(isset($resits))

        @component('livewire.admin.transcript.semester-result',
       ['results'=>$resits, 'isseminster'=>false])
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
    <div style="display: flex; flex-grow: 1;">
        <div class="text-center title">
        </div>
        <div style="flex-grow: 8" class="score text-left">
        </div>
        <div class="text-center score">
        </div>
        <div class="text-center score">
        </div>
        <div class="text-center score">
        </div>
        <div class="text-center score">
        </div>
        <div class="text-center score border-none">
        </div>
    </div>
</div>