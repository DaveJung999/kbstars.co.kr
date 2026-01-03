<?php
//=======================================================
// 설  명 : 인클루드 파일 - inc_month.php
// 책임자 : 박선민 , 검수: 03/09/20
// Project: sitePHPbasic
// ChangeLog
//   DATE   수정인			 수정 내용
// -------- ------ --------------------------------------
// 03/09/20 박선민 마지막 수정
//=======================================================

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
	// 인쿨루드인 경우에만 허용
	if (realpath($_SERVER["PATH_TRANSLATED"]) == realpath(__FILE__)) {
		echo "직접 호출되어서 거부함";
		exit;
	}

	// 쓰기 권한이 있는지 확인
	if(privAuth($dbinfo, "priv_write"))	$enable_write = true;


	////////////////////////////
	// 반복되지 않은 일정 구하기
	// $outCal[YYYY-MM-DD]
	$searchDateFrom = "{$intThisYear}-{$intThisMonth}-01";
	$searchDateTo	= "{$intThisYear}-{$intThisMonth}-{$intLastDay}";

	$sql = "SELECT * from {$table_calendar} WHERE {$sql_where_cal} AND retimes=0 ";
	$sql .= "AND (startdate>='{$searchDateFrom}' AND startdate<='{$searchDateTo}') ";
	$sql .= " AND (dtype = 'hour' OR dtype = 'day') ";
	$sql .= " ORDER BY startdate, starthour";
	$result	= db_query($sql);
	while( $list=db_array($result) ) {
		if($list['dtype'] == "day" )
			$lhour= "[ 하루 종일 ]";
		else
			$lhour="[{$list['starthour']}:{$list['startmin']}~{$list['endhour']}:{$list['endmin']}]";

		// 권한체크
		if(!privAuth($list,"priv_level")) {
			$list['title']	= "비공개 일정";
			$list['content']	= "비공개 일정";

			// URL Link
			$href['view'] = "javascript: return false;";
		}
		else {
			$list['title'] = cut_string($list['title'], 20);
			$list['title'] = htmlspecialchars($list['title'],ENT_QUOTES);
			$list['content'] = cut_string($list['content'], 150);
			$list['content'] = htmlspecialchars($list['content'],ENT_QUOTES);
			$list['content'] = replace_string($list['content'], 'text');	// 문서 형식에 맞추어서 내용 변경

			// URL Link
			if($enable_write) 
				$href['view'] = "./index.php?".href_qs("mode=view&bmode={$_GET['mode']}&uid={$list['uid']}",$qs_basic);
			else
				$href['view'] = "javascript: void(0)";

		} // end if.. else

		$outCal[$list['startdate']] .= "<table><tr><td><img src=/img/calendar/".korfile($list['kind']).".gif border=0 onMouseOver=\"view('{$list['title']}', '{$lhour}','{$list['content']}');\" onMouseOut=\"noview();\"></td><td><font face=굴림><span style='font-size:9pt'><a href='{$href['view']}' onMouseOver=\"view('{$list['title']}', '{$lhour}','{$list['content']}');\" onMouseOut=\"noview();\">{$list['title']}</a></span></font></td></tr></table>\n"	;
	} // end while
	////////////////////////////

	////////////////////////////
	// 반복 일정 구하기
	// $outCal['day']
	$sql = "SELECT * from {$table_calendar} WHERE {$sql_where_cal} AND retimes>0 ";
	$sql .= " AND (startdate<='{$searchDateTo}' AND enddate >='{$searchDateFrom}') ";
	$sql .= " AND (dtype = 'hour' or dtype = 'day') ";
	$sql .="  ORDER BY starthour";
	$result	= db_query($sql);
	while( $list=db_array($result) ) {
		// 반복되는 첫 $tmp_time 구함
		if(strcmp($list['startdate'],$searchDateFrom)<0) {
			$tmp_time = strtotime($searchDateFrom);
			switch($list['retype']) {
				case "day"://일일단위 반복설정
					// - 레코드 저장일과 출력셀의 날짜와의 날짜차이
					$cday	= userDateDiff("d",$list['startdate'],$searchDateFrom)-1;
					
					if($cday%$list['retimes']>0)
						$tmp_time += ($list['retimes']-$cday%$list['retimes']) * 86400;
					break;
				case "week"://주단위 반복설정
					// - 레코드 저장일과 출력셀의 날짜와의 날짜차이
					$cday	= userDateDiff("d",$list['startdate'],$searchDateFrom)-1;

					// 주단위기에 retimes에서 7을 곱함
					if($cday%($list['retimes']*7)>0)
						$tmp_time += ($list['retimes']*7-$cday%($list['retimes']*7)) * 86400;
					break;
				case "month"://월단위 반복설정
					// 월단위기에 startdate의 일(Day)임
					$tmp_time = strtotime("substr($searchDateFrom,0,8)".substr($list['startdate'],-2));
					break;
			} // end switch
		}
		else {
			// 기간안에 startdate가 있기에 그것이 첫날임
			$tmp_time = strtotime($list['startdate']);
		}

		if($list['dtype'] == "day" )
			$lhour= "[ 하루 종일 ]";
		else
			$lhour="[{$list['starthour']}:{$list['startmin']}~{$list['endhour']}:{$list['endmin']}]";

		// 권한체크
		// 권한체크
		if(!privAuth($list,"priv_level")) {
			$list['title']	= "비공개 일정";
			$list['content']	= "비공개 일정";

			// URL Link
			$href['view'] = "javascript: return false;";
		}
		else {
			$list['title'] = cut_string($list['title'], 12);
			$list['title'] = htmlspecialchars($list['title'],ENT_QUOTES);
			$list['content'] = cut_string($list['content'], 150);
			$list['content'] = htmlspecialchars($list['content'],ENT_QUOTES);
			$list['content'] = replace_string($list['content'], 'text');	// 문서 형식에 맞추어서 내용 변경

			// URL Link
			$href['view'] = "./index.php?".href_qs("mode=view&bmode={$_GET['mode']}&uid={$list['uid']}",$qs_basic);

		} // end if.. else


		// 일정 변수에 저장
		$tmp_enddate = (strcmp($searchDateTo,$list['enddate'])<0) ? $searchDateTo : $list['enddate'];
		$tmp_time_enddate = strtotime($tmp_enddate);
		while($tmp_time<=$tmp_time_enddate) {// 말일을 지나기 전까지
			$tmp = date("Y-m-d",$tmp_time);
			$outCal[$tmp] .= "<table><tr><td><img src=/img/calendar/".urlencode($list['kind']).".gif border=0 onMouseOver=\"view('{$list['title']}', '{$lhour}','{$list['content']}');\" onMouseOut=\"noview();\"></td><td><font face=굴림><span style='font-size:9pt'><a href='{$href['view']}' onMouseOver=\"view('{$list['title']}', '{$lhour}','{$list['content']}');\" onMouseOut=\"noview();\">{$list['title']}</a></span></font></td></tr></table>\n"	;
			switch($list['retype']) {
				case "day":
					$tmp_time	+= $list['retimes'] * 86400;
					break;
				case "week":
					$tmp_time	+= $list['retimes'] * 7*86400;
					break;
				case "month": 
					$tmp_time	+= $list['retimes'] * 30*86400;
					break;
			} // end switch
		} // end while
	} // end while
	////////////////////////////

//=======================================================
// Start... (DB 작업 및 display)

//=======================================================
?>
<script src="DWConfiguration/ActiveContent/IncludeFiles/AC_RunActiveContent.js" type="text/javascript"></script>

<div ID='overDiv' STYLE='position:absolute;top=30;substr=100; visibility:hide; z-index:2;'></div>
<script LANGUAGE="JavaScript" src="./cal_div.js" type="Text/JavaScript"></script>
<table width="690" border="0" cellpadding="0" cellspacing="0" background="/img/content_line.gif" style="line-height:100%; margin-top:0; margin-bottom:0;">
							
							<tr>
								<td width="690">						
									<table align="center" style="line-height:100%; margin-top:0; margin-bottom:0;" border="0" cellpadding="0" cellspacing="0" width="620">
										
										<tr>
											<td width="620">									<form name="form1" action="index-list.php">
										<p align="right" style="line-height:100%; margin-top:0; margin-bottom:0;">&nbsp;										  </p>
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
										  <tr>
											<td width="55%" align="center"><p style="line-height: normal"><font color="#040777"size="5">
<?php
											/*
											$isfile = 'img/'.date("Ym",$intThisTimestamp).'.gif';
											
											
											if(is_file('../'.$isfile))
												echo "<img src=/{$isfile} width=145 height=29 />";
											else	
												echo date("Y년 m월",$intThisTimestamp);
											*/
											
?><script src="/img/kb_calender_date.php?date_y=<?=date("Y",$intThisTimestamp); 
?>&date_m=<?=date("m",$intThisTimestamp); 
?>"></script></font></p>											</td>
											<td width="45%"><table width="100%" border="0" align="right" cellpadding="0" cellspacing="0">
											  <tr>
												<td align="center">
<?php
if ($intThisDay >= $intPrevLastDay )
	$intPrevDay=$intPrevLastDay;
else
	$intPrevDay=$intThisDay;

if ($intThisDay >= $intNextLastDay )
	$intNextDay=$intNextLastDay;
else
	$intNextDay=$intThisDay;

// URL Link
$href['PrevYear'] = "{$_SERVER['PHP_SELF']}?" 
					. href_qs("mode=month&date={$intPrevYear}-{$intPrevMonth}-01",$qs_basic);
$href['NextYear'] = "{$_SERVER['PHP_SELF']}?" 
					. href_qs("mode=month&date={$intNextYear}-{$intNextMonth}-01",$qs_basic);

