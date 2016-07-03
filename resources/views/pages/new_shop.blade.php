@extends('layout')

@section('content')
<div class="rulet_bg" style="overflow:visible;">
    <div class="rulet_title">{!! trans('shop.title') !!}</div>
    <div class="shop_select">
        <em>{{ trans('shop.select.t1') }}</em>
        <select class="intro-select1" id="searchType" multiple="multiple">
            <option value="Knife">Knife</option>
            <option value="Rifle">Rifle</option>
            <option value="Shotgun">Shotgun</option>
            <option value="Sniper Rifle">Sniper Rifle</option>
            <option value="Pistol">Pistol</option>
            <option value="SMG">SMG</option>
            <option value="Machinegun">Machinegun</option>
            <option value="Container">Container</option>
            <option value="Sticker">Sticker</option>
            <option value="Music Kit">Music Kit</option>
            <option value="Key">Key</option>
            <option value="Pass">Pass</option>
            <option value="Gift">Gift</option>
            <option value="Tag">Tag</option>
            <option value="Tool">Tool</option>
        </select>
    </div>
    <div class="shop_select shop_select2">
        <em>{{ trans('shop.select.t2') }}</em>
        <select class="intro-select1" id="searchQuality" multiple="multiple">
            <option value="Factory new">Factory new</option>
            <option value="Minimal Wear">Minimal Wear</option>
            <option value="Field-Tested">Field-Tested</option>
            <option value="Well-Worn">Well-Worn</option>
            <option value="Battle-Scarred">Battle-Scarred</option>
            <option value="Normal">Normal</option>
            <option value="Normal">Not Painted</option>
        </select>
    </div>
    <input type="text" id="searchName" name="searchName" placeholder="{{ trans('shop.select.t3') }}" class="shop_search" />
    <div class="clear"></div>
    <div class="shop_line">
    </div>
    <div class="hidden">
        <div class="shop_left" style="width: 100%;">
            <div class="shop_item_loop list-products">
            </div>
        </div>
    </div>
</div>
<script>
    var options = {
        maxPrice : 100000,
        minPrice : 0,
        searchName : $('#searchName').val(),
        searchType : null,
        searchRarity: null,
        searchQuality: null,
        sort: 'desc'
    }, timer;
        function getSortedItems(){
            $.post('{{ route("ajax") }}', {action:'shopSort',options:options}, function(response){
                var html = '';
                var i = 0;
                response.forEach(function(item){
                    i++;
                    html += '<div class="shop_item shop_item_c1">';
                    html += '<div class="shop_item_n ell">'+ item.name +'</div>';
                    html += '<div class="shop_item_w"><img src="https://steamcommunity-a.akamaihd.net/economy/image/class/730/'+ item.classId +'/120fx120f" /></div>';
                    html += '<div class="shop_item_n ell">'+ item.quality +'</div>';
                    html += '<div class="shop_item_r">'+ item.price +' <span>{!! trans("all.valute") !!}</span></div>';
                    html += '<div class="shop_item_n ell"><a class="buyItem" href="#" data-item="'+ item.id +'">{!! trans("shop.buy") !!}</a></div>';
                    html += '</div>';
                })
                $('.list-products').html(html);
                //$('#countItems').show();
                //$('#paginator').html(response.pages);
                

                $('.buyItem').click(function () {
                    var that = $(this);
                    $.ajax({
                        url: '{{ route("shop.buy") }}',
                        type: 'POST',
                        dataType: 'json',
                        data: {id: $(this).data('item')},
                        success: function (data) {
                            if (data.success) {
                                that.notify(data.msg, {position: 'bottom middle', className :"success"});
                                setTimeout(function(){that.parent().parent().hide()}, 5500);
                            }
                            else {
                                if(data.msg) that.notify(data.msg, {position: 'bottom middle', className :"error"});
                            }
                        },
                        error: function () {
                            that.notify("Произошла ошибка. Попробуйте еще раз", {position: 'bottom middle', className :"error"});
                        }
                    });
                    return false;
                });
            });
        }
        $(function(){
            /* Price */


            /* Select */

            $('#searchType').change(function(){
                options.searchType = $(this).val();
                clearTimeout(timer);
                timer = setTimeout(getSortedItems, 100);
                console.log(options);
            })
            $('#searchRarity').change(function(){
                options.searchRarity = $(this).val();
                clearTimeout(timer);
                timer = setTimeout(getSortedItems, 100);
                console.log(options);
            })
            $('#searchQuality').change(function(){
                options.searchQuality = $(this).val();
                clearTimeout(timer);
                timer = setTimeout(getSortedItems, 100);
                console.log(options);
            })

            $('#searchName').keyup(function(){
                options.searchName = $(this).val();
                clearTimeout(timer);
                timer = setTimeout(getSortedItems, 100);
                console.log(options);
            })

            $('.buyItem').click(function () {
                var that = $(this);
                $.ajax({
                    url: '{{ route("shop.buy") }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {id: $(this).data('item')},
                    success: function (data) {
                        if (data.success) {
                            that.notify(data.msg, {position: 'bottom middle', className :"success"});
                            setTimeout(function(){that.parent().parent().hide()}, 5500);
                        }
                        else {
                            if(data.msg) that.notify(data.msg, {position: 'bottom middle', className :"error"});
                        }
                    },
                    error: function () {
                        that.notify("Произошла ошибка. Попробуйте еще раз", {position: 'bottom middle', className :"error"});
                    }
                });
                return false;
            });
            setTimeout(getSortedItems, 1500);
        });

</script>
@endsection

