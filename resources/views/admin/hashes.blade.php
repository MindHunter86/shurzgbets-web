@extends('admin_layout')

@section('content')
<section class="content">
<div class="row">	
  <div class="col-md-12">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Хэши игр</h3>
        </div><!-- /.box-header -->
        <!-- form start -->

          <div class="box-body">
            {!! $games->render() !!}
            <table  class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID Игры</th>
                    <th>Хэш игры</th>
                    <th>Статус</th>
                  </tr>
                </thead>
                <tbody>

                  @forelse($games as $game)
                  <tr>
                    <td>{{ $game->id }}</td>
                    <td>{{ $game->rand_number }}</td>
                    @if($game->status == \App\Game::STATUS_NOT_STARTED)
                      <td><span class="badge bg-green">Еще не началась</span></td>
                    @endif
                    @if($game->status == \App\Game::STATUS_PLAYING)
                      <td><span class="badge bg-green">Игра идет</span></td>
                    @endif
                    @if($game->status == \App\Game::STATUS_PRE_FINISH)
                      <td><span class="badge bg-green">Завершается</span></td>
                    @endif
                    @if($game->status == \App\Game::STATUS_FINISHED)
                      <td><span class="badge bg-green">Закончена</span></td>
                    @endif
                    @if($game->status == \App\Game::STATUS_ERROR)
                      <td><span class="badge bg-red">Ошибка</span></td>
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