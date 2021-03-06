@extends('admin_layout')

@section('content')
<section class="content">
<div class="row">	
  <div class="col-md-12">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">История игр</h3>
        </div><!-- /.box-header -->
        <!-- form start -->
          <div class="box-body">
            <button class="btn btn-info pull-right sendAllTrades">Переотправить все за сутки</button>
            {!! $games->render() !!}
            <table  class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID Игры</th>
                    <th>Победитель</th>
                    <th>Банк</th>
                    <th>Дата игры</th>
                    <th>Статус</th>
                  </tr>
                </thead>
                <tbody>

                  @forelse($games as $game)
                  <tr>
                    <td><a href="/admin/history/{{ $game->id }}">{{ $game->id }}</a></td>
                    <td>{{ $game->winner->username }}</td>
                    <td>{{ $game->price }}</td>
                    <td>{{ $game->finished_at->format('d-m-Y H:i') }}</td>
                    @if($game->status_prize == \App\Game::STATUS_PRIZE_WAIT_TO_SENT)
                      <td><span class="badge bg-green">Отправляется</span></td>
                    @endif
                    @if($game->status_prize == \App\Game::STATUS_PRIZE_SEND)
                      <td><span class="badge bg-green">Отправлен</span></td>
                    @endif
                    @if($game->status_prize == \App\Game::STATUS_PRIZE_SEND_ERROR)
                      <td>
                        <span class="badge bg-red" data-toggle="tooltip" title="{{ trans('steamcode.'.$game->send_code) }} (код #{{ $game->send_code }})">Ошибка</span>
                        <button data-gameid="{{ $game->id }}" class="btn btn-info btn-xs sendTradeFromHistory">Переотправить</button>
                      </td>
                    @endif
                  </tr>
                  @empty
                      <center><h1 style="color: #33BDA6;">Игр нет!</h1></center>
                  @endforelse
                </tbody>
            </table>
            {!! $games->render() !!}
          </div><!-- /.box-body -->
      </div><!-- /.box -->
      <!-- general form elements disabled -->
      <!-- /.box -->
  </div>
</div>
</section>
@endsection