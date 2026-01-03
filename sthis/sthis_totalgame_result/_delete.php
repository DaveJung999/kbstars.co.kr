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
<FORM ACTION="ok.php" method=POST name=myform>
	<input type=hidden name="db" value="<?php echo $db ; ?>">
	<input type=hidden name="mode" value="delete">
	<input type=hidden name="cateuid" value="<?php echo $cateuid ; ?>">
	<input type=hidden name="pern" value="<?php echo $pern; ?>">
	<input type=hidden name="sc_column" value="<?php echo $sc_column; ?>">
	<input type=hidden name="sc_string" value="<?php echo $sc_string ; ?>">
	<input type=hidden name="page" value="<?php echo $page ; ?>">
	<input type=hidden name="uid" value="<?php echo $uid ; ?>">
</FORM>
</BODY>
</HTML>
