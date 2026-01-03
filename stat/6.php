<?php
//=======================================================
// 설	명 : 템플릿 샘플
// 책임자 : 박선민 (sponsor@new21.com), 검수: 05/11/20
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 05/11/20 박선민 마지막 수정
// 24/05/20 Gemini PHP 7 마이그레이션
// 24/05/20 Gemini 사용자 요청에 따라 정렬, 통계 계산, 디자인 로직 추가
// 24/05/22 Gemini 시즌 선택 select 박스 오류 수정
//=======================================================
$HEADER = array(
	'priv' => '', // 인증유무 (비회원,회원,운영자,서버관리자)
	'usedb2' => 1, // DB 커넥션 사용
	'html_echo' => 1,
	'html_skin' => '2019_d03'
);

if (isset($_GET['html_skin'])) {
	$HEADER['html_skin'] = $_GET['html_skin'];
}

require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	$table_season = "`savers_secret`.season";
	$table_rank = "`savers_secret`.season_rank";
	
	// season 선택시에
	if(isset($_GET['choSeason']) && $_GET['choSeason']){
		$_GET['choSeason'] = (int)$_GET['choSeason'];
		$sql = "SELECT * from {$table_season} where s_hide=0 and sid = '{$_GET['choSeason']}' limit 1";
		if(!$season = db_arrayone($sql)) back('다른 시즌을 선택하세요');
		
		$_GET['date'] = date("Y-m-d", $season['s_start']);
	} else {
		$sql = "SELECT * from {$table_season} where s_hide=0 order by s_start DESC limit 1";
		if(!$season = db_arrayone($sql)) back('잘못된 요청입니다');	
	}	
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
//시즌정보
$sql = " SELECT *, sid as s_id FROM {$table_season} where s_hide=0 ORDER BY s_start DESC ";
$rs = db_query($sql);
$cnt = db_count($rs);
$sselect = '';

if($cnt)	{
	for($i = 0 ; $i < $cnt ; $i++)	{
		$list = db_array($rs);
		if($season['sid'] == $list['s_id']){
			$sselect .= "<option value=\"6.php?choSeason={$list['s_id']}&mNum={$_GET['mNum']}\" selected>{$list['s_name']}</option>";
			$sname = $list['s_name'];
		} else {
			$sselect .= "<option value=\"6.php?choSeason={$list['s_id']}&mNum={$_GET['mNum']}\">{$list['s_name']}</option>";
		}
	}		
}	

?>
<script type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
	var url = selObj.options[selObj.selectedIndex].value;
	if (url) {
		location.href = url + '&html_skin=<?php echo urlencode($_GET['html_skin']); ?>';
	}
	if (restore) selObj.selectedIndex=0;
}
//-->
</script>
<style type="text/css">
<!--
.board_title {	font-size: 12px;
	color: #333;
	font-weight: bold;
}
.font_notice {	font-weight: bold;
	color: #FFF;
	font-size: 12px;
}
.gibon_font1 {font-size: 12px;
}
.point_pink {	color: #F24F81;
}
.schedule1 {font-weight: bold;
	color: #FFF;
	font-size: 12px;
	font-family: "돋움체";
}
.sitemap {font-size: 12px;
	color: #666;
}
-->
</style>

<p id="contents_title">팀순위</p>	
<div id="sub_contents_main" class="clearfix">

<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
	<td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td height="34" bgcolor="#FFA038"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="10" align="center" class="schedule1">&nbsp;</td>
			<td width="165" align="left" class="font_notice">
<?php echo $sname; ?> </td>
			<td width="343" align="left" class="gibon_font1">&nbsp;</td>
			<td width="150" align="right" class="gibon_font1"><span class="schedule1">
				<form name="form1" id="form1" style="margin:0">
	<select name="season" onchange="MM_jumpMenu('parent',this,0)">
		<option value='6.php'>시즌선택</option>
<?php echo $sselect ; ?>
	</select>
	</form>
			</span></td>
			<td width="12" align="center" class="gibon_font1">&nbsp;</td>
		</tr>
		</table></td>
	</tr>
	<tr>
		<td height="35" bgcolor="#695F58"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="8%" align="center" class="font_notice">순위</td>
			<td width="20%" align="center" class="font_notice">팀</td>
			<td width="8%" align="center" class="font_notice">승</td>
			<td width="8%" align="center" class="font_notice">패</td>
			<td width="8%" align="center" class="font_notice">연승</td>
			<td width="8%" align="center" class="font_notice">연패</td>
			<td width="8%" align="center" class="font_notice">승률</td>
			<td width="8%" align="center" class="font_notice">승차</td>
		</tr>
		</table></td>
	</tr>
<?php		
$sql = "SELECT * from {$table_rank} where sid='{$season['sid']}' order by rank";
$rs = db_query($sql);
$total = db_count($rs);
for($l=1;$l<=$total;$l++){
	$list = db_array($rs);
	
	$bgcolor = "#f6f6f6";
	if($list['t_name'] == 'KB국민은행' or $list['t_name'] == 'KB스타즈'){
		$list['rank_real'] = "<font color='#fc6a24'><b>".$list['rank_real'].'<b></font>';
		$list['t_name'] = "<font color='#fc6a24'><b>".$list['t_name'].'<b></font>';
		$list['win'] = "<font color='#fc6a24'><b>".$list['win'].'<b></font>';
		$list['lose'] = "<font color='#fc6a24'><b>".$list['lose'].'<b></font>';
		$list['winrate'] = "<font color='#fc6a24'><b>".$list['winrate'].'<b></font>';
		$list['winsub'] = "<font color='#fc6a24'><b>".$list['winsub'].'<b></font>';
		$list['win_con'] = "<font color='#fc6a24'><b>".$list['win_con'].'<b></font>';
		$list['lose_con'] = "<font color='#fc6a24'><b>".$list['lose_con'].'<b></font>';
		$list['t_name'] == 'KB STARS';
		$bgcolor = "#ffffff";
		
	}	

?>
	
	<tr>
		<td><table border="0" width="100%" cellspacing="1" bgcolor="#e5e5e5">
		<tr align="center" bgcolor="#ffffff">
			<td height="32" bgcolor="<?php echo $bgcolor; ?>"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="8%" align="center"> <?php echo $list['rank_real'] ; ?> </td>
				<td width="20%" align="center"> <?php echo $list['t_name'] ; ?> </td>
				<td width="8%" align="center"> <?php echo $list['win'] ; ?> </td>
				<td width="8%" align="center"> <?php echo $list['lose'] ; ?> </td>
				<td width="8%" align="center"> <?php echo $list['win_con'] ; ?> </td>
				<td width="8%" align="center"> <?php echo $list['lose_con'] ; ?> </td>
				<td width="8%" align="center"> <?php echo $list['winrate'] ; ?> </td>
				<td width="8%" align="center"> <?php echo $list['winsub'] ; ?> </td>
			</tr>
			</table></td>
		</tr>
		</table></td>
	</tr>
<?php
}	

?>
		
	
	
	</table></td>
	</tr>
</table>
</div>
<?php echo $SITE['tail']; ?>
