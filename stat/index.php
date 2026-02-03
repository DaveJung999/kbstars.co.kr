<?php
//=======================================================
// 설	명 : 템플릿 샘플
// 책임자 : 박선민 (sponsor@new21.com), 검수: 05/11/20
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 05/11/20 박선민 마지막 수정
//=======================================================
header('Content-Type: text/html; charset=UTF-8'); // 한글깨짐 방지 (UTF-8 사용시)
$HEADER = array(
//	'priv' => '', // 인증유무 (비회원,회원,운영자,서버관리자)
	'usedb2' => 1, // DB 커넥션 사용
	'html_echo' => 1,
	'html_skin' => '2019_d03'
);

if( $_GET['html_skin']) 
	$HEADER['html_skin'] = $_GET['html_skin'];
	
$_GET['mNum'] = $_GET['mNum'] ? $_GET['mNum'] : '0301';
	

require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	$thisPath			= dirname(__FILE__);
	include_once($_SERVER['DOCUMENT_ROOT']."/scalendar/userfuntions.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/scalendar/function_lunartosol.php");

	// 각종 날짜변수 - 현재 날짜
	$nowTime		= time();
	$NowThisYear	= date("Y");
	$NowThisMonth	= date("m");
	$NowThisDay		= date("d");

	// 기본 URL QueryString
	$qs_basic	= href_qs($qs_basic); // 해당값 초기화

	$table_season = "season";
	
	$sql = "select max(sid) as max_sid FROM {$table_season} where s_hide=0";
	$max_sid = db_resultone($sql, 0, 'max_sid');
	
	//2015-10-01
	if(!$_GET['choSeason']) 
		$_GET['choSeason'] = $max_sid;

	
	// season 선택시에
	$_GET['choSeason'] = (int)$_GET['choSeason'];
	$sql = "SELECT * from {$table_season} where s_hide=0 and sid = '{$_GET['choSeason']}' limit 1";

	if(!$season = db_arrayone($sql)) back('다른 시즌을 선택하세요');
	
	//echo $season['sid'];
	if(!$_GET['date']){
		if ( date("Y-m-d") > date("Y-m-d",$season['s_start']) && 	$season['sid'] == $max_sid )
			$_GET['date'] = date("Y-m-d");
		else
			$_GET['date'] = date("Y-m-d",$season['s_start']) ;
	}

	// YYYY-MM-DD 형식에 맞는지 정규식으로 검증
	if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/", $_GET['date'])) {
		back("잘못된 날짜 형식입니다 (YYYY-MM-DD) . ");
	}

	$_GET['date'] = date("Y-m-d",strtotime($_GET['date']));
	$intThisTimestamp	= strtotime("+1 month",strtotime($_GET['date'])) -1;
	
	// date값 결정(시즌에따라)
	$sql = "SELECT * from {$table_season} where s_hide=0 and s_start <= {$intThisTimestamp} order by s_start DESC limit 1";
	if(!$season = db_arrayone($sql)){
		$sql = "SELECT * from {$table_season} where s_hide=0 order by s_start DESC limit 1";
		if(!$season = db_arrayone($sql)) back('잘못된 요청입니다');
	}
	
	// 각종 날짜 변수 - 넘오온 날짜
	$intThisTimestamp	= strtotime($_GET['date']);
	$intThisYear	= date("Y",$intThisTimestamp);
	$intThisMonth	= date("m",$intThisTimestamp);
	$intThisDay		= date("d",$intThisTimestamp);
	$intThisWeekday	= date("w",$intThisTimestamp); 

	switch ($intThisWeekday){
		Case 0: $varThisWeekday="일"; break;
		Case 1: $varThisWeekday="월"; break;
		Case 2: $varThisWeekday="화"; break;
		Case 3: $varThisWeekday="수"; break;
		Case 4: $varThisWeekday="목"; break;
		Case 5: $varThisWeekday="금"; break;
		Case 6: $varThisWeekday="토"; break;
	}

	// 각종 날짜변수 - 이전달,다음달
	if($intThisMonth == 1) { // 1월달이라면 
		$intPrevYear	= $intThisYear-1;
		$intPrevMonth	= 12;
		$intNextYear	= $intThisYear ;
		$intNextMonth	= 2;
	}
	elseif($intThisMonth == 12) {
		$intPrevYear	= $intThisYear;
		$intPrevMonth	= 11;
		$intNextYear	= $intThisYear + 1;
		$intNextMonth	= 1;
	} else {
		$intPrevYear	= $intThisYear;
		$intPrevMonth	= $intThisMonth - 1;
		$intNextYear	= $intThisYear;
		$intNextMonth	= $intThisMonth+1;
	}

	// 각종 날짜변수 - 월말일
	$intLastDay		= userLastDay($intThisMonth,$intThisYear);
	$intPrevLastDay = userLastDay($intPrevMonth,$intPrevYear);
	$intNextLastDay = userLastDay($intNextMonth,$intNextYear);

	$intFirstWeekday = date('w', strtotime($intThisYear."-".$intThisMonth."-1"));

	$thisFullDate	= date("Y년 n월 j일",$intThisTimestamp) . " {$varThisWeekday}요일";
	$sol2lun = sol2lun(date("Ymd",$intThisTimestamp));
	$sol2lun = explode("-", $sol2lun);
	$thisFullDate.= "	(음력 {$sol2lun[1]}월 {$sol2lun[2]}일)";
	$href['today']	= "{$_SERVER['PHP_SELF']}?" . href_qs("mode=day&date=".date("Y-m-d"),$qs_basic);
	$href['day']	= "{$_SERVER['PHP_SELF']}?" . href_qs("mode=day&date=".$_GET['date'],$qs_basic);
	$href['week']	= "{$_SERVER['PHP_SELF']}?" . href_qs("mode=week&date=".$_GET['date'],$qs_basic);
	$href['month']	= "{$_SERVER['PHP_SELF']}?" . href_qs("mode=month&date=".$_GET['date'],$qs_basic);

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
	$table_game = "game";

	$sql = "SELECT * from {$table_game} WHERE sid='{$season['sid']}' and (g_home=13 or g_away=13) ";
	$result	= db_query($sql);
	while( $list=db_array($result) ){
		$list['startdate'] = date("Y-m-d",$list['g_start']);
		if($list['g_home'] == 13){
			$outIcon[$list['startdate']] = "<img src='/images/2011/image/calendarIcoHome.jpg' width='12' height='13' border='0' align='absmiddle' />";
			$list['strLogo'] = "/images/team_logo/emble/emble_{$list['g_away']}.jpg";
			$list['strWin'] = ($list['home_score'] > $list['away_score']) ? "승" : "패";
		} else {
			$outIcon[$list['startdate']] = "<img src='/images/2011/image/calendarIcoAway.jpg' width='12' height='13' border='0' align='absmiddle' />";		
			$list['strLogo'] = "/images/team_logo/emble/emble_{$list['g_home']}.jpg";
			$list['strWin'] = ($list['home_score'] < $list['away_score']) ? "승" : "패";
		}
		if($list['g_ground_tv']) $outIcon[$list['startdate']] .= "<img src='/img/t-icon.gif' width=11 height=13 border=0>";
		
		if( $list['g_home'] == 19 && $list['g_away'] == 13 ){
			$list['strGround'] = mb_substr($list['g_ground'],0,3,"UTF-8");
		} else {
			$list['strGround'] = mb_substr($list['g_ground'],0,2,"UTF-8");
		}

		$list['strHour'] = date('A g',$list['g_start']) . '시';
		$min_i = date('i',$list['g_start']);
		$list['strHour'] = $min_i == '00' ? $list['strHour'] : $list['strHour'].$min_i."분" ;

		if($list['home_score'] or $list['away_score']) { 
			$list['strScore'] = "{$list['home_score']}:{$list['away_score']} {$list['strWin']}";
		}

		$list['strLogo'] = "<img src='{$list['strLogo']}' width='90' height='40' border='0' />";
		if($list['home_score'] or $list['away_score']){
			$list['strLogo'] = "<a href='2-read.php?gid={$list['gid']}&mNum={$_GET['mNum']}&html_skin={$_GET['html_skin']}&choSeason={$_GET['choSeason']}&date={$_GET['date']}'>".$list['strLogo']."</a>";
		}

		// 날짜별 경기 앰블럼, 시간
		$outCal[$list['startdate']] .= "
						<table width='100%' border='0' cellspacing='0' cellpadding='0'>
							<tr>
								<td align='center'>{$list['strLogo']}</td>
							</tr>
							<tr>
								<td align='center'>".htmlspecialchars($list['strGround'], ENT_QUOTES, 'UTF-8')." {$list['strHour']}</td>
							</tr>
						</table>"	;
		
		// 날짜별 경기결과 및 승패
		$outScore[$list['startdate']] .= "{$list['strScore']}";
	}
