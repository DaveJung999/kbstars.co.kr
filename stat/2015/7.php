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
	'usedb2' => 1, // DB 커넥션 사용
	'html_echo' => 1,
	'html_skin' => 'stat'
);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php'); 
?>
<script>
<!--
	function goReserv(){
		window.open("http://wwwaff.ticketlink.co.kr/ticketlink/aff_home.jsp?def_user_cd=INT0206&aff_kind=list","win1",	"toolbar=0, status=0, scrollbars=yes, location=0, menubar=0, width=630, height=410");
		return false;
	}
	
	function goView(){
		window.open("http://wwwaff.ticketlink.co.kr/ticketlink/aff_home.jsp?def_user_cd=INT0206&aff_kind=cancel","win1",	"toolbar=0, status=0, scrollbars=yes, location=0, menubar=0, width=630, height=410");
		return false;
	}
-->
</script>
<table	border="0" cellpadding="0" cellspacing="0" width="690">
	<tr>
	<td width="680"><p ><span style="font-size:10pt;"><img src="/img/season_ticket.gif" width="690" height="69" border="0" /></span></p></td>
	</tr>
	<tr>
	<td width="680" height="30" align="center"><br />
		<br />
		<table width="520" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td><img src="/img/ticketbox_ticket_sangdan.jpg" width="520" height="305" /></td>
	</tr>
	<tr>
		<td><table width="520" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td><img src="/img/ticketbox_btn_leftside.jpg" width="20" height="55" /></td>
			<td><a href="#" onclick="goReserv();"><img src="/img/ticketbox_btn_gotogame.jpg" width="215" height="55" border="0" /></a></td>
			<td><img src="/img/ticketbox_btn_mid.jpg" width="46" height="55" /></td>
			<td><a href="#" onclick="goView();"><img src="/img/ticketbox_btn_cancle.jpg" width="218" height="55" border="0" /></a></td>
			<td><img src="/img/ticketbox_btn_rightside.jpg" width="21" height="55" /></td>
			</tr>
		</table></td>
	</tr>
	<tr>
		<td><img src="/img/ticketbox_ticket_hadanline.jpg" width="520" height="30" /></td>
	</tr>
	</table>	<p>&nbsp;</p></td>
	</tr>
</table>
<?php echo $SITE['tail']; ?>
