<html>
<head>
    <title></title>
    <style>
        body{
            font-family:Arial, Helvetica, sans-serif;
        }
        @page { size: portrait; }
        @page {
            size: A4;

        }
        @media print {
            html, body {
                width: 210mm;
                height: 297mm;
            }
            /* ... the rest of the rules ... */
        }
        .head1{
            width:960px;
            height:130px;
            border:1px solid#000;

        }
        .left_box{
            width:400px;
            height:130px;
            border-right:;
            font-size:12px;
            float:left;
            text-align:center;
        }
        .middle_box{
            width:130px;
            height:130px;
            border-right:;
            font-size:12px;
            float:left;
        }
        .red{
            color:  red;
        }
        .blue{
            color:  blue;
        }
        .middle_box img{
            width:120px;
            height:120px;
            padding:5px 5px;

        }
        .right_box{
            width:400px;
            height:130px;
            text-align:center;

            font-size:12px;
            float:left;
        }
        .head2{
            width:960px;
            padding:10px 10px;
            font-weight:bold;
            text-align:center;

        }
        .head3{
            width:960px;
            height:50px;
            text-align:center;
            font-size:16px;

        }
        .footer1{
            width:960px;
            height:80px;
            border:1px solid#000;

        }
        .footer1_left{
            width:300px;
            float:left;
            height:80px;
            border:1px solid#000;

        }
        .footer1 h2{
            font-family: italic small-caps bold 12px/30px Georgia, serif;;
            font-size:18px;
            text-align:center;
            margin:0px;

        }
        .footer1_right{
            width:200px;
            float:left;
            height:80px;


        }

        table{
            width:960px;
            line-height:1.5;
            border-collapse:collapse;
            padding:5px 5px;

        }
        .table,tr,td,th{
            border-collapse:collapse;
            border:1px solid#000;
        }
    </style>
</head>
<div class="head1">
    <div class="left_box">
        Republic of Cameroon<br>
        Peace-Work-FatherLand<br>
        Ministry of Secondary Education<br>
        <strong>SABIBI COMPREHENSIVE COLLEGE</strong>
    </div>
    <div class="middle_box">
        <img src="{{asset('public/assets/images/logo.png')}}">
    </div>
    <div class="right_box">
        Republique du Cameroun<br>
        Paix-Travail-Patrie<br>
        Ministère de Enseignements Secondaires<br>

        <strong>SABIBI COMPREHENSIVE COLLEGE</strong>
    </div>
</div>
<div class="head2">
    ACADEMIC REPORT SHEET /BULLETIN DE NOTES - {{$term->name}} {{$year->name}} <BR>
    GRAMMAR
</div>
<div class="head3">
    <div style="float:left;width:100px;font-weight:bold;">Name /Nom :</div>
    <div style="float:left;width:350px; font-weight:normal; font-style:italic;
			">{{$user->name}}</div>

    <div style="float:left;width:140px;font-weight:bold;">Class / Classe :</div>
    <div style="float:left;width:360px; font-weight:normal; font-style:italic;
			">{{$user->class(\App\Helpers\Helpers::instance()->getYear())->name}}</div>

    <div style="clear:both; height:10px"></div>
    <div style="float:left;width:150px;font-weight:bold;">Reg No/ Matricule :</div>
    <div style="float:left;width:100px; font-weight:normal; font-style:italic;
			">{{$user->matric}}</div>

    <div style="float:left;width:150px;font-weight:bold;">Place of Birth /<br>
        Lieu de naissance

        :</div>
    <div style="float:left;width:220px; font-weight:normal; font-style:italic;
			">{{$user->pob}}</div>


    <div style="float:left;width:150px;font-weight:bold;">Date of Birth /<br>
        Date de naissance

        :</div>
    <div style="float:left;width:180px; font-weight:normal; font-style:italic;
			"> {{$user->dob}}</div>

