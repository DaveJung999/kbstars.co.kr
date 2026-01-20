<?php
$HEADER=array(
	'priv' => "운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
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

//공헌도
function contribute1($score, $stl, $bs, $re_def, $re_off, $ast, $gd, $min){
	$con1 = ($score + $stl + $bs + $re_def) * 1.0 + ($re_off + $ast + $gd) * 1.5 + $min/4;
	return $con1;
}
function contribute2($tover, $f2, $f3, $fft){
	$con2 = ($tover*1.5 + $f2*1.0 + $f3*0.9 + $fft*0.8);
	return $con2;
}

?>
<style type="text/css">
<!--
.style1 {color: #333333}
.style2 {
	color: #CC3300;
	font-weight: bold;
}
-->
</style>
<?php
if(isset($_GET['html']) && $_GET['html'] == "print")	$print = "&nbsp;";
	else $print = "<input type='image' src='/images/print_img.gif' border='0' onClick=\"window.open('list.php?html=print&season={$s_list['s_id']}&game={$game}');\">";

	$season = isset($_REQUEST['season']) ? $_REQUEST['season'] : null;
	$game 	= isset($_REQUEST['game']) ? $_REQUEST['game'] : null;

	//시즌
	$s_sql = " SELECT *, sid as s_id FROM season ORDER BY s_start DESC ";
	$s_rs	= db_query( $s_sql );
	$s_cnt = db_count( $s_rs );
	$s_select = "";

	if( $s_cnt )	{
		for( $i = 0 ; $i < $s_cnt ; $i++ ){
			$s_list = db_array( $s_rs );

			if( $i == 0 && !$season )
				$season = $s_list['s_id'];

			if( $season == $s_list['s_id'] ){
				$s_select .= "<option value='list.php?season={$s_list['s_id']}&game={$game}&html={$html}' selected>{$s_list['s_name']}</option>";
			} else {
				$s_select .= "<option value='list.php?season={$s_list['s_id']}&game={$game}&html={$html}'>{$s_list['s_name']}</option>";
			}
		}
	}

	if( !$game ) $game = 1;

	$g_sel1 = ($game == 1) ? "selected" : "";
	$g_sel2 = ($game == 2) ? "selected" : "";
	$g_sel3 = ($game == 3) ? "selected" : "";

	if( $game == 1 ){
		$divison = "정규시즌";
	}else if( $game == 2 ){
		$divison = "플레이오프";
	}else if( $game == 3 ){
		$divison = "챔피언결정전";
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
		<td background="/images/admin/tbox_bg.gif"><strong>선수별 경기기록 </strong></td>
		<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
		<br>
		<table width="97%" border="0" align="center" cellpadding="1" cellspacing="1">
			<tr>
			<td height="40"><form name="form1" id="form1">
				<select name="season" onchange="MM_jumpMenu('this',this,0)">
					<option value="list.php?season=&amp;game=">시즌선택</option>
					<?php echo $s_select ; ?>
				</select>
				<select name="game" onchange="MM_jumpMenu('this',this,0)">
					<option value="list.php?season=&amp;game=">경기구분</option>
					<option value="list.php?season=<?php echo htmlspecialchars($season) ; ?>&amp;game=1" <?php echo $g_sel1 ; ?>>정규시즌</option>
					<option value="list.php?season=<?php echo htmlspecialchars($season) ; ?>&amp;game=2" <?php echo $g_sel2 ; ?>>플레이오프</option>
					<option value="list.php?season=<?php echo htmlspecialchars($season) ; ?>&amp;game=3" <?php echo $g_sel3 ; ?>>챔피언결정전</option>
				</select>
			</form></td>
			</tr>
		</table>
		<table width="97%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
			<tr align="center" bgcolor="#D2BF7E">
				<td height="35" rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">배<br /> 번</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong>선수</strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">G</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">Min</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">2P</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">2PA</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong>%</strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">3P</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">3PA</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">%</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">FG%</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">FT</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">FTA</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">%</span></strong></td>
				<td height="25" colspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">RE-<br /> BOUNDS</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">RPG</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">Ast</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">APG</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">w/<br /> FT</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">w/<br /> oFT</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">Stl</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">BS</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">GD</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">TO</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">PTS</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">PPG</span></strong></td>
				<td rowspan="2" align="center" bgcolor="#D2BF7E"><strong><span class="style1">공헌도</span></strong></td>
			</tr>
			<tr>
				<td height="25" align="center" bgcolor="#D2BF7E"><strong><span class="style1">Off</span></strong></td>
				<td align="center" bgcolor="#D2BF7E"><strong><span class="style1">Def</span></strong></td>
			</tr>
<?php
//선수정보
	$p_sql = " SELECT * FROM player_teamhistory WHERE tid = 13 and sid={$season} ORDER BY length(pbackno), pbackno ASC ";
	$p_rs	= db_query( $p_sql );
	$p_cnt = db_count( $p_rs );

	if( $p_cnt ){
		for($i=0 ; $i<$p_cnt ; $i++){
			$p_list = db_array( $p_rs );

			//선수 시즌 기록
			$r_sql = " SELECT count(r.rid) as cnt,
							sum(r.min) as min,
							sum(r.2p_m) as m2,
							sum(r.2p_a) as a2,
							sum(r.2p_a) - sum(r.2p_m) as f2,
							sum(r.3p_m) as m3,
							sum(r.3p_a) as a3,
							sum(r.3p_a) - sum(r.3p_m) as f3,
							sum(r.ft_m) as mft,
							sum(r.ft_a) as aft,
							sum(r.ft_a) - sum(r.ft_m) as fft,
							sum(r.re_off) as re_off,
							sum(r.re_def) as re_def,
							sum(r.ast) as ast,
							sum(r.stl) as stl,
							sum(r.w_ft) as w_ft,
							sum(r.w_oft) as w_oft,
							sum(r.bs) as bs,
							sum(r.gd) as gd,
							sum(r.tover) as tover,
							sum(r.1qs) + sum(r.2qs) + sum(r.3qs) + sum(r.4qs) + sum(r.e1s) + sum(r.e2s) + sum(r.e3s) as score
					FROM record r, game g
					WHERE r.gid=g.gid and r.pid={$p_list['pid']} and r.sid={$season} and g.g_division = '{$divison}' ";

			$r_rs	= db_query( $r_sql );
			$r_cnt = db_count( $r_rs );

			if( $r_cnt )	{
				for( $j=0 ; $j < $r_cnt ; $j++ ){
					$r_list = db_array( $r_rs );
					//출전시간
					$min = "0:00";
					$min1 = 0;
					$min2 = 0;
					$cont_min = 0;
					if($r_list['min']){
						$min2 = $r_list['min'] % 60;
						$min1 = (int)($r_list['min'] / 60);
						$min = sprintf("%d:%02d", $min1, $min2);
						$cont_min = $r_list['min'] / 60;
					}
					//2점슛 성공률
					$r_list['p2'] = (isset($r_list['a2']) && $r_list['a2'] > 0) ? number_format($r_list['m2'] / $r_list['a2'] * 100, 1) : 0;
					
					//3점슛 성공률
					$r_list['p3'] = (isset($r_list['a3']) && $r_list['a3'] > 0) ? number_format($r_list['m3'] / $r_list['a3'] * 100, 1) : 0;
					
					//필드골 성공률
					$total_a = (isset($r_list['a2']) ? $r_list['a2'] : 0) + (isset($r_list['a3']) ? $r_list['a3'] : 0);
					$total_m = (isset($r_list['m2']) ? $r_list['m2'] : 0) + (isset($r_list['m3']) ? $r_list['m3'] : 0);
					$r_list['p_fg'] = ($total_a > 0) ? number_format($total_m / $total_a * 100, 1) : 0;
					
					//자유투 성공률
					$r_list['pft'] = (isset($r_list['aft']) && $r_list['aft'] > 0) ? number_format($r_list['mft'] / $r_list['aft'] * 100, 1) : 0;
					
					//경기당 리바운드 RPG
					$total_rebound = (isset($r_list['re_off']) ? $r_list['re_off'] : 0) + (isset($r_list['re_def']) ? $r_list['re_def'] : 0);
					$r_list['re'] = (isset($r_list['cnt']) && $r_list['cnt'] > 0) ? number_format($total_rebound / $r_list['cnt'], 1) : 0;
					
					//경기당 어시스트 APG
					$r_list['apg'] = (isset($r_list['ast']) && $r_list['cnt'] > 0) ? number_format($r_list['ast'] / $r_list['cnt'], 1) : 0;
					
					//경기당 평균득점 PPG
					$r_list['ppg'] = (isset($r_list['score']) && $r_list['cnt'] > 0) ? number_format($r_list['score'] / $r_list['cnt'], 1) : 0;
					
					//공헌도
					$con1 = contribute1($r_list['score'], $r_list['stl'], $r_list['bs'], $r_list['re_def'], $r_list['re_off'], $r_list['ast'], $r_list['gd'], $cont_min);
					$con2 = contribute2($r_list['tover'], $r_list['f2'], $r_list['f3'], $r_list['fft']);
					$con = ($r_list['cnt'] > 0) ? number_format($con1 - $con2, 2) : 0;
				}
			}
?>
			<tr align="center" bgcolor="#F8F8EA" onMouseOver="this.style.backgroundColor='#C6E2F9'" onMouseOut="this.style.backgroundColor=''">
				<td height="30" align="center" bgcolor="#F8F8EA"><?php echo htmlspecialchars($p_list['pbackno']) ; ?></td>
				<td height="25" align="center" nowrap="nowrap"><?php echo htmlspecialchars($p_list['pname']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['cnt']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($min) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['m2']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['a2']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['p2']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['m3']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['a3']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['p3']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['p_fg']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['mft']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['aft']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['pft']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['re_off']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['re_def']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['re']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['ast']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['apg']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['w_ft']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['w_oft']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['stl']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['bs']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['gd']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['tover']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['score']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($r_list['ppg']) ; ?></td>
				<td height="25" align="center"> <?php echo htmlspecialchars($con) ; ?></td>
			</tr>
<?php
		}
	}
?>
		</table></td>
	</tr>
</table>

<br>
<?php echo $SITE['tail']; ?>