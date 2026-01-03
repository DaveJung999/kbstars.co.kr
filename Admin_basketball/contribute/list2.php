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

if(isset($_GET['html']) && $_GET['html'] == "print")	$print = "&nbsp;";
else $print = "<img src='/images/print_img.gif' border='0' onClick=\"window.open('list2.php?html=print&pid={$pid}&tid={$tid}');\">";

//시즌정보
$sql_season = " SELECT *, sid as s_id FROM `savers_secret`.season ORDER BY s_start DESC ";
$rs_season = db_query($sql_season);
$cnt_season = db_count($rs_season);

if($cnt_season)	{
	for($i = 0 ; $i < $cnt_season ; $i++)	{
		$list_season = db_array($rs_season);
		//최신 시즌
		if ($i == 0 && !isset($_GET['session_id'])) $session_id = $list_season['s_id'];

		if($session_id == $list_season['s_id']){
			$sselect .= "<option value='list2.php?pid=&session_id={$list_season['s_id']}&tid={$tid}' selected>{$list_season['s_name']}</option>";
			$sname = $list_season['s_name'];
		} else {
			$sselect .= "<option value='list2.php?pid=&session_id={$list_season['s_id']}&tid={$tid}'>{$list_season['s_name']}</option>";
			if($i == 0) $sname = $list_season['s_name'];
		}
	}
}
//상대팀 선택
$tsql = " SELECT * FROM `savers_secret`.team WHERE tid != 6";
$trs = db_query($tsql);
$tcnt = db_count($trs);
if($tcnt)	{
	for($i=0 ; $i<$tcnt ; $i++){
		$olist = db_array($trs);
		if ($i == 0 && !isset($_GET['tid'])) $tid = $olist['tid'];
		if($tid == $olist['tid']){
			$osel .= "<option value='list2.php?pid={$pid}&session_id={$session_id}&tid={$olist['tid']}' selected>{$olist['t_name']}</option>";
			$t_name = $olist['t_name'];
		} else {
			$osel .= "<option value='list2.php?pid={$pid}&session_id={$session_id}&tid={$olist['tid']}'>{$olist['t_name']}</option>";
			if($i == 0) $t_name = $olist['t_name'];
		}
	}
}
if(!$tid)	$tid = $t_id[0];

//선수이름 선택
$sql = " SELECT * FROM `savers_secret`.player_teamhistory WHERE tid = 6 and sid = {$session_id} ORDER BY length(pbackno), pbackno ASC";
$rs = db_query($sql);
$cnt = db_count($rs);

if($cnt)	{
	for($i = 0 ; $i < $cnt ; $i++)	{
		$list = db_array($rs);
		//
		if ($i == 0 && !isset($_GET['pid'])) $pid = $list['pid'];

		if($pid == $list['pid']){
			$select .= "<option value='list2.php?pid={$list['pid']}&session_id={$session_id}&tid={$tid}' selected>{$list['pname']}</option>";
			$pname = $list['pname'];
		} else {
			$select .= "<option value='list2.php?pid={$list['pid']}&session_id={$session_id}&tid={$tid}'>{$list['pname']}</option>";
			if($i == 0) $pname = $list['pname'];
		}
	}
}
if(!$pid)	$pid = $p_id[0]; ?>
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

