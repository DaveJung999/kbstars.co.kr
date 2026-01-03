<?php

####################################################################################
//					준비
####################################################################################
include "lib.php";

if (!$step) {
	include "header.inc";
?>
<table align="center" border="0" width="100%" height="100%">
<tr><td align="center" valign="middle">

<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
	codebase="http://active.macromedia.com/flash4/cabs/swflash.cab#version=4,0,0,0"
	width="300" height="200">
	<param name="movie" value="nalog_image/logo.swf">
	<param name="play" value="true">
	<param name="loop" value="false">
	<param name="quality" value="high">
	<embed src="nalog_image/logo.swf" play="true" loop="true" quality="high"
		pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash"
		width="300" height="200"></embed>
</object>

</td></tr>
</table>

</body>
</html>
<?php
}

if ($step == 1) {
	include "header.inc";
?>
<table align="center" border="0" width="100%" height="100%">
<tr><td align="center" valign="middle">
	<a href="install.php?step=2&mode=<?php echo $mode; ?>" onfocus="this.blur()">
		<img src="nalog_image/logo.gif" border="0" alt="Click here to install">
	</a><br><br>
	<img src="nalog_image/powered.gif" alt="powered by">
</td></tr>
</table>
<?php
}

if ($step == 2) {
	include "header.inc";
?>
<script language="javascript">
function chk() {
	if (!install.language.value) {
		alert('n@log error :\n\nselect language');
		return false;
	}
}
</script>
<br><br>
<table align="center" width="500" cellpadding="2" cellspacing="0" border="0" bgcolor="#F1F9FD">
<form name="install" method="post" action="install.php" onsubmit="return chk()">
<input type="hidden" name="step" value="3">
<input type="hidden" name="mode" value="<?php echo $mode; ?>">

<tr><td colspan="2" bgcolor="white">
	<a href="http://navyism.com" target="_blank"><img src="nalog_image/logo_small.gif" border="0"></a>
</td></tr>
<tr><td colspan="2" bgcolor="white">
	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
	<td><font color="#008CD6" size="4"><b>&nbsp;Language Selection</b></font></td>
	<td align="right"><?php echo $help; ?></td>
	</tr>
	</table>
</td></tr>
<tr><td colspan="2" height="3" bgcolor="#2CBBFF"></td></tr>
<tr><td colspan="2" height="8"></td></tr>
<tr><td colspan="2" align="center">
Please select the default language of your n@log 5 as listed below.<br><br>
<select size="10" style="width:95%" class="input" name="language">
<?php
$handle = @opendir("language");
if (!$handle) {
	nalog_msg("n@log couldn't find language pack directory `language`\nAll processing will stop");
	exit;
}
$i = 0;
while ($dir = @readdir($handle)) {
	if ($dir == "." || $dir == "..") continue;
	if (!@include "language/$dir/language.php") {
		nalog_msg("language/$dir/language.php have an error");
		continue;
	}
	if ($lang['name']) {
		echo "<option value=\"$dir\">".$lang['name']."</option>\n";
		$i++;
	}
}
if (!$i) {
	nalog_msg("n@log couldn't find language pack directory\nAll processing will stop");
}
?>
</select>
</td></tr>
<tr><td colspan="2" height="5"></td></tr>
<tr><td colspan="2" bgcolor="#E3F1FF">
	<table width="90%" cellpadding="2" cellspacing="0" align="center">
	<tr><td align="center"><input type="submit" value=" NEXT " class="button"></td></tr>
	</table>
<tr><td colspan="2" height="8"></td></tr>
<tr><td colspan="2" height="3" bgcolor="#2CBBFF"></td></tr>
<tr><td colspan="2" bgcolor="white" align="right">
<font size="1" face="tahoma">n@log analyzer <?php echo $nalog_info['version']; ?> &copy;2001-2003</font>
<a href="http://navyism.com" target="_blank"><font size="1"><b>navyism</b></font></a>
</td></tr>
</table>
<?php
}

if ($step == 3) {
	if (!@include "language/$language/language.php") { nalog_go("install.php"); }
	echo $lang['head'];

	$action = ($mode == "upgrade") ? "upgrader.php" : "install_er.php";
?>
<br><br>
<table align="center" width="500" cellpadding="2" cellspacing="0" border="0" bgcolor="#F1F9FD">
<form name="install" method="post" action="<?php echo $action; ?>">
<input type="hidden" name="language" value="<?php echo $language; ?>">

<tr><td colspan="2" bgcolor="white">
	<a href="http://navyism.com" target="_blank"><img src="nalog_image/logo_small.gif" border="0"></a>
</td></tr>
<tr><td colspan="2" bgcolor="white">
	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
	<td><font color="#008CD6" size="4"><b>&nbsp;<?php echo $lang['install_license_title']; ?></b></font></td>
	<td align="right"><?php echo $help; ?></td>
	</tr>
	</table>
</td></tr>
<tr><td colspan="2" height="3" bgcolor="#2CBBFF"></td></tr>
<tr><td colspan="2" height="8"></td></tr>
<tr><td colspan="2" align="center">
<?php echo $lang['install_license_agreement']; ?><br><br>
<textarea style="width:95%" cols="92" rows="<?php echo $lang['install_license_textarea_rows']; ?>" class="input" readonly>
<?php echo $lang['install_license_text']; ?>
</textarea>
</td></tr>
<tr><td colspan="2" height="5"></td></tr>
<tr><td colspan="2" bgcolor="#E3F1FF">
	<table width="90%" cellpadding="2" cellspacing="0" align="center">
	<tr><td><?php echo $lang['install_license_ask']; ?></td></tr>
	<tr><td align="center">
		<input type="submit" value="<?php echo $lang['install_license_agree']; ?>" class="button"> 
		<input type="button" class="button" value="<?php echo $lang['install_license_decline']; ?>" onclick="location.href='http://navyism.com'">
	</td></tr>
	</table>
<tr><td colspan="2" height="8"></td></tr>
<tr><td colspan="2" height="3" bgcolor="#2CBBFF"></td></tr>
<tr><td colspan="2" bgcolor="white" align="right"><?php echo $lang['copy']; ?></td></tr>
</table>
<?php
}
?>
</body>
</html>
