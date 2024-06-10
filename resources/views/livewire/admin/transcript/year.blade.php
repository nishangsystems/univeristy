<tr style="vertical-align: top">
    <td colspan="18" style="padding: 0px">
        <div style="display: flex; height: 100%">

            @php
                $semesters   = $batch->groupBy('semester_id')->all();
                ksort($semesters);
                $semesters   = collect($semesters);
                if($semesters->first()[0]['level'] > 5){
                    $first = isset($semesters[8]) ? $semesters[8] : ( isset($semesters[18]) ? $semesters[18] : null);
                    $second =isset($semesters[9]) ?  $semesters[9] : (isset($semesters[19]) ?  $semesters[19] : null);
                    $resit =isset($semesters[12]) ?  $semesters[12] : (isset($semesters[20]) ?  $semesters[20] : null);
                     $hnd = isset($semesters["HND"])?$semesters["HND"]:null;
                     if(isset($hnd)){
                         $grade = \App\Models\Grading::find($hnd[0]['gp'] );
                     }
                }else{
                    $first = isset($semesters[1])?$semesters[1]:null;
                    $hnd = isset($semesters["HND"])?$semesters["HND"]:null;
                    $second = isset($semesters[2])?$semesters[2]:null;
                    $resit = isset($semesters[3])?$semesters[3]:null;
                }

            @endphp


            @if(isset($hnd))
                @component('livewire.admin.transcript.semester', ['results'=>$hnd , 'resits'=>null, 'class'=>'',
                                'isLast'=>false,
                              'tca'=>$tca,
                             'tce'=>$tca,
                             'tgpa'=>$grade
                         ]) @endcomponent

                @component('livewire.admin.transcript.semester',
                            ['results'=>null ,
                                 'resits'=>null,
                                  'class'=>'border-right-2',
                                 'isLast'=>true,
                                 'tca'=>$tca,
                'tce'=>$tce,
                'tgpa'=>$tgpa
                             ]) @endcomponent
            @else
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
            @endif
        </div>
    </td>
</tr>