<script type="text/JavaScript">
<!--
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
			<td background="/images/admin/tbox_bg.gif"><strong>선수별 공헌도 </strong></td>
			<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
		<br>
		<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
			<form name="form1" id="form1">
				<td><select name="session_id" onchange="MM_jumpMenu('this',this,0)">
					<option value='list3.php?pid='>시즌선택</option>
					<?php echo $sselect ; ?>
				</select>
					<select name="pid" onchange="MM_jumpMenu('this',this,0)">
					<option option="option" value="list2.php?pid=&amp;tid=">선수선택</option>
					<?php echo $select ; ?>
					</select>
					<select name="tid" onchange="MM_jumpMenu('this',this,0)">
					<option option="option" value="list2.php?pid=&amp;tid=">상대팀선택</option>
					<?php echo $osel ; ?>
					</select></td>
				<td width="70" align="right">
				<?php echo $print ; ?></td>
			</form>
			</tr>
		</table>
		<table width="97%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
			<tr>
			<td height="40" align="center" bgcolor="#F8F8EA"><b>
				[ <?php echo $sname ; ?> ] <font color="#0066FF"><?php echo $pname ; ?> </font></b>
				<strong><font color="#CC3300">선수의	VS </font></strong> <b><font color="#0066FF"><?php echo $t_name ; ?></font></b> 
				<strong><font color="#CC3300">공헌도</font></strong></td>
			</tr>
		</table>
		<table width="97%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
			<tr align="center" bgcolor="#D2BF7E">
				<td height="40" bgcolor="#D2BF7E">구분</td>
				<td>Min</td>
				<td>2P<br /> 시도 </td>
				<td>2P<br /> 실패</td>
				<td>2P<br /> 성공률</td>
				<td>3P<br /> 시도</td>
				<td>3P<br /> 실패</td>
				<td>3P<br /> 성공률</td>
				<td>FT<br /> 시도</td>
				<td>FT<br /> 실패</td>
				<td>FT<br /> 성공률</td>
				<td>OR</td>
				<td>DR</td>
				<td>Ast</td>
				<td>Stl</td>
				<td>BS</td>
				<td>TO</td>
				<td>w/FT</td>
				<td>득점</td>
				<td>평균<br /> 득점</td>
				<td>공헌도</td>
			</tr>
<?php
$gsql = " SELECT * FROM `savers_secret`.game WHERE (g_home = 6	AND g_away = {$tid}) or (g_home = {$tid}	AND g_away = 6) and sid={$session_id} ";
$grs = db_query($gsql);
$gcnt = db_count($grs);
if($gcnt){
	$min0 = 0;
	$m3 = 0;
	$a3 = 0;
	$m2 = 0;
	$a2 = 0;
	$mft = 0;
	$aft = 0;
	$re_off = 0;
	$re_def = 0;
	$ast = 0;
	$stl = 0;
	$gd = 0;
	$bs = 0;
	$w_oft = 0;
	$tover = 0;
	$qs1 = 0;
	$qs2 = 0;
	$qs3 = 0;
	$qs4 = 0;
	$e1s = 0;
	$e2s = 0;
	$e3s = 0;

	for($j=0 ; $j<$gcnt ; $j++){
		$glist = db_array($grs);
		$gid[$j] = $glist['gid'];

		$sql5 = " SELECT * FROM `savers_secret`.record WHERE pid={$pid} and gid={$gid[$j]} and sid={$session_id} ";
		$rs5 = db_query($sql5);
		$cnt5 = db_count($rs5);

		if($cnt5){
			$list5 = db_array($rs5);

			$qs1 += $list5['1qs'];
			$qs2 += $list5['2qs'];
			$qs3 += $list5['3qs'];
			$qs4 += $list5['4qs'];
			$e1s += $list5['e1s'];
			$e2s += $list5['e2s'];
			$e3s += $list5['e3s'];
			$min0 += $list5['min'];
			$m3 += $list5['3p_m'];
			$a3 += $list5['3p_a'];
			$m2 += $list5['2p_m'];
			$a2 += $list5['2p_a'];
			$mft += $list5['ft_m'];
			$aft += $list5['ft_a'];
			$re_off += $list5['re_off'];
			$re_def += $list5['re_def'];
			$ast += $list5['ast'];
			$stl += $list5['stl'];
			$gd += $list5['gd'];
			$bs += $list5['bs'];
			$w_oft += $list5['w_oft'];
			$tover += $list5['tover'];

		}
	}
	$min2 = $min0 % 60;
	$min1 = (int)($min0 / 60);
	$min = sprintf("%02d:%02d", $min1, $min2);
	$cont_min = $min0 / 60;

	$f2 = $a2 - $m2;
	$f3 = $a3 - $m3;
	$fft = $aft - $mft;
	$p2 = ($a2 > 0) ? number_format($m2 / $a2 * 100, 1) : 0;
	$p3 = ($a3 > 0) ? number_format($m3 / $a3 * 100, 1) : 0;
	$pft = ($aft > 0) ? number_format($mft / $aft * 100, 1) : 0;

	$score = ($qs1*1) + ($qs2*1) + ($qs3*1) + ($qs4*1) + ($e1s*1) + ($e2s*1) + ($e3s*1);
	$game_count = $gcnt;
	$avg = ($game_count > 0) ? number_format($score / $game_count, 1) : 0;

	$contribute1 = contribute1($score, $stl, $bs, $re_def, $re_off, $ast, $gd, $cont_min);
	$contribute2 = contribute2($tover, $f2, $f3, $fft);
	$cont = number_format($contribute1 - $contribute2, 2);
}

