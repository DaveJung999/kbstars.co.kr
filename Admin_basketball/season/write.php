<?php
$HEADER=array(
	'priv' =>	"운영자,경기관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
	'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
	'html_echo' => '', // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
	'log' => '' // log_site 테이블에 지정한 키워드로 로그 남김
);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $HTTP_HOST);
//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
// $seHTTP_REFERER는 어디서 링크하여 왔는지 저장하고, 로그인하면서 로그에 남기고 삭제된다.
if( !isset($_SESSION['seUserid']) && !isset($_SESSION['seHTTP_REFERER']) && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER["HTTP_HOST"]) === false ){
	// PHP 7에서 session_register() 함수는 제거되었습니다. $_SESSION 슈퍼글로벌 배열에 직접 할당해야 합니다.
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

if($mode == "modify" && $s_id)	{
	$sql = " SELECT * FROM `savers_secret`.season WHERE sid = {$s_id} ";
	$rs = db_query($sql);
	$cnt = db_count($rs);

	if($cnt){
		$list = db_array($rs);
		//시즌 시작일
		$s_start1 = date("Y", $list['s_start']);
		$s_start2 = date("m", $list['s_start']);
		$s_start3 = date("d", $list['s_start']);

		${"sel_".$s_start1} = "selected";
		${"sel_".$s_start2} = "selected";
		${"sell_".$s_start3} = "selected";

		//시즌 종료일
		$s_end1 = date("Y", $list['s_end']);
		$s_end2 = date("m", $list['s_end']);
		$s_end3 = date("d", $list['s_end']);

		${"se_".$s_end1} = "selected";
		${"se_".$s_end2} = "selected";
		${"see_".$s_end3} = "selected";

		// hide
		$s_hide = $list['s_hide'];
		$dsp_plo = $list['dsp_plo'];
		$dsp_chp = $list['dsp_chp'];
		$kpoint_hide = $list['kpoint_hide'];
		$pnt_race = $list['pnt_race'];

	} else {
		back("수정할 시즌이 없습니다.");
	}
}else if($mode == "modify" && !$s_id){
	back("수정할 시즌이 없습니다.");
}

//팀 정보 가져오기
$t_sel1 = "<option value=''>팀선택</option>";
$t_sel2 = "<option value=''>팀선택</option>";
$t_sel3 = "<option value=''>팀선택</option>";
$t_sel4 = "<option value=''>팀선택</option>";



$sql_t = " select * from `savers_secret`.team order by tid asc ";
$rs_t = db_query($sql_t);
$cnt_t = db_count($rs_t);

if($cnt_t)	{
	for($i = 0 ; $i < $cnt_t ; $i++)	{
		$list_t = db_array($rs_t);

		// davej 2024-10-09
		$list_t['t_name'] = $list_t['t_name']." (".$list_t['tid'].")";

		if($list_t['tid'] == ($list['1st'] ?? null))
			$t_sel1 .= "<option value=\"{$list_t['tid']}\" selected>{$list_t['t_name']}</option>";
		else
			$t_sel1 .= "<option value=\"{$list_t['tid']}\">{$list_t['t_name']}</option>";

		if($list_t['tid'] == ($list['2nd'] ?? null))
			$t_sel2 .= "<option value=\"{$list_t['tid']}\" selected>{$list_t['t_name']}</option>";
		else
			$t_sel2 .= "<option value=\"{$list_t['tid']}\">{$list_t['t_name']}</option>";

		if($list_t['tid'] == ($list['3rd'] ?? null))
			$t_sel3 .= "<option value=\"{$list_t['tid']}\" selected>{$list_t['t_name']}</option>";
		else
			$t_sel3 .= "<option value=\"{$list_t['tid']}\">{$list_t['t_name']}</option>";

		if($list_t['tid'] == ($list['4th'] ?? null))
			$t_sel4 .= "<option value=\"{$list_t['tid']}\" selected>{$list_t['t_name']}</option>";
		else
			$t_sel4 .= "<option value=\"{$list_t['tid']}\">{$list_t['t_name']}</option>";
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

<script>
	function check_form(){
		var form = document.write;
		if(form.s_name.value.length < 1){
			alert("시즌명을 입력하세요.");
			return false;
		}else if(form.s_start1.value.length < 1){
			alert("시즌 시작일을 입력하세요.");
			return false;
		}else if(form.s_start2.value.length < 1){
			alert("시즌 시작일을 입력하세요.");
			return false;
		}else if(form.s_start3.value.length < 1){
			alert("시즌 시작일을 입력하세요.");
			return false;
		}else if(form.s_end1.value.length < 1){
			alert("시즌 종료일을 입력하세요.");
			return false;
		}else if(form.s_end2.value.length < 1){
			alert("시즌 종료일을 입력하세요.");
			return false;
		}else if(form.s_end3.value.length < 1){
			alert("시즌 종료일을 입력하세요.");
			return false;
		} else {
			form.submit();
			return true;
		}
	}
</script>
<form name="write" method="post" action="ok.php">
<input name="mode" type="hidden" value="<?php echo $mode ; ?>">
<input name="s_id" type="hidden" value="<?php echo $s_id ; ?>">

<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td><table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
		<td width="22"><img src="/images/admin/tbox_l.gif" width="22" height="22"></td>
		<td background="/images/admin/tbox_bg.gif"><strong>시즌정보 </strong></td>
		<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
		<br />
	<table width="97%"	border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#666666">
		<tr>
			<td height="30" bgcolor="#D2BF7E" align="center"><strong> 시 즌 명</strong></td>
			<td bgcolor="#F8F8EA">&nbsp;&nbsp;
			<input name="s_name" type="text" id="s_name" value="<?php echo $list['s_name'] ?? ''; ?>" size="40">
			(정규시즌
			<input name="pnt_race" type="checkbox" id="pnt_race" value="1" <?php	if(isset($pnt_race) && $pnt_race == 1) echo "checked" ; ?> />
			)</td>
		</tr>
		<tr>
			<td height="30" bgcolor="#D2BF7E" align="center"><strong>라운드 수 </strong></td>
			<td bgcolor="#F8F8EA">&nbsp;&nbsp;
				<input name="roundno" type="text" id="roundno" value="<?php echo $list['roundno'] ?? ''; ?>" size="5" /></td>
		</tr>
		<tr>
			<td height="30" bgcolor="#D2BF7E" align="center"><strong>시즌 시작일</strong></td>
			<td bgcolor="#F8F8EA">&nbsp;&nbsp;
			<select name="s_start1" id="s_start1">
<?php
				$y_cur = date("Y");
				// 등록 시 현재년도 설정
				if(($_GET['mode'] ?? 'write') == 'write')
					${"sel_".$y_cur} = "selected";

				// 3년 후까지 for
				for($y = 1998 ; $y < $y_cur + 3 ; $y++)	{
					if (isset(${"sel_$y"}))
						echo "<option value='{$y}' selected>{$y}</option>\n";
					else
						echo "<option value='{$y}'>{$y}</option>\n";
				}
?>
			</select>년
				<select name="s_start2" id="s_start2">
				<option value="01" <?php echo $sel_01 ?? '' ; ?>>1월</option>
				<option value="02" <?php echo $sel_02 ?? '' ; ?>>2월</option>
				<option value="03" <?php echo $sel_03 ?? '' ; ?>>3월</option>
				<option value="04" <?php echo $sel_04 ?? '' ; ?>>4월</option>
				<option value="05" <?php echo $sel_05 ?? '' ; ?>>5월</option>
				<option value="06" <?php echo $sel_06 ?? '' ; ?>>6월</option>
				<option value="07" <?php echo $sel_07 ?? '' ; ?>>7월</option>
				<option value="08" <?php echo $sel_08 ?? '' ; ?>>8월</option>
				<option value="09" <?php echo $sel_09 ?? '' ; ?>>9월</option>
				<option value="10" <?php echo $sel_10 ?? '' ; ?>>10월</option>
				<option value="11" <?php echo $sel_11 ?? '' ; ?>>11월</option>
				<option value="12" <?php echo $sel_12 ?? '' ; ?>>12월</option>
			</select>월
			<select name="s_start3" id="s_start3">
				<option value="01" <?php echo $sell_01 ?? '' ; ?>>1일</option>
				<option value="02" <?php echo $sell_02 ?? '' ; ?>>2일</option>
				<option value="03" <?php echo $sell_03 ?? '' ; ?>>3일</option>
				<option value="04" <?php echo $sell_04 ?? '' ; ?>>4일</option>
				<option value="05" <?php echo $sell_05 ?? '' ; ?>>5일</option>
				<option value="06" <?php echo $sell_06 ?? '' ; ?>>6일</option>
				<option value="07" <?php echo $sell_07 ?? '' ; ?>>7일</option>
				<option value="08" <?php echo $sell_08 ?? '' ; ?>>8일</option>
				<option value="09" <?php echo $sell_09 ?? '' ; ?>>9일</option>
				<option value="10" <?php echo $sell_10 ?? '' ; ?>>10일</option>
				<option value="11" <?php echo $sell_11 ?? '' ; ?>>11일</option>
				<option value="12" <?php echo $sell_12 ?? '' ; ?>>12일</option>
				<option value="13" <?php echo $sell_13 ?? '' ; ?>>13일</option>
				<option value="14" <?php echo $sell_14 ?? '' ; ?>>14일</option>
				<option value="15" <?php echo $sell_15 ?? '' ; ?>>15일</option>
				<option value="16" <?php echo $sell_16 ?? '' ; ?>>16일</option>
				<option value="17" <?php echo $sell_17 ?? '' ; ?>>17일</option>
				<option value="18" <?php echo $sell_18 ?? '' ; ?>>18일</option>
				<option value="19" <?php echo $sell_19 ?? '' ; ?>>19일</option>
				<option value="20" <?php echo $sell_20 ?? '' ; ?>>20일</option>
				<option value="21" <?php echo $sell_21 ?? '' ; ?>>21일</option>
				<option value="22" <?php echo $sell_22 ?? '' ; ?>>22일</option>
				<option value="23" <?php echo $sell_23 ?? '' ; ?>>23일</option>
				<option value="24" <?php echo $sell_24 ?? '' ; ?>>24일</option>
				<option value="25" <?php echo $sell_25 ?? '' ; ?>>25일</option>
				<option value="26" <?php echo $sell_26 ?? '' ; ?>>26일</option>
				<option value="27" <?php echo $sell_27 ?? '' ; ?>>27일</option>
				<option value="28" <?php echo $sell_28 ?? '' ; ?>>28일</option>
				<option value="29" <?php echo $sell_29 ?? '' ; ?>>29일</option>
				<option value="30" <?php echo $sell_30 ?? '' ; ?>>30일</option>
				<option value="31" <?php echo $sell_31 ?? '' ; ?>>31일</option>
			</select>일</td>
		</tr>
		<tr>
			<td height="30" bgcolor="#D2BF7E" align="center"><strong>시즌 종료일</strong></td>
			<td bgcolor="#F8F8EA">&nbsp;&nbsp;
			<select name="s_end1" id="s_end1">
<?php
				$y_cur = date("Y");
				// 등록 시 현재년도 설정
				if(($_GET['mode'] ?? 'write') == 'write')
					${"se_".$y_cur} = "selected";

				// 3년 후까지 for
				for($y = 1998 ; $y < $y_cur + 3 ; $y++)	{
					if (isset(${"se_{$y}"}))
						echo "<option value='{$y}' selected>{$y}</option>\n";
					else
						echo "<option value='{$y}'>{$y}</option>\n";
				}
?>
			</select>년
				<select name="s_end2" id="s_end2">
				<option value="01" <?php echo $se_01 ?? '' ; ?>>1월</option>
				<option value="02" <?php echo $se_02 ?? '' ; ?>>2월</option>
				<option value="03" <?php echo $se_03 ?? '' ; ?>>3월</option>
				<option value="04" <?php echo $se_04 ?? '' ; ?>>4월</option>
				<option value="05" <?php echo $se_05 ?? '' ; ?>>5월</option>
				<option value="06" <?php echo $se_06 ?? '' ; ?>>6월</option>
				<option value="07" <?php echo $se_07 ?? '' ; ?>>7월</option>
				<option value="08" <?php echo $se_08 ?? '' ; ?>>8월</option>
				<option value="09" <?php echo $se_09 ?? '' ; ?>>9월</option>
				<option value="10" <?php echo $se_10 ?? '' ; ?>>10월</option>
				<option value="11" <?php echo $se_11 ?? '' ; ?>>11월</option>
				<option value="12" <?php echo $se_12 ?? '' ; ?>>12월</option>
			</select>월
			<select name="s_end3" id="s_end3">
				<option value="01" <?php echo $see_01 ?? '' ; ?>>1일</option>
				<option value="02" <?php echo $see_02 ?? '' ; ?>>2일</option>
				<option value="03" <?php echo $see_03 ?? '' ; ?>>3일</option>
				<option value="04" <?php echo $see_04 ?? '' ; ?>>4일</option>
				<option value="05" <?php echo $see_05 ?? '' ; ?>>5일</option>
				<option value="06" <?php echo $see_06 ?? '' ; ?>>6일</option>
				<option value="07" <?php echo $see_07 ?? '' ; ?>>7일</option>
				<option value="08" <?php echo $see_08 ?? '' ; ?>>8일</option>
				<option value="09" <?php echo $see_09 ?? '' ; ?>>9일</option>
				<option value="10" <?php echo $see_10 ?? '' ; ?>>10일</option>
				<option value="11" <?php echo $see_11 ?? '' ; ?>>11일</option>
				<option value="12" <?php echo $see_12 ?? '' ; ?>>12일</option>
				<option value="13" <?php echo $see_13 ?? '' ; ?>>13일</option>
				<option value="14" <?php echo $see_14 ?? '' ; ?>>14일</option>
				<option value="15" <?php echo $see_15 ?? '' ; ?>>15일</option>
				<option value="16" <?php echo $see_16 ?? '' ; ?>>16일</option>
				<option value="17" <?php echo $see_17 ?? '' ; ?>>17일</option>
				<option value="18" <?php echo $see_18 ?? '' ; ?>>18일</option>
				<option value="19" <?php echo $see_19 ?? '' ; ?>>19일</option>
				<option value="20" <?php echo $see_20 ?? '' ; ?>>20일</option>
				<option value="21" <?php echo $see_21 ?? '' ; ?>>21일</option>
				<option value="22" <?php echo $see_22 ?? '' ; ?>>22일</option>
				<option value="23" <?php echo $see_23 ?? '' ; ?>>23일</option>
				<option value="24" <?php echo $see_24 ?? '' ; ?>>24일</option>
				<option value="25" <?php echo $see_25 ?? '' ; ?>>25일</option>
				<option value="26" <?php echo $see_26 ?? '' ; ?>>26일</option>
				<option value="27" <?php echo $see_27 ?? '' ; ?>>27일</option>
				<option value="28" <?php echo $see_28 ?? '' ; ?>>28일</option>
				<option value="29" <?php echo $see_29 ?? '' ; ?>>29일</option>
				<option value="30" <?php echo $see_30 ?? '' ; ?>>30일</option>
				<option value="31" <?php echo $see_31 ?? '' ; ?>>31일</option>
			</select>일</td>
		</tr>
		<tr>
			<td height="30" bgcolor="#D2BF7E" align="center"><strong>우승팀</strong></td>
			<td bgcolor="#F8F8EA">&nbsp;&nbsp;
			<select name="1st"> <?php echo $t_sel1 ; ?></select></td>
		</tr>
		<tr>
			<td height="30" bgcolor="#D2BF7E" align="center"><strong>준우승팀</strong></td>
			<td bgcolor="#F8F8EA">&nbsp;&nbsp;
			<select name="2nd"> <?php echo $t_sel2 ; ?></select></td>
		</tr>
		<tr>
			<td height="30" bgcolor="#D2BF7E" align="center"><strong>PO1</strong></td>
			<td bgcolor="#F8F8EA">&nbsp;&nbsp;
			<select name="3rd"> <?php echo $t_sel3 ; ?></select></td>
		</tr>
		<tr>
			<td width="25%" height="30" bgcolor="#D2BF7E" align="center"><strong>PO2</strong></td>
			<td width="74%" bgcolor="#F8F8EA">&nbsp;&nbsp;
			<select name="4th"> <?php echo $t_sel4 ; ?></select></td>
		</tr>
		<tr>
			<td width="25%" height="30" bgcolor="#D2BF7E" align="center"><strong>플레이오프진출</strong></td>
			<td width="74%" bgcolor="#F8F8EA">&nbsp;&nbsp;
				<input name="dsp_plo" type="checkbox" id="dsp_plo" value="1" <?php	if(isset($dsp_plo) && $dsp_plo == 1) echo "checked" ; ?>> 진출</td>
		</tr>
		<tr>
			<td width="25%" height="30" bgcolor="#D2BF7E" align="center"><strong>챔피언결정전진출</strong></td>
			<td width="74%" bgcolor="#F8F8EA">&nbsp;&nbsp;
				<input name="dsp_chp" type="checkbox" id="dsp_chp" value="1" <?php	if(isset($dsp_chp) && $dsp_chp == 1) echo "checked" ; ?>> 진출</td>
		</tr>
		<tr>
			<td height="30" bgcolor="#D2BF7E" align="center"><strong>기록숨기기</strong></td>
			<td bgcolor="#F8F8EA">&nbsp;&nbsp;
			<input name="s_hide" type="checkbox" id="s_hide" value="1" <?php	if(isset($s_hide) && $s_hide == 1) echo "checked" ; ?> /> 기록 숨기기</td>
		</tr>
		<tr>
			<td width="25%" height="30" bgcolor="#D2BF7E" align="center"><strong>kpoint 숨기기</strong></td>
			<td width="74%" bgcolor="#F8F8EA">&nbsp;&nbsp;
				<input name="kpoint_hide" type="checkbox" id="kpoint_hide" value="1" <?php	if(isset($kpoint_hide) && $kpoint_hide == 1) echo "checked" ; ?>> kpoint 숨기기</td>
		</tr>
	</table>
	<br>
	<table width="90%"	border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td align="center"><input name="Submit" type="button" class="CCbox03" onClick="check_form();" value=" 저장 ">
			&nbsp; <input name="back" type="button" class="CCbox03" id="back" onclick="javascript:history.back();" value=" 뒤로 " /></td>
		</tr>
	</table>
	<br></td>
	</tr>
</table>
</form>
<?php echo $SITE['tail']; ?>