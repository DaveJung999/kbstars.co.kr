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
else $print = "<img src='/images/print_img.gif' border='0' onClick=\"window.open('list3.php?html=print&pid={$pid}&tid={$tid}');\">";

//시즌정보
$sql_season = " SELECT *, sid as s_id FROM season ORDER BY s_start DESC ";
$rs_season = db_query($sql_season);
$cnt_season = db_count($rs_season);

if($cnt_season)	{
	for($i = 0 ; $i < $cnt_season ; $i++)	{
		$list_season = db_array($rs_season);
		//최신 시즌
		if ($i == 0 && !isset($_GET['session_id'])) $session_id = $list_season['s_id'];

		if($session_id == $list_season['s_id'])
			$sselect .= "<option value='list3.php?pid=&session_id={$list_season['s_id']}' selected>{$list_season['s_name']}</option>";
		else
			$sselect .= "<option value='list3.php?pid=&session_id={$list_season['s_id']}'>{$list_season['s_name']}</option>";
	}
}

//선수이름 선택
$sql = " SELECT * FROM player_teamhistory WHERE tid = {$tid} and sid = {$session_id} ORDER BY length(pbackno), pbackno ASC";
$rs = db_query($sql);
$cnt = db_count($rs);

if($cnt)	{
	for($i = 0 ; $i < $cnt ; $i++)	{
		$list = db_array($rs);
		//최신 시즌
		if ($i == 0 && !isset($_GET['pid'])) $pid = $list['pid'];

		if($pid == $list['pid']){
			$select .= "<option value='list3.php?pid={$list['pid']}&session_id={$session_id}' selected>{$list['pname']}</option>";
			$pname = $list['pname'];
		} else {
			$select .= "<option value='list3.php?pid={$list['pid']}&session_id={$session_id}'>{$list['pname']}</option>";
			if($i == 0)
				$pname = $list['pname'];
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
					<option option="option" value="list3.php?pid=">선수선택</option>
					<?php echo $select ; ?>
					</select></td>
				<td width="70"> <?php echo $print ; ?></td>
			</form>
			</tr>
		</table>
		<table width="97%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
			<tr>
			<td height="40" align="center" bgcolor="#F8F8EA"><font color="#0066FF"><strong><?php echo $pname ; ?>
			</strong></font> <strong><font color="#CC3300">선수 공헌도</font></strong></td>
			</tr>
		</table>
		<table width="97%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
			<tr align="center" bgcolor="#D2BF7E">
				<td height="40" bgcolor="#D2BF7E">구분</td>
				<td>Min</td>
				<td>2P<br /> 시도 </td>
				<td bgcolor="#D2BF7E">2P<br /> 실패</td>
				<td bgcolor="#D2BF7E">2P<br /> 성공률</td>
				<td>3P<br /> 시도</td>
				<td>3P<br /> 실패</td>
				<td bgcolor="#D2BF7E">3P<br /> 성공률</td>
				<td>FT<br /> 시도</td>
				<td>FT<br /> 실패</td>
				<td bgcolor="#D2BF7E">FT<br /> 성공률</td>
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
//선수 평균/누적 성적 시작
	$sql = " SELECT
				sum(min) as min,
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
			FROM record
			WHERE pid = {$pid} and sid = {$session_id} ";
	$rs = db_query($sql);
	$cnt = db_count($rs);

	if($cnt){
		$list = db_array($rs);

		//출전시간
		if(isset($list['min']) && $list['min']){
			$min2 = $list['min'] % 60;
			$min1 = (int)($list['min'] / 60);
			$list['min2'] = sprintf("%02d:%02d", $min1, $min2);
			$cont_min = $list['min'] / 60;
		} else {
			$list['min2'] = "00:00";
			$cont_min = 0;
		}

		//2득점 성공률
		if(isset($list['2p_a']) && $list['2p_a']){
			$list['2p_p'] = number_format(($list['2p_a'] - $list['2p_f']) / $list['2p_a'] * 100, 1);
			if($list['2p_p'] == 0.0)	$list['2p_p'] = 0;
		} else {
			$list['2p_p'] = 0;
		}
		//3득점 성공률
		if(isset($list['3p_a']) && $list['3p_a']){
			$list['3p_p'] = number_format(($list['3p_a'] - $list['3p_f']) / $list['3p_a'] * 100, 1);
			if($list['3p_p'] == 0.0)	$list['3p_p'] = 0;
		} else {
			$list['3p_p'] = 0;
		}
		//FT 성공률
		if(isset($list['ft_a']) && $list['ft_a']){
			$list['ft_p'] = number_format(($list['ft_a'] - $list['ft_f']) / $list['ft_a'] * 100, 1);
			if($list['ft_p'] == 0.0)	$list['ft_p'] = 0;
		} else {
			$list['ft_p'] = 0;
		}
		//평균득점
		if(isset($list['score']) && $list['cnt'] > 0)
			$list['avg'] = number_format($list['score'] / $list['cnt']);
		else
			$list['avg'] = 0;
		//공헌도
		$con1 = contribute1($list['score'], $list['stl'], $list['bs'], $list['re_def'], $list['re_off'], $list['ast'], $list['gd'], $cont_min);
		$con2 = contribute2($list['tover'], $list['2p_f'], $list['3p_f'], $list['ft_f']);
		if($list['cnt'] > 0)
			$list['cont'] = number_format($con1 - $con2, 2);
		else
			$list['cont'] = 0;
	}

?>
			<tr align="center" bgcolor="#f0f0f0">
				<td height="30">합계</td>
				<td><?php echo $list['min2'] ; ?></td>
				<td><?php echo $list['2p_a'] ; ?></td>
				<td><?php echo $list['2p_f'] ; ?></td>
				<td><?php echo $list['2p_p'] ; ?></td>
				<td><?php echo $list['3p_a'] ; ?></td>
				<td><?php echo $list['3p_f'] ; ?></td>
				<td><?php echo $list['3p_p'] ; ?></td>
				<td><?php echo $list['ft_a'] ; ?></td>
				<td><?php echo $list['ft_f'] ; ?></td>
				<td><?php echo $list['ft_p'] ; ?></td>
				<td height="30"><?php echo $list['re_off'] ; ?></td>
				<td><?php echo $list['re_def'] ; ?></td>
				<td><?php echo $list['ast'] ; ?></td>
				<td><?php echo $list['stl'] ; ?></td>
				<td><?php echo $list['bs'] ; ?></td>
				<td><?php echo $list['tover'] ; ?></td>
				<td><?php echo $list['w_oft'] ; ?></td>
				<td><?php echo $list['score'] ; ?></td>
				<td><?php echo $list['avg'] ; ?></td>
				<td><?php echo $list['cont'] ; ?></td>
			</tr>
<?php
//선수 평균/누적 성적 끝

//최근 5경기 기록 시작
	$sql5 = "SELECT *
			FROM `record`
			WHERE pid={$pid} and tid={$tid} and sid = {$session_id}
			ORDER BY rdate desc
			";
	$rs5 = db_query($sql5);
	$cnt5 = db_count($rs5);
	if($cnt5)	{
		for($i=0 ; $i<$cnt5 ; $i++)	{
			$list5 = db_array($rs5);
			//출전시간
			if(isset($list5['min']) && $list5['min']){
				$min2 = $list5['min'] % 60;
				$min1 = (int)($list5['min'] / 60);
				$min = sprintf("%02d:%02d", $min1, $min2);
				$cont_min = $list5['min'] / 60;
			} else {
				$min = "00:00";
				$cont_min = 0;
			}
			//2P 실패 및 2P 성공률
			$list5['2p_f'] = $list5['2p_a'] - $list5['2p_m'];
			if(isset($list5['2p_a']) && $list5['2p_a']){
				$list5['2p_p'] = number_format( $list5['2p_m'] / $list5['2p_a'] * 100, 1);
				if($list5['2p_p'] == 0.0) $list5['2p_p'] = 0;
			} else {
				$list5['2p_p'] = 0;
			}
			//3P 실패 및 3P 성공률
			$list5['3p_f'] = $list5['3p_a'] - $list5['3p_m'];
			if(isset($list5['3p_a']) && $list5['3p_a']){
				$list5['3p_p'] = number_format( $list5['3p_m'] / $list5['3p_a'] * 100, 1);
				if($list5['3p_p'] == 0.0) $list5['3p_p'] = 0;
			} else {
				$list5['3p_p'] = 0;
			}
			//FT 실패 및 FT 성공률
			$list5['ft_f'] = $list5['ft_a'] - $list5['ft_m'];
			if(isset($list5['ft_a']) && $list5['ft_a']){
				$list5['ft_p'] = number_format( $list5['ft_m'] / $list5['ft_a'] * 100, 1);
				if($list5['ft_p'] == 0.0) $list5['ft_p'] = 0;
			} else {
				$list5['ft_p'] = 0;
			}
			//득점
			$list5['score'] = $list5['1qs'] + $list5['2qs'] + $list5['3qs'] + $list5['4qs'] + $list5['e1s'] + $list5['e2s'] + $list5['e3s'];
			//공헌도
			$con1 = contribute1($list5['score'], $list5['stl'], $list5['bs'], $list5['re_def'], $list5['re_off'], $list5['ast'], $list5['gd'], $cont_min);
			$con2 = contribute2($list5['tover'], $list5['2p_f'], $list5['3p_f'], $list5['ft_f']);
			$list5['contribute'] = number_format($con1 - $con2, 2);

			//게임날짜
			$grs = db_query(" select * FROM game where gid = {$list5['gid']} ");
			$glist = db_array($grs);
			$list5['gday'] = $glist['g_start'];
			$list5['gday'] = date("y/m/d", $list5['gday']);
			//상대팀
			if($tid == $glist['g_home'])	$other_tid = $glist['g_away'];
			else						$other_tid = $glist['g_home'];

			$trs = db_query(" select * FROM team where tid = {$other_tid} ");
			$tlist = db_array($trs);
			$other_name = $tlist['t_name'];
			//날짜-상대팀
			$list5['seperate'] = "<font color='darkblue'>[".$list5['gday']."]</font> 대 ".$other_name;
?>
			<tr bgcolor="#F8F8EA">
				<td height="30" bgcolor="#F8F8EA">&nbsp;&nbsp;&nbsp;<?php echo $list5['seperate'] ; ?></td>
				<td align="center"> <?php echo $min ; ?></td>
				<td align="center"> <?php echo $list5['2p_a'] ; ?></td>
				<td align="center"> <?php echo $list5['2p_f'] ; ?></td>
				<td align="center"> <?php echo $list5['2p_p'] ; ?></td>
				<td align="center"> <?php echo $list5['3p_a'] ; ?></td>
				<td align="center"> <?php echo $list5['3p_f'] ; ?></td>
				<td align="center"> <?php echo $list5['3p_p'] ; ?></td>
				<td align="center"> <?php echo $list5['ft_a'] ; ?></td>
				<td align="center"> <?php echo $list5['ft_f'] ; ?></td>
				<td align="center"> <?php echo $list5['ft_p'] ; ?></td>
				<td height="30" align="center" bgcolor="#F8F8EA"><?php echo $list5['re_off']; ?></td>
				<td align="center" bgcolor="#F8F8EA"><?php echo $list5['re_def']; ?></td>
				<td align="center" bgcolor="#F8F8EA"><?php echo $list5['ast']; ?></td>
				<td align="center" bgcolor="#F8F8EA"><?php echo $list5['stl']; ?></td>
				<td align="center" bgcolor="#F8F8EA"><?php echo $list5['bs']; ?></td>
				<td align="center" bgcolor="#F8F8EA"><?php echo $list5['tover']; ?></td>
				<td align="center" bgcolor="#F8F8EA"><?php echo $list5['w_oft']; ?></td>
				<td align="center" bgcolor="#F8F8EA"><?php echo $list5['score']; ?></td>
				<td align="center" bgcolor="#F8F8EA"></td>
				<td align="center" bgcolor="#F8F8EA"><b>
				<?php echo $list5['contribute'] ; ?>
				</b></td>
			</tr>
<?php
		}
	}
//최근 5경기 기록 끝; ?>
		</table></td>
	</tr>
</table>

<p></p>
<?php echo $SITE['tail']; ?>