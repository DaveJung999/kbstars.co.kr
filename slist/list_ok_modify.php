<?php
//=======================================================
// 설	명 : 심플리스트 처리(ok.php)
// 책임자 : 박선민 (), 검수: 05/11/20
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 05/11/20 박선민 마지막 수정
// 25/08/11 Gemini	PHP 7 마이그레이션
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
	$uid_req = $_REQUEST['uid'] ?? 0;

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

	// 넘어온값 기본 처리
	$qs=array(
				//'title' =>  'post,trim,notnull='	. urlencode('제목을 입력하시기 바랍니다.'),
		);

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// mode값에 따른 함수 호출
switch($_REQUEST['mode'] ?? ''){
	case 'write':
		$uid = write_ok($dbinfo, $qs);
		
		// 어느 페이지로 이동할 것인지 결정
		$goto = $goto_req ?: ($dbinfo['goto_write'] ?: 'read.php?' . href_qs('uid='.$uid,$qs_basic));
		back('',$goto);
		break;
	case 'modify':
		modify_ok($dbinfo, $qs);

		// 어느 페이지로 이동할 것인지 결정
		$goto = $goto_req ?: ($dbinfo['goto_modify'] ?: 'read.php?' . href_qs('uid='.$uid_req,$qs_basic));
		back('',$goto);
		break;
	case 'delete':
		delete_ok($dbinfo);
		
		// 어느 페이지로 이동할 것인지 결정
		$goto = $goto_req ?: ($dbinfo['goto_delete'] ?: 'list.php?'.href_qs('uid=',$qs_basic));
		back('',$goto);
		break;
	default :
		back('잘못된 요청입니다.');
} // end switch

//=======================================================
// User functions.. . (사용자 함수 정의)
//=======================================================
function modify_ok(&$dbinfo,$qs){
	$sql_where = ' 1 '; // init
	
	// $qs 추가, 체크후 값 가져오기
	$qs['uid']	= 'post,trim,notnull='	. urlencode('고유번호가 넘어오지 않았습니다');
	$qs=check_value($qs);

	// 해당 게시물 읽어오기
	$uid_safe = db_escape($qs['uid']);
	$passwd_safe = db_escape($_POST['passwd'] ?? '');
	$sql = "SELECT *,password('{$passwd_safe}') as pass FROM `{$dbinfo['table']}` WHERE uid='{$uid_safe}' AND $sql_where LIMIT 1";
	$list = db_arrayone($sql);
	if(!$list) back('수정할 게시물이 없습니다 . 확인 바랍니다.');
	
	// 수정 권한 체크
	if(!privAuth($dbinfo,'priv_modify') ){
		if(($list['bid'] ?? 0) > 0){
			if( ($list['bid'] != ($_SESSION['seUid'] ?? '')) || 'nobid' == substr($dbinfo['priv_modify'],0,5) )
				back('수정하실 권한이 없습니다.');
		} else {
			if( ($list['passwd'] ?? '') != ($list['pass'] ?? '')) back('정확한 비밀번호를 입력하여 주십시오');
		}
	} // end if

	/////////////////////////////////
	// 추가되어 있는 테이블 필드 포함
	$set_parts = [];
	// bid, nume, re, passwd, type는 수정 불가
	$skip_fields = array( 'bid', 'num', 're', 'passwd', 'type',
					'uid', 'upfiles', 'upfiles_totalsize', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip', 'rdate', 'fdate');
	if($fieldlist = userGetAppendFields($dbinfo['table'], $skip_fields)){
		foreach($fieldlist as $value){
			// 해당 필드 데이터값 확정
			switch($value){
				case 'content' : // <br>태그 다음에 꼭 new line 들어가게
					if(isset($qs['content'])) $qs['content'] = preg_replace("/<br>([^\r\n])/i","<br>\n$1",$qs['content']);
					elseif(isset($_POST['content'])) $_POST['content'] = preg_replace("/<br>([^\r\n])/i","<br>\n$1",$_POST['content']);
					break;				
				case 'docu_type' : // html값이 아니면 text로
					$_POST['docu_type'] = strtolower($_POST['docu_type'] ?? ($dbinfo['default_docu_type'] ?? ''));
					if($_POST['docu_type'] != 'html') $_POST['docu_type']='text';
					break;
				case 'userid' :
					if(($list['bid'] ?? '') == ($_SESSION['seUid'] ?? '')) { // 관리자권한으로 수정했으면 변경불가
						switch($dbinfo['enable_userid'] ?? 'userid'){
							case 'name'		: $qs['userid'] = $_SESSION['seName']; break;
							case 'nickname'	: $qs['userid'] = $_SESSION['seNickname']; break;
							default			: $qs['userid'] = $_SESSION['seUserid']; break;
						}
					}
					break;
				case 'email' :
					if(isset($_POST['email'])) $qs['email']	= check_email($_POST['email']);
					elseif(($list['bid'] ?? '') == ($_SESSION['seUid'] ?? '')) // 관리자권한으로 수정했으면 변경불가
						$qs['email']	= $_SESSION['seEmail'];
					break;
				case 'ip' :	$qs['ip'] = remote_addr(); break; // 정확한 IP 주소
			} // end switch

			// sql_set 만듦
			if(isset($qs[$value])) {
				$safe_value = db_escape($qs[$value]);
				$set_parts[] = "`{$value}` = '{$safe_value}'";
			}
			elseif(isset($_POST[$value])) {
				$safe_value = db_escape($_POST[$value]);
				$set_parts[] = "`{$value}` = '{$safe_value}'";
			}
		} // end foreach
	} // end if
	////////////////////////////////

	$sql_set = implode(', ', $set_parts);
	$sql = "UPDATE `{$dbinfo['table']}` SET `rdate`=UNIX_TIMESTAMP()" . ($sql_set ? ", " . $sql_set : "") . " WHERE uid='{$uid_safe}'";
	db_query($sql);

	return true;
} // end func.

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