?>
<style type="text/css">
<!--
.board_title {	font-size: 12px;
	color: #333;
	font-weight: bold;
}
.font_notice1 {font-weight: bold;
	color: #000;
}
.point_pink {	color: #F24F81;
}
.schedule {	font-weight: bold;
	color: #333;
	font-size: 12px;
	font-family: "돋움체";
}
.win {	color: #03F;
}
-->
</style>
<p id="contents_title">일정 및 결과</p> 
<div id="sub_contents_main" class="clearfix">

<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
	<td><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td height="35" align="left"><form name="form" id="form">
			<span style="line-height:100%;">
				<select name="choSeason" onchange="javascript: window.location='?mNum=<?php echo $_GET['mNum'] ; ?>&html_skin=<?php echo $_GET['html_skin'] ; ?>&choSeason='+this.value;" >
<?php
$sql = "select * from season where s_hide=0 order by s_start DESC";
$rs_tmp = db_query($sql);
while($ltmp = db_array($rs_tmp)){
	if( $ltmp['sid'] == $season['sid'] ){
		echo "				<option value='{$ltmp['sid']}' selected>".htmlspecialchars($ltmp['s_name'],ENT_QUOTES,'UTF-8')."</option>\n";
	} else {
		echo "				<option value='{$ltmp['sid']}'>".htmlspecialchars($ltmp['s_name'],ENT_QUOTES,'UTF-8')."</option>\n";
	}
} 
?>
				</select>
				</span>
			</form></td>
			<td>&nbsp;</td>
			<td width="180" align="right">
