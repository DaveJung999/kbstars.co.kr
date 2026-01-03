<HTML>
<HEAD>
<TITLE>AutoLogin</TITLE>
</HEAD>
<SCRIPT LANGUAGE="Javascript1.1">
<!--
function myLoad(){
	document.myform.submit();
	return true;
}
window.onload=myLoad;
// --></SCRIPT>
<BODY>
<?php

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

?>
<FORM ACTION="ok.php" method=POST name=myform>
	<input type=hidden name="db" value="<?=$db?>">
	<input type=hidden name="mode" value="delete">
	<input type=hidden name="cateuid" value="<?=$cateuid?>">
	<input type=hidden name="pern" value="<?=$pern?>">
	<input type=hidden name="sc_column" value="<?=$sc_column?>">
	<input type=hidden name="sc_string" value="<?=$sc_string?>">
	<input type=hidden name="page" value="<?=$page?>">
	<input type=hidden name="uid" value="<?=$uid?>">
</FORM>
</BODY>
</HTML>