</div>
<table>
    <thead>
        <tr>
            <th>MATIERES/SUBECTS</TH>
            @foreach($term->sequences as $seq)
                <th>{{$seq->name}}/2</th>
            @endforeach
            <th>Ave /20<br>Moy /20</th>
            <th>Coef</th>
            <th>Total</th>
            <th>Position</th><th>Teacher</th>
        </tr>
    </thead>
    <tbody>
    @foreach($subjects as $subject)
        <tr>
            <td>{{$subject->name}}</td>
            @php
                $t = 0;
            @endphp
            @foreach($term->sequences as $seq)
                @php
                    $p =\App\Helpers\Helpers::instance()->getScore($seq->id, $subject->id, $user->class($year->id)->id,$year->id, $user->id);
                    $t += $p;
                @endphp
                <td class="{{$p < 10?'red':'blue'}}" >{{$p??'NA'}}/20</td>
            @endforeach
            <td>{{$t/2}}</td>
            <td>{{$subject->coef}}</td>
            <td class="{{$t/2 < 10?'red':'blue'}}">{{$t/2 * $subject->coef}}</td>
        </tr>
    @endforeach
    <tr>
        <td>Average </td>
        @php
            $t = 0;
        @endphp
        @foreach($term->sequences as $seq)
            @php
                $p = $user->averageScore($seq->id, $year->id);
                $t += $p;
            @endphp
            <th class="{{$p < 10?'red':'blue'}}">{{  $p }}</th>
        @endforeach
        <td>{{$t/2}}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>




    </tbody>
</table>
<table  >
    <tbody>
    <!-----General Conduct/ CONDUITE GENERALE---------->

    <tr>
        <td style="font-weight:bold; text-align:center;
					text-transform:uppercase">SEMESTER RESULTS <BR>
            RESULTAT SEQUENTIEL DE L'ELEVE</td><td style="width:50px"></td>
        <td style="font-weight:bold; text-align:center;
					text-transform:uppercase">CLASS PROFILE /PROFILE DE LA CLASSE</td>
        <td style="width:50px"></td><td style="font-weight:bold; text-align:center;
					text-transform:uppercase">
            DISCIPLINE
    </tr>
    <tr style="font-weight:bold" >
        <td>Term Ave / Moy Trimestriel  </td><td style="width:50px"><b class="{{$t/2 < 10?'red':'blue'}}">{{$t/2}}</b></td><td>Highest Ave/ Moy Premier  </td>
        <td style="width:50px"></td> <td>Absences: </td>
    </tr>


    <tr style="font-weight:bold" >
        <td>Term Rank / Rang Trimestriel </td><td style="width:50px"></td><td>Lowest Ave / Moy Dernier </td>
        <td style="width:50px"></td> <td>Warning /Averttissements : </td>

    </tr>



    <tr style="font-weight:bold">
        <td>  </td><td style="width:50px"></td><td>Class Ave/ Moy Classe </td>
        <td style="width:50px"></td> <td>Punishment /Consignes  : </td>
    </tr>

    <tr style="font-weight:bold" >
        <td> </td><td style="width:50px"></td><td></td>
        <td style="width:50px"></td> <td>Suspension /Exc Temporaire </td>

    </tr>


    </tbody>
</table>
<div class="footer1" style="height:auto; overflow:hidden; border:none" >
    <div class="footer1_left">
        <table style="width:300px">
            <thead>
            <tr>
                <th></th><th>Ave/ Moy </th><th>Rank / Rang</th>
            </tr>

            </thead>
            <tbody>
            @foreach($term->sequences as $seq)
                <tr>
                    @php
                    $t =  $user->averageScore($seq->id, $year->id);
                    @endphp
                    <td>Eval / Test : {{$seq->id}}</td>
                    <td class="{{$t < 10?'red':'blue'}}">{{$t}}</td>
                    <td>{{$user->rank($seq->id, $year->id)}}</td>
                </tr>
            @endforeach

            </tbody>

        </table>

    </div>

    <div class="footer1_left" style="font-size:16px; width:430px; height:auto; overflow:hidden">
        <h2>Remarks/ Appreciation Travail </h2>
        <div style="width:430px; height:25px;padding:5px 5px;  float:left; border:1px solid#000">
        </div>

        <h2>Class Council Decison / Décision du conseil de classe </h2>
        <div style="width:430px; height:25px; padding:5px 5px; float:left; border:1px solid#000">
        </div>

    </div>

    <div class="footer1_left" style="width:200px; border:none; text-align:center; padding:10px 10px">
        Buea <?php echo date('d/m/Y G:i'); ?> <br><br>

        The Principal/ Le Proviseur

    </div>

</div>
<body>
</body>
</html>
