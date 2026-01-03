<?php
$HEADER=array(
	'priv' =>	"운영자,경기관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
	'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
	'html_echo' => '', // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
	'log' => '' // log_site 테이블에 지정한 키워드로 로그 남김
);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']); // PHP 7에서 $HTTP_HOST 대신 $_SERVER['HTTP_HOST'] 사용
//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
// $seHTTP_REFERER는 어디서 링크하여 왔는지 저장하고, 로그인하면서 로그에 남기고 삭제된다.
// session_register 함수는 PHP 5.4.0부터 삭제
if( !isset($_SESSION['seUserid']) && !isset($_SESSION['seHTTP_REFERER']) && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]) === false ){
	$_SESSION['seHTTP_REFERER']=$_SERVER['HTTP_REFERER'];
}
//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
?>
<script>
function del(){
	var answer=confirm("삭제하시겠습니까?");

	if(answer)
		return true;
	else
		return false;
}
</script>
<link href="/css/basic_text.css" rel="stylesheet" type="text/css">
<link href="/css/link01.css" rel="stylesheet" type="text/css">

<style type="text/css">
<!--
body {
	margin-left: 5px;
	margin-top: 15px;
	margin-right: 5px;
	margin-bottom: 5px;
	background-color:F8F8EA;
}
-->
</style>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td><table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td width="22"><img src="/images/admin/tbox_l.gif" width="22" height="22"></td>
			<td background="/images/admin/tbox_bg.gif"><strong>팀정보 </strong></td>
			<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
		<br>
		<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr align="right">
				<td height="40"><input name="back3" type="button" class="CCbox04" id="back3" onclick="location.href='write.php?mode=write'" value=" 팀등록 "/></td>
			</tr>
		</table>
		<table width="97%" border="0" align="center" cellpadding="6" cellspacing="1" bordercolorlight="#cccccc" bgcolor="#666666">
			<tr align="center" bgcolor="#e6eae6">
				<td width="9%" height="30" bgcolor="#D2BF7E"><strong><span class="style1">번호</span></strong></td>
				<td width="60%" bgcolor="#D2BF7E"><strong><span class="style1">팀 명 </span></strong></td>
				<td width="8%" bgcolor="#D2BF7E"><strong>tid</strong></td>
				<td width="11%" bgcolor="#D2BF7E"><strong><span class="style1">수정</span></strong></td>
				<td width="12%" bgcolor="#D2BF7E"><strong><span class="style1">삭제</span></strong></td>
			</tr>
<?php
	$sql = " SELECT * FROM `savers_secret`.team ORDER BY tid ASC ";
	$rs = db_query($sql);
	$cnt = db_count($rs);
	if($cnt)	{
		for($i = 0 ; $i < $cnt ; $i++)	{
			$list = db_array($rs);
?>
			<tr align="center" bgcolor="#F8F8EA" onMouseOver="this.style.backgroundColor='#C6E2F9'" onMouseOut="this.style.backgroundColor=''">
				<td height="30"><span class="style2">&nbsp;<?php echo $i+1 ; ?></span></td>
				<td><span class="style2">&nbsp;<?php echo htmlspecialchars($list['t_name']) . " (".$list['tid'].")" ; ?> </span></td>
				<td>&nbsp;<?php echo htmlspecialchars($list['tid']) ; ?></td>
				<td><input name="back" type="button" class="CCboxw" id="back" onclick="location.href='write.php?mode=modify&amp;tid=<?php echo htmlspecialchars($list['tid']) ; ?>'" value=" 수정 "/></td>
				<td><input name="back2" type="button" class="CCboxw" id="back2" onclick="javascript:if(del()) location.href='ok.php?mode=delete&amp;tid=<?php echo htmlspecialchars($list['tid']) ; ?>' " value=" 삭제 "/></td>
			</tr>
<?php
		}
	} else {
		echo "<tr align=center><td colspan=5 height=30 bgcolor='#F8F8EA'>&nbsp;등록된 팀이 없습니다.</td></tr>";
	}
?>
		</table>
		<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr align="right">
				<td height="40"><input name="back4" type="button" class="CCbox04" id="back4" onclick="location.href='write.php?mode=write'" value=" 팀등록 "/></td>
			</tr>
		</table>
		<br>
	</td>
	</tr>
</table>
<?php echo $SITE['tail']; ?>