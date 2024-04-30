<tr style="vertical-align: top">
    <td colspan="18" style="padding: 0px 5px;">
        <div style="display: flex; height: 100%">
            @php
                $semesters   = $batch->groupBy('semester_id')->all();
                ksort($semesters);
                $semesters   = collect($semesters);
                if($semesters->first()[0]['level'] > 5){
                    $first = isset($semesters[8])?$semesters[8]:null;
                    $second =isset($semesters[9])?$semesters[9]:null;
                    $resit =isset($semesters[12])?$semesters[12]:null;
                }else{
                    $first = isset($semesters[1])?$semesters[1]:null;
                    $second =isset($semesters[2])?$semesters[2]:null;
                    $resit =isset($semesters[3])?$semesters[3]:null;
                }

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