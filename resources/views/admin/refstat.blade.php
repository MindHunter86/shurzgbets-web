@extends('admin_layout')

@section('content')
<section class="content">
<div class="row">
  <div class="col-md-12">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Управление кэшем</h3>
        </div>
        <div class="box-body">
            @if ($itemCount < 2)
            <span class="pull-left text-danger">Вещи отсутствуют! Необходимо пополнить инвентарь бота и обновить кэш.</span>
            @elseif ($itemCount <= config('referal.warningCount'))
            <span class="pull-left text-info">В кэше осталось мало вещей ({{ $itemCount }} шт.), рекомендуется пополнить инвентарь бота и обновить кэш.</span>
            @endif
            <button type="submit" class="btn btn-info pull-right updateReferalCache">Обновить данные с бота</button>
        </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Статистика отдачи вещей по реферальной системе</h3>
        </div><!-- /.box-header -->
        <!-- form start -->
          <div class="box-body">
            <table  class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID трейдоффера</th>
                    <th>Вещи</th>
                    <th>Получатель</th>
                    <th>Суммарная стоимость</th>
                    <th>Дата отправки</th>
                  </tr>
                </thead>
                <tbody>

                  @forelse($transactions as $trans)
                  <tr>
                    <td><a href="https://steamcommunity.com/tradeoffer/{{ $trans->tradeId }}" target="_blank">{{ $trans->tradeId }}</a></td>
                    <td>
                        @foreach(json_decode($trans->referal_items) as $item)
                            <p>{{ $item->market_hash_name }}</p>
                        @endforeach
                    </td>
                    <td>{{ $trans->user->username }} (#{{ $trans->user->steamid64 }})</td>
                    <td>{{ $trans->total_price }}</td>
                    <td>{{ $trans->sended_at }}</td>
                  </tr>
                  @empty
                      <center><h1 style="color: #33BDA6;">Еще никто не воспользовался!</h1></center>
                  @endforelse
                </tbody>
            </table>
          </div><!-- /.box-body -->
      </div><!-- /.box -->
      <!-- general form elements disabled -->
      <!-- /.box -->
  </div>
</div>
</section>
@endsection