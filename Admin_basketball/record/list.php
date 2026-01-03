<?php
$HEADER=array(
	'priv' => "운영자,뉴스관리자,사진관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
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

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

//시즌정보
$sql = " SELECT *, sid as s_id FROM `savers_secret`.season ORDER BY s_start DESC ";
$rs = db_query($sql);
$cnt = db_count($rs);
$sselect = "";

if($cnt)	{
	for($i = 0 ; $i < $cnt ; $i++)	{
		$list = db_array($rs);
		if (!$season && $i == 0 ) $season = $list['s_id'];
		if($season == $list['s_id'])
			$sselect .= "<option value=list.php?season={$list['s_id']} selected>{$list['s_name']}</option>";
		else
			$sselect .= "<option value=list.php?season={$list['s_id']}>{$list['s_name']}</option>";
	}
}

$t_rs = db_query(" select * from `savers_secret`.team order by tid ");
$t_cnt = db_count($t_rs);
$t_select = "";

if($t_cnt)	{
	for($i=0 ; $i<$t_cnt ; $i++){
		$t_list = db_array($t_rs);

		// davej 2024-10-09
		$t_list['t_name'] = $t_list['t_name']." (".$t_list['tid'].")";

		if($tid == $t_list['tid'])
			$t_select .= "<option value=list.php?tid={$t_list['tid']}&season={$season} selected>{$t_list['t_name']}</option>";
		else
			$t_select .= "<option value=list.php?tid={$t_list['tid']}&season={$season}>{$t_list['t_name']}</option>";
	}
}

?>
<style type="text/css">
<!--
.style3 {color: #333333}
-->
</style>
<script language="JavaScript" type="text/JavaScript">
<!--
function del(){
	var answer=confirm("삭제하시겠습니까?");

	if(answer)
		return true;
	else
		return false;
}

function MM_jumpMenu(targ,selObj,restore){ //v3.0
	eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
	if (restore) selObj.selectedIndex=0;
}
//-->
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
			<td background="/images/admin/tbox_bg.gif"><strong>한 경기 종합기록	</strong></td>
			<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
		<br>
		<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
			<form name="form1" id="form1">
				<td width="42%"><select name="season" onchange="MM_jumpMenu('this',this,0)">
					<option value='list.php?season='>시즌선택</option>
					<?php echo $sselect ; ?>
				</select>
					<select name="team" onchange="MM_jumpMenu('this',this,0)">
					<option value='list.php?tid='>팀선택</option>
					<?php echo $t_select ; ?>
					</select> </td>
				<td width="58%" height="40" align="right">(<strong><a href="/Admin_basketball/game/list.php">경기정보</a></strong>에 등록 된 자료가 나타납니다.)&nbsp;&nbsp; </td>
			</form>
			</tr>
		</table>
		<table width="97%" border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#666666">
			<tr align="center" bgcolor="#D2BF7E">
				<td width="15%" height="30" bgcolor="#D2BF7E"><strong>경기일</strong></td>
				<td width="20%" bgcolor="#D2BF7E"><strong>홈팀</strong></td>
				<td width="20%" bgcolor="#D2BF7E"><strong>어웨이팀</strong></td>
				<td width="25%" bgcolor="#D2BF7E"><strong>지역</strong></td>
				<td width="10%" bgcolor="#D2BF7E"><strong>경기결과</strong></td>
				<td width="10%" bgcolor="#D2BF7E"><strong>등록</strong></td>
			</tr>
<?php

	//경기 정보 가져오기
	$gsql = " select * FROM `savers_secret`.game ";
	$sql_where = " where ";
	if($season)
		$sql_where .= " sid = {$season} ";
	if($season && $tid)
		$sql_where .= " and (g_home = {$tid} or g_away = {$tid})";
	if(!$season && $tid)
		$sql_where .= " g_home = {$tid} or g_away = {$tid}";
	if(!$season && !$tid)
		$sql_where .= " 1 ";
			
	$orderby = " ORDER BY g_start	";
			
	$gsql = $gsql.$sql_where.$orderby;
	$grs = db_query($gsql);
	$gcnt = db_count($grs);
	$score = [];

	if($gcnt){
		for($i = 0 ; $i < $gcnt ; $i++)	{
			$glist = db_array($grs);
			$glist['g_start'] = date("Y-m-d", $glist['g_start']);

			//팀아이디를 팀이름으로 변경
			$trs = db_query("select * from `savers_secret`.team order by tid");
			
			$tcnt = db_count($trs);
			for($j=0 ; $j < $tcnt ; $j++){
				$tlist = db_array($trs);
				if($glist['g_home'] == $tlist['tid'])	{
					$glist['g_home'] = $tlist['t_name']." (".$tlist['tid'].")";
					$glist['home_tid'] = $tlist['tid'];
					if ($tlist['tid'] == '13') $glist['g_home'] = "<b>".$glist['g_home']."</b>";
				}
				if($glist['g_away'] == $tlist['tid']) {
					$glist['g_away'] = $tlist['t_name']." (".$tlist['tid'].")";
					$glist['away_tid'] = $tlist['tid'];
					if ($tlist['tid'] == '13') $glist['g_away'] = "<b>".$glist['g_away']."</b>";
				}
			}			
			
			$rrs = db_query(" select count(rid) as cnt from `savers_secret`.record where gid={$glist['gid']} ");
			$rcount = db_array($rrs);
			if($rcount['cnt'] > 0){
				$href_read = "<a href='read.php?gid={$glist['gid']}&season={$season}&tid={$tid}'><font color='blue'>{$glist['g_start']}</font></a>";
			} else {
				$href_read = "{$glist['g_start']}";
			}

			//홈팀 경기 결과
			$home = "SELECT sum(1qs + 2qs + 3qs + 4qs + e1s + e2s + e3s) as sum from `savers_secret`.record WHERE gid = {$glist['gid']} and tid = {$glist['home_tid']}";
			$home_rs = db_query($home);
			$home_score = db_array($home_rs);

			//어웨이팀 경기결과
			$away = "SELECT sum(1qs + 2qs + 3qs + 4qs + e1s + e2s + e3s) as sum from `savers_secret`.record WHERE gid = {$glist['gid']} and tid = {$glist['away_tid']}";
			$away_rs = db_query($away);
			$away_score = db_array($away_rs);

			if(isset($home_score['sum']) && isset($away_score['sum']))
				$score[$i] = "{$home_score['sum']} : {$away_score['sum']}";
			else if (isset($glist['home_score']) && $glist['home_score'] > 0 && isset($glist['away_score']) && $glist['away_score'] > 0)
				$score[$i] = "{$glist['home_score']} : {$glist['away_score']}";
			else
				$score[$i] = "";

			if ($glist['home_tid'] == '13' || $glist['away_tid'] == '13'){
				$score[$i] = "<b>".$score[$i]."</b>";
				$bgcolor = " bgcolor = '#FDF2FD'";
			} else {
				$bgcolor = " bgcolor = '#F8F8EA'";
			}
?>
			<tr align="center" bgcolor="#F8F8EA" onMouseOver="this.style.backgroundColor='#C6E2F9'" onMouseOut="this.style.backgroundColor=''">
				<td height="30"><?php echo $href_read ; ?></td>
				<td height="25"><?php echo $glist['g_home'] ; ?></td>
				<td height="25"><?php echo $glist['g_away'] ; ?></td>
				<td height="25"><?php echo $glist['g_ground'] ; ?></td>
				<td height="25"><?php echo $score[$i] ; ?></td>
				<td height="25"><input name="write" type="button" class="CCboxw" style="cursor: pointer" value=" 등록 " onClick="javascript:location.href='write.php?mode=write&amp;gid=<?php echo $glist['gid'] ; ?>&season=<?=$season?>&tid=<?=$tid?>'" /></td>
			</tr>
<?php
		}
	} else {
			echo "<tr align=center><td colspan=6 height=80 bgcolor='#F8F8EA'>&nbsp;등록된 경기가 없습니다.</td></tr>";
	}
?>
			</table></td>
	</tr>
</table>

<br>
<?php echo $SITE['tail']; ?>