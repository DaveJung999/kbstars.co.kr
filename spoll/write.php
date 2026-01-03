<?php
//=======================================================
// 설	명 : 설문 종합관리(list.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 03/08/25
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 03/08/25 박선민 마지막 수정
// 25/08/11 Gemini	PHP 7 마이그레이션
//=======================================================
$HEADER=array(
		'priv' => '운영자', // 인증유무 (비회원,회원,운영자,서버관리자)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useApp' => 1,
		'useBoard2' => 1,
		'useSkin' => 1, // 템플릿 사용
		'useCheck' => 1, // check_value()
		'html_echo' => 1,
		'html_skin' => 'd14' ,
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']);

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
// 기본 URL QueryString
$qs_basic = "db={$db}".					//table 이름
			"&mode=".					// mode값은 list.php에서는 당연히 빈값
			"&cateuid={$cateuid}".		//cateuid
			"&pern={$pern}" .				// 페이지당 표시될 게시물 수
			"&sc_column={$sc_column}".	//search column
			"&sc_string=" . urlencode(stripslashes($sc_string)) . //search string
			"&page={$page}";				//현재 페이지

$table_pollinfo = "{$SITE['th']}pollinfo";	//게시판 관리 테이블

// 관리자페이지 환경파일 읽어드림
//	$rs=db_query("select * from {$SITE['th']}admin_tableinfo where skin='{$SITE['th']}' or skin='basic' order by uid DESC");
//	$pageinfo=db_count() ? db_array($rs) : back("관리자페이지 환경파일을 읽을 수가 없습니다");

if(isset($_GET['mode']) && $_GET['mode'] == "modify"){
	$mode = "modify";
	$rs_list = db_query("SELECT * FROM {$table_pollinfo} WHERE uid='{$_GET['uid']}'");
	$list = db_count() ? db_array($rs_list) : back("게시물의 정보가 없습니다");
	
	userFormSpecialChars($SITE['database'], $table_pollinfo, $list);

	## 대문자는 설문 시작날짜 소문자는 마감날짜
	$Y = isset($list['startdate']) ? date('Y',$list['startdate']) : '';
	$M = isset($list['startdate']) ? date('n',$list['startdate']) : '';
	$D = isset($list['startdate']) ? date('j',$list['startdate']) : '';
	$T = isset($list['startdate']) ? date('t',$list['startdate']) : '';

	$y = isset($list['enddate']) ? date('Y',$list['enddate']) : '';
	$m = isset($list['enddate']) ? date('n',$list['enddate']) : '';
	$d = isset($list['enddate']) ? date('j',$list['enddate']) : '';
	$t = isset($list['enddate']) ? date('t',$list['enddate']) : '';
} else {
	$mode = "write";
	$list['skin'] = "poll_basic";
}
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
?>
<SCRIPT language="JavaScript">
<!--
/*
function change(form){
		if(form.q_num.selectedIndex != -1)
			self.window.open(form.q_num.options[form.q_num.selectedIndex].value, target="_self");
}
*/
//-->
</SCRIPT>
<table cellpadding="0" cellspacing="0" width="100%" height="21" bgcolor="#CE966B">
	<tr>
		<td width="98%" align="right"><font color="white"><a href="/d05_supporters/poll.php" class="white">설문조사</a></font></td>
		<td width="2%">&nbsp;</td>
	</tr>
</table>
<br>
<table width="89%" cellpadding="0" cellspacing="0" align="center">
	<tr>
	<td>
	<form name="form1" method="post" action="./ok.php" style="margin:0px" >
	<input type="hidden" name="mode" value="<?php echo $mode ; ?>">
	<input type="hidden" name="uid" value="<?php echo isset($list['uid']) ? $list['uid'] : ''; ?>">
		<table width="100%" cellpadding="5">
<?php
if($mode == "write") {
} else {
} // end if
?>
		<tr>
			<td bgcolor="#CCCCCC" width="10%" height="25"> <div align="right"><b><font color="#000000" size="2">권한</font></b></div></td>
			<td bgcolor="#f6f6f6" width="90%" height="25"><font size="2">
			<input type="text" name="priv" value="<?php echo isset($list['priv']) ? $list['priv'] : ''; ?>">
			(회원,운영자 등등)
</font></td>
		</tr>
		<tr>
			<td bgcolor="#CCCCCC" width="10%" height="25"> <div align="right"><font size="2"><b><font color="#000000">성별</font></b></font></div></td>
			<td bgcolor="#f6f6f6" width="90%" height="25"><font size="2">전체
			<input type="radio" name="sex" value="0"<?php echo (isset($list['sex']) && $list['sex'] == 0 ? "checked" : "");	?>>
			남자
			<input type="radio" name="sex" value="1"<?php echo (isset($list['sex']) && $list['sex'] == 1 ? "checked" : "");	?>>
			여자
			<input type="radio" name="sex" value="2"<?php echo (isset($list['sex']) && $list['sex'] == 2 ? "checked" : "");	?>>
			</font></td>
		</tr>
		<tr>
			<td bgcolor="#CCCCCC" width="10%" height="25"> <div align="right"><font size="2"><b><font color="#000000">연령별</font></b></font></div></td>
			<td bgcolor="#f6f6f6" width="90%" height="25"> <font size="2">
			<select name="age">
				<option value="0">전체</option>
				<option value="10/19"<?php echo (isset($list['age']) && $list['age'] == '10/19' ? "selected" : "") ; ?>>10대</option>
				<option value="20/29"<?php echo (isset($list['age']) && $list['age'] == '20/29' ? "selected" : "") ; ?>>20대</option>
				<option value="30/39"<?php echo (isset($list['age']) && $list['age'] == '30/39' ? "selected" : "") ; ?>>30대</option>
				<option value="40/49"<?php echo (isset($list['age']) && $list['age'] == '40/49' ? "selected" : "") ; ?>>40대</option>
				<option value="50/100"<?php echo (isset($list['age']) && $list['age'] == '50/100' ? "selected" : "") ; ?>>50대이상</option>
				<option value="10/29"<?php echo (isset($list['age']) && $list['age'] == '10/29' ? "selected" : "") ; ?>>10대~20대</option>
				<option value="10/39"<?php echo (isset($list['age']) && $list['age'] == '10/39' ? "selected" : "") ; ?>>10대~30대</option>
				<option value="10/49"<?php echo (isset($list['age']) && $list['age'] == '10/49' ? "selected" : "") ; ?>>10대~40대</option>
				<option value="20/39"<?php echo (isset($list['age']) && $list['age'] == '20/39' ? "selected" : "") ; ?>>20대~30대</option>
				<option value="20/49"<?php echo (isset($list['age']) && $list['age'] == '20/49' ? "selected" : "") ; ?>>20대~40대</option>
				<option value="20/100"<?php echo (isset($list['age']) && $list['age'] == '20/100' ? "selected" : "") ; ?>>20대이상</option>
				<option value="30/49"<?php echo (isset($list['age']) && $list['age'] == '30/49' ? "selected" : "") ; ?>>30대~40대</option>
				<option value="30/100"<?php echo (isset($list['age']) && $list['age'] == '30/100' ? "selected" : "") ; ?>>30대이상</option>
				<option value="40/100"<?php echo (isset($list['age']) && $list['age'] == '40/100' ? "selected" : "") ; ?>>40대이상</option>
			</select>
			</font></td>
		</tr>
		<tr>
			<td bgcolor="#CCCCCC" width="10%" height="26"> <div align="right"><b><font color="#000000"><font size="2">투표
				시작일</font></font></b></div></td>
			<td bgcolor="#f6f6f6" width="90%" height="26"><font size="2">
			<select name="start_time_y">
<?php
if($mode == "modify"){
					for($i=date('Y'); $i<date('Y')+2; $i++){
						echo ($i == $Y ? "<option value='{$i}' selected>{$i}</option>" : "<option value='{$i}'>{$i}</option>" );
					}
				}
				else{
					for($i=date('Y'); $i<date('Y') +2; $i++){
						echo ($i == date('Y') ? "<option value='{$i}' selected>{$i}</option>" : "<option value='{$i}'>{$i}</option>" );
					}
				}
?>
			</select>
			년
			<select name="start_time_m">
<?php
if($mode == "modify"){
					for($i=1; $i<13; $i++){
						echo ($i == $M ? "<option value='{$i}' selected>{$i}</option>" : "<option value='{$i}'>{$i}</option>" );
					}
				}
				else{
					for($i=1; $i<13; $i++){
						echo ($i == date('n') ? "<option value='{$i}' selected>{$i}</option>" : "<option value='{$i}'>{$i}</option>" );
					}
				}
?>
			</select>
			월
			<select name="start_time_d">
<?php
if($mode == "modify"){
					for($i=1; $i< (int)$T+1; $i++){
						echo ($i == $D ? "<option value='{$i}' selected>{$i}</option>" : "<option value='{$i}'>{$i}</option>" );
					}
				}
				else{
					for($i=1; $i<date('t')+1; $i++){
						echo ($i == date('j') ? "<option value='{$i}' selected>{$i}</option>" : "<option value='{$i}'>{$i}</option>" );
					}
				}
?>
			</select>
			일</font></td>
		</tr>
		<tr>
			<td bgcolor="#CCCCCC" width="10%"> <div align="right"><b><font color="#000000"><font size="2">투표
				마감일</font></font></b></div></td>
			<td bgcolor="#f6f6f6" width="90%"><font size="2">
			<select name="end_time_y">
<?php
if($mode == "modify"){
					for($i=date('Y'); $i<date('Y')+2; $i++){
						echo ($i == $y ? "<option value='{$i}' selected>{$i}</option>" : "<option value='{$i}'>{$i}</option>" );
					}
				}
				else{
					for($i=date('Y'); $i<date('Y') +2; $i++){
						echo ($i == date('Y') ? "<option value='{$i}' selected>{$i}</option>" : "<option value='{$i}'>{$i}</option>" );
					}
				}
?>
			</select>
			년
			<select name="end_time_m">
<?php
if($mode == "modify"){
					for($i=1; $i<13; $i++){
						echo ($i == $m ? "<option value='{$i}' selected>{$i}</option>" : "<option value='{$i}'>{$i}</option>" );
					}
				}
				else{
					for($i=1; $i<13; $i++){
						echo ($i == date('n') ? "<option value='{$i}' selected>{$i}</option>" : "<option value='{$i}'>{$i}</option>" );
					}
				}
?>
			</select>
			월
			<select name="end_time_d">
<?php
if($mode == "modify"){
					for($i=1; $i< (int)$t+1; $i++){
						echo ($i == $d ? "<option value='{$i}' selected>{$i}</option>" : "<option value='{$i}'>{$i}</option>" );
					}
				}
				else{
					for($i=1; $i<date('t')+1; $i++){
						echo ($i == date('j') ? "<option value='{$i}' selected>{$i}</option>" : "<option value='{$i}'>{$i}</option>" );
					}
				}
?>
			</select>
			일</font></td>
		</tr>
		<tr>
			<td bgcolor="#CCCCCC" width="10%"> <div align="right"><b><font color="#000000"><font size="2">투표
				주제</font></font></b></div></td>
			<td bgcolor="#f6f6f6" width="90%"> <textarea name="title" cols="60" rows="5"><?php echo isset($list['title']) ? $list['title'] : ''; ?></textarea> </td>
		</tr>
<?php
## 질문 항목수에 따라.. . 항목을 나열한다.
for($i=1; $i<21; $i++){
	$question = "q".$i;
?>
		<tr>
			<td width="10%" bgcolor="#CCCCCC"> <div align="right"><b><font color="#000000"><font size="2">항목<?php echo $i;?> </font></font></b></div></td>
			<td width="90%" bgcolor="#f6f6f6"> <input type="text" name="q<?php echo $i;?>" size="60" value="<?php echo isset($list[$question]) ? $list[$question] : ''; ?>">	</td>
		</tr>
<?php
} 
?>
		<tr>
			<td width="10%" bgcolor="#CCCCCC" height="50"> <div align="right">&nbsp;</div></td>
			<td width="90%" bgcolor="#f6f6f6" height="50"> <input type="submit" name="Submit" value="	:: 설문 만들기 ::	">	</td>
		</tr>
		</table>
	</form>
	
	</td>
	</tr>
</table>
<br><?php
//=======================================================
// User functions.. . (사용자 함수 정의)
//=======================================================
// 03/08/25
function userFormSpecialChars($table, &$list) {
	$safe_table = db_escape($table);
	$result = db_query("SHOW COLUMNS FROM `{$safe_table}`");
	if (!$result) return false;

	$string_types = ['char', 'varchar', 'tinytext', 'text', 'mediumtext', 'longtext', 'enum', 'set'];

	while ($row = db_array($result)) {
		$field_name = $row['Field'];
		$field_type = strtolower(preg_replace('/\(.*/', '', $row['Type']));

		if (isset($list[$field_name]) && in_array($field_type, $string_types)) {
			$list[$field_name] = htmlspecialchars($list[$field_name], ENT_QUOTES, 'UTF-8');
		}
	}
	db_free_result($result);
	
	return true;
}

echo $SITE['tail']; ?>
