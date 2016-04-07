@extends('admin_layout')

@section('content')
<section class="content">
<div class="row">
	<div class="col-md-6">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Вещи из игры с учетом комиссии</h3>
        </div><!-- /.box-header -->
        <!-- form start -->
          <div class="box-body">
            @foreach(json_decode($game->won_items) as $w)
                <span>
                  @if(!isset($w->img))
                    <img style="width: 70px;" src="https://steamcommunity-a.akamaihd.net/economy/image/class/{{ \App\Http\Controllers\GameController::APPID }}/{{ $w->classid }}/200fx200f" alt="" />
                  @else
                    <img src="{{ $w->img }}" alt="" />
                  @endif
                  <p>{{ $w->name }}</p>
                </span>
            @endforeach
          </div><!-- /.box-body -->
      </div><!-- /.box -->
      <!-- general form elements disabled -->
      <!-- /.box -->
    </div>
</div>
</section>
@endsection