?>
			<tr align="center" bgcolor="#F2F2F2">
			<td height="30" bgcolor="#F2F2F2">합계</td>
			<td>&nbsp;<?php echo $min ; ?></td>
			<td>&nbsp;<?php echo $a2 ; ?></td>
			<td>&nbsp;<?php echo $f2 ; ?></td>
			<td>&nbsp;<?php echo $p2 ; ?></td>
			<td>&nbsp;<?php echo $a3 ; ?></td>
			<td>&nbsp;<?php echo $f3 ; ?></td>
			<td>&nbsp;<?php echo $p3 ; ?></td>
			<td>&nbsp;<?php echo $aft ; ?></td>
			<td>&nbsp;<?php echo $fft ; ?></td>
			<td>&nbsp;<?php echo $pft ; ?></td>
			<td height="30" bgcolor="#F2F2F2">&nbsp;<?php echo $re_off ; ?></td>
			<td bgcolor="#F2F2F2">&nbsp;<?php echo $re_def ; ?></td>
			<td bgcolor="#F2F2F2">&nbsp;<?php echo $ast ; ?></td>
			<td bgcolor="#F2F2F2">&nbsp;<?php echo $stl ; ?></td>
			<td bgcolor="#F2F2F2">&nbsp;<?php echo $bs ; ?></td>
			<td bgcolor="#F2F2F2">&nbsp;<?php echo $tover ; ?></td>
			<td bgcolor="#F2F2F2">&nbsp;<?php echo $w_oft ; ?></td>
			<td bgcolor="#F2F2F2">&nbsp;<?php echo $score ; ?></td>
			<td bgcolor="#F2F2F2">&nbsp;<?php echo $avg ; ?></td>
			<td bgcolor="#F2F2F2">&nbsp;<b>
<?php echo $cont ; ?>
			</b></td>
			</tr>
