<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript" src=".__PUBLIC__/Js/zepto.js"></script>
<div style="width: 100%;height: 1000px;margin-top: 0px;padding-top:0px;overflow:auto;" id="selectpaymentmgr-report">
    <!--显示点击的是哪一个字母-->
    <div id="showLetter" class="showLetter"><span>A</span></div>
    <!--城市索引查询-->
    <div class="letter">
        <ul>
            <li><a href="javascript:;">总部</a></li>
            <li><a href="javascript:;">历史</a></li>
            <li><a href="javascript:;">A</a></li>
            <li><a href="javascript:;">B</a></li>
            <li><a href="javascript:;">C</a></li>
            <li><a href="javascript:;">D</a></li>
            <li><a href="javascript:;">E</a></li>
            <li><a href="javascript:;">F</a></li>
            <li><a href="javascript:;">G</a></li>
            <li><a href="javascript:;">H</a></li>
            <li><a href="javascript:;">J</a></li>
            <li><a href="javascript:;">K</a></li>
            <li><a href="javascript:;">L</a></li>
            <li><a href="javascript:;">M</a></li>
            <li><a href="javascript:;">N</a></li>
            <li><a href="javascript:;">P</a></li>
            <li><a href="javascript:;">Q</a></li>
            <li><a href="javascript:;">R</a></li>
            <li><a href="javascript:;">S</a></li>
            <li><a href="javascript:;">T</a></li>
            <li><a href="javascript:;">W</a></li>
            <li><a href="javascript:;">X</a></li>
            <li><a href="javascript:;">Y</a></li>
            <li><a href="javascript:;">Z</a></li>
        </ul>
    </div>
    <!--城市列表-->
    <div class="container">
        <div class="city">

        </div>
    </div>
</div>

