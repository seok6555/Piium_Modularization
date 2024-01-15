<?php
include_once('head.php');

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
    opcache_reset();
}


//팝업 출력하기 위한 sql문
$popup_sql = "select * from popup_tbl order by id";
$popup_stt=$db_conn->prepare($popup_sql);
$popup_stt->execute();

$today = date("Y-m-d H:i:s");
$view_sql = "insert into view_log_tbl
                              (view_cnt,  reg_date)
                         value
                              (? ,?)";


$db_conn->prepare($view_sql)->execute(
    [1, $today]);


?>
<link rel="stylesheet" type="text/css" href="css/index.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="css/reset.css" rel="stylesheet" />
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script type="text/javascript" src="js/jquery-1.12.4.min.js"></script>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src='https://www.google.com/recaptcha/api.js?render=6LedurUoAAAAANkVhXinDvwKmShRsRISFcnSshlI'></script>




<!-- layer popup -->
<?
$arr = array();
$left_count = 0;
$top = 10;
$top2 = 10;
$z_index = 9999;
while ($popup = $popup_stt->fetch()) {
    $arr[] = $popup['id'];
    ?>
    <div class="layer-popup pc"
         style="display: block; width: 80%; max-width: <?= $popup['width'] ?>px; height: <?= $popup['height'] ?>px; top: 10%; left: 5%; z-index: <?= $z_index ?>;">
        <div id="agreePopup<?= $popup['id'] ?>" class="agree-popup-frame">
            <img src="data/popup/<?= $popup['file_name'] ?>" style=" height:calc(<?= $popup['height'] ?>px - 36px);"
                 alt="<?= $popup['popup_name'] ?>">
            <div class="show-chk-wrap">
                <a href="javascript:todayClose('agreePopup<?= $popup['id'] ?>', 1);" class="today-x-btn">오늘하루닫기</a>
                <a class="close-popup x-btn">닫기</a>
            </div>
        </div>
    </div>

    <div class="layer-popup mobile"
         style="display: block; width: 80%; max-width: <?= $popup['width_mobile'] ?>px; height: <?= $popup['height_mobile'] ?>px; top: 10%; left: 10%; z-index: <?= $z_index ?>;">
        <div id="agreePopup_mo<?= $popup['id'] ?>" class="agree-popup-frame">
            <img src="data/popup/<?= $popup['file_name_mobile'] ?>" style=" height:calc(<?= $popup['height'] ?>px - 36px);"
                 alt="<?= $popup['popup_name'] ?>">
            <div class="show-chk-wrap">
                <a href="javascript:todayClose('agreePopup_mo<?= $popup['id'] ?>', 1);" class="today-x-btn">오늘하루닫기</a>
                <a class="close-popup x-btn">닫기</a>
            </div>
        </div>
    </div>
    <?
    $z_index -= 1;
    $top += 10;
    $top2 += 15;
}
?>

<script>
    // * today popup close
    $(document).ready(function () {
        <?
        for ($i = 0; $i < count($arr); $i++) {
        ?>
        todayOpen('agreePopup<?= $arr[$i] ?>');
        todayOpen('agreePopup_mo<?= $arr[$i] ?>');
        <? } ?>
        $(".close-popup").click(function () {
            $(this).parent().parent().hide();
        });
    });

    // 창열기
    function todayOpen(winName) {
        var blnCookie = getCookie(winName);
        var obj = eval("window." + winName);
        console.log(blnCookie);
        if (blnCookie != "expire") {
            $('#' + winName).show();
        } else {
            $('#' + winName).hide();
        }
    }
    // 창닫기
    function todayClose(winName, expiredays) {
        setCookie(winName, "expire", expiredays);
        var obj = eval("window." + winName);
        $('#' + winName).hide();
    }

    // 쿠키 가져오기
    function getCookie(name) {
        var nameOfCookie = name + "=";
        var x = 0;
        while (x <= document.cookie.length) {
            var y = (x + nameOfCookie.length);
            if (document.cookie.substring(x, y) == nameOfCookie) {
                if ((endOfCookie = document.cookie.indexOf(";", y)) == -1)
                    endOfCookie = document.cookie.length;
                return unescape(document.cookie.substring(y, endOfCookie));
            }
            x = document.cookie.indexOf(" ", x) + 1;
            if (x == 0)
                break;
        }
        return "";
    }

    // 24시간 기준 쿠키 설정하기
    // 만료 후 클릭한 시간까지 쿠키 설정
    function setCookie(name, value, expiredays) {
        var todayDate = new Date();
        todayDate.setDate(todayDate.getDate() + expiredays);
        document.cookie = name + "=" + escape(value) + "; path=/; expires=" + todayDate.toGMTString() + ";"
    }
</script>



<!--상단바-->
<div class="topbar-container">
    <div class="topbar-wrap">
        <div class="meun-wrap">
            <div class="meunlist-wrap">
                <div class="meunlist">
                    <img class="logo" img src="img/logo.png" onclick="">
                    <a href="#menu1">소개</a>
                    <a href="#menu2">메뉴</a>
                    <a href="#menu3">고객후기</a>
                    <a href="#menu4">매장현황&매출</a>
                    <a href="#menu5">성공포인트</a>
                    <a href="#menu6">본사지원</a>
                    <a href="#menu7">가맹절차</a>
                    <a href="#menu8">창업문의</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 페이지 1-->
<div class="page1" id="menu1">
    <img class="tit" src="img/page1-text.png">
    <img class="rogo" src="img/page1-rogo.png">
    <img class="img" src="img/page1-img.png">
    <img class="img1" src="img/page1-img-1.png">
</div>

<!-- 페이지 2-->
<div class="page2">
    <div class="textboxx">
        <img class="textbox" src="img/page2-textbox.png">
        <img class="textbox1" src="img/page2-textbox-1.png">
    </div>
    <div class="tit">
        <img class="tit" src="img/page2-text1.png">
    </div>
    <div class="page2-box">
        <img class="box" src="img/page2-box1.png" data-aos="fade-up" data-aos-duration="1000">
        <img class="box" src="img/page2-box2.png" data-aos="fade-down" data-aos-duration="1000" data-aos-delay="150">
        <img class="box" src="img/page2-box3.png" data-aos="fade-up" data-aos-duration="1000">
    </div>
    <div class="page2-box-mo">
        <img class="box" src="img/page2-box1-1.png" data-aos="fade-right" data-aos-duration="1000">
        <img class="box1" src="img/page2-box1-2.png" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="150">
        <img class="box" src="img/page2-box1-3.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="150" data-aos-delay="300">
    </div>
</div>

<!-- 페이지 3-->
<div class="page3" id="menu2">
    <img class="tit" src="img/page3-text.png">
    <div class="meun">
        <img class="img" src="img/menu-1.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="0">
        <img class="img" src="img/menu-2.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="150">
        <img class="img" src="img/menu-3.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="300">
        <img class="img" src="img/menu-4.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="450">
        <img class="img" src="img/menu-5.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="600">

        <img class="img" src="img/menu-6.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="0">
        <img class="img" src="img/menu-7.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="150">
        <img class="img" src="img/menu-8.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="300">
        <img class="img" src="img/menu-9.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="450">
        <img class="img" src="img/menu-10.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="600">
    </div>
    <div class="tit1">
        <img class="tit1" src="img/sidemenu.png">
    </div>
    <div class="side">
        <img class="sidemeun" src="img/side-1.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="0">
        <img class="sidemeun" src="img/side-2.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="150">
        <img class="sidemeun" src="img/side-3.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="300">
        <img class="sidemeun" src="img/side-4.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="450">
        <img class="sidemeun" src="img/side-5.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="600">
        <img class="sidemeun" src="img/side-6.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="750">
        <img class="sidemeun" src="img/side-7.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="900">
    </div>


</div>

<!-- 페이지 4-->
<div class="page4" id="menu3">
    <img class="text" src="img/page4-text1.png">
    <div class="link">
        <img class="img1" src="img/page4-img1.png" data-aos="fade-down-right" data-aos-duration="1000">
        <div class="video-wrap" data-aos="fade-down-left" data-aos-duration="1000">
            <video muted autoplay playsinline loop controls="false">
            <source src="video.MP4" type="video/mp4">
            <strong>Your browser does not support the video tag.</strong>
            </video>
        </div>
    </div>
    <div class="text1">
        <img class="text1" src="img/page4-text2.png">
    </div>
    <div class="img">
        <img class="img3" src="img/page4-img3.png">
    </div>
    <div class="text2">
        <img class="text2" src="img/page4-text3.png" data-aos="flip-right" data-aos-duration="4000">
    </div>
    <!-- 페이지4 슬라이도-->
    <div class="menu-slide-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="img/page4-ri1.png"></div>
            <div class="swiper-slide"><img src="img/page4-ri2.png"></div>
            <div class="swiper-slide"><img src="img/page4-ri3.png"></div>
            <div class="swiper-slide"><img src="img/page4-ri4.png"></div>
            <div class="swiper-slide"><img src="img/page4-ri5.png"></div>
            <div class="swiper-slide"><img src="img/page4-ri6.png"></div>
            <div class="swiper-slide"><img src="img/page4-ri7.png"></div>
            <div class="swiper-slide"><img src="img/page4-ri8.png"></div>
            <div class="swiper-slide"><img src="img/page4-ri9.png"></div>
            <div class="swiper-slide"><img src="img/page4-ri10.png"></div>
            <div class="swiper-slide"><img src="img/page4-ri11.png"></div>
        </div>
    </div>
</div>

<!--페이지5-->
<div class="page5" id="menu4">
    <img class="text" src="img/page5-text1.png">
    <div data-aos="flip-down" data-aos-duration="500"><img src="img/page5-addText.png" alt="" class="textAdd"></div>
    <div class="search-slide-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="img/page5-se1.png"></div>
            <div class="swiper-slide"><img src="img/page5-se2.png"></div>
            <div class="swiper-slide"><img src="img/page5-se3.png"></div>
            <div class="swiper-slide"><img src="img/page5-se4.png"></div>
            <div class="swiper-slide"><img src="img/page5-se5.png"></div>
            <div class="swiper-slide"><img src="img/page5-se6.png"></div>
            <div class="swiper-slide"><img src="img/page5-se7.png"></div>
            <div class="swiper-slide"><img src="img/page5-se8.png"></div>
        </div>
    </div>
    <div class="img-wrap pc">
        <img src="img/page5-img1.png">
        <img src="img/page5-img2.png">
        <img src="img/page5-img3.png">
    </div>
    <div class="menu-slide-container mobile">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="img/page5-img1.png"></div>
            <div class="swiper-slide"><img src="img/page5-img2.png"></div>
            <div class="swiper-slide"><img src="img/page5-img3.png"></div>
        </div>
    </div>
    <div class="text2">
        <img class="text2" src="img/page5-text2.png">
    </div>
    <div class="text3">
        <img class="text3" src="img/page5-text3.png">
    </div>
    <div class="open">
        <img class="store" src="img/page5-open1.png" data-aos="flip-right" data-aos-duration="1000">
        <img class="store" src="img/page5-open2.png" data-aos="flip-right" data-aos-duration="1000">
        <img class="store" src="img/page5-open3.png" data-aos="flip-right" data-aos-duration="1000">
    </div>
    <div class="open-mo">
        <img class="store1" src="img/page5-open1-1.png" data-aos="flip-right" data-aos-duration="1000">
        <img class="store1" src="img/page5-open1-2.png" data-aos="flip-right" data-aos-duration="1000">
        <img class="store1" src="img/page5-open1-3.png" data-aos="flip-right" data-aos-duration="1000">
    </div>
    <div class="text4">
        <img class="text4" src="img/page5-text4.png">
        <img class="text4" src="img/page5-text5.png">
    </div>
    <div class="menu-slide-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="img/page5-re1.png"></div>
            <div class="swiper-slide"><img src="img/page5-re2.png"></div>
            <div class="swiper-slide"><img src="img/page5-re3.png"></div>
            <div class="swiper-slide"><img src="img/page5-re4.png"></div>
            <div class="swiper-slide"><img src="img/page5-re5.png"></div>
        </div>
    </div>
</div>

<!--페이지6-->
<div class="page6" id="menu5">
    <div class="text">
        <img class="text" src="img/page6-text1.png">
    </div>
    <div class="text2" >
        <img class="text2" src="img/page6-text2.png" data-aos="fade-up" data-aos-duration="1000">
    </div>
    <div class="imgbox">
        <img class="img1" src="img/page6-img1.png">
        <img class="img1" src="img/page6-img2.png">
    </div>
    <div class="way">
        <img class="1" src="img/page6-text3.png" data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="0">
        <img class="1" src="img/page6-text4.png" data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="150">
        <img class="1" src="img/page6-text5.png" data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="300">
    </div>
</div>




<!--페이지7-->
<div class="page7" id="menu6">
    <div class="tit">
        <img class=tit src="img/page7-text1.png">
    </div>
    <div class="tit1">
        <img class="tit1" src="img/page7-text2.png">
    </div>
    <div class="imgbox">
        <img class="box" src="img/page7-img1.png" data-aos="flip-up" data-aos-duration="1000">
        <img class="box" src="img/page7-img2.png" data-aos="flip-up" data-aos-duration="1000">
    </div>
    <div class="tiit">
        <img class="tiit" src="img/page7-text3.png">
    </div>
    <div class="review-slide-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="img/page7-silde1.png"></div>
            <div class="swiper-slide"><img src="img/page7-silde2.png"></div>
            <div class="swiper-slide"><img src="img/page7-silde3.png"></div>
            <div class="swiper-slide"><img src="img/page7-silde4.png"></div>
            <div class="swiper-slide"><img src="img/page7-silde5.png"></div>
            <div class="swiper-slide"><img src="img/page7-silde6.png"></div>
            <div class="swiper-slide"><img src="img/page7-silde7.png"></div>
            <div class="swiper-slide"><img src="img/page7-silde8.png"></div>
            <div class="swiper-slide"><img src="img/page7-silde9.png"></div>
            <div class="swiper-slide"><img src="img/page7-silde10.png"></div>
            <div class="swiper-slide"><img src="img/page7-silde11.png"></div>
            <div class="swiper-slide"><img src="img/page7-silde12.png"></div>
            <div class="swiper-slide"><img src="img/page7-silde13.png"></div>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</div>

<!--페이지8-->
<div class="page8" id="menu7">
    <div class="tit">
        <img class="tit" src="img/page8-text1.png">
    </div>
    <div class="textbox">
        <img class="box" src="img/page8-box1.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="0">
        <img class="box" src="img/page8-box2.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="150">
        <img class="box" src="img/page8-box3.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="300">
        <img class="box" src="img/page8-box4.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="450">
        <img class="box" src="img/page8-box5.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="600">
        <img class="box" src="img/page8-box6.png" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="750">
    </div>
</div>

<!--페이지9-->
<div class="page9">
    <div class="tit">
        <img class="tit" src="img/page9-text.png">
    </div>
    <div class="gr">
        <img class="gr" src="img/page9-gr.png">
    </div>
    <div class="gr1">
        <img class="gr1" src="img/page9-gr-1.png">
    </div>
</div>

<div class="contact-container" id="menu8">
    <img class="tit" src="img/contact-tit.png">
    <form name="contact_form" id="contact_form" method="post" action="contact_write.php" onsubmit="return FormSubmit();">
        <input type="hidden" name="writer_ip" value="<?= get_client_ip() ?>" />
        <input type="hidden" name="action" value="go">
        <div class="contact-wrap">
            <div class="agree-wrap">
                <label>
                    <input type="checkbox" name="agree" required="">
                    <img class="text1" src="img/14_agreeText.png">
                </label>
                <img class="text2 agree-open" src="img/14_seeText.png">
            </div>
            <div class="plus">"샾인샾 문의는 받지 않습니다."</div>
            <div class="input-item">
                <div class="label-wrap">
                    <p>성함</p>
                </div>
                <div class="input-wrap">
                    <input type="text" name="name" required>
                </div>
            </div>
            <div class="input-item">
                <div class="label-wrap">
                    <p>연락처</p>
                </div>
                <div class="input-wrap">
                    <input type="text" name="phone" required>
                </div>
            </div>
            <div class="input-item">
                <div class="label-wrap">
                    <p>
                        문의사항<br>
                        (희망 상담 시간)
                    </p>
                </div>
                <div class="input-wrap">
                    <textarea name="contact_desc"></textarea>
                </div>
            </div>
            <input type="hidden" id="g-recaptcha" name="g-recaptcha">
            <input class="submit" type="submit" value="창업 문의하기" class="g-recaptcha" data-sitekey="6LedurUoAAAAANkVhXinDvwKmShRsRISFcnSshlI" data-callback='frmSubmit' data-action='submit'  />

            <div class="agree-modal">
                <p class="head">이용약관</p>
                <p class="txt">
                    (주)77년생곱도리전골식당 이하 회사의 개인정보 수 및 활용에 관한 내용<br>
                    개인정보 수집 및 이용 개인정보 수집주체 : 77년생곱도리전골식당<br>
                    개인정보 수집항목 : 이름, 연락처, 창업희망지역, 문의사항, IP 등 개인을 식별할 수 있는 기타 정보 포함<br>
                    개인정보 수집 및 이용목적 : 마케팅<br>
                    개인정보 보유 및 이용기간 : 수집일로부터 3년 (고객 동의 철회 시 지체없이 파기)
                </p>
            </div>
        </div>
    </form>
</div>
<div class="footer-wrap">
    <div class="text-wrap">
        <p class="text">Copyright (주)77년생곱도리전골식당. All Rights Reserved</p>
    </div>
</div>
<div class="modal-bg"></div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script type="text/javascript">
    AOS.init();


    grecaptcha.ready(function() {
        grecaptcha.execute('6LedurUoAAAAANkVhXinDvwKmShRsRISFcnSshlI', {action: 'submit'}).then(function(token) {
            document.getElementById('g-recaptcha').value = token;
        });
    });

    $(".agree-open").click(function (){
        $(".modal-bg").show();
        $(".agree-modal").fadeIn("500");
        $(".modal-close").show();
    });
    $(".modal-close").click(function (){
        $(".modal-bg").hide();
        $(".modal-container").hide();
        $(".modal-close").hide();
        $(".agree-modal").hide();
    });
    $(".modal-bg").click(function (){
        $(".modal-bg").hide();
        $(".agree-modal").hide();
    });
// 10.23 add
$(function () {

var menuSlide = new Swiper(".search-slide-container", {
    slidesPerView: 5,
    spaceBetween: 40,
    loop: true,
    centeredSlides: true,
    pagination: {
    el: '.swiper-pagination',
    },

    autoplay: {
        delay: 2500,
        disableOnInteraction: false,
    },
    breakpoints: {
        0: {
            slidesPerView: 1.5,
            spaceBetween: 20
        },
        768: {
            slidesPerView: 2.5,
            spaceBetween: 30
        },
        1024: {
            slidesPerView: 4.2,
            spaceBetween: 30
        },
    }
});

}); 
    
$(function () {

    var menuSlide = new Swiper(".menu-slide-container", {
        slidesPerView: 4,
        spaceBetween: 20,
        loop: true,
        centeredSlides: true,
        pagination: {
        el: '.swiper-pagination',
        },

        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        breakpoints: {
            0: {
                slidesPerView: 1.5,
                spaceBetween: 0
            },
            768: {
                slidesPerView: 2,
                spaceBetween: 20
            },
            1024: {
                slidesPerView: 4,
                spaceBetween: 20
            },
        }
    });

});


$(function () {
    var menuSlide = new Swiper(".review-slide-container", {
        slidesPerView: 3,
        spaceBetween: 20,
        loop: true,
        centeredSlides: true,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        pagination : { // 페이징 설정
            el : '.swiper-pagination',
            clickable : true, // 페이징을 클릭하면 해당 영역으로 이동, 필요시 지정해 줘야 기능 작동
        },
        breakpoints: {
            0: {
                slidesPerView: 1.5,
                spaceBetween: 20
            },
            768: {
                slidesPerView: 2,
                spaceBetween: 20
            },
            1024: {
                slidesPerView: 3,
                spaceBetween: 20
            },
        }
    });
});
</script>

<!--문자 알림-->
<script type = "text/javascript">
    function setPhoneNumber(val){
        var numList = val.split("-");
        document.smsForm.sphone1.value=numList[0];
        document.smsForm.sphone2.value=numList[1];
        if(numList[2] != undefined){
            document.smsForm.sphone3.value=numList[2];
        }
    }
    function loadJSON(){
        var data_file = "message_send2.php";
        var http_request = new XMLHttpRequest();
        try{
            // Opera 8.0+, Firefox, Chrome, Safari
            http_request = new XMLHttpRequest();
        }catch (e){
            // Internet Explorer Browsers
            try{
                http_request = new ActiveXObject("Msxml2.XMLHTTP");

            }catch (e) {

                try{
                    http_request = new ActiveXObject("Microsoft.XMLHTTP");
                }catch (e){
                    // Eror
                    alert("지원하지 않는브라우저!");
                    return false;
                }

            }
        }
        http_request.onreadystatechange = function(){
            if (http_request.readyState == 4  ){
                // Javascript function JSON.parse to parse JSON data
                var jsonObj = JSON.parse(http_request.responseText);
                if(jsonObj['result'] == "Success"){
                    var aList = jsonObj['list'];
                    var selectHtml = "<select name=\"sendPhone\" onchange=\"setPhoneNumber(this.value)\">";
                    selectHtml += "<option value='' selected>발신번호를 선택해주세요</option>";
                    for(var i=0; i < aList.length; i++){
                        selectHtml += "<option value=\"" + aList[i] + "\">";
                        selectHtml += aList[i];
                        selectHtml += "</option>";
                    }
                    selectHtml += "</select>";
                    document.getElementById("sendPhoneList").innerHTML = selectHtml;
                }
            }
        }

        http_request.open("GET", data_file, true);
        http_request.send();
    }

</script>

<?php
include_once('tale.php');
?>