<?php
if($season['s_end']>$season['s_start']){
	$prevMonthTmp = "";
	$nextMonthTmp = "";
	$prevHref = "";
	$nextHref = "";
	

	$dt_elements = explode("-" ,$_GET['date']);
	$_gDate = mktime (0, 0, 0, $dt_elements['1'], $dt_elements['2'], $dt_elements['0']);

	if(date("Ym", $season['s_start']) < date("Ym", $_gDate)){
		$prevMonthTmp = date("Y-m-01",strtotime("-1 month",$_gDate));
		$prevHref = " onclick=\"javascript:window.location.href='index.php?choSeason={$_GET['choSeason']}&mNum={$_GET['mNum']}&html_skin={$_GET['html_skin']}&date=".$prevMonthTmp."'\" style=\"cursor:pointer\"" ;
	}
	if(date("Ym", $season['s_end']) > date("Ym", $_gDate)){
		$nextMonthTmp = date("Y-m-01",strtotime("+1 month",$_gDate));
		$nextHref = " onclick=\"javascript:window.location.href='index.php?choSeason={$_GET['choSeason']}&mNum={$_GET['mNum']}&html_skin={$_GET['html_skin']}&date=".$nextMonthTmp."'\" style=\"cursor:pointer\"" ;
	}
}
?>
			
			<table width="150" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="13"><img src="/images/2011/image/btn_pre02.gif" <?php echo $prevHref ; ?>	width="13" height="13" /></td>
				<td align="center"> <?php echo date("Y년 m월",$intThisTimestamp); ?> </td>
				<td width="13"><img src="/images/2011/image/btn_next02.gif" <?php echo $nextHref ; ?>	width="13" height="13" /></td>
			</tr>
			</table></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	<table width='100%' border='0' align="center" cellpadding="0" cellspacing="1" bgcolor='#dbdbdb' >
		<tr bgcolor="#FD930C">
			<td height="46" align="center"><strong style="color:#fff; font-size:15px;">일</strong></td>
				<td align="center"><strong style="color:#fff; font-size:15px;">월</strong></td>
				<td align="center"><strong style="color:#fff; font-size:15px;">화</strong></td>
				<td align="center"><strong style="color:#fff; font-size:15px;">수</strong></td>
				<td align="center"><strong style="color:#fff; font-size:15px;">목</strong></td>
				<td align="center"><strong style="color:#fff; font-size:15px;">금</strong></td>
			<td align="center"><strong style="color:#fff; font-size:15px;">토</strong></td>
		</tr>