<style>
    /*显示点击是哪个字母*/
    .showLetter {
        position: fixed;
        color: #105eae;
        width: 50px;
        height: 50px;
        top: 45%;
        left: 45%;
        border-radius: 50%;
        border: #105eae 1px solid;
        text-align: center;
        display: none;
    }

    .showLetter span {
        width: 50px;
        height: 50px;
        line-height: 50px;
        font-size: 30px;
    }

    .seach {
        width: 90%;
        height: 45px;
        padding-top: 10px;
        background: #fff;
    }

    .seach input {
        width: 90%;
        height: 45px;
        font-size: 16px;
        padding-left: 10px;
        padding-right: 10px;
        border: 1px solid #eee;
        color: #000
    }

    .letter {
        width: auto;
        position: absolute;
        top: 20px;
        right: 35px;
        text-align: center;
    }

    .letter ul {
        list-style-type: none;
    }

    .letter ul li a {
        text-decoration: none;
        color: #105eae;
        font-size: 15px
    }


    /*城市弹层*/
    .container {
        width: 90%;
        right: 20;
        overflow-y: scroll
    }


    .city {
        width: 90%;
        padding: 20px 20px 20px 10px;
    }

    .history {
        margin-top: 15px;
        cursor: pointer;
    }

    .hot {
        overflow: hidden;
    }

    .hot div {
        display: block;
        width: 29%;
        height: 40px;
        float: left;
        text-align: center;
        line-height: 40px;
        color: black;
        margin-top: 10px;
        margin-right: 3%;
        border: 1px solid #eee;
        border-radius: 5px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .hotCity {
        font-size: 16px;
        cursor: pointer;
    }

    .clear-history {
        display: block;
        float: right;
    }

    .city-list {
        width: 100%;
    }

    .city-list .city-letter {
        font-size: 18px;
        color: #105eae;
        display: inline-block;
        padding-top: 1px;
        padding-bottom: 4px;
        border-bottom: 1px solid #e8ecf1;
        width: 100%;
    }

    .city-list p {
        color: black;
        width: 95%;
        min-height: 20px;
        line-height: 30px;
        border-bottom: 1px solid #e8ecf1;
        cursor: pointer;
        font-size: 20px;
        margin-block-start: 1px;
        margin-block-end: 10px;
        margin-inline-start: 1px;
        margin-inline-end: 20px;
    }

    .tips {
        cursor: pointer;
        color: red;
    }

    .UISelect {
        margin-top: 5px
    }
</style>

<script>
    var cityList = '';
    var hotCity = '';
    var findCode = '';
    $(function () {

        // 选择城市
        $('body').on('click', '.city-list p', function () {
            var data = $(this).text();
            saveHistory(data);
            code = findCode[data];
            $('#YingshouIncomeMgrCreateviewForm input[name="paymentcode"]').val(code);
            $('#YingshouIncomeMgrCreateviewForm input[name="name"]').val(data);
            $('#globel-dialog-div').dialog('close');
        });


        $.ajax({
            type: "GET",
            url: "__URL__/getCompanyPayment/company/<?php echo ($company); ?>",
            dataType: "json",
            success: function (data) {
                cityList = data.city;
                hotCity = data.area;
                findCode = data.findcode;
                init();

            }
        })

    })

    function bindHistoryClick() {
        $('.history .hot').on('click', 'div', function () {
            var data = $(this).text();
            saveHistory(data);
            code = findCode[data];
            $('#YingshouIncomeMgrCreateviewForm input[name="paymentcode"]').val(code);
            $('#YingshouIncomeMgrCreateviewForm input[name="name"]').val(data);
            $('#globel-dialog-div').dialog('close');
        });

    }

    function clearHistory() {
        localStorage.setItem('hotelHistory', '');
        initHistory();
    }

    function saveHistory(data) {
        var hotelHistory = localStorage.getItem('hotelHistory');
        if (!hotelHistory) {
            hotelHistory = '';
        }
        if (hotelHistory.indexOf(data) > -1) {
            var arr = hotelHistory.split(',');
            var newArr = [];
            $.each(arr, function (i, item) {
                if (item !== data) {
                    newArr.push(item);
                }
            })
            hotelHistory = newArr.join(',');
        }
        localStorage.setItem('hotelHistory', data + ',' + hotelHistory);
        initHistory();
    }

    function init() {
        $('.city').html('');
        var hotHtml = '';
        hotHtml += '<div class="tips" id="热门1">总公司</div>';
        hotHtml += '<div class="hot hotCity" id="hotCity">';
        $.each(hotCity, function (i, item) {
            hotHtml += '<div>' + item + '</div>'
        })
        hotHtml += '</div>';
        hotHtml += '<div class="history"></div>';
        $('.city').append(hotHtml);

        initHistory();

        var html = '';
        $.each(cityList, function (i, item) {
            html += '<div class="city-list"><span class="city-letter" id="' + item.key + '1">' + item.key +
                '</span>';
            $.each(item.data, function (j, data) {
                html += '<p>' + data + '</p>';
            })
            html += '</div>';
        })
        $('.city').append(html);


        //绑定事件
        $('#hotCity').on('click', 'div', function () {
            var data = $(this).text();
            saveHistory(data);
            code = findCode[data];
            $('#YingshouIncomeMgrCreateviewForm input[name="paymentcode"]').val(code);
            $('#YingshouIncomeMgrCreateviewForm input[name="name"]').val(data);
            $('#globel-dialog-div').dialog('close');
        });

        $('.letter').bind("click", function (event) {
            var e = event || window.event;
            var top = $(window).scrollTop();

            event.preventDefault(); //阻止默认滚动
            // var touch = e.touches[0];
            var ele = document.elementFromPoint(e.clientX, e.clientY - top);

            if (ele.tagName === 'A') {
                var s = $(ele).text();
                //if($('#' + s + '1').offset().top) return;

                document.getElementById('selectpaymentmgr-report').scrollTop = document.getElementById('selectpaymentmgr-report').scrollTop + $('#' + s + '1').offset().top - 96;
                //$('#selectpaymentmgr-report').scrollTop(0);
                $("#showLetter span").html(s.substring(0, 1));
                $("#showLetter").show().delay(500).hide(0);
            }
        });

        //scrollToLocation();

    }

    function scrollToLocation() {
        var mainContainer = $('#thisMainPanel'),
            scrollToContainer = ('#S1');
        //滚动到<div id="thisMainPanel">中类名为son-panel的最后一个div处
        //scrollToContainer = mainContainer.find('.son-panel:eq(5)');//滚动到<div id="thisMainPanel">中类名为son-panel的第六个处
        //非动画效果
        //mainContainer.scrollTop(
        //  scrollToContainer.offset().top - mainContainer.offset().top + mainContainer.scrollTop()
        //);
        //动画效果
        mainContainer.animate({
            scrollTop: scrollToContainer.offset().top - mainContainer.offset().top + mainContainer
                .scrollTop()
        }, 2000); //2秒滑动到指定位置
    }

    function initHistory() { // 历史记录渲染
        var hotelHistory = localStorage.getItem('hotelHistory');
        var history = '';
        history +=
            '<div class="tips" id="历史1">历史记录<div class="clear-history" onClick="clearHistory();">清空历史</div></div>';
        history += '<div class="hot">';
        if (hotelHistory) {
            $.each(hotelHistory.split(','), function (i, item) {
                if (item) {
                    history += '<div>' + item + '</div>'
                }
            })
        }
        history += '</div>';
        $('.history').html(history);
        bindHistoryClick();
    }

    ;
    (function ($) {

        $('.letter').bind("touchstart touchmove", function (e) {
            var top = $(window).scrollTop();
            e.preventDefault(); //阻止默认滚动
            var touch = e.touches[0];
            var ele = document.elementFromPoint(touch.pageX, touch.pageY - top);

            if (ele.tagName === 'A') {
                var s = $(ele).text();
                $(window).scrollTop($('#' + s + '1').offset().top)
                $("#showLetter span").html(s.substring(0, 1));
                $("#showLetter").show().delay(500).hide(0);
            }
        });

        $('.letter').bind("touchend", function (e) {
            $("#showLetter").hide(0);
        });

    })(Zepto);
</script>