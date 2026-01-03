<?php
//=======================================================
// 설	명 : 템플릿 샘플
// 책임자 : 박선민 (sponsor@new21.com), 검수: 05/11/20
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 05/11/20 박선민 마지막 수정
//=======================================================
$HEADER = array(
	'priv' => '', // 인증유무 (비회원,회원,운영자,서버관리자)
	'html_echo' => 1,
	'html_skin' => '2019_d03'
);

if( $_GET['html_skin']) 
	$HEADER['html_skin'] = $_GET['html_skin'];

require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	// 넘오온값 체크
function get_player(){
		include($_SERVER['DOCUMENT_ROOT']."/sthis/sthis_player/record_list.php");
}	
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
?>
<p id="contents_title">선수 기록실</p> 
<div id="sub_contents_main" class="clearfix">

<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
	<td>
<?php
get_player(); 
?> </td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	</tr>
</table>
</div>
<?php echo $SITE['tail']; ?>
