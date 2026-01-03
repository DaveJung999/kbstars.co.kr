<?php
//=======================================================
// 설	명 : 경기 정보 처리 (write.php)
// 책임자 : 박선민 (sponsor@new21.com)
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 25/08/15 Gemini AI PHP 7+ 마이그레이션 및 유효성 검사 수정
//=======================================================
$HEADER=array(
		'priv' => "운영자,뉴스관리자,사진관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'html_echo' => '', // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
		'log' => '' // log_site 테이블에 지정한 키워드로 로그 남김
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']);
//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
// $seHTTP_REFERER는 어디서 링크하여 왔는지 저장하고, 로그인하면서 로그에 남기고 삭제된다.
if( !isset($_SESSION['seUserid']) && !isset($_SESSION['seHTTP_REFERER']) && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER["HTTP_HOST"]) === false ){
	$_SESSION['seHTTP_REFERER'] = $_SERVER['HTTP_REFERER'];
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

$list = [];
if($mode == "modify" && $gid)	{
	$sql = " SELECT *, sid as s_id FROM `savers_secret`.game WHERE gid = " . (int)$gid;
	$rs = db_query($sql);
	$cnt = db_count($rs);

	if($cnt){
		$list = db_array($rs);

		if(($list['g_start'] ?? 0) > 0){
			$list['g_start'] 	= date("YmdHis", $list['g_start']);
			$start_y 		= substr($list['g_start'], 0, 4);
			$start_m 		= substr($list['g_start'], 4, 2);
			$start_d 		= substr($list['g_start'], 6, 2);
			$start_h 		= substr($list['g_start'], 8, 2);
			$start_mm 		= substr($list['g_start'], 10, 2);
		}
		if(($list['g_end'] ?? 0) > 0){
			$list['g_end']	= date("YmdHis", $list['g_end']);
			$end_y			= substr($list['g_end'], 0, 4);
			$end_m 			= substr($list['g_end'], 4, 2);
			$end_d 			= substr($list['g_end'], 6, 2);
			$end_h 			= substr($list['g_end'], 8, 2);
			$end_mm 		= substr($list['g_end'], 10, 2);
		}
	}
} else {
	$list['s_id'] = $_GET['season'] ?? null;
	$sql = "select * from `savers_secret`.season where sid='" . db_escape($list['s_id']) . "'";
	if($season_info = db_arrayone($sql)){
		$list['g_start'] = $season_info['s_start'];
		$list['g_end'] = $season_info['s_end'];

		if(($list['g_start'] ?? 0) > 0){
			$list['g_start'] 	= date("YmdHis", $list['g_start']);
			$start_y 		= substr($list['g_start'], 0, 4);
			$start_m 		= substr($list['g_start'], 4, 2);
			$start_d 		= substr($list['g_start'], 6, 2);
			$start_h 		= substr($list['g_start'], 8, 2);
			$start_mm 		= substr($list['g_start'], 10, 2);
		}
		if(($list['g_end'] ?? 0) > 0){
			$list['g_end']	= date("YmdHis", $list['g_end']);
			$end_y			= substr($list['g_end'], 0, 4);
			$end_m 			= substr($list['g_end'], 4, 2);
			$end_d 			= substr($list['g_end'], 6, 2);
			$end_h 			= substr($list['g_end'], 8, 2);
			$end_mm 		= substr($list['g_end'], 10, 2);
		}
	}

	// E-internet 높이 넓이 초기값 셋팅
	$list['etv_width'] = 342 ;
	$list['etv_height'] = 417 ;
}

//시즌 정보 가져오기
$ssql = " select *, sid as s_id from `savers_secret`.season ";
$srs = db_query($ssql);
$scnt = db_count($srs);

$s_sid = [];
$s_name = [];
if($scnt)	{
	while($slist = db_array($srs)) {
		$s_sid[] 	= $slist['s_id'];
		$s_name[] = $slist['s_name'];
	}
}

//팀정보 가져오기
$tsql = " select * from `savers_secret`.team order by tid ";
$trs = db_query($tsql);
$tcnt = db_count($trs);

$t_tid = [];
$t_name = [];
if($tcnt)	{
	while($tlist = db_array($trs)) {
		$t_tid[] 	= $tlist['tid'];
		$t_name[] = $tlist['t_name']."(". $tlist['tid'].")";
	}
}

?>
<script>
function check_form(){
	var form = document.write;
	
	if(form.s_id.value.length < 1){
		alert("시즌명을 선택하세요.");
		return false;
	}else if(form.start_y.value.length < 1 || form.start_y.value == '년'){
		alert("시작일을 선택하세요.");
		return false;
	}else if(form.start_m.value.length < 1 || form.start_m.value == '월'){
		alert("시작일을 선택하세요.");
		return false;
	}else if(form.start_d.value.length < 1 || form.start_d.value == '일'){
		alert("시작일을 선택하세요.");
		return false;
	}else if(form.g_ground.value.length < 1){
		alert("경기장을 입력하세요.");
		form.g_ground.focus();
		return false;
	}else if(form.g_home.value == ""){
		alert("홈팀을 선택하세요.");
		return false;
	}else if(form.g_away.value == ""){
		alert("어웨이팀을 선택하세요.");
		return false;
	}else if(form.g_division.value.length < 1 || form.g_division.value == '경기구분 선택'){
		alert("경기구분을 선택하세요.");
		return false;
	} else {
		form.submit();
		return true;
	}
}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td><table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
		<td width="22"><img src="/images/admin/tbox_l.gif" width="22" height="22"></td>
		<td background="/images/admin/tbox_bg.gif"><strong>경기정보 </strong></td>
		<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
		<br>
		<form action="ok.php" method="post" name="write" id="write">
			<input name="mode" type="hidden" value="<?php echo htmlspecialchars($mode, ENT_QUOTES, 'UTF-8') ; ?>" />
			<input name="gid" type="hidden" value="<?php echo htmlspecialchars($gid ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />
			<input name="season" type="hidden" value="<?php echo htmlspecialchars($_GET['season'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />
			<table width="97%"	border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#666666">
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>경기번호</strong></td>
				<td colspan="3" bgcolor="#F8F8EA">&nbsp;&nbsp;
				<input name="gameno" type="text" size="6" maxlength="6" value="<?php echo htmlspecialchars($list['gameno'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /></td>
			</tr>
			<tr>
				<td width="13%" height="30" align="center" bgcolor="#D2BF7E"><strong>시 즌 명 </strong></td>
				<td bgcolor="#F8F8EA">&nbsp;&nbsp;
					<select name="s_id" id="s_id">
					<option value="">시즌 선택</option>
<?php
				for($i = 0 ; $i < $scnt ; $i++)	{
					if(isset($list['s_id']) && $list['s_id'] == $s_sid[$i])
						echo "<option value='".htmlspecialchars($s_sid[$i], ENT_QUOTES, 'UTF-8') . "' selected>".htmlspecialchars($s_name[$i], ENT_QUOTES, 'UTF-8') . "</option>";
					else
						echo "<option value='".htmlspecialchars($s_sid[$i], ENT_QUOTES, 'UTF-8') . "'>".htmlspecialchars($s_name[$i], ENT_QUOTES, 'UTF-8') . "</option>";
				}

?>
					</select> </td>
				<td width="11%" height="24%" align="center" bgcolor="#D2BF7E"><strong>경기구분</strong></td>
				<td bgcolor="#F8F8EA">&nbsp;&nbsp;
					<select name="g_division">
					<option value="">경기구분 선택</option>
<?php
			$g_division = $list['g_division'] ?? '';
			$options = ["정규리그", "플레이오프", "챔피언결정전"];

			foreach ($options as $option) {
				$selected = ($g_division == $option) ? 'selected' : '';
				echo "<option value='".htmlspecialchars($option, ENT_QUOTES, 'UTF-8') . "' {$selected}>".htmlspecialchars($option, ENT_QUOTES, 'UTF-8') . "</option>";
			}
?>
				</select></td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>시작일시</strong></td>
				<td width="38%" bgcolor="#F8F8EA">&nbsp;&nbsp;
					<select name="start_y">
					<option>년</option>
<?php
					//년도 마지막 년.......
					$end_year = date("Y") + 4;

					for($i = 1998 ; $i < $end_year ; $i ++){
						if(isset($start_y) && $start_y == $i)
							echo "<option value='".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "' selected>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
						else
							echo "<option value='".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "'>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
					}

?>
					</select>
					<select name="start_m">
					<option>월</option>
<?php
					for($i = 1 ; $i < 13 ; $i ++){
						$month_val = sprintf('%02d', $i);
						if(isset($start_m) && $start_m == $month_val)
							echo "<option value='".htmlspecialchars($month_val, ENT_QUOTES, 'UTF-8') . "' selected>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
						else
							echo "<option value='".htmlspecialchars($month_val, ENT_QUOTES, 'UTF-8') . "'>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
					}

?>
					</select>
					<select name="start_d">
					<option>일</option>
<?php
					for($i = 1 ; $i <= 31 ; $i ++){
						$day_val = sprintf('%02d', $i);
						if(isset($start_d) && $start_d == $day_val)
							echo "<option value='".htmlspecialchars($day_val, ENT_QUOTES, 'UTF-8') . "' selected>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
						else
							echo "<option value='".htmlspecialchars($day_val, ENT_QUOTES, 'UTF-8') . "'>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
					}

?>
					</select>
				&nbsp;
				<select name="start_h">
					<option>시 </option>
<?php
					for($i = 1 ; $i <= 24 ; $i ++){
						$hour_val = sprintf('%02d', $i);
						if(isset($start_h) && $start_h == $hour_val)
							echo "<option value='".htmlspecialchars($hour_val, ENT_QUOTES, 'UTF-8') . "' selected>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
						else
							echo "<option value='".htmlspecialchars($hour_val, ENT_QUOTES, 'UTF-8') . "'>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
					}

?>
					</select>
					<select name="start_mm">
					<option>분</option>
<?php
					for($i = 0 ; $i < 60 ; $i ++){
						$minute_val = sprintf('%02d', $i);
						if(isset($start_mm) && $start_mm == $minute_val)
							echo "<option value='".htmlspecialchars($minute_val, ENT_QUOTES, 'UTF-8') . "' selected>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
						else
							echo "<option value='".htmlspecialchars($minute_val, ENT_QUOTES, 'UTF-8') . "'>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
					}

?>
				</select></td>
				<td width="11%" height="24%" align="center" bgcolor="#D2BF7E"><strong>종료일시</strong></td>
				<td width="38%" bgcolor="#F8F8EA">&nbsp;&nbsp;
					<select name="end_y">
					<option>년</option>
<?php
					for($i = 1998 ; $i < $end_year ; $i ++){
						if(isset($end_y) && $end_y == $i)
							echo "<option value='".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "' selected>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
						else
							echo "<option value='".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "'>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
					}

?>
					</select>
					<select name="end_m">
					<option>월</option>
<?php
					for($i = 1 ; $i < 13 ; $i ++){
						$month_val = sprintf('%02d', $i);
						if(isset($end_m) && $end_m == $month_val)
							echo "<option value='".htmlspecialchars($month_val, ENT_QUOTES, 'UTF-8') . "' selected>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
						else
							echo "<option value='".htmlspecialchars($month_val, ENT_QUOTES, 'UTF-8') . "'>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
					}

?>
					</select>
					<select name="end_d">
					<option>일</option>
<?php
					for($i = 1 ; $i <= 31 ; $i ++){
						$day_val = sprintf('%02d', $i);
						if(isset($end_d) && $end_d == $day_val)
							echo "<option value='".htmlspecialchars($day_val, ENT_QUOTES, 'UTF-8') . "' selected>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
						else
							echo "<option value='".htmlspecialchars($day_val, ENT_QUOTES, 'UTF-8') . "'>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
					}

?>
					</select>
					&nbsp;
					<select name="end_h">
					<option>시</option>
<?php
					for($i = 1 ; $i <= 24 ; $i ++){
						$hour_val = sprintf('%02d', $i);
						if(isset($end_h) && $end_h == $hour_val)
							echo "<option value='".htmlspecialchars($hour_val, ENT_QUOTES, 'UTF-8') . "' selected>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
						else
							echo "<option value='".htmlspecialchars($hour_val, ENT_QUOTES, 'UTF-8') . "'>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
					}

?>
					</select>
					<select name="end_mm">
					<option>분</option>
<?php
					for($i = 0 ; $i < 60 ; $i ++){
						$minute_val = sprintf('%02d', $i);
						if(isset($end_mm) && $end_mm == $minute_val)
							echo "<option value='".htmlspecialchars($minute_val, ENT_QUOTES, 'UTF-8') . "' selected>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
						else
							echo "<option value='".htmlspecialchars($minute_val, ENT_QUOTES, 'UTF-8') . "'>".htmlspecialchars($i, ENT_QUOTES, 'UTF-8') . "</option>";
					}

?>
				</select></td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>경 기 장</strong></td>
				<td colspan="3" bgcolor="#F8F8EA">&nbsp;&nbsp;
				<input name="g_ground" type="text" id="g_ground" size="30" maxlength="25" value="<?php echo htmlspecialchars($list['g_ground'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /></td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>홈 팀 </strong></td>
				<td bgcolor="#F8F8EA">&nbsp;&nbsp;
					<select name="g_home" id="g_home">
					<option value="">홈팀 선택</option>
<?php
					for($i=0 ; $i<count($t_tid) ; $i++)	{
						if(isset($list['g_home']) && $list['g_home'] == $t_tid[$i])
							echo "<option value='".htmlspecialchars($t_tid[$i], ENT_QUOTES, 'UTF-8') . "' selected>".htmlspecialchars($t_name[$i], ENT_QUOTES, 'UTF-8') . "</option>";
						else
							echo "<option value='".htmlspecialchars($t_tid[$i], ENT_QUOTES, 'UTF-8') . "'>".htmlspecialchars($t_name[$i], ENT_QUOTES, 'UTF-8') . "</option>";
					}

?>
				</select></td>
				<td width="11%" bgcolor="#D2BF7E"><div align="center"><strong>어웨이팀</strong></div></td>
				<td bgcolor="#F8F8EA">&nbsp;&nbsp;
					<select name="g_away" id="g_away">
					<option value="">어웨이팀 선택</option>
<?php
					for($i=0 ; $i<count($t_tid) ; $i++)	{
						if(isset($list['g_away']) && $list['g_away'] == $t_tid[$i])
							echo "<option value='".htmlspecialchars($t_tid[$i], ENT_QUOTES, 'UTF-8') . "' selected>".htmlspecialchars($t_name[$i], ENT_QUOTES, 'UTF-8') . "</option>";
						else
							echo "<option value='".htmlspecialchars($t_tid[$i], ENT_QUOTES, 'UTF-8') . "'>".htmlspecialchars($t_name[$i], ENT_QUOTES, 'UTF-8') . "</option>";
					}

?>
				</select></td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>경기 결과 </strong></td>
				<td colspan="3" align="center" bgcolor="#F8F8EA">&nbsp;&nbsp;<table width="95%" border="0" cellpadding="3" cellspacing="1" bgcolor="#999999" >
					<tr>
					<td width="30%" bgcolor="#C1C1C1"><p align="center" >팀</p></td>
					<td width="10%" bgcolor="#C1C1C1"><p align="center" >&nbsp;1Q</p></td>
					<td width="10%" bgcolor="#C1C1C1"><p align="center" >2Q</p></td>
					<td width="10%" bgcolor="#C1C1C1"><p align="center" >3Q</p></td>
					<td width="10%" bgcolor="#C1C1C1"><p align="center" >4Q</p></td>
					<td width="10%" bgcolor="#C1C1C1"><p align="center" >EQ</p></td>
					<td width="20%" bgcolor="#C1C1C1"><p align="center" >합계</p></td>
					</tr>
					<tr>
					<td bgcolor="#F8F8EA"><div align="center">홈팀 </div></td>
					<td bgcolor="#F8F8EA"><p align="center" >
						<input name="home_1q" type="text" id="home_1q" value="<?php echo htmlspecialchars($list['home_1q'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="5" maxlength="3" onchange="write_sum_score(this.form,'home');" />
					</p></td>
					<td bgcolor="#F8F8EA"><p align="center" >
						<input name="home_2q" type="text" id="home_2q" value="<?php echo htmlspecialchars($list['home_2q'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="5" maxlength="3" onchange="write_sum_score(this.form,'home');"/>
					</p></td>
					<td bgcolor="#F8F8EA"><p align="center" >
						<input name="home_3q" type="text" id="home_3q" value="<?php echo htmlspecialchars($list['home_3q'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="5" maxlength="3" onchange="write_sum_score(this.form,'home');" />
					</p></td>
					<td bgcolor="#F8F8EA"><p align="center" >
						<input name="home_4q" type="text" id="home_4q" value="<?php echo htmlspecialchars($list['home_4q'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="5" maxlength="3" onchange="write_sum_score(this.form,'home');" />
					</p></td>
					<td bgcolor="#F8F8EA"><p align="center">
						<input name="home_eq" type="text" id="home_eq" value="<?php echo htmlspecialchars($list['home_eq'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="5" maxlength="3" onchange="write_sum_score(this.form,'home');" />
					</p></td>
					<td bgcolor="#F8F8EA"><p align="center">
						<input name="home_score" type="text" id="home_score" value="<?php echo htmlspecialchars($list['home_score'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="5" maxlength="3" />
					</p></td>
					</tr>
					<tr>
					<td bgcolor="#E8E8D6"><p align="center" >어웨이팀 </p></td>
					<td bgcolor="#E8E8D6"><p align="center" >
						<input name="away_1q" type="text" id="away_1q" value="<?php echo htmlspecialchars($list['away_1q'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="5" maxlength="3" onchange="write_sum_score(this.form,'away');" />
					</p></td>
					<td bgcolor="#E8E8D6"><p align="center" >
						<input name="away_2q" type="text" id="away_2q" value="<?php echo htmlspecialchars($list['away_2q'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="5" maxlength="3" onchange="write_sum_score(this.form,'away');" />
					</p></td>
					<td bgcolor="#E8E8D6"><p align="center" >
						<input name="away_3q" type="text" id="away_3q" value="<?php echo htmlspecialchars($list['away_3q'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="5" maxlength="3" onchange="write_sum_score(this.form,'away');" />
					</p></td>
					<td bgcolor="#E8E8D6"><p align="center" >
						<input name="away_4q" type="text" id="away_4q" value="<?php echo htmlspecialchars($list['away_4q'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="5" maxlength="3" onchange="write_sum_score(this.form,'away');" />
					</p></td>
					<td bgcolor="#E8E8D6"><p align="center">
						<input name="away_eq" type="text" id="away_eq" value="<?php echo htmlspecialchars($list['away_eq'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="5" maxlength="3" onchange="write_sum_score(this.form,'away');" />
					</p></td>
					<td bgcolor="#E8E8D6"><p align="center">
						<input name="away_score" type="text" id="away_score" value="<?php echo htmlspecialchars($list['away_score'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" size="5" maxlength="3" />
					</p></td>
					</tr>
				</table>
					<input name="view_main" type="checkbox" id="view_main" value="1" size="5" <?php if(isset($list['view_main'])) echo "checked" ; ?>>
				메인페이지 랭크 보이기 선정</td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>방송사</strong></td>
				<td colspan="3" bgcolor="#F8F8EA">&nbsp;&nbsp;
				<input name="g_ground_tv" type="text" id="g_ground_tv" size="30" maxlength="25" value="<?php echo htmlspecialchars($list['g_ground_tv'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /></td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>심 판 </strong></td>
				<td colspan="3" bgcolor="#F8F8EA">&nbsp;&nbsp;
				<input name="g_referee1" type="text" size="6" maxlength="6" value="<?php echo htmlspecialchars($list['g_referee1'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />
				&nbsp;
				<input name="g_referee2" type="text" size="6" maxlength="6" value="<?php echo htmlspecialchars($list['g_referee2'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />
				&nbsp;
				<input name="g_referee3" type="text" size="6" maxlength="6" value="<?php echo htmlspecialchars($list['g_referee3'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /></td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>기 록 원</strong></td>
				<td colspan="3" bgcolor="#F8F8EA">&nbsp;&nbsp;
				<input name="g_recorder1" type="text" size="6" maxlength="6" value="<?php echo htmlspecialchars($list['g_recorder1'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />
				&nbsp;
				<input name="g_recorder2" type="text" size="6" maxlength="6" value="<?php echo htmlspecialchars($list['g_recorder2'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />
				&nbsp;
				<input name="g_recorder3" type="text" size="6" maxlength="6" value="<?php echo htmlspecialchars($list['g_recorder3'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />
				&nbsp;
				<input name="g_recorder4" type="text" size="6" maxlength="6" value="<?php echo htmlspecialchars($list['g_recorder4'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /></td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>관 중 수</strong></td>
				<td colspan="3" bgcolor="#F8F8EA">&nbsp;&nbsp;
				<input name="g_audience" type="text" id="g_audience" size="7" maxlength="7" value="<?php echo htmlspecialchars($list['g_audience'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />명</td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>팀리바운드</strong></td>
				<td colspan="3" bgcolor="#F8F8EA">&nbsp;&nbsp;홈팀
				<input name="home_tr" type="text" size="5" maxlength="3" value="<?php echo htmlspecialchars($list['home_tr'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />개,&nbsp;&nbsp;&nbsp;&nbsp;어웨이팀
				<input name="away_tr" type="text" size="5" maxlength="3" value="<?php echo htmlspecialchars($list['away_tr'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />개</td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>벤치 파울</strong></td>
				<td colspan="3" bgcolor="#F8F8EA">&nbsp;&nbsp;홈팀
				<input name="home_bf" type="text" size="5" maxlength="3" value="<?php echo htmlspecialchars($list['home_bf'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />개,&nbsp;&nbsp;&nbsp;&nbsp;어웨이팀
				<input name="away_bf" type="text" size="5" maxlength="3" value="<?php echo htmlspecialchars($list['away_bf'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />개</td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>E-Internet</strong></td>
				<td colspan="3" bgcolor="#F8F8EA">&nbsp;&nbsp;
				url :
				<input name="etv_url" type="text" id="etv_url" size="50" maxlength="250" value="<?php echo htmlspecialchars($list['etv_url'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>"	style="width:80%"/>
				(전체주소)<br>
				<br />
				&nbsp;&nbsp;
				새창 윈도우 넓이(기본-342) :
				<input name="etv_width" type="text" id="etv_width" size="5" maxlength="5" value="<?php echo htmlspecialchars($list['etv_width'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;새창 윈도우 높이(기본-417) :
				<input name="etv_height" type="text" id="etv_height" size="5" maxlength="5" value="<?php echo htmlspecialchars($list['etv_height'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /></td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>문자중계<br />
				</strong></td>
				<td colspan="3" bgcolor="#F8F8EA">&nbsp;&nbsp;season_gu:
				<input name="sms_season_gu" type="text" id="sms_season_gu" size="4" maxlength="25" value="<?php echo htmlspecialchars($list['sms_season_gu'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />
				&nbsp;&nbsp;&nbsp;game_type:
				<input name="sms_game_type" type="text" id="sms_game_type" size="4" maxlength="25" value="<?php echo htmlspecialchars($list['sms_game_type'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />
				&nbsp;&nbsp;&nbsp;game_no:
				<input name="sms_gameno" type="text" id="sms_gameno" size="4" maxlength="25" value="<?php echo htmlspecialchars($list['sms_gameno'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />&nbsp;&nbsp;&nbsp;
			
				<span class="bmails001 style1">(한국여자농구연맹 문자중계 서비스 링크 시 필요합니다.)</span> <br>
				<br />
				&nbsp; ex) http://wkbl.or.kr/live/page/live_pop_result.asp?<strong>season_gu</strong>=<span class="style1">023</span>&amp;<strong>game_type</strong>=<span class="style1">01</span>&amp;<strong>game_no</strong>=<span class="style1">6</span></td>
			</tr>
			</table>
			</br>
			<table	width="97%" border="0" align="center">
			<tr>
				<td colspan="4" align="center"><input name="button" type="button" class="CCbox03" onclick="check_form();" value=" 입 력 " />
				&nbsp;&nbsp;
				<input name="button" type="button" class="CCbox03" onclick="javascript:location.href='list.php?season=<?php echo $season ; ?>'" value=" 목 록 " /></td>
			</tr>
			</table>
			<br />
		</form></td>
	</tr>
</table>

<br />
<script>
function write_sum_score(form,input){
	var q1 = eval('form.'+input+'_1q');
	var q2 = eval('form.'+input+'_2q');
	var q3 = eval('form.'+input+'_3q');
	var q4 = eval('form.'+input+'_4q');
	var eq = eval('form.'+input+'_eq');

	var score = eval('form.'+input+'_score');
	var sum;
	sum = Number(q1.value) + Number(q2.value) + Number(q3.value) + Number(q4.value) + Number(eq.value);
	score.value=sum;
}
</script>
<?php if (isset($SITE['tail'])) echo $SITE['tail']; ?>
