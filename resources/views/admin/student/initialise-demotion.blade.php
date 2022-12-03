<?php

use Illuminate\Support\Facades\Http;

?>
@extends('admin.layout')
@section('section')
<div class="w-100">
    <div class="w-100 py-3 mx-auto">
        <h2 class="fw-bolder text-dark mx-auto text-center">Student Demotion</h2>
        <div class="w-100 py-1">
            <div class="py-3">
                <table class="table">
                    <thead class="bg-light text-capitalize">
                        <th>##</th>
                        <th>{{__('text.word_from')}}</th>
                        <th>{{__('text.word_to')}}</th>
                        <th>{{__('text.word_date')}}</th>
                        @if(request('type') == 'promotion')
                        <th>{{__('text.word_action')}}</th>
                        @endif
                    </thead>
                    <tbody>
                        @php($k = 1)
                        @foreach(\App\Models\Promotion::where(['type'=>request('type')])->get() as $prom)
                        <tr class="border-bottom border-light">
                            <td class="border-right border-secondary">{{$k++}}</td>
                            <td class="border-right border-secondary">{{$prom->class->name().' '.$prom->year->name}}</td>
                            <td class="border-right border-secondary">{{$prom->nextClass->name().' '.$prom->nextYear->name}}</td>
                            <td class="border-right border-secondary">{{$prom->created_at}}</td>
                            @if(request('type') == 'promotion')
                            <td class="border-right border-secondary">
                                <a href="" class="btn btn-sm btn-warning">{{__('text.word_cancel')}}</a>|
                                <a href="{{route('admin.students.demote', $prom->id)}}" class="btn btn-danger btn-sm">{{__('text.word_demote')}}</a>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')

@endsection