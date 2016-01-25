@extends('layout')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Профиль</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-4">
                    <img class="img-thumbnail" src="{{ $u->avatar }}" alt="{{ $u->username }}"/>
                </div>
                <div class="col-md-8">
                    <ul class="list-group">
                        <li class="list-group-item">{{ $u->username }}</li>
                        <li class="list-group-item">Количество побед: {{ $wins }}</li>
                        <li class="list-group-item">Количество проигрышей: {{ $looses }}</li>
                        <li class="list-group-item">Сумма выигрышей: {{ $win_price }} руб.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection