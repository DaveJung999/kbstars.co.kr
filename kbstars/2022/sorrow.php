
<?php
$path = "../../nalog504";
$counter = "main";
include "$path/nalog.php"; 

//=======================================================
// 설	명 : 
// 책임자 : 박선민 (sponsor@new21.com), 검수: 06/01/24
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 06/01/24 박선민 마지막 수정
//=======================================================
$HEADER = array(
	'priv' => '', // 인증유무 (비회원,회원,운영자,서버관리자)
	'usedb2' => 1, // DB 커넥션 사용
	'useApp' => 1, // cut_string()
	'useBoard2' => 1, // board2Count(),board2CateInfo()
	'html_echo' => 1,
	'html_skin' => '2022_main_2nd_dev'
);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php'); 

// 슬라이드 선수이미지 가져오
function mainSlide(){
	include("{$_SERVER['DOCUMENT_ROOT']}/sinc/skin/inc_2022_main_slide.php");
}

// 게시판 게시물 가져오기............................
function board($db, $skin, $cut_length=40, $rows=5, $cateuid=0){
	global $SITE, $GAMEINFO, $PlayerCateBoard, $DEBUG; // global 변수 추.
	
	$oldGET = $_GET;
	$_GET = array( db =>	"$db", // 게시물 db
				cut_length =>	"$cut_length", // 게시물 제목 길이
				limitno =>	0,
				limitrows =>	$rows,
				sql_where =>	" 1 ",
				cateuid =>	"$cateuid",
				sql_order =>	" num desc, re ",
				enable_listreply =>	"no",
				skin =>	"$skin", // 게시판 스킨
				html_type => "no" // Site 해더 넣지 않음
				);
	include("{$_SERVER['DOCUMENT_ROOT']}/sboard2/list.php");
	$_GET = $oldGET;
}

function photo($db, $skin, $limitrows=1, $row_pern=1, $cateuid=0){
	global $SITE, $GAMEINFO, $PlayerCateBoard, $DEBUG; // global 변수 추.
	
	$oldGET = $_GET;
	$_GET = array( db =>	"$db", // 게시물 db
				cut_length =>	30, // 게시물 제목 길이
				limitno =>	0,
				limitrows =>	"$limitrows",
				row_pern =>	"$row_pern",
				mNum =>	$oldGET['mNum'],
				sql_where =>	" 1 ",
				cateuid =>	"$cateuid",
				sql_order =>	" num desc, re ",
				enable_listreply =>	"no",
				skin =>	"$skin", // 게시판 스킨
				html_type => "no" // Site 해더 넣지 않음
				);
	include("{$_SERVER['DOCUMENT_ROOT']}/sboard2/list.php");
	$_GET = $oldGET;
} 

?>
		
			<div id="main_bg" class="clearfix">
				<div id="main_cont" class="clearfix">
					<div id="main_left" class="clearfix">

						<!-- 2017/07/22 -->
						<div id="edd" style="visibility:hidden;opacity:0">
							<div id="main_player" class="clearfix">
<?php
 mainSlide(); 
?>
							</div>
						</div>

					</div>
					<div id="main_right" class="clearfix">
<?php
$sql_con = "select * from new21_board2_contents_2016 where uid = 67 ";
							$list_con = db_arrayone($sql_con);

							if ( $list_con['data1'] == 'IMG' ){
								echo "<div id='tdnxtgame_img' class='clearfix' style='height : 389'>".$list_con['content']."</div>";
							} elseif ( $list_con['data1'] == 'NXTIMG' ){			
								include("{$_SERVER['DOCUMENT_ROOT']}/sinc/skin/inc_2022_main_todays_game.php");
								echo "<div id='nextgame_img' class='clearfix' style='height : 185'>".$list_con['content']."</div>";
							} else {			
								include("{$_SERVER['DOCUMENT_ROOT']}/sinc/skin/inc_2022_main_todays_game.php");
								include("{$_SERVER['DOCUMENT_ROOT']}/sinc/skin/inc_2022_main_next_game.php");
							} 

?>
						
						<div id="notice" class="clearfix">
							<div id="notice_title" class="clearfix">
								<img id="title_notice" src="/images/2017/new/title_notice.png" class="image" />
								<a href="/kbstars/2022/d04/01.php?mNum=0401"><img src="/images/2016/new/btn_more.png" name="notice_more" class="image" id="notice_more" /></a>
							</div>
<?php board("news", "2022_gonggi_basic", 58, 5, 1); ?>
						</div>
						<div id="main_banner" class="clearfix"> <img src="/images/2021/main/1006/banner_fb.jpg" name="Image_banner" usemap="#Image_bannerMap" class="image" id="Image_banner" />
						<map name="Image_bannerMap">
							<area shape="rect" coords="6,7,107,111" href="http://www.facebook.com/KBSTARSBASKETBALL/" target="_blank">
							<area shape="rect" coords="108,7,212,111" href="http://www.youtube.com/KBSTARSBASKETBALL" target="_blank">
							<area shape="rect" coords="213,7,315,111" href="http://bj.afreecatv.com/kbbasketball" target="_blank">
							<area shape="rect" coords="316,7,427,111" href="http://www.instagram.com/kbstarsbasketball/" target="_blank">
						</map>
						</div>
					</div>
				</div>
			</div>

			<div id="photozone_380" class="clearfix">
				<div id="title" class="clearfix">
					<img id="title_news" src="/images/2017/new/title_news.png" class="image" />
					<a href="/kbstars/2022/d04/02.php?mNum=0402"><img src="/images/2016/new/btn_more.png" name="news_more" class="image" id="news_more" /></a>
				</div>
				<div id="photo_cont" class="clearfix">
<?php photo("news", "2022_gonggi_news", 4, 4, 2); ?>
				</div>
			</div><?php
//=======================================================
// 팝업창 관리 
//=======================================================
$today_pop = strtotime(date("Y-m-d"));

$sql_pop = "select * from {$SITE['th']}board2_popup Where data3 = 'yes' and data4 <= {$today_pop} and data5 >= {$today_pop} ";

$rs_list_pop = db_query($sql_pop);
$cnt = db_count();
if($cnt > 0){ 
?>
		<script type="text/javascript">
			<!--

			function getFuture(f){
				var d = new Date();
				d.setTime(d.getTime() + (86400000 * f));
				return d;
			}

			function GetCookie (name){
				var arg = name + "=";
				var alen = arg.length;
				var clen = document.cookie.length;
				var i = 0;
				while (i < clen){
					var j = i + alen;
					if (document.cookie.substring(i, j) == arg)
						return getCookieVal (j);
					i = document.cookie.indexOf(" ", i) + 1;
					if (i == 0) break; 
				}
				return null;
			}

			function getCookieVal (offset){
				var endstr = document.cookie.indexOf (";", offset);
				if (endstr == -1)
					endstr = document.cookie.length;
				return unescape(document.cookie.substring(offset, endstr));
			}

			function change(th){
				if(th.selectedIndex != 0){
					if(th.options[th.selectedIndex].value == "") return;
					self.window.open(th.options[th.selectedIndex].value, target="_blank");
				}
			}

		//-->
		</script>
<?php
for ($k = 0 ; $k < $cnt;$k++){
		$list_pop = db_array($rs_list_pop);
		
		$width = $list_pop['data0'];
		if($list_pop['data2'] == "yes")	$width = $width + 16;
			echo "
		<script type='text/javascript'>
			if(GetCookie('popup_$list_pop['uid']') != '$list_pop['uid']'){
				void(window.open('/spopup/popupskin/$list_pop['skin']/index.php?uid=$list_pop['uid']','popup_$list_pop['uid']','height=$list_pop['data1'], width=$width, left=$list_pop['data6'], top=$list_pop['data7'],scrollbars=$list_pop['data2'],location=no,directories=no,personalbar=no,status=no,menubar=no,toolbar=no,resizable=no'));
			}
		</script>
			";
	}
 } ; 

//=======================================================

echo $SITE['tail']; ?>
