<?php
$HEADER=array(
	'priv' =>	"운영자,뉴스관리자,사진관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
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
<script language="JavaScript" type="text/JavaScript">
<!--
function del(){
	var answer=confirm("정말 삭제하시겠습니까?");

	if(answer)
		return true;
	else
		return false;
}
function putSettings() 
{ 
	with(factory.printing)
	{
		header = ''; // 머릿말
		footer = ''; // 꼬릿말
		portrait = false; // true이면 세로 인쇄, false이면 가로 인쇄.
		leftMargin = 0; // 왼쪽 여백
		rightMargin = 1; // 오른쪽 여백
		topMargin = 0; // 윗쪽 여백
		bottomMargin = 0; // 아랫쪽 여백
	} 
}

function doPrint(frame)
{
	putSettings();
	factory.printing.Print(false, frame);
}
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->
</script>
<object id=factory style="display:none;" classid="clsid:1663ed61-23eb-11d2-b92f-008048fdd814" viewastext codebase="http://www.meadroid.com/scriptx/ScriptX.cab#Version=6,1,429,14"></object>
<?php

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

//시즌정보
$sql = " SELECT *, sid as s_id FROM season ORDER BY s_start DESC ";
$rs = db_query($sql);
$cnt = db_count($rs);
$sselect = "";

if($cnt)	{
	for($i = 0 ; $i < $cnt ; $i++)	{
		$list = db_array($rs);

		if($i == 0 and !$season) $season = $list['s_id'];

		if($season == $list['s_id'])
			$sselect .= "<option value=list.php?season={$list['s_id']} selected>{$list['s_name']}</option>";
		else
			$sselect .= "<option value=list.php?season={$list['s_id']}>{$list['s_name']}</option>";
	}
}

?>
<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td><table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td width="22"><img src="/images/admin/tbox_l.gif" width="22" height="22"></td>
			<td background="/images/admin/tbox_bg.gif"><strong>시즌팀순위 </strong></td>
			<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
		<br>
		<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr align="right">
				<td width="71%" align="left"><form name="form1" id="form1"><select name="season" onchange="MM_jumpMenu('this',this,0)">
					<option value='list.php?season='>시즌선택</option>
					<?php echo $sselect ; ?>
				</select></form></td>
				<td width="29%" height="40" align="right"><input name="back3" type="button" class="CCbox04" id="back3" onclick="location.href='write.php?mode=write&amp;season=<?php echo htmlspecialchars($season); ?>'" value=" 팀순위 등록 "/></td>
			</tr>
	</table>
		<table width="97%" border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#666666">
			<tr align="center" bgcolor="#D2BF7E">
				<td height="30"><strong>실제순위</strong></td>
				<td><strong>팀명</strong></td>
				<td><strong>승 / 패 </strong></td>
				<td class="style2"><strong>승률 / 승차 </strong></td>
				<td class="style2"><strong>연속 승패 </strong></td>
				<td><strong>수정</strong></td>
				<td><strong>삭제</strong></td>
			</tr>
<?php
		//경기 정보 가져오기
		$gsql = " SELECT * FROM season_rank	";
		$sql_where = " WHERE ";
		$sql_where .= " sid = {$season} ";
		$orderby = " ORDER BY rank ";
		$gsql = $gsql.$sql_where.$orderby;
		$grs = db_query($gsql);
		$gcnt = db_count($grs);

		if($gcnt){
			while($list = db_array($grs)){
?>
			<tr align="center" bgcolor="#F8F8EA" onMouseOver="this.style.backgroundColor='#C6E2F9'" onMouseOut="this.style.backgroundColor=''">
				<td height="30"><?php echo htmlspecialchars($list['rank_real']) ; ?></td>
				<td><?php echo htmlspecialchars($list['t_name']) . " (".htmlspecialchars($list['tid']) . ")" ; ?></td>
				<td><?php echo htmlspecialchars($list['win']) ; ?> / <?php echo htmlspecialchars($list['lose']) ; ?></td>
				<td><?php echo htmlspecialchars($list['winrate']) ; ?> / <?php echo htmlspecialchars($list['winsub']) ; ?></td>
				<td><?php echo htmlspecialchars($list['win_con']) ; ?> / <?php echo htmlspecialchars($list['lose_con']) ; ?></td>
				<td><input name="back" type="button" class="CCboxw" id="back" onclick="location.href='write.php?mode=modify&amp;uid=<?php echo htmlspecialchars($list['uid']) ; ?>'" value=" 수정 "/></td>
				<td><input name="back2" type="button" class="CCboxw" id="back2" onclick="javascript:if(del()) location.href='ok.php?mode=delete&amp;uid=<?php echo htmlspecialchars($list['uid']) ; ?>' " value=" 삭제 "/></td>
			</tr>
<?php
			}
		} else {
				echo "<tr align=center><td colspan=7 height=30	bgcolor='#F8F8EA'>&nbsp;등록된 순위가 없습니다.</td></tr>";
		}
?>
		</table>
		<table width="97%" border="0" align="center">
			<tr align="right">
			<td height="40"><input name="back4" type="button" class="CCbox04" id="back4" onclick="location.href='write.php?mode=write&amp;season=<?php echo htmlspecialchars($season); ?>'" value=" 팀순위 등록 "/></td>
			</tr>
		</table></td>
	</tr>
</table>
<?php echo $SITE['tail']; ?>