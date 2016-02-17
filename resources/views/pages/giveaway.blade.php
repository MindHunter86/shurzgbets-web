@extends('layout')

@section('content')
<div class="content_bg">
    <div class="full">
        <div class="content_title"><div>История <b>раздач</b></div></div>
        <div class="clear"></div>
        <div class="inv_table">
            <div class="inv_table_panel">
                <div class="type1">Предмет</div>
                <div class="type2">Победитель</div>
                <div class="type3">Участники</div>
            </div>
        </div>
        @foreach($lottery as $lot)
            <div class="inv_table_info">

                <div class="type3">
                    <span style="color:green;">{{ $lot->players }} / {{ $lot->max }}</span>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection