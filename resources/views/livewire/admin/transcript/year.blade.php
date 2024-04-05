<tr>
    <td colspan="18" style="padding: 0px 5px;">
        <div style="display: flex;">
            @php
                $semesters   = $batch->groupBy('semester_id')->all();
                ksort($semesters);
                $semesters   = collect($semesters);

                $first = isset($semesters[1])?$semesters[1]:null;
                $second =isset($semesters[2])?$semesters[2]:null;
                $resit =isset($semesters[3])?$semesters[3]:null;
            @endphp

            @component('livewire.admin.transcript.semester',
        ['results'=>$first ,
        'resits'=>$resit,
         'class'=>'border-right-2',
            'isLast'=>false,
            'tca'=>$tca,
            'tce'=>$tce,
            'tgpa'=>$tgpa
        ]) @endcomponent

            @component('livewire.admin.transcript.semester', ['results'=>$second , 'resits'=>null, 'class'=>'', 'isLast'=>$isLast, 'tca'=>$tca,
            'tce'=>$tce,
            'tgpa'=>$tgpa]) @endcomponent
        </div>
    </td>
</tr>