<?php
//=======================================================
// 설	명 : 설문조사 삽입 예제
// 책임자 : 박선민 (sponsor@new21.com), 검수: 03/08/25
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 03/08/25 박선민 마지막 수정
//=======================================================
$HEADER=array(
		'priv' => '', // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
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
	$table_pollinfo = "{$SITE['th']}pollinfo";
	
	if($_GET['db'] != "") $_REQUEST['db'] = $_GET['db'];

	$rs_poll = db_query("SELECT * from {$table_pollinfo} WHERE db ='{$_REQUEST['db']}'");
	if(!$list_poll = db_array($rs_poll)) exit;
	

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
$today = time(); 
?>
<link href="/stpl/basic/style.css" rel="stylesheet" type="text/css">
<form name="form1" method="post" action="/spoll/poll_ok.php" style="margin:0px">
<input type="hidden" name="mode" value="poll">
<input type="hidden" name="uid" value="<?php echo $list_poll['uid']; ?>">
<input type="hidden" name="db" value="<?php echo $list_poll['db']; ?>">
	<table width="160" cellspacing="0" cellpadding="0" align="center">
	<tr> 
		<td width="160"> 
		<table width="160" cellspacing="0" cellpadding="3" align="center">
		<tr> 
			<td height="50" > <font color="#000000"> 
<?php echo $list_poll['title']; ?>
				</font> </td>
			</tr>
			<tr> 
			<td bgcolor="#F6F6F6" > 
				<table width="100%" cellspacing="0" cellpadding="0">
<?php
for($i=1; $i<$list_poll['q_num']+1; $i++){ 
?>
				<tr> 
					<td> 
					<input type="radio" name="val" value="<?php echo $i; ?>">
<?php echo $list_poll["q{$i}"]; ?>
					</td>
				</tr>
<?php
} // end for
?>
				</table></td>
			</tr>
			<tr> 
			<td bgcolor="#F5F5F5" align="center" > 
				<input type="image" src="images/vote.gif" value="투표하기">
				<a href="#" onClick="javascript: window.open('/spoll/poll_result.php?db=<?php echo $list_poll['db'] ; ?>','','width=600,height=500')"><img src="images/view.gif" border="0"></a></td>
			</tr>
		</table>
		</td>
	</tr>
	</table>
</form>