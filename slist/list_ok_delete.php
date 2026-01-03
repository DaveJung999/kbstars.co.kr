<?php
//=======================================================
// 설	명 : 심플리스트 처리(ok.php)
// 책임자 : 박선민 (), 검수: 05/11/20
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 05/11/20 박선민 마지막 수정
//=======================================================
$HEADER = array(
	'priv' => '', // 인증유무 (비회원,회원,운영자,서버관리자)
	'usedb2' => 1, // DB 커넥션 사용
	'useSkin' => 1, // 템플릿 사용
	'useCheck' => 1, // check_value()
	'useApp' => 1 // remote_addr()
);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	// 1 . 넘어온값 체크
	$getinfo_get = $_GET['getinfo'] ?? '';
	$goto_req = $_REQUEST['goto'] ?? '';

	// 2 . 기본 URL QueryString
	$qs_basic	= 'mode=&limitno=&limitrows=&time=';

	// 기본 URL QueryString
	$qs_basic = "db=" . ($_REQUEST['db'] ?? ($table ?? '')) .			//table 이름
				"&mode=" . ($_REQUEST['mode'] ?? '') .		// mode값은 list.php에서는 당연히 빈값
				"&cateuid=" . ($_REQUEST['cateuid'] ?? '') .		//cateuid
				"&team=" . ($_REQUEST['team'] ?? '') .				// 페이지당 표시될 게시물 수
				"&pern=" . ($_REQUEST['pern'] ?? '') .				// 페이지당 표시될 게시물 수
				"&sc_column=" . ($_REQUEST['sc_column'] ?? '') .	//search column
				"&sc_string=" . urlencode(stripslashes($_REQUEST['sc_string'] ?? '')) . //search string
				"&team=" . ($_REQUEST['team'] ?? '').
				"&html_headtpl=" . ($_REQUEST['html_headtpl'] ?? '').
				"&pid=" . ($_REQUEST['pid'] ?? '').
				"&pname=" . ($_REQUEST['pname'] ?? '').
				"&goto=" . ($_REQUEST['goto'] ?? '').
				"&page=" . ($_REQUEST['page'] ?? '');

	if($getinfo_get != 'cont')
		$qs_basic .= '&pern=&row_pern=&page_pern=&html_type=&html_skin=&skin=';
	$qs_basic	= href_qs($qs_basic); // 해당값 초기화

	// 3 . $dbinfo 가져오기
	include_once('config.php');

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
	delete_ok($dbinfo);
	
	// 어느 페이지로 이동할 것인지 결정
	$goto = $goto_req ?: ($dbinfo['goto_delete'] ?: 'list.php?'.href_qs('uid=',$qs_basic));
	back('',$goto);

//=======================================================
// User functions.. . (사용자 함수 정의)
//=======================================================
// 삭제
function delete_ok(&$dbinfo){
	global $thisUrl;
	$sql_where = ' 1 ' ;

	// $qs 추가, 체크후 값 가져오기
	$qs=array(
			'uid' =>  'request,trim,notnull='	. urlencode('고유넘버가 넘어오지 않았습니다.'),
			'passwd' =>  'request,trim'
		);
	$qs=check_value($qs);

	// 해당 게시물 읽어오기
	$uid_safe = db_escape($qs['uid']);
	$passwd_safe = db_escape($qs['passwd']);
	$sql = "SELECT *,password('{$passwd_safe}') as pass FROM `{$dbinfo['table']}` WHERE uid='{$uid_safe}' AND $sql_where LIMIT 1";
	
	$list = db_arrayone($sql);
	if(!$list) back('이미 삭제되었거나 잘못된 요청입니다');

	// 삭제 권한 체크
	if(!privAuth($dbinfo,'priv_delete')) {// 게시판 전체 삭제 권한을 가졌다면
		if( 'nobid' == substr($dbinfo['priv_delete'],0,5) )
			back('삭제하실 수 없습니다.');
		elseif(($list['bid'] ?? 0) > 0) { // 회원이면
			if($list['bid'] != ($_SESSION['seUid'] ?? ''))
				back('삭제하실 수 없습니다.');
		} else { // 비회원이면 passwd 검사
			if(($list['passwd'] ?? '') != ($list['pass'] ?? '')){
				if(isset($_SERVER['QUERY_STRING']))
					back('비밀번호를 입력하여 주십시오','delete.php?'.$_SERVER['QUERY_STRING']);
				else back('비밀번호를 정확히 입력하십시오');
			}
		}
	}

	// 삭제
	db_query("DELETE FROM `{$dbinfo['table']}` WHERE uid='{$uid_safe}' AND  $sql_where ");
	return true;
} // end func delete_ok()

//=======================================================
// User functions.. . (사용자 함수 정의)
//=======================================================
/**
 * 추가 입력해야할 필드를 가져옵니다. (Modernized version)
 * @param string $table The table name.
 * @param array $skip_fields Fields to exclude.
 * @return array|false List of additional fields or false on failure.
 */
function userGetAppendFields(string $table, array $skip_fields = [])
{
	if (empty($table)) {
		return false;
	}

	$result = db_query("SHOW COLUMNS FROM {$table}");

	if (!$result) {
		return false;
	}

	$fieldlist = [];
	while($row = db_array($result)) {
		if(!in_array($row['Field'], $skip_fields)){
			$fieldlist[] = $row['Field'];
		}
	}
	db_free($result); 

	return isset($fieldlist) ? $fieldlist : false;
}

?>
