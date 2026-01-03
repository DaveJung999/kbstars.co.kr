<?php
####################################################################################
//					준비
####################################################################################
if (!@include "nalog_connect.php") {
	echo "<script language='javascript'>alert('Please install n@log first :)')</script>
<meta http-equiv='refresh' content='0;url=install.php'>";
	exit;
}
include "lib.php";
if (!@include "nalog_language.php") { nalog_go("install.php"); }
if (!@include "language/$language/language.php") { nalog_go("install.php"); }

####################################################################################
//					체크
####################################################################################
nalog_admin_check($_SERVER['PHP_SELF']);
$set = nalog_config($counter);
if (!$set) { nalog_error($lang['counter_manager_error_not_exist']); }
nalog_chk_num($total, 0, $lang['counter_manager_error_total_is'], "");
nalog_chk_num($cookie_time, 0, $lang['counter_manager_error_cookie_time'], "");
nalog_chk_num($connecting, 0, $lang['counter_manager_error_connect_time'], "");
nalog_chk_num($log_limit, 0, $lang['counter_manager_error_log_limit'], "");

####################################################################################
//					저장
####################################################################################
$value = [
	"skin" => $_POST['skin'] ?? '',
	"cookie" => $_POST['cookie'] ?? '0', // INT 타입일 수 있음
	"cookie_time" => $_POST['cookie_time'] ?? '0', // INT 타입
	"counter_check" => $_POST['counter_check'] ?? '0', // 체크박스: 1 또는 0
	"now_check" => $_POST['now_check'] ?? '0', // 체크박스: 1 또는 0
	"log_check" => $_POST['log_check'] ?? '0', // 체크박스: 1 또는 0
	"skin_check" => $_POST['skin_check'] ?? '0', // 체크박스: 1 또는 0
	"connecting" => $_POST['connecting'] ?? '0', // INT 타입
	"counter_limit" => $_POST['counter_limit'] ?? '0', // INT 타입
	"log_limit" => $_POST['log_limit'] ?? '0', // INT 타입
	"member_id" => $_POST['member_id'] ?? '',
	
	// auth_* 필드는 INT 타입(1 또는 0)입니다.
	"auth_time" => $_POST['auth_time'] ?? '0',
	"auth_day" => $_POST['auth_day'] ?? '0',
	"auth_week" => $_POST['auth_week'] ?? '0',
	"auth_month" => $_POST['auth_month'] ?? '0',
	"auth_year" => $_POST['auth_year'] ?? '0',
	"auth_log" => $_POST['auth_log'] ?? '0',
	"auth_dlog" => $_POST['auth_dlog'] ?? '0',
	"auth_os" => $_POST['auth_os'] ?? '0',
	"auth_member" => $_POST['auth_member'] ?? '0',
	"auth_ip" => $_POST['auth_ip'] ?? '0', // 👈 이 부분을 명확히 '0'으로 수정

	"total" => $_POST['total'] ?? '0', // INT 타입
	"check_admin" => $_POST['check_admin'] ?? '0', // 체크박스: 1 또는 0
	"time_zone1" => $_POST['time_zone1'] ?? '0', // INT 타입
	"time_zone2" => $_POST['time_zone2'] ?? '0' // INT 타입
];

$set_parts = [];
foreach ($value as $key => $val) {
    // 컬럼명 $key를 백틱(`)으로 감싸서 예약어 충돌을 방지합니다.
	$set_parts[] = "`$key`='" . mysqli_real_escape_string($connect, $val) . "'";
}
$set_string = implode(", ", $set_parts);

$query = "UPDATE nalog3_config_$counter SET $set_string WHERE no=1";
$is_ok = mysqli_query($connect, $query);



if (!$is_ok) {
	nalog_msg("upgrade to 5.0.2");
	nalog_go("upgrader.php");
}

####################################################################################
//					이동
####################################################################################
mysqli_close($connect);
nalog_go("admin_counter.php?counter=$counter&mode=$mode");
?>
