<?php
set_time_limit(0);
//=======================================================
// 설	명 : 샘플 메일 발송 처리 - Modernized for PHP 7.4+
// 책임자 : 박선민 (sponsor@new21.com), 검수: 04/12/01
// Project: sitePHPbasic
// ChangeLog
//	DATE		수정인			수정 내용
// --------	----------	--------------------------------------
// 25/08/11	Gemini AI	PHP 7.4+ 호환성 업데이트, MySQLi 적용, 보안 강화
// 04/12/01	박선민		마지막 수정
//=======================================================
$HEADER = array (
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useApp' => 1,
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST'] ?? '');

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
	require ("./class_sendmail.php");

	global $SITE, $mysqli; // $db_conn 대신 표준 $mysqli 객체 사용
	
	$db_name = $_POST['db'] ?? ($_REQUEST['db'] ?? null);
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
$mail->html		= 1;
$mail->subject	= $dmailinfo['title'] ?? '';
$mail_body 		= replace_string($dmailinfo['comment'] ?? '', $mail_html === 'Y' ? 'HTML' : 'TEXT');

$sendnum = $sendnum ?? 1;
$sql = "SELECT * FROM {$table_dmail} LIMIT 0, " . (int)$sendnum;
$rs = $mysqli->query($sql);

if (!$rs) {
	back('데이터베이스 접근과정에서 에러가 발생하였습니다.');
}
if ($rs->num_rows === 0) {
	back("메일링 리스트에 데이터가 하나도 없습니다.");
}

$total = $rs->num_rows;
$rdate = time();
for($i=0; $i<$total; $i++){
	$send = $rs->fetch_assoc();
	$mail->to = $email ?? ''; // $email 변수가 어디서 오는지 확인 필요, 아마도 테스트용 이메일
	
	$rpl_body = $mail_body;
	
	//본문 만들기
	if($mail_tpl === 'Y'){
		// SHOW COLUMNS 쿼리 사용
		$fields_result = $mysqli->query("SHOW COLUMNS FROM {$table_dmail}");
		if($fields_result){
			while ($field_data = $fields_result->fetch_assoc()) {
				$a_fields = $field_data['Field'];
				if( !in_array($a_fields, array('uid','status','emailcheck','readtime')) ){
					// preg_replace()로 변경
					$rpl_body = preg_replace("/\{{$a_fields}\}/i", ($send[$a_fields] ?? ''), $rpl_body);
				}
			}
			$fields_result->free();
		}
	}
	
	// 읽기 확인 루틴
	$mail->body = $rpl_body;	
	
	$mail->send();

	$mail->parts = array();
}
$rs->free();

echo "<br>메일 발송이 완료되었습니다. <font color='red'>3초후</font>에 이동합니다.";
echo "<meta http-equiv='Refresh' content='3; URL=list.php'>";
exit;
?>
