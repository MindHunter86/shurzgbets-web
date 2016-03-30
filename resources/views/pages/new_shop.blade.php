@extends('layout')

@section('content')
<div class="rulet_bg" style="overflow:visible;">
    <div class="rulet_title"><b>Магазин</b> предметов</div>
    <div class="shop_select">
        <em>Категории:</em>
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
        <em>Качество:</em>
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
    <input type="text" id="searchName" name="searchName" placeholder="Поиск" class="shop_search" />
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
                    html += '<div class="shop_item_r">'+ item.price +' <span>руб.</span></div>';
                    html += '<div class="shop_item_n ell"><a class="buyItem" href="#" data-item="'+ item.id +'">Купить</a></div>';
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
                                setTimeout(function(){that.parent().parent().parent().hide()}, 5500);
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
                            setTimeout(function(){that.parent().parent().parent().hide()}, 5500);
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


@section('content')
<script type="text/javascript" src="{{ asset('new/js/jquery.multiple.select.js') }}"></script>
<script type="text/javascript" src="{{ asset('new/js/jquery-ui.min.js') }}"></script>
<div class="content_bg">
    <div class="full store-page">
        <div class="content_title"><div>МАГАЗИН ПРЕДМЕТОВ <b>CS:GO</b></div></div>
        <div class="clear"></div>
        <div class="filters">
        <form>
        <div class="field select left">
            <div class="title">Категория</div>
                <select id="searchType" multiple="multiple">
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
        <div class="field select left">
            <div class="title">Качество</div>
                <select id="searchQuality" multiple="multiple">
                <option value="Factory new">Factory new</option>
                <option value="Minimal Wear">Minimal Wear</option>
                <option value="Field-Tested">Field-Tested</option>
                <option value="Well-Worn">Well-Worn</option>
                <option value="Battle-Scarred">Battle-Scarred</option>
                <option value="Normal">Normal</option>
                </select>
            </div>
        <div class="field select left">
            <div class="title">Редкость</div>
                <select id="searchRarity" multiple="multiple">
                <option value="Тайное">Тайное</option>
                <option value="Classified">Classified</option>
                <option value="Restricted">Restricted</option>
                <option value="Industrial Grade">Industrial Grade</option>
                <option value="Mil-Spec Grade">Mil-Spec Grade</option>
                <option value="Consumer Grade">Consumer Grade</option>
                <option value="High Grade">High Grade</option>
                <option value="Base Grade">Base Grade</option>
                <option value="Exotic">Exotic</option>
                <option value="Covert">Covert</option>
                </select>
            </div>
        <div class="field price left">
            <div class="title">Диапазон цен</div>
            <div class="wrapper-price-bar">
            <div id="price-bar"></div>
            </div>
            <span id="price-min"></span><span id="price-max"></span>
        </div>
        <div class="field search left">
            <input id="searchName" type="text" name="searchName" value="" placeholder="Поиск">
        </div>
        <div class="clr-b"></div>
        </form>
        </div> 
        <div class="list-products">
 
        </div>
        <div class="clr-b"></div>
        <center id="paginator"></center>
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
                    html += '<div class="shop_item_w"><img src="https://steamcommunity-a.akamaihd.net/economy/image/class/730/'+ item.classId +'/120fx120f" /></div>';
                    html += '<div class="shop_item_n ell">'+ item.name +'</div>';
                    html += '<div class="shop_item_r">'+ item.price +' <span>руб.</span></div>';
                    html += '<div class="shop_item_n ell"><a class="buyItem" href="#" data-item="'+ item.id +'">Купить</a></div>';
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
                                setTimeout(function(){that.parent().parent().parent().hide()}, 5500);
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
            $('#price-bar').slider({
                range: true,
                min: 0,
                max: 100000,
                values: [0, 100000],
                slide: function( event, ui ){
                    $('#price-min').html(ui.values[0]);
                    $('#price-max').html(ui.values[1]);

                    clearTimeout(timer);
                    timer = setTimeout(getSortedItems, 300);
                    options.minPrice = ui.values[0];
                    options.maxPrice = ui.values[1];

                    moveValueLabels();
                }
            });
            $('#price-min').html($('#price-bar').slider('values', 0));
            $('#price-max').html($('#price-bar').slider('values', 1));


            function moveValueLabels() {
                var pos_first_handle = $('.ui-slider-handle:first').position();
                var pos_last_handle = $('.ui-slider-handle:last').position();
                $('#price-min').css('left', pos_first_handle.left);
                $('#price-max').css('left', pos_last_handle.left);
            }

            moveValueLabels();

            /* Select */
            $('select').multipleSelect({
                selectAll: false,
                width: '161px',
                placeholder: 'Все',
                allSelected: 'Выбраны все',
                countSelected: 'Выбраны # из %'
            });


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
                            setTimeout(function(){that.parent().parent().parent().hide()}, 5500);
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