@extends('admin_layout')

@section('content')
<section class="content">
<div class="row">
  <div class="col-md-12">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Оповещение</h3>
        </div>
        <div class="box-body">
            <div class="form-group">
              <label for="news-header" class="col-sm-2 control-label">Заголовок оповещения</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="news-header" placeholder="Заголовок" value="{{ $news->header }}">
              </div>
              <label for="news-message" class="col-sm-2 control-label">Сообщение</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="news-message" placeholder="Сообщение" value="{{ $news->message }}">
              </div>
            </div>
        </div><!-- /.box-body -->
        <div class="box-footer">
          <button type="submit" class="btn btn-info addNews">Включить оповещение</button>
          <button type="submit" class="btn btn-danger removeNews">Удалить</button>
        </div><!-- /.box-footer -->
    </div>
  </div>
</div>
<!--
<div class="row">
  <div class="col-md-12">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Управление ставками</h3>
        </div>
          <div class="box-body">
          @if ($stakeDecline)
            <button type="submit" class="btn btn-info stakesOn">Включить прием ставок</button>
            <button type="submit" class="btn btn-danger stakesOff" style="display: none;">Отключить прием ставок</button>
            <span class="text-info" id="stake-info">Новые ставки после завершения игры не принимаются</span>
          @else
            <button type="submit" class="btn btn-info stakesOn" style="display: none;">Включить прием ставок</button>
            <button type="submit" class="btn btn-danger stakesOff">Отключить прием ставок</button>
            <span class="text-info" id="stake-info" style="display: none;">Новые ставки после завершения игры не принимаются</span>
          @endif
          </div>
      </div>
  </div>
</div>
-->
</section>
@endsection