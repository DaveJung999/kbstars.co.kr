<?php
$HEADER=array(
	'priv' =>	"운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
	'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
	'html_echo' => '', // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
	'log' => '' // log_site 테이블에 지정한 키워드로 로그 남김
);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']); // PHP 7에서 $HTTP_HOST 대신 $_SERVER['HTTP_HOST'] 사용

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

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
//공헌도
function contribute1($score, $stl, $bs, $re_def, $re_off, $ast, $gd, $min){
	$con1 = ($score + $stl + $bs + $re_def) * 1.0 + ($re_off + $ast + $gd) * 1.5 + $min/4;
	return $con1;
}
function contribute2($tover, $f2, $f3, $fft){
	$con2 = ($tover*1.5 + $f2*1.0 + $f3*0.9 + $fft*0.8);
	return $con2;
}


if(isset($_GET['html']) && $_GET['html'] == "print")	$print = "";
else $print = "<img src='/images/print_img.gif' border='0' onClick=\"window.open('list.php?html=print&season={$s_list['s_id']}&tid={$tid}');\">";

//시즌정보
$sql = " SELECT *, sid as s_id FROM `savers_secret`.season ORDER BY s_start DESC ";
$rs = db_query($sql);
$cnt = db_count($rs);
$sselect = "";

if($cnt)	{
	for($i = 0 ; $i < $cnt ; $i++)	{
		$list = db_array($rs);
		//최신 시즌
		if ($i == 0 && !isset($_GET['season'])) $season = $list['s_id'];

		if($season == $list['s_id'])
			$sselect .= "<option value=list.php?tid={$tid}&season={$list['s_id']} selected>{$list['s_name']}</option>";
		else
			$sselect .= "<option value=list.php?tid={$tid}&season={$list['s_id']}>{$list['s_name']}</option>";
	}
}

$t_rs = db_query(" select * FROM `savers_secret`.team ");
$t_cnt = db_count($t_rs);
$t_select = "";

if($t_cnt)	{
	for($i=0 ; $i<$t_cnt ; $i++){
		$t_list = db_array($t_rs);

		//최신 시즌
		if ($t_list['tid'] == 6 && !isset($_GET['tid'])) $tid = $t_list['tid'];

		if($tid == $t_list['tid'])
			$t_select .= "<option value=list.php?tid={$t_list['tid']}&season={$season} selected>{$t_list['t_name']}</option>";
		else
			$t_select .= "<option value=list.php?tid={$t_list['tid']}&season={$season}>{$t_list['t_name']}</option>";
	}
}

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

<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td><table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
		<td width="22"><img src="/images/admin/tbox_l.gif" width="22" height="22"></td>
		<td background="/images/admin/tbox_bg.gif"><strong>선수별 팀 공헌도 </strong></td>
		<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
		<br>
		<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
			<form name="form1" id="form1">
				<td><select name="season" onchange="MM_jumpMenu('this',this,0)">
					<option value='list.php?season='>시즌선택</option>
					<?php echo $sselect ; ?>
				</select>
					<select name="team" onchange="MM_jumpMenu('this',this,0)">
					<option value="list.php?season=">팀선택</option>
					<?php echo $t_select ; ?>
					</select></td>
				<td width="70" align="right">&nbsp;</td>
			</form>
			</tr>
		</table>
		<table width="97%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
			<tr align="center" bgcolor="#D2BF7E">
			<td height="35" bgcolor="#D2BF7E">선수명</td>
			<td>G</td>
			<td>Min</td>
			<td>2P<br /> 시도</td>
			<td>2P<br /> 실패</td>
			<td>2P%</td>
			<td>3P<br /> 시도</td>
			<td>3P<br /> 실패</td>
			<td>3P%</td>
			<td>FT<br /> 시도</td>
			<td>FT<br /> 실패</td>
			<td>FT%</td>
			<td>OR</td>
			<td>DR</td>
			<td>Ast</td>
			<td>Stl</td>
			<td>BS</td>
			<td>TO</td>
			<td>GD</td>
			<td>w/FT</td>
			<td>총득점</td>
			<td>평균<br /> 득점</td>
			<td>공헌도</td>
			</tr>
