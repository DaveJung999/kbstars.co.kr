<?php
####################################################################################
//					헤더
####################################################################################
header('P3P: CP="NOI CURa ADMa DEVa TAIa OUR DELa BUS IND PHY ONL UNI COM NAV INT DEM PRE"');

####################################################################################
//					준비
####################################################################################
if (!@include "nalog_connect.php") {
	echo "<script language='javascript'>alert('Please install n@log first :)')</script>
	<meta http-equiv='refresh' content='0;url=install.php'>";
	exit;
}
include "lib.php";
@include "nalog_language.php";
@include "language/$language/language.php";

####################################################################################
//					계정체크
####################################################################################
if ($id != $admin_id) {
	nalog_error($lang['login_error_id_wrong']);
}
if ($pass != $admin_pass) {
	nalog_error($lang['login_error_pass_wrong']);
}

####################################################################################
//					쿠키굽기
####################################################################################
if ($auto) {
	$auto = 30 * 24 * 3600;
} else {
	$auto = 0;
}
setcookie("nalog_admin", md5($admin_id . $admin_pass), $auto, "/");

####################################################################################
//					이동
####################################################################################
mysqli_close($connect); // mysql_close → mysqli_close
if ($go) {
	nalog_go($go);
	exit;
}
if ($history) {
	nalog_go($history);
	exit;
}
nalog_go("root.php");
?>
