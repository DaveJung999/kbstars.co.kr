<?php
####################################################################################
//					헤더
####################################################################################
header('P3P: CP="NOI CURa ADMa DEVa TAIa OUR DELa BUS IND PHY ONL UNI COM NAV INT DEM PRE"');

####################################################################################
//					준비
####################################################################################
include "lib.php";
if(!@include "language/$language/language.php"){ nalog_go("install.php"); }

####################################################################################
//					체크
####################################################################################
$host = trim($host);
$db_id = trim($db_id);
$db_pass = trim($db_pass);
$db_name = trim($db_name);
$admin_id = trim($admin_id);
$admin_pass = trim($admin_pass);
$admin_pass2 = trim($admin_pass2);

if($admin_pass != $admin_pass2){ nalog_error($lang['install_mysql_error_admin_match']); exit; }

####################################################################################
//					접속
####################################################################################
$connect = @mysqli_connect($host, $db_id, $db_pass, $db_name);
if(!$connect){ nalog_error($lang['install_ing_error_db_id']); exit; }

####################################################################################
//					접속파일생성
####################################################################################
if(file_exists("nalog_connect.php")){ nalog_error("Delete `nalog_connect.php` file, and try again"); }

$fp = @fopen("nalog_connect.php", "w");
if(!$fp){ nalog_error($lang['install_ing_error_permission1']); }
fwrite($fp, "<?php
\$connect_host = \"$host\";
\$connect_id = \"$db_id\";
\$connect_pass = \"$db_pass\";
\$connect_db = \"$db_name\";
\$admin_id = \"$admin_id\";
\$admin_pass = \"$admin_pass\";

\$connect = @mysqli_connect(\$connect_host, \$connect_id, \$connect_pass, \$connect_db);
?>");
fclose($fp);

####################################################################################
//					언어팩파일생성
####################################################################################
$fp = @fopen("nalog_language.php", "w");
if(!$fp){ nalog_error($lang['install_ing_error_permission2']); }
fwrite($fp, "<?php
\$language=\"$language\";
?>");
fclose($fp);

####################################################################################
//					플러그인폴더생성
####################################################################################
@mkdir("plug_in_config", 0777);

####################################################################################
//					쿠키주기
####################################################################################
setcookie("nalog_admin", md5($admin_id.$admin_pass), 0, "/");

####################################################################################
//					퍼미션변경
####################################################################################
@chmod("plug_in_config", 0777);
@chmod("nalog_connect.php", 0777);
@chmod("nalog_language.php", 0777);

####################################################################################
//					기본테이블생성
####################################################################################
include "nalog_default_schema.php";

####################################################################################
//					3.x->4.x 자동업데이트
####################################################################################
$temp = @nalog_total("nalog3_data");
if(!$temp){ include "nalog_default_schema.php"; }

####################################################################################
//					테이블꺼내기
####################################################################################
$tables = nalog_list_bd();
$total = count($tables);

for($i=0; $i<$total; $i++)
{
	if(!$tables[$i]){ break; }
	$counter = $tables[$i];

	####################################################################################
	//					4.0.3->4.0.4 자동업데이트
	####################################################################################
	$query = "ALTER TABLE nalog3_dlog_$counter ADD bookmark TINYINT DEFAULT '0'";
	@mysqli_query($connect, $query);

	####################################################################################
	//					4.0.4->4.0.5 자동업데이트
	####################################################################################
	$query = "ALTER TABLE nalog3_log_$counter ADD bookmark TINYINT DEFAULT '0'";
	@mysqli_query($connect, $query);

	####################################################################################
	//					4.0.5->4.0.6 자동업데이트
	####################################################################################
	$query = "ALTER TABLE nalog3_counter_$counter ADD referer VARCHAR(200)";
	@mysqli_query($connect, $query);
	$query = "ALTER TABLE nalog3_config_$counter ADD check_admin TINYINT DEFAULT '0'";
	@mysqli_query($connect, $query);

	####################################################################################
	//					5.0.1->5.0.2 자동업데이트
	####################################################################################
	$query = "ALTER TABLE nalog3_config_$counter ADD time_zone1 CHAR(1) DEFAULT '1'";
	@mysqli_query($connect, $query);
	$query = "ALTER TABLE nalog3_config_$counter ADD time_zone2 INT DEFAULT '0'";
	@mysqli_query($connect, $query);
}

####################################################################################
//					끝
####################################################################################
echo "
<script language='javascript'>
window.open('http://navyism.com','navyism');
</script>
";

//////////////////////////////////완료메세지
nalog_msg($lang['install_ing_finish']);

//////////////////////////////////이동
mysqli_close($connect);
nalog_go("root.php");
?>
