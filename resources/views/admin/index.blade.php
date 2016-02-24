@extends('admin_layout')

@section('content')
<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
	    Главная
	    <small>Статистика за последние 30 дней</small>
	  </h1>
	</section>
<!-- Main content -->
	<section class="content">
        <div class="row">
            <div class="col-md-12">
              <!-- AREA CHART -->
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">Статистика комиссии. Заработано {{ $sum }} руб. Средняя комиссия за день {{ $average }} руб. За все время бот раздал {{ $botSumBet }} руб.</h3>
                </div>
                <div class="box-body chart-responsive">
                  <div class="chart" id="line-chart" style="height: 300px;"></div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div>
            <div class="col-md-6">
              <!-- AREA CHART -->
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">Статистика игр. Всего игр  {{ $sumplays }}. Среднее количество игр в день {{ $averageGame }} </h3>
                </div>
                <div class="box-body chart-responsive">
                  <div class="chart" id="game-chart" style="height: 300px;"></div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Почасовая статистика комиссии</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body chart-responsive">
              <div class="chart" id="bar-chart" style="height: 300px;"></div>
            </div>
            <!-- /.box-body -->
          </div>
            </div>
            <div class="col-md-6">
            <div class="box box-info">
              <div class="box-header with-border">
                <h3 class="box-title">Рефереры</h3>
              </div><!-- /.box-header -->
              <!-- form start -->
                <div class="box-body">
                  <table id="example2" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Реферер</th>
                          <th>Количество переходов</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($referer as $ref)
                        <tr>
                          <td><a target="_blank" href="{{ $ref->referer }}">{{ substr($ref->referer, 0, 50) }}</a></td>
                          <td>{{ $ref->count }}</td>
                        </tr>
                        @empty
                            <center><h1 style="color: #33BDA6;">реферов нет!</h1></center>
                        @endforelse
                      </tbody>
                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div>
        </div>    
	</section><!-- /.content -->
	<script> 
	 	var line = new Morris.Line({
          element: 'line-chart',
          resize: true,
          data: {!! $items !!},
          xkey: 'y',
          ykeys: ['item1'],
          labels: ['Комиссия'],
          lineColors: ['#3c8dbc'],
          hideHover: 'auto'
        });
       	var game = new Morris.Line({
          element: 'game-chart',
          resize: true,
          data: {!! $plays !!},
          xkey: 'y',
          ykeys: ['item1'],
          labels: ['Игр за день'],
          lineColors: ['#3c8dbc'],
          hideHover: 'auto'
        });
      var bar = new Morris.Bar({
        element: 'bar-chart',
        resize: true,
        data: {!! $hourgames !!},
        barColors: ['#00a65a'],
        xkey: 'y',
        ykeys: ['a'],
        labels: ['Комиссия'],
        hideHover: 'auto'
      });
	</script>
@endsection