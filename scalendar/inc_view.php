<?php
//=======================================================
// 설  명 : 인클루드 파일 - inc_view.php
// 책임자 : 박선민 , 검수: 03/11/14
// Project: sitePHPbasic
// ChangeLog
//   DATE   수정인			 수정 내용
// -------- ------ --------------------------------------
// 03/09/16 박선민 마지막 수정
// 03/11/14 박선민 버그수정
//=======================================================

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
	// 인쿨루드인 경우에만 허용
	if ($_SERVER["PATH_TRANSLATED"] == realpath(__FILE__)) {
		echo "직접 호출되어서 거부함";
		exit;
	}

	$sql = "SELECT *  FROM {$table_calendar} WHERE uid ='{$_GET['uid']}'";
	if(!$list=db_arrayone($sql))
		back("해당 일정이 없습니다");

	// 인증 체크(자기 글이면 무조건 보기)
	if(!privAuth($list, "priv_level",1)) back("비공개 일정이거나 레벨이 부족합니다");

	$list['title']	= htmlspecialchars($list['title'],ENT_QUOTES);
	$list['content']	= htmlspecialchars($list['content'],ENT_QUOTES);
	$list['content']	= replace_string($list['content'], 'text');	// 문서 형식에 맞추어서 내용 변경

	$list['start_timestamp'] = strtotime($list['startdate']) + $list['starthour']*3600 + $list['startmin']*60;
	$list['end_timestamp'] = strtotime($list['enddate']) + $list['endhour']*3600 + $list['endmin']*60;

	// URL Link
	$href['edit']		= "./index.php?{$qs_basic}&mode=edit&date={$_GET['date']}&uid={$_GET['uid']}";
	$href['delete']	= "./ok.php?{$qs_basic}&mode=delete&date={$_GET['date']}&uid={$_GET['uid']}";
//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
?>
<table border="0" width="590" cellspacing="0" cellpadding="0" bordercolor="#000000" bordercolorlight="#000000">
	<tr>
		<td>
			<div align="center">
			<table bgcolor=#E3F1FF border="1" width="590" cellspacing="0" cellpadding="0" bordercolor="#ffffff" bordercolorlight="#000000">
		<tr>
				<td width=100 height=30>
					<font color=#333399><span style="font-size: 9pt"><b>제목:</b></font>
				</td>
				<td>
					<font color=#8B4500><span style="font-size: 9pt"><b>
					<?=$list['title']?></b>
					</span></font>
				</td>
			</tr>
			<tr>
				<td>
					<font color=#333399 align=right><span style="font-size: 9pt"><b>장소:</b></font>
				</td>
				<td height=25>
					<font color=#8B4500><span style="font-size: 9pt"><b>
					<?=$list['place']?></b>&nbsp;
					</span></font>
				</td>
			</tr>

			<tr>
				<td height=30>
					<font color=#333399><span style="font-size: 9pt"><b>일정공개레벨:</b></font>
				</td>
				<td>
					<font color=#8B4500><span style="font-size: 9pt"><b>
					<?=(int)$list['priv_level']?> 레벨이상
					</b></font>					
					
				</td>
			</tr>
			<tr>
				<td height=30>
					<font color=#333399><span style="font-size: 9pt"><b>일자:</b></font>
				</td>
				<td>
					<font color=#8B4500><span style="font-size: 9pt"><b>
<?php
						if  ($list['dtype'] == "day" ) {
							$lhour= "[ 하루 종일 ]";
							echo date("Y년 n월 j일",$list['start_timestamp']), $lhour;
						}
						elseif ($list['dtype'] == "month" ) {
							$lhour=$intThisMonth."월중 일정";
							echo $lhour;
						}
						else {
							$lhour="[{$list['starthour']}:{$list['startmin']}~{$list['endhour']}:{$list['endmin']}]";
							echo date("Y년 n월 j일",$list['start_timestamp']), $lhour;
						}
?>

					</b></font>
				</td>
			</tr>
<?php
	if ($list['retimes']>0){
?>
			<tr>
				<td bgcolor=#E3e1FF >
					<font color=#cc3333><span style="font-size: 9pt"><b>반복설정 :</b></font>
				</td>
				<td bgcolor=#E3e1FF height=50>
					<span style="font-size: 9pt"><b>본 일정은 
<?php
					switch ($list['retimes'])	{
						Case 1:	$txt_reid	= "매";		break;
						Case 2:	$txt_reid	= "둘째";	break;
						Case 3:	$txt_reid	= "셋째";	break;
						Case 4:	$txt_reid	= "넷째";	break;
					}

					switch ($list['retype'])	{
						Case 1:	$txt_retype = "일";		break;
						Case 2:	$txt_retype = "주";		break;
						Case 3:	$txt_retype = "월";		break;
						Case 4:	$txt_retype = "년";		break;
					}


					echo date("Y년 n월 j일까지",$list['end_timestamp']);
					echo " {$txt_reid}{$txt_retype}마다 반복 설정되었습니다";
?>
					</b></font>
				</td>
			</tr>
<?php
	}	
?>
			<tr>
				<td height=30>
					<font color=#333399><span style="font-size: 9pt"><b>일정내용:</b></font>
				</td>
				<td>
				<font color=#8B4500><span style="font-size: 9pt"><b>
					<?=$list['content']; ?>
					</b></font>
				</td>
			</tr>
			<tr>
				<td width="100" bgcolor=#efefef height=40>&nbsp;
				</td>
				<td bgcolor=#efefef>&nbsp;
					<a href='<?=$href['edit']
?>'>
					<img src="images/modi.gif" width="43" height="22" border="0" ></a>&nbsp;
					<a href='<?=$href['delete']
?>' onClick="javascript: return confirm('해당 일정을 정말로 삭제하시겠습니까?');">
					<img src="images/del.gif" width=43 height=22 border=0>
					</a>&nbsp;
					<a href='javascript:history.back(-1)'>
					<img src="images/cancle.gif" width="43" height="22" border="0">
					</a>
					</font>
				</td>
			</tr>
		</table>
	</td>
</td>
</table>