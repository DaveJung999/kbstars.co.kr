<?php
set_time_limit(0);
//=======================================================
// 설	명 : 메일 리스트 편집 - Modernized for PHP 7.4+
// 책임자 : 박선민 (sponsor@new21.com), 검수: 03/08/30
// Project: sitePHPbasic
// ChangeLog
//	DATE		수정인			수정 내용
// --------	----------	--------------------------------------
// 25/08/11	Gemini AI	PHP 7.4+ 호환성 업데이트, MySQLi 적용, 보안 강화
// 03/08/30	박선민		마지막 수정
//=======================================================
$HEADER = array (
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useApp' => 1,
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php"); // 'sin' -> 'sinc' 오타 수정
//page_security("", $_SERVER['HTTP_HOST'] ?? '');

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================

/*
// 03/06/11
	// status
	NULL : 발송 가능
	FAIL : 발송 실패
	SEND : 발송됨

	// emailcheck
	9 : 발송 준비
	41 : 메일 가짜
	42 : 메일 유저 없음
	43 : 도메인미등록
	44 : 메일 서버 거부
	45 : 접속실패
*/
require ("./class_sendmail.php");

global $SITE, $mysqli; // $db_conn 대신 표준 $mysqli 객체 사용

$db_name = $_GET['db'] ?? null;
if (!$db_name) {
	back("db값이 넘어오지 않았습니다");
}

$table_dmailinfo = ($SITE['th'] ?? '') . "dmailinfo";
$table_dmail	 = ($SITE['th'] ?? '') . "dmail_" . $db_name;

// SQL 인젝션 방지를 위해 Prepared Statement 사용
$stmt = $mysqli->prepare("SELECT * FROM {$table_dmailinfo} WHERE db=?");
$stmt->bind_param("s", $db_name);
$stmt->execute();
$result = $stmt->get_result();
$dmailinfo = $result->fetch_assoc();
$stmt->close();

if (!$dmailinfo) {
	back("메일 정보를 찾을 수 없습니다.");
}

//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
$mail = new mime_mail;
$mail->from		= $dmailinfo['s_mail'] ?? '';
$mail->name		= $dmailinfo['s_name'] ?? '';
$mail_tpl		= $dmailinfo['tpl_yesno'] ?? 'N';
$mail_html		= $dmailinfo['h_yesno'] ?? 'N';
$mail->html	= 1;
$mail->subject	= $dmailinfo['title'] ?? '';
$mail_body 		= replace_string($dmailinfo['comment'] ?? '', $mail_html === 'Y' ? 'HTML' : 'TEXT');

##	전체 메일 발송
$sql_where = " status is null and emailcheck=0 ";
$sql = "SELECT count(*) as count FROM {$table_dmail} WHERE  $sql_where ";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
$total_remaining = $row['count'] ?? 0;
$result->free();

echo "$total_remaining 명 남았습니다.\n<br>";
echo str_pad(" ", 256);
flush();

$sql = "SELECT * FROM {$table_dmail} WHERE $sql_where LIMIT 0, 500 ";
$rs_dmail = $mysqli->query($sql);

if (!$rs_dmail) {
	back('데이터베이스 조회에 문제가 발생하였습니다.');
}

$total = $rs_dmail->num_rows;
$rdate = time();

// UPDATE를 위한 Prepared Statement 준비
$update_stmt = $mysqli->prepare("UPDATE {$table_dmail} SET status=? WHERE uid=? LIMIT 1");

for($i=0; $i<$total; $i++){
	$send = $rs_dmail->fetch_assoc();
	$mail->to = $send['email'] ?? '';
	
	$rpl_body = $mail_body;
	//본문 만들기
	if($mail_tpl === 'Y'){
		// SHOW COLUMNS 쿼리 사용
		$fields_result = $mysqli->query("SHOW COLUMNS FROM {$table_dmail}");
		if($fields_result){
			while ($field_data = $fields_result->fetch_assoc()) {
				$a_fields = $field_data['Field'];
				if( !in_array($a_fields,array('uid','status','emailcheck','readtime')) ){
					// preg_replace()로 변경
					$rpl_body = preg_replace("/\{{$a_fields}\}/i", ($send[$a_fields] ?? ''), $rpl_body);
				}
			}
			$fields_result->free();
		}
	}

	// 읽기 확인 루틴
	$check_read = "<img src='http://" . ($_SERVER['HTTP_HOST'] ?? '') . "/sjoin/dmail/check.php?db=". urlencode($db_name) ."&uid=". urlencode($send['uid'] ?? '') ."&email=". urlencode($send['email'] ?? '') ."' width=0 height=0 border=0> ";
	$mail->body = $check_read . $rpl_body;
	
	if($mail->send()) {
		$status = 'SEND';
		$update_stmt->bind_param("si", $status, $send['uid']);
		$update_stmt->execute();
	} else {
		$status = 'FAIL';
		$update_stmt->bind_param("si", $status, $send['uid']);
		$update_stmt->execute();
		sleep(2); // db server 부하를 줄이기 위해
	}

	if($i%100 == 0){
		echo str_pad("\n<br>",256);
		flush();
	}
	$mail->parts = array();

	echo ".";
	//echo ($send['userid'] ?? '') . "$i 번째 : (" . ($send['email'] ?? '') . ")님에게 메일을 발송하였습니다.<br>";
}
$rs_dmail->free();
$update_stmt->close();

if($total === 0){
	echo "메일 발송이 완료되었습니다. <font color='red'>3초후</font>에 이동합니다.";
	echo "<meta http-equiv='Refresh' content='3; URL=list.php'>";
	exit;
}
else {
	echo "<meta http-equiv='Refresh' content='0; URL=". ($_SERVER['PHP_SELF'] ?? '') ."?db=". urlencode($db_name) ."'>";
}
?>
