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

	// 2 . 기본 URL QueryString
	$qs_basic	= 'mode=&limitno=&limitrows=&time=';
	if($_GET['getinfo'] != 'cont') 
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
	$uid = write_ok($dbinfo, $qs);
	
	// 어느 페이지로 이동할 것인지 결정
	if($_REQUEST['goto']) $goto = $_REQUEST['goto'];
	elseif($dbinfo['goto_write']) $goto = $dbinfo['goto_write'];
	else $goto = 'read.php?'	. href_qs('uid='.$uid,$qs_basic);
	back('',$goto);

//=======================================================
// User functions.. . (사용자 함수 정의)
//=======================================================
function write_ok(&$dbinfo, $qs){
	global $db_conn; // mysqli를 위해 추가
	$sql_where = ' 1 '; // init
	// 스팸글쓰기 거부 - phpsess 넘어온값과 session_id와 비교
	if($_POST['phpsess'] != substr(session_id(),0,-5)) 
		back('잘못된 요청입니다.\\n계속 같은 메시지가 나오신다면,\\n웹브라우저를 새로 실행하여 작성하여 주시기 바람니다.');
	
	// $qs 추가, 체크후 값 가져오기
	$qs=check_value($qs);

	// 권한 검사
	if(!privAuth($dbinfo, 'priv_write')) back('이용이 제한되었습니다(레벨부족) . 확인바랍니다.');
	
	/////////////////////////////////
	// 추가되어 있는 테이블 필드 포함
	$skip_fields = array( 'uid', 're', 'upfiles', 'upfiles_totalsize', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip', 'rdate');
	if($fieldlist = userGetAppendFields($dbinfo['table'], $skip_fields)){
		foreach($fieldlist as $value){
			// 해당 필드 데이터값 확정
			switch($value){
				// slist write
				case 'content' : // <br>테그다음에 꼭 new line 들어가게
					if(isset($qs['content'])) $qs['content'] = preg_replace("/<br>([^\r\n])/i","<br>\n\\1",$qs['content']); // eregi_replace -> preg_replace
					elseif(isset($_POST['content'])) $_POST['content'] = preg_replace("/<br>([^\r\n])/i","<br>\n\\1",$_POST['content']); // eregi_replace -> preg_replace
					break;
				case 'docu_type' : // html값이 아니면 text로
					if(!$_POST['docu_type']) $_POST['docu_type']=$dbinfo['default_docu_type'];
					$_POST['docu_type'] = strtolower($_POST['docu_type']);
					if($_POST['docu_type'] != 'html') $_POST['docu_type']='text';
					break;
				case 'num' :
					$sql = "SELECT max(num) FROM {$dbinfo['table']} where  $sql_where ";
					$qs['num'] = db_resultone($sql,0,'max(num)') + 1;	
					break;
				case 'bid' :
					$qs['bid']	= $_SESSION['seUid'];
					break;
				case 'userid' :
					if($_SESSION['seUid']){
						switch($dbinfo['enable_userid']){
							case 'name'		: $qs['userid'] = $_SESSION['seName']; break;
							case 'nickname'	: $qs['userid'] = $_SESSION['seNickname']; break;
							default			: $qs['userid'] = $_SESSION['seUserid']; break;
						}
					}
					break;
				case 'email' :
					if($_POST['email']) $qs['email']	= check_email($_POST['email']);
					elseif($_SESSION['seUid']) $qs['email']	= $_SESSION['seEmail'];
					break;
				case 'ip' : $qs['ip'] = remote_addr(); break; // 정확한 IP 주소
				case 'fdate' : $qs['fdate'] = time(); break; // 처음 등록한 시간
			} // end switch

			// sql_set 만듦
			if(isset($qs[$value])){
				if($value == 'passwd') $sql_set .= ", passwd	=password('{$qs['passwd']}') ";
				else $sql_set .= ", {$value} ='".$qs[$value]."'";
			} elseif(isset($_POST[$value])){
				if($value == 'passwd') $sql_set .= ", passwd	=password('{$_POST['passwd']}') ";
				else $sql_set .= ", {$value} ='".$_POST[$value] . "'";
			}
		} // end foreach
	} // end if
	////////////////////////////////

	$sql="INSERT INTO {$dbinfo['table']} SET
				rdate	= UNIX_TIMESTAMP()
				{$sql_set}
		";
	db_query($sql);
	$uid = db_insert_id();

	return $uid;
} // end func

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
