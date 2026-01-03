<?php
//=======================================================
// 설	명 : 인클루드 파일 - inc_input.php
// 책임자 : 박선민 (sponsor@new21.com), 검수: 03/09/16
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 03/09/16 박선민 마지막 수정
//=======================================================

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
// 인쿨루드인 경우에만 허용
if ($_SERVER["PATH_TRANSLATED"] == realpath(__FILE__)){
	echo "직접 호출되어서 거부함";
	exit;
}
if($_GET['mode'] == "edit"){
	$sql = "SELECT * FROM {$table_calendar} WHERE uid ='{$_GET['uid']}'";
	if(!$list=db_arrayone($sql))
		back("해당 일정이 없습니다");

	// 인증 체크(자기 글이면 무조건 보기)
	if(!privAuth($list, "priv_level",1)) back("비공개 일정이거나 레벨이 부족합니다");

	$list['title']	= htmlspecialchars($list['title'],ENT_QUOTES);
	$list['content']	= htmlspecialchars($list['content'],ENT_QUOTES);

	$list['start_timestamp'] = strtotime($list['startdate']) + $list['starthour']*3600 + $list['startmin']*60;
	$list['end_timestamp'] = strtotime($list['enddate']) + $list['endhour']*3600 + $list['endmin']*60;
} else {
	$list['startdate']	= $_GET['date'] ? $_GET['date'] : date("Y-m-d");
	$list['enddate']		= $list['startdate'];
	$list['starthour']	= $_GET['starthour'] ? $_GET['starthour']: 9;
	$list['endhour']		= $list['starthour'] +1;
	$list['dtype']		= "hour";

	$_GET['mode'] == "input";
}
$form_input = "name=cal action='ok.php' method=post >";
$form_input .= substr(href_qs("mode={$_GET['mode']}&uid={$_GET['uid']}",$qs_basic,1),0,-1);
$href['delete']	= "./ok.php?{$qs_basic}&mode=delete&date={$_GET['date']}&uid={$_GET['uid']}";
//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
?>
<script LANGUAGE="JavaScript" src="/scommon/js/chkform.js" type="Text/JavaScript"></script>
<script LANGUAGE="JavaScript" src="/scommon/js/inputcalendar.js" type="Text/JavaScript"></script>
<table border="0" width="95%" cellspacing="0" cellpadding="0" bordercolor="#000000" bordercolorlight="#000000">
	<tr>
		<td>
			<table bgcolor=#E3F1FF border="1" width="100%" cellspacing="0" cellpadding="0" bordercolor="#ffffff" bordercolorlight="#000000">
			<form onsubmit="return chkForm(this)" <?php echo $form_input ; ?> >
			<tr>
				<td width=100 height=30>
					<font color=#333399><span style="font-size: 9pt"><b>제목:</b></font></td>
				<td valign="top">
					<input type=text name=title size=40 maxlength=40 value="<?php echo $list['title'] ; ?>" hname="일정 제목을 입력하여 주세요." required></font></td>
			</tr>
			<tr>
				<td>
					<font color=#333399><span style="font-size: 9pt"><b>장소:</b></font></td>
				<td>
					<font><input type=text name=place maxlength=40 value="<?php echo $list['place'] ; ?>"></font></td>
			</tr>

			<tr>
				<td height=30>
					<font color=#333399><span style="font-size: 9pt"><b>일자:</b></font></td>
				<td>
					<INPUT name="startdate" TYPE=text id="startdate" ONCLICK="Calendar(this);" VALUE="<?php echo $list['startdate'] ; ?>" size='10' readonly></td>
			</tr>
			<tr>
				<td height=35 bgcolor=#E3e1FF>
					<font color=#333399><span style="font-size: 9pt"><b>시작 시간:</b></font></td>
				<td bgcolor=#E3e1FF>
					<font><select name=starthour onChange="changeEndHour(this.form)">
<?php
						for($i=0; $i < 24; $i++)
						{
							echo "<option value=".$i;
							if(intval($list['starthour']) == $i )
								echo " selected ";

							if($i < 13)
								echo " >".$i." AM \n";
							else
								echo " >" . ($i-12) . " PM \n";
						} 
?>
					</select></font>
					<font color=#339999 size=1>▶</font>
					<font>	<select name=endhour onChange="changeDurHour(this.form)">
<?php
						for($i=0; $i < 23; $i++)
						{
							echo "<option value=".$i;
							if(intval($list['endhour']) == $i )
								echo " selected ";

							if($i < 13 )
								echo " >".$i." AM \n";
							else
								echo " >" . ($i-12) . " PM \n";
						} 
?>
					</select></font>							
					</td>
			</tr>
			<tr>
				<td width="100" bgcolor=#efefef height=30>&nbsp;</td>
				<td bgcolor=#efefef>&nbsp;
					<input name="Submit" type="image" src=" images/save.gif" value="확인">
					<a href='javascript:history.back(-1)'>
					<img src="images/cancle.gif" width="43" height="22" border="0">
					</a>
					<a href='<?php echo $href['delete']; ?>' onClick='javascript:return confirm("해당 일정을 정말로 삭제하시겠습니까?");'> 
			<img src='images/del.gif' width=43 height=22 border=0> </a>
			</form>
			</font></td>
			</tr>
		</table>
	</td>
</td>
</table>