<?php
$intPrintDay	= 1;
$Stop_Flag		= 0;
for($intNextWeek=1; $intNextWeek < 7 ; $intNextWeek++) {
	echo "			<tr bgcolor='#ffffff'>\n				";
	for($intNextDay=1; $intNextDay < 8	; $intNextDay++) {
		echo "<td width='14.3%' height='110' align='left'>";

		if ($intPrintDay == 1 and $intNextDay<$intFirstWeekday+1) {
			echo "<font size=2 color=white>.</font> ";
		} else {
			if ($intPrintDay > $intLastDay ) {
				echo "<font size=2 color=white>.</font> ";
			} else {
				$intcday = $intThisYear."-".$intThisMonth."-" . (($intPrintDay<10)?"0":"").$intPrintDay;
				$href['goinput'] = "./index.php?" .	href_qs("mode=input&date={$intcday}",$qs_basic);
				$href['goday'] = "./index.php?" . href_qs("mode=day&date={$intcday}",$qs_basic);

				$strThisDay = '';
				if( $intThisYear-$NowThisYear == 0 and $intThisMonth-$NowThisMonth == 0 and $intPrintDay-$intThisDay == 0 ){
					if($enable_write or !empty($outCal[$intcday])) 
						$strThisDay =	"<b><a href='{$href['goday']}&html_skin={$_GET['html_skin']}'><font size=2 color=darkorange>{$intPrintDay}◈</font></a></b> ";
					else 
						$strThisDay =	"<b><font color=darkorange>{$intPrintDay}◈</font></b> <br>";
				}
				elseif( $intNextDay == 1 ) {
					if($enable_write or !empty($outCal[$intcday])) 
						$strThisDay =	"<b><a href='{$href['goday']}&html_skin={$_GET['html_skin']}'><font size=2 color=red>{$intPrintDay}</font></a></b>";
					else 
						$strThisDay =	"<b><font color=red>{$intPrintDay}</font></b>";
				}
				else{
					if($enable_write or !empty($outCal[$intcday])) 
						$strThisDay =	"<b><font color=#000000><a href='{$href['goday']}&html_skin={$_GET['html_skin']}'>{$intPrintDay}</a></font></b>";
					else 
						$strThisDay =	"<b><font color=#000000>{$intPrintDay}</font></b>";
				}

				// 일정 내용 출력
				echo "
					<table width='95%' border='0' cellspacing='0' cellpadding='0' align='center'>
						<tr>
						<td class='schedule'>$strThisDay</td>
						<td align='right'>".(isset($outIcon[$intcday]) ? $outIcon[$intcday] : '')."</td>
						</tr>
						<tr>
						<td height='70' align='center' colspan='2'>".(isset($outCal[$intcday]) ? $outCal[$intcday] : '')."</td>
						</tr>
						<tr>
						<td height='20' align='center' class='schedule' colspan='2'><span class='win'>".(isset($outScore[$intcday]) ? $outScore[$intcday] : '')."</span></td>
						</tr>
					</table>
				";
			}
			$intPrintDay	+= 1;
			if ($intPrintDay>$intLastDay )
				$Stop_Flag=1;
		}
		echo "</td>\n				";
	}
	echo "</tr>\n				";
	if ($Stop_Flag == 1 )	break;
}
?>
	</table>
		</td>
	</tr>
	<tr>
	<td align="left"><span style="padding-left:10px;"><img src="/img/h_a_t.gif" width="172" height="16" border="0" /></span></td>
	</tr>
</table>

</div>
<?php echo $SITE['tail']; ?>
