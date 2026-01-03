<?php
if(!@include"nalog_connect.php"){echo"<script language='javascript'>alert('Please install n@log first :)')</script>
<meta http-equiv='refresh' content='0;url=install.php'>";exit;}
include "lib.php";
####################################################################################
//					4.x->5.x 자동업데이트
####################################################################################
if(!$language){
	nalog_go("install.php?mode=upgrade&step=1");
}
if($language && !nalog_admin_check4()){
	nalog_go("login.php?language=$language&go=upgrader.php?language=$language");
}
if($language){
####################################################################################
//					언어설정
####################################################################################
	$fp = @fopen("nalog_language.php", "w");
	if(!$fp){nalog_error("Permission Denied. Change the permission of n@log directory (707 or 777)");}
	@fwrite($fp, "<?php
\$language=\"$language\";
?>");
	fclose($fp);
}
@mkdir("plug_in_config",0777);
####################################################################################
//					3.x->4.x 자동업데이트
####################################################################################
$temp=@nalog_total("nalog3_data");
if(!$temp){include"nalog_default_schema.php";}
####################################################################################
//					꺼내기
####################################################################################
$tables=nalog_list_bd();
$total=count($tables);
for($i=0;$i<$total;$i++)
{
	if(!$tables[$i]){break;}
	$counter=$tables[$i];
####################################################################################
//					4.0.3->4.0.4 자동업데이트
####################################################################################
	$query="alter table nalog3_dlog_$counter add bookmark tinyint default '0'";
	@mysqli_query($connect,$query);
####################################################################################
//					4.0.4->4.0.5 자동업데이트
####################################################################################
	$query="alter table nalog3_log_$counter add bookmark tinyint default '0'";
	@mysqli_query($connect,$query);
####################################################################################
//					4.0.5->4.0.6 자동업데이트
####################################################################################
	$query="alter table nalog3_counter_$counter add referer varchar(200)";
	@mysqli_query($connect,$query);
	$query="alter table nalog3_config_$counter add check_admin tinyint default '0'";
	@mysqli_query($connect,$query);
####################################################################################
//					5.0.1->5.0.2 자동업데이트
####################################################################################
	$query="alter table nalog3_config_$counter add time_zone1 char(1) default '1'";
	@mysqli_query($connect,$query);
	$query="alter table nalog3_config_$counter add time_zone2 int default '0'";
	@mysqli_query($connect,$query);
}
@mysqli_close($connect);
nalog_msg("Finished for upgrade");
nalog_go("root.php");
?>