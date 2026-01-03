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

$new_board = $counter;

####################################################################################
//					관리자 체크
####################################################################################
nalog_admin_check($_SERVER['HTTP_REFERER']);

####################################################################################
//					접속기록 삭제
####################################################################################
if ($mode === "del_counter") {
	nalog_drop("nalog3_counter_" . $counter);
	include "nalog_schema.php";
}

####################################################################################
//					로그기록 삭제
####################################################################################
if ($mode === "del_log") {
	nalog_drop("nalog3_log_" . $counter);
	nalog_drop("nalog3_dlog_" . $counter);
	include "nalog_schema.php";
}

####################################################################################
//					통계기록 삭제
####################################################################################
if ($mode === "del_data") {
	$query = "DELETE FROM nalog3_data WHERE counter=?";
	$stmt = $connect->prepare($query);
	$stmt->bind_param("s", $counter);
	$stmt->execute();
	$stmt->close();
}

####################################################################################
//					os 기록 삭제
####################################################################################
if ($mode === "del_os") {
	$query = "DELETE FROM nalog3_os WHERE counter=?";
	$stmt = $connect->prepare($query);
	$stmt->bind_param("s", $counter);
	$stmt->execute();
	$stmt->close();
}

####################################################################################
//					개별 로그 삭제1
####################################################################################
if ($mode === "del_log_1") {
	$query = "DELETE FROM nalog3_log_" . $counter . " WHERE no=?";
	$stmt = $connect->prepare($query);
	$stmt->bind_param("i", $no);
	$stmt->execute();
	$stmt->close();

	$connect->close();
	nalog_go($_SERVER['HTTP_REFERER']);
}

####################################################################################
//					개별 로그 삭제2
####################################################################################
if ($mode === "del_log_2") {
	$query = "DELETE FROM nalog3_dlog_" . $counter . " WHERE no=?";
	$stmt = $connect->prepare($query);
	$stmt->bind_param("i", $no);
	$stmt->execute();
	$stmt->close();

	$connect->close();
	nalog_go($_SERVER['HTTP_REFERER']);
}

####################################################################################
//					개별 로그 삭제3
####################################################################################
if ($mode === "del_log_3") {
	foreach ($chk as $id) {
		if (!$id) continue;
		$query = "DELETE FROM nalog3_log_" . $counter . " WHERE no=?";
		$stmt = $connect->prepare($query);
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->close();
	}
	$connect->close();
	nalog_go($_SERVER['HTTP_REFERER']);
}

####################################################################################
//					개별 로그 삭제4
####################################################################################
if ($mode === "del_log_4") {
	foreach ($chk as $id) {
		if (!$id) continue;
		$query = "DELETE FROM nalog3_dlog_" . $counter . " WHERE no=?";
		$stmt = $connect->prepare($query);
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->close();
	}
	$connect->close();
	nalog_go($_SERVER['HTTP_REFERER']);
}

####################################################################################
//					종료 및 이동
####################################################################################
$connect->close();
nalog_go("admin_counter.php?counter=$counter&mode=10");
?>
