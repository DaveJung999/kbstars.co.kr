<?php
//=======================================================
// 설	명 : 인클루드 파일 - inc_view.php
// 책임자 : 박선민 (sponsor@new21.com), 검수: 03/11/14
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 03/09/16 박선민 마지막 수정
// 03/11/14 박선민 버그수정
//=======================================================

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
// 인쿨루드인 경우에만 허용
if ($_SERVER["PATH_TRANSLATED"] == realpath(__FILE__)){
	echo "직접 호출되어서 거부함";
	exit;
}

$sql = "SELECT * FROM {$table_calendar} WHERE uid ='{$_GET['uid']}'";
if(!$list=db_arrayone($sql))
	back("해당 일정이 없습니다");

// 인증 체크(자기 글이면 무조건 보기)
if(!privAuth($list, "priv_level",1)) back("비공개 일정이거나 레벨이 부족합니다");

	if(privAuth($dbinfo, "priv_write"))	$enable_write = true;

$list['title']	= htmlspecialchars($list['title'],ENT_QUOTES);
$list['content']	= htmlspecialchars($list['content'],ENT_QUOTES);
$list['content']	= replace_string($list['content'], 'text');	// 문서 형식에 맞추어서 내용 변경

$list['start_timestamp'] = strtotime($list['startdate']) + $list['starthour']*3600 + $list['startmin']*60;
$list['end_timestamp'] = strtotime($list['enddate']) + $list['endhour']*3600 + $list['endmin']*60;

// URL Link
$href['edit']		= "./index.php?{$qs_basic}&mode=edit&date={$_GET['date']}&uid={$_GET['uid']}";
$href['delete']	= "./ok.php?{$qs_basic}&mode=delete&date={$_GET['date']}&uid={$_GET['uid']}";
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
?>
<table cellpadding="0" cellspacing="0" width="95%" height="25" style="border-collapse:collapse;">
	<tr>
		<td width="484" height="2" bgcolor="#CCCCCC">
		</td>
		<td width="484" height="2" bgcolor="#CCCCCC">
		</td>
	</tr>
	<tr>
		<td width="934" height="50" colspan="2">
<?php
	$list['start_timestamp'] = strtotime($list['startdate']) + $list['starthour']*3600 + $list['startmin']*60;
	$you= date("w",$list['start_timestamp']);
	switch($you){
		case '0':
				$list['you'] = "일";		
			break;
		case '1':
				$list['you'] = "월";		
			break;
		case '2':
				$list['you'] = "화";		
			break;
		case '3':
				$list['you'] = "수";		
			break;
		case '4':
				$list['you'] = "목";		
			break;
		case '5':
				$list['you'] = "금";		
			break;
		case '6':
				$list['you'] = "토";		
			break;
	} // end switch
	echo "<img src='images/icon_bluejum.gif'><b><font color='#840000'>".date("Y년n월 j일",$list['start_timestamp']) . " {$list['you']}요일 일정</font></b><br><br>";
	
	$sql = "SELECT * from {$table_calendar} WHERE startdate = '{$list['startdate']}'";
	$i = 1;
	$result	= db_query($sql);
	while( $list=db_array($result) ){
		if(privAuth($dbinfo, "priv_write"))	
			echo "&nbsp;&nbsp;{$i} . <a href = 'index.php?db=player&mode=edit&date={$list['startdate']}&uid={$list['uid']}'>{$list['starthour']}시 ~ {$list['endhour']}시 : {$list['title']}({$list['place']})</a><br>";
		else
			echo "&nbsp;&nbsp;{$i} . {$list['starthour']}시 ~ {$list['endhour']}시 : {$list['title']}({$list['place']})<br>";
		$i++;	
	} 

?>
		</td>
	</tr>
	<tr>
		<td width="934" height="30" colspan="2">
		</td>
	</tr>
	<tr>
		<td width="484" height="2" bgcolor="#CECFCE">
		</td>
		<td width="484" height="2" bgcolor="#CECFCE">
		</td>
	</tr>
</table>
<br>
<br>
<?php
include("inc_month.php"); 
?>
	
<br>