echo "<a href='{$href['PrevYear']}'>◀ 이전달[{$intPrevMonth}월] </a>&nbsp;&nbsp;";
echo "<a href='{$href['NextYear']}'> 다음달[{$intNextMonth}월] ▶ </a>";
?>												</td>
											  </tr>
											</table></td>
										  </tr>
										</table>
											</form>											</td>
										</tr>
										<tr>
											<td width="620" height="7">
												<p align="center" style="line-height:100%; margin-top:0; margin-bottom:0;"><img src="/img/yuil.gif" width="622" height="18" border="0"></p>											</td>
										</tr>
										<tr>
											<td width="620" height="32">
												<p style="line-height:100%; margin-top:0; margin-bottom:0;">&nbsp;</p>											</td>
										</tr>
									</table>
									<table align="center" border="1" cellspacing="0" width="600" bordercolordark="white" bordercolorlight="#CCCCCC" style="line-height:100%; margin-top:0; margin-bottom:0;">
<?php
// for문 초기값 정의
$intPrintDay	= 1;  //출력 초기일 값은 1부터
$Stop_Flag		= 0;
for($intNextWeek=1; $intNextWeek < 7 ; $intNextWeek++) {  //주단위 루프 시작, 최대 6주 
	echo "<tr> \n";
	for($intNextDay=1; $intNextDay < 8  ; $intNextDay++) {  //요일단위 루프 시작, 일요일부터
		echo "<td height=90 width=85 valign=top > ";
		
		if ($intPrintDay==1 and $intNextDay<$intFirstWeekday+1) { //첫주시작일이 1보다 크면
			echo "<font face=굴림 size=2 color=white>.</font> \n";
			//$intFirstWeekday=$intFirstWeekday-1;
		}
		else {  //
			if ($intPrintDay > $intLastDay ) { //입력날짜가 월말보다 크다면
				echo "<font face=굴림 size=2 color=white>.</font> \n";
			}
			else { //입력날짜가 현재월에 해당되면
				$intcday=$intThisYear."-".$intThisMonth."-".(($intPrintDay<10)?"0":"").$intPrintDay;

				// URL Link
				//$href['goday']	= "./index.php?" . href_qs("mode=day&date={$intcday}",$qs_basic);
				$href['goday']=$href['goinput']	= "./index.php?" .  href_qs("mode=input&date={$intcday}",$qs_basic);
				

				$strThisDay = '';
				if( $intThisYear-$NowThisYear==0 and $intThisMonth-$NowThisMonth==0 and $intPrintDay-$intThisDay==0 ) {
					//오늘 날짜이면은 글씨폰트를 다르게
					if($enable_write or $outCal2[$intcday]) 
						$strThisDay =  "<b><a href='{$href['goday']}'><font face=굴림 size=2 color=darkorange>{$intPrintDay}◈</font></a></b> ";
					else 
						$strThisDay =  "<b><font face=굴림 color=darkorange>{$intPrintDay}◈</font></b> <br>\n";
					
				}
				elseif( $intNextDay==1 ) { 
					//일요일이면 빨간 색으로
					if($enable_write or $outCal2[$intcday]) 
						$strThisDay =  "<b><a href='{$href['goday']}'><font face=굴림 size=2 color=red>{$intPrintDay}</font></a></b>\n";
					else 
						$strThisDay =  "<b><font face=굴림 color=red>{$intPrintDay}</font></b>\n";
				}
				else{  
					// 그외의 경우
					if($enable_write or $outCal2[$intcday]) 
						$strThisDay =  "<b><font face=굴림 color=#000000><a href='{$href['goday']}'>{$intPrintDay}</a></font></b>\n";
					else 
						$strThisDay =  "<b><font face=굴림 color=#000000>{$intPrintDay}</font></b>\n";
				}

				// 일정 내용 출력
				echo "
							<table align=center width=100% border='0' cellspacing='0'  bordercolordark='white' bordercolorlight='#CCCCCC' style='line-height:100%; margin-top:0; margin-bottom:0;'>
								<tr>
									<td width='12'>
										<p style='line-height:100%; margin-top:0; margin-bottom:0;'><font size='2'>$strThisDay</font></p>														</td>
									<td align=right>
										<p style='line-height:100%; margin-top:0; margin-bottom:0;'>{$outIcon[$intcday]}</p>														</td>
								</tr>
								<tr>
									<td colspan=2>{$outCal[$intcday]}</td>
								</tr>
							</table>
				";
			} // end if.. else

			$intPrintDay	+= 1;  //날짜값을 1 증가
			if ($intPrintDay>$intLastDay )  //만약 날짜값이 월말값보다 크면 루프문 탈출
				$Stop_Flag=1;
		} // end if.. else
		echo "</td>";
	} // end for intNextDay
	echo "</tr>";
	if ($Stop_Flag==1 )	break;
} // end for intNextWeek
?>
			</table>		</td>
	</tr>
</table>