<?php
	//선수 평균/누적 성적 끝
	//최근 5경기 기록 시작
	for($k=0 ; $k < count($gid) ; $k++)	{
		$min=""; $a2=""; $f2=""; $p2=""; $a3=""; $f3=""; $p3=""; $aft=""; $fft=""; $pft=""; $re_off=""; $re_def=""; $ast=""; $stl=""; $bs=""; $tover=""; $w_oft=""; $score=""; $avg=""; $cont="";
		$sqlg = " SELECT *	FROM `savers_secret`.record WHERE pid = {$pid} AND gid = {$gid[$k]} and sid={$session_id} ";
		$rsg = db_query($sqlg);
		$cntg = db_count($rsg);
		if($cntg){
			$listg = db_array($rsg);
		//상대팀명 팀아이디, 팀이름 - 국민은행제외(tid 6)===========================================================================================
			$rst = db_query(" SELECT t_name FROM `savers_secret`.team A, `savers_secret`.game B WHERE (A.tid = B.g_home OR A.tid = B.g_away) AND B.gid = {$gid[$k]} AND A.tid != 6 ");
			$list_tname = db_array($rst);
		//============================================================================================================================================
			$min = $listg['min'];
			$a2 = $listg['2p_a'];
			$a3 = $listg['3p_a'];
			$aft = $listg['ft_a'];
			$m2 = $listg['2p_m'];
			$m3 = $listg['3p_m'];
			$mft = $listg['ft_m'];
			$f2 = $a2 - $m2;
			$f3 = $a3 - $m3;
			$fft = $aft - $mft;

			$p2 = ($a2 > 0) ? number_format(($m2 / $a2) * 100, 1) : 0;
			$p3 = ($a3 > 0) ? number_format(($m3 / $a3) * 100, 1) : 0;
			$pft = ($aft > 0) ? number_format(($mft / $aft) * 100, 1) : 0;
			$score = ($listg['1qs']*1) + ($listg['2qs']*1) + ($listg['3qs']*1) + ($listg['4qs']*1) + ($listg['e1s']*1) + ($listg['e2s']*1) + ($listg['e3s']*1);

			if($min){
				$min2 = $min % 60;
				$min1 = (int)($min / 60);
				$min = sprintf("%02d:%02d", $min1, $min2);
				$cont_min = $min / 60;
			} else {
				$min = "00:00";
				$cont_min = 0;
			}

			$con1 = contribute1($score, $listg['stl'], $listg['bs'], $listg['re_def'], $listg['re_off'], $listg['ast'], $listg['gd'], $cont_min);
			$con2 = contribute2($listg['tover'], $f2, $f3, $fft);
			$contribute = number_format($con1 - $con2, 2);
			$aa++;

			//게임날짜
			$grs = db_query(" select * FROM `savers_secret`.game where gid = {$gid[$k]} ");
			$glist = db_array($grs);
			$glist['g_start'] = date("y/m/d", $glist['g_start']);
?>
			<tr align="center" bgcolor="#F8F8EA">
				<td height="30" bgcolor="#F8F8EA">&nbsp;<?php echo	"<font color='darkblue'>[".$glist['g_start']."]</font> ".$list_tname['t_name'] ; ?></td>
				<td>&nbsp;<?php echo $min ; ?></td>
				<td>&nbsp;<?php echo $a2 ; ?></td>
				<td>&nbsp;<?php echo $f2 ; ?></td>
				<td>&nbsp;<?php echo $p2 ; ?></td>
				<td>&nbsp;<?php echo $a3 ; ?></td>
				<td>&nbsp;<?php echo $f3 ; ?></td>
				<td>&nbsp;<?php echo $p3 ; ?></td>
				<td>&nbsp;<?php echo $aft ; ?></td>
				<td>&nbsp;<?php echo $fft ; ?></td>
				<td>&nbsp;<?php echo $pft ; ?></td>
				<td height="30" bgcolor="#F8F8EA">&nbsp;<?php echo $listg['re_off'] ; ?></td>
				<td bgcolor="#F8F8EA">&nbsp;<?php echo $listg['re_def'] ; ?></td>
				<td bgcolor="#F8F8EA">&nbsp;<?php echo $listg['ast'] ; ?></td>
				<td bgcolor="#F8F8EA">&nbsp;<?php echo $listg['stl'] ; ?></td>
				<td bgcolor="#F8F8EA">&nbsp;<?php echo $listg['bs'] ; ?></td>
				<td bgcolor="#F8F8EA">&nbsp;<?php echo $listg['tover'] ; ?></td>
				<td bgcolor="#F8F8EA">&nbsp;<?php echo $listg['w_oft'] ; ?></td>
				<td bgcolor="#F8F8EA">&nbsp;<?php echo $score ; ?></td>
				<td bgcolor="#F8F8EA">&nbsp;</td>
				<td bgcolor="#F8F8EA">&nbsp;<b>	<?php echo $contribute ; ?>
				</b></td>
			</tr>
<?php
		}
	}
//최근 5경기 기록 끝; ?>
		</table></td>
	</tr>
</table>

<p>&nbsp;</p>
<?php echo $SITE['tail']; ?>