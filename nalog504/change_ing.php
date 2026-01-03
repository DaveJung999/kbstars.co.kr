<?php
####################################################################################
//					헤더
####################################################################################
header('P3P: CP="NOI CURa ADMa DEVa TAIa OUR DELa BUS IND PHY ONL UNI COM NAV INT DEM PRE"');

####################################################################################
//					준비
####################################################################################
if(!@include "nalog_connect.php"){echo "<script language='javascript'>alert('Please install n@log first :)')</script>
<meta http-equiv='refresh' content='0;url=install.php'>";exit;}
include "lib.php";
if(!@include "nalog_language.php"){nalog_go("install.php");}
if(!@include "language/$language/language.php"){echo "<script>window.close()</script>";}
nalog_admin_check2();

####################################################################################
//					접속파일생성
####################################################################################
$fp = @fopen("nalog_connect.php", "w");
fwrite($fp, "<?php\n\$connect_host = \"$connect_host\";\n\$connect_id   = \"$connect_id\";\n\$connect_pass = \"$connect_pass\";\n\$connect_db   = \"$connect_db\";\n\$admin_id     = \"$admin_idx\";\n\$admin_pass   = \"$admin_passx\";\n\n\$connect = @mysqli_connect(\$connect_host, \$connect_id, \$connect_pass, \$connect_db);\n?>");
fclose($fp);

####################################################################################
//					쿠키주기
####################################################################################
setcookie("nalog_admin", md5($admin_idx.$admin_passx), 0, "/");

####################################################################################
//					퍼미션
####################################################################################
@chmod("nalog_connect.php", 0777);

@mysqli_close($connect);

nalog_msg($lang['change_admin_finish']);
echo "
<script language='javascript'>
window.close();
</script>
";
?>