<?php
//선수정보
/*	이전 자료 davej....................................2007-04-12
	$psql = " SELECT * FROM `savers_secret`.player ";
	if($tid)
		$sqlwhere = " WHERE tid = {$tid} ORDER BY p_num ASC";
	$psql = $psql.$sqlwhere;
*/

	$psql = " SELECT * FROM `savers_secret`.player_teamhistory ";
	if($tid)
		$sqlwhere = " WHERE tid = {$tid} and sid = {$season} ORDER BY length(pbackno), pbackno ASC";
	$psql = $psql.$sqlwhere;

	$prs	= db_query($psql);
	$pcnt = db_count($prs);
	if($pcnt){
		for($i = 0 ; $i < $pcnt ; $i++){
			$plist = db_array($prs);
			if($i % 2)	$bgcolor = "#EFF1EF";
			else		$bgcolor = "#F8F8EA";

			//팀별 시즌별 선수 기록
			$con_sql = " SELECT sum(min) as min,
						sum(2p_a) as 2p_a,
						sum(2p_m) as 2p_m,
						sum(2p_a - 2p_m) as 2p_f,
						sum(3p_a) as 3p_a,
						sum(3p_m) as 3p_m,
						sum(3p_a - 3p_m) as 3p_f,
						sum(ft_a) as ft_a,
						sum(ft_m) as ft_m,
						sum(ft_a - ft_m) as ft_f,
						sum(re_off) as re_off,
						sum(re_def) as re_def,
						sum(ast) as ast,
						sum(stl) as stl,
						sum(bs) as bs,
						sum(gd) as gd,
						sum(tover) as tover,
						sum(w_oft) as w_oft,
						sum(1qs + 2qs + 3qs + 4qs + e1s + e2s + e3s) as score,
						count(pid) as cnt
						FROM `savers_secret`.record ";
			$con_where = " WHERE tid = {$tid} ";
			if($season)		$con_where .= " AND sid = {$season} ";
			$con_where .= " and pid = {$plist['pid']} ";
			$con_sql = $con_sql.$con_where;
			$con_rs = db_query($con_sql);
			$con_cnt = db_count($con_rs);

			if($con_cnt)	{
				for($j=0 ; $j<$con_cnt ; $j++)	{
					$con_list = db_array($con_rs);
					//출전시간
					$min2 = 0; $min1 = 0; $cont_min = 0;
					if(isset($con_list['min']) && $con_list['min']){
						$min2 = $con_list['min'] % 60;
						$min1 = (int)($con_list['min'] / 60);
						$con_list['min2'] = sprintf("%02d:%02d", $min1, $min2);
						$cont_min = $con_list['min'] / 60;
					} else {
						$con_list['min2'] = "00:00";
						$cont_min = 0;
					}
					//2득점 성공률
					$con_list['2p_p'] = (isset($con_list['2p_a']) && $con_list['2p_a'] > 0) ? number_format($con_list['2p_m'] / $con_list['2p_a'] * 100, 1) : 0;
					//3득점 성공률
					$con_list['3p_p'] = (isset($con_list['3p_a']) && $con_list['3p_a'] > 0) ? number_format($con_list['3p_m'] / $con_list['3p_a'] * 100, 1) : 0;
					//자유투 성공률
					$con_list['ft_p'] = (isset($con_list['ft_a']) && $con_list['ft_a'] > 0) ? number_format($con_list['ft_m'] / $con_list['ft_a'] * 100, 1) : 0;
					//평균득점
					$con_list['avg'] = (isset($con_list['score']) && $con_list['cnt'] > 0) ? number_format($con_list['score'] / $con_list['cnt'], 1) : 0;

					//공헌도
					$contribute1 = contribute1($con_list['score'], $con_list['stl'], $con_list['bs'], $con_list['re_def'], $con_list['re_off'], $con_list['ast'], $con_list['gd'], $cont_min);
					$contribute2 = contribute2($con_list['tover'], $con_list['2p_f'], $con_list['3p_f'], $con_list['ft_f']);
					$con_list['cont'] = number_format($contribute1 - $contribute2, 2);
?>
			<tr align="center" bgcolor="#F8F8EA">
			<td height="25" align="center" bgcolor="#F8F8EA"><?php echo htmlspecialchars($plist['pname']) ; ?></td>
			<td align="center" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['cnt']) ; ?></td>
			<td align="center" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['min2']) ; ?></td>
			<td align="center" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['2p_a']) ; ?></td>
			<td align="center" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['2p_f']) ; ?></td>
			<td align="center" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['2p_p']) ; ?></td>
			<td align="center" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['3p_a']) ; ?></td>
			<td align="center" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['3p_f']) ; ?></td>
			<td align="center" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['3p_p']) ; ?></td>
			<td align="center" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['ft_a']) ; ?></td>
			<td align="center" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['ft_f']) ; ?></td>
			<td align="center" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['ft_p']) ; ?></td>
			<td height="15" align="center" valign="middle" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['re_off']) ; ?></td>
			<td align="center" valign="middle" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['re_def']) ; ?></td>
			<td align="center" valign="middle" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['ast']) ; ?></td>
			<td align="center" valign="middle" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['stl']) ; ?></td>
			<td align="center" valign="middle" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['bs']) ; ?></td>
			<td align="center" valign="middle" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['tover']) ; ?></td>
			<td align="center" valign="middle" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['gd']) ; ?></td>
			<td align="center" valign="middle" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['w_oft']) ; ?></td>
			<td align="center" valign="middle" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['score']) ; ?></td>
			<td align="center" valign="middle" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['avg']) ; ?></td>
			<td align="center" valign="middle" bgcolor="#F8F8EA"><?php echo htmlspecialchars($con_list['cont']) ; ?></td>
			</tr>
<?php
}
			}
		}
	}
?>
		</table></td>
	</tr>
</table>

<p></p>
<?php echo $SITE['tail']; ?>