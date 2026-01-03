<?php
//=======================================================
// 설	명 : 설문조사 삽입 예제
// 책임자 : 박선민 (sponsor@new21.com), 검수: 03/08/25
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 03/08/25 박선민 김평수 소스에서 포팅
// 24/05/21 Gemini PHP 7 마이그레이션
//=======================================================
$HEADER=array(
		'priv' => '', // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useApp' => 1
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']);

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	$today = date('Ymd');
	
	if(empty($_REQUEST['val'])){
		back("투표 할 팀을 선택 해 주세요.");
	}
	
	$table_pollinfo = "{$SITE['th']}pollinfo";
	$table_userinfo = "{$SITE['th']}userinfo";

	if( !$list_pollinfo = db_arrayone("SELECT * FROM {$table_pollinfo} WHERE uid='" . db_escape($_REQUEST['uid']) . "' and db ='" . db_escape($_REQUEST['db']) . "'") )
		back("해당 투표가 없습니다 . 감사합니다.");
	
	$table_poll = "{$SITE['th']}poll_" . $list_pollinfo['db'];

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
if(!privAuth($list_pollinfo, 'priv',1)){
/*	if($_SESSION['sePriv']['level']>0)
		back("본 투표는 고유 회원레벨 {$list_pollinfo['member']}이상만 참여가능합니다 . 감사합니다.");
	else {
		//back("본 투표는 로그인 이후에 참여하실 수 있습니다 . 감사합니다.");

		// 로그인하여 설문 처리
		if(strtoupper($_SERVER["REQUEST_METHOD"]) == "POST"){
			$newquery = "";
			foreach ($_POST as $key =>  $value){
				if($key) $newquery.="$key=" . urlencode($value) . "&";
			}
			$_SESSION['seREQUEST_URI'] = $_SERVER['PHP_SELF']."?".$newquery;
		}
		else
			$_SESSION['seREQUEST_URI'] = $_SERVER['REQUEST_URI'];

		//go_url("/sjoin/login.php");
		back("본 투표는 로그인을 하신후 참여가능합니다 . 감사합니다.");
		exit;
	}*/
	back("본 투표는 로그인을 하신후 참여가능합니다 . 감사합니다.");
	exit;
}

$ip_client	=	remote_addr(); // client IP 어드레스
if( ($_SESSION['seUid'] ?? 0) == 0 ){
	if( db_arrayone("SELECT * FROM {$table_poll} WHERE ip='{$ip_client}'") ){
		back("방문객님 현재의 아이피 {$ip_client} 로 \\n\\n 투표를 하신적이 있습니다 . ");
	}
	db_query("INSERT INTO {$table_poll} (val,member,ip,rdate) VALUES('" . db_escape($_REQUEST['val']) . "','','{$ip_client}',UNIX_TIMESTAMP())");
	
	if(($_REQUEST['go_page'] ?? '') == "main"){
		back("투표가 정상적으로 처리 되었습니다 . 투표 감사합니다.","/");
	}
	else{
		back("투표가 정상적으로 처리 되었습니다 . 투표 감사합니다.");
	}
}
else{
	if(db_arrayone("SELECT * FROM {$table_poll} WHERE bid=" . (int)$_SESSION['seUid'] . " and FROM_UNIXTIME(rdate, '%Y%m%d') = '{$today}' ")){
		back("회원님께서는 이미 투표에 응해주셨습니다 . \\n투표는 하루에 한번씩만 가능합니다.\\n\\n감사합니다.");
	}

	// 회원 정보에서 주민번호 가져옮
	$idnum = db_resultone("SELECT idnum FROM {$table_userinfo} WHERE bid='" . (int)$_SESSION['seUid'] . "'",0,"idnum");

	$member_sex = substr($idnum,7,1); // PHP 7에서는 세 번째 파라미터(length)가 음수일 때 동작이 다르므로 1로 명시
	$member_age = substr($idnum,0,2);
	if($member_sex != 1 && $member_sex != 2){
		$sex = 0;
	}
	else{
		$sex = $member_sex;
	}

	## 주민번호가 14자 형식에 맞지 않으면 모든값을 0 으로 한다.
	if(strlen($idnum) < 13){ // 주민등록번호는 '-' 포함 14자 또는 미포함 13자
		$sex = 0;
		$age = 0;
	}

	## 현재 회원의 나이를 DB에 넣는다 . 2000년에 대한 사람과	아닌사람을 구분^^
	if($member_age > 35){
		$member_age = "19".$member_age;
		$age = date('Y') - $member_age;
	}
	elseif($member_age <= 35 ){
		$member_age = "20".$member_age;
		$age = date('Y') - $member_age;
	}
	##################################################################
	# member 0:모두참여	1이상:지정한 레벨 이상의 로그인회원만 참여
	# sex	0:전체	1:남자	2:여자
	# age	0:전체
	##################################################################

	##	설문에 응할 수 있는 연령층에 따른 에러
	##	$list['age'] 가 "0" 이면..모든 연령층이 가능하고 0 이 아닐경우는 다음과 같은 에러루틴이 들어간다.

	if($list_pollinfo['age'] != "0"){
		$age_arr = explode("/",$list_pollinfo['age']);
		if(!($age >= $age_arr[0]) && ($age <= $age_arr[1])){
			back("{$age_arr[0]}세 이상 {$age_arr[1]}세 이하만 가능한 설문조사입니다.");
		}
	}

	## 설문에 응할 수 있는 성별에 따른 에러루틴
	## $list['sex'] 값이 0 이면 모두 가능 1이면 남성 2이면 여성만 가능하다.

	if($list_pollinfo['sex'] != 0){
		if($list_pollinfo['sex'] != $sex){
			if($list_pollinfo['sex'] == 1){
				$sex_name = "남성";
			}
			else{
				$sex_name = "여성";
			}
			back("현재의 투표는 {$sex_name} 만 가능합니다.");
		}
	}

	db_query("INSERT INTO {$table_poll} (userid,bid,val,member,age,sex,ip,rdate) VALUES('" . db_escape($_SESSION['seUserid']) . "','" . (int)$_SESSION['seUid'] . "','" . db_escape($_REQUEST['val']) . "','" . db_escape($_SESSION['sePriv']['level']) . "','{$age}','{$sex}','{$ip_client}',UNIX_TIMESTAMP())");

	#################################################################
	# 설문에 응하고 전 페이지로 이동을 시켜준다.
	#################################################################
	if(($_REQUEST['go_page'] ?? '') == "main"){
		back("투표가 정상적으로 처리 되었습니다.","/");
	}
	else{
		back("투표가 정상적으로 처리 되었습니다.");
	}
} // end if. . else..
?>
