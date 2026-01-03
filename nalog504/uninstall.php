<?php
####################################################################################
//					준비
####################################################################################
if(!@include"nalog_connect.php"){echo"<script language='javascript'>alert('Please install n@log first :)')</script>
<meta http-equiv='refresh' content='0;url=install.php'>";exit;}
include "lib.php";
if(!@include "nalog_language.php"){nalog_go("install.php");}
if(!@include"language/$language/language.php"){nalog_go("install.php");}
####################################################################################
//					체크
####################################################################################
nalog_admin_check("login.php?go=root.php");
####################################################################################
//					꺼내기
####################################################################################
$tables=nalog_list_bd();
$total=count($tables);
for($i=0;$i<$total;$i++){
	if(!$tables[$i]){break;}
	$new_board=$tables[$i];
	@nalog_drop("nalog3_counter_".$new_board);
	@nalog_drop("nalog3_config_".$new_board);
	@nalog_drop("nalog3_now_".$new_board);
	@nalog_drop("nalog3_log_".$new_board);
	@nalog_drop("nalog3_dlog_".$new_board);
}
@nalog_drop("nalog3_data");
@nalog_drop("nalog3_os");
@unlink("nalog_connect.php");
@unlink("nalog_language.php");
$handle=@opendir("./");
while ($dir = @readdir($handle))
{
	if(preg_match("/\.jpg$/i",$dir)){@unlink("$dir");}
}
$handle=@opendir("plug_in_config");
while ($dir = readdir($handle))
{
	if($dir=="." || $dir==".."){continue;}
	@unlink("plug_in_config/$dir");
}
@rmdir("plug_in_config");
@mysqli_close($connect);
nalog_msg($lang['uninstall_finish']);
nalog_go("http://navyism.com");
?>