<?php
//=======================================================
// 설	명 : 심플리스트-추가/수정 - Modernized for PHP 7.4+
// 책임자 : 박선민 (sponsor@new21.com), 검수: 05/11/20
// Project: sitePHPbasic
// ChangeLog
//	DATE		수정인			수정 내용
// --------	----------	--------------------------------------
// 25/08/11	Gemini AI	PHP 7.4+ 호환성 업데이트, MySQLi 적용, 보안 강화
// 05/11/20	박선민		마지막 수정
//=======================================================
$HEADER = array(
	'private' => '1', // 브라우저 캐쉬
	'priv' => '', // 인증유무 (비회원,회원,운영자,서버관리자)
	'usedb2' => 1, // DB 커넥션 사용
	'useSkin' => 1, // 템플릿 사용
	'useBoard2' => 1, // 보드관련 함수 포함
);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	// 1 . 넘어온값 체크
	$mode = $_GET['mode'] ?? 'write';

	// 2 . $dbinfo 가져오기
	include_once('config.php');
	global $SITE, $mysqli, $thisUrl;

	// 3 . 기본 URL QueryString
	$qs_basic	= 'mode=&limitno=&limitrows=&time=';
	if(($_GET['getinfo'] ?? '') != 'cont') {
		$qs_basic .= '&pern=&row_pern=&page_pern=&html_type=&html_skin=&skin=';
	}
	// - uid필드를 제외하고 테이블 필드 이름과 같은 get값은 삭제
	$skip_fields = array('uid');
	if($fieldlist = userGetAppendFields($dbinfo['table'], $skip_fields)) {
		foreach($fieldlist as $value) $qs_basic .= "&{$value}=";
	}		
	$qs_basic		= href_qs($qs_basic); // 해당값 초기화
	
	// 4 . SQL문 where절
	$sql_where = ' 1 ';
	
	$list = [];
	// 5 . 수정모드라면
	if($mode === 'modify'){
		$uid = $_GET['uid'] ?? 0;
		$stmt = $mysqli->prepare("select * from {$dbinfo['table']} where uid=? and $sql_where LIMIT 1");
		$stmt->bind_param("i", $uid);
		$stmt->execute();
		$result = $stmt->get_result();
		$list = $result->fetch_assoc();
		$stmt->close();

		if (!$list) {
			back('해당 데이터가 없습니다.');
		}

		// 수정 권한 체크
		if(!privAuth($dbinfo,'priv_modify',(int)($list['bid'] ?? 0)) ){
			if(($list['bid'] ?? 0) > 0 and (($list['bid'] != ($_SESSION['seUid'] ?? null)) or 'nobid' === substr($dbinfo['priv_modify'],0,5)) )
				back('수정하실 권한이 없습니다.');
		} // end if
			
		/////////////////////////////////
		// 추가되어 있는 테이블 필드 포함
		$skip_fields = array('uid', 'bid', 'passwd', 'db', 'cateuid', 'num', 're', 'upfiles', 'upfiles_totalsize', 'docu_type', 'type', 'priv', 'ip', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip' ,	'rdate');
		if($fieldlist = userGetAppendFields($dbinfo['table'], $skip_fields)){
			foreach($fieldlist as $value){
				if (isset($list[$value])) {
					$list[$value]	= htmlspecialchars($list[$value],ENT_QUOTES, 'UTF-8');
				}
			}
		}
		////////////////////////////////
		
		$form_default = " method='post' action='{$thisUrl}ok.php' ENCTYPE='multipart/form-data'>";
		$form_default .= href_qs('mode=modify&rdate='.($_GET['rdate'] ?? ''),$qs_basic,1);
		$form_default = substr($form_default,0,-1);
	} else { // write 모드라면
		// 인증 체크
		if(!privAuth($dbinfo, 'priv_write',1))
			back('글을 작성하실 권한이 없습니다.');
		
		// form_default
		$form_default = " method='post' action='{$thisUrl}ok.php' ENCTYPE='multipart/form-data'>";
		$form_default .= href_qs('mode=write&phpsess='.substr(session_id(),0,-5),$qs_basic,1);
		$form_default = substr($form_default,0,-1);
			
		// 기본값 설정
		// - table에서 기본 필드값 가져오기
		$list = userGetDefaultFromTable($dbinfo['table']);
		$list['docu_type'] = $dbinfo['default_docu_type'] ?? '';
		if(isset($dbinfo['default_title'])) $list['title'] = $dbinfo['default_title'];
		if(isset($dbinfo['default_content'])) $list['content'] = $dbinfo['default_content'];
	}
	
	// 6 . 공통 할당
	$list['docu_type_checked'] = (strtolower($list['docu_type'] ?? '') == 'html') ? ' checked ' : '';
	if( !($mode === 'modify' and ($list['bid'] ?? null) != ($_SESSION['seUid'] ?? null)) ){
		switch($dbinfo['enable_userid'] ?? ''){
			case 'name'		: {$list['userid']} = $_SESSION['seName'] ?? ''; break;
			case 'nickname'	: {$list['userid']} = $_SESSION['seNickname'] ?? ''; break;
			default			: {$list['userid']} = $_SESSION['seUserid'] ?? ''; break;
		}
		$list['email']	= ($_SESSION['seEmail'] ?? null) ? $_SESSION['seEmail'] : ($list['email'] ?? '');
	}
		
	// URL Link...
	$href['list']	= ($thisUrl ?? '') .'list.php?'.$qs_basic;
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// 템플릿 기반 웹 페이지 제작
$skinfile=basename(__FILE__,'.php').'.html';
if( !is_file('skin/'.($dbinfo['skin'] ?? 'basic').'/'.$skinfile) ) $dbinfo['skin']='basic';
$tpl = new phemplate('skin/'.($dbinfo['skin'] ?? 'basic')); // 템플릿 시작
$tpl->set_file('html',$skinfile,TPL_BLOCK);
// 템플릿 기본 할당
$tpl->tie_var('get'				,$_GET);	// get값으로 넘어온것들
$tpl->tie_var('dbinfo'			,$dbinfo);	// dbinfo 정보 변수
$tpl->tie_var('href'			,$href);	// 게시판 각종 링크
$tpl->set_var('session.seUserid'	,$_SESSION['seUserid'] ?? '');	// 로그인 userid
$tpl->set_var('session.seName'		,$_SESSION['seName'] ?? '');		// 로그인 이름
$tpl->set_var('session.seNickname'	,$_SESSION['seNickname'] ?? '');	// 로그인 별명
$tpl->set_var('mode_'.$mode,true);		// mode_write, mode_modify 값있게
$tpl->set_var('form_default'	,$form_default);

// 템플릿 마무리 할당
userEnumSetFieldsToOptionTag($dbinfo['table'],$list); // $list['필드_option']에 enum,set필드 <option>..</option>생성
$tpl->tie_var('list'				,$list);

// 블럭 : 권한 입력 부분
if(($dbinfo['enable_priv'] ?? 'N') === 'Y')	$tpl->process('priv','priv');

// 마무리
$tpl->echoHtml($dbinfo, $SITE);

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

/**
 * enum,set필드라면, $list['필드_option'] 만들어줌 (Modernized version)
 * @param string $table
 * @param array &$list
 */
function	userEnumSetFieldsToOptionTag(string $table, array &$list) {
	global $mysqli;
	$safe_table = $mysqli->real_escape_string($table);
	$table_def = $mysqli->query("SHOW FIELDS FROM {$safe_table}");
	if(!$table_def) return;

	while($row_table_def = $table_def->fetch_assoc()){
		$field = $row_table_def['Field'];

		$row_table_def['True_Type'] = preg_replace('/\(.*/', '', $row_table_def['Type']);
		if($row_table_def['True_Type'] == 'enum')
			$aFieldValue = array($list[$field] ?? null);
		elseif($row_table_def['True_Type'] == 'set')
			$aFieldValue = explode(',', $list[$field] ?? '');
		else continue;
		
		$return	= '';

		// The value column (depends on type)
		// ----------------
		$enum		= str_replace($row_table_def['True_Type'].'(', '', $row_table_def['Type']);
		$enum		= preg_replace('/\)$/', '', $enum);
		$enum		= explode('\',\'', substr($enum, 1, -1));

		// show dropdown or radio depend on length
		foreach ($enum as $enum_atom){
			// Removes automatic MySQL escape format
			$enum_atom = str_replace('\'\'', '\'', str_replace('\\\\', '\\', $enum_atom));
			$return .= '<option value="'	. htmlspecialchars($enum_atom,ENT_QUOTES) . '"';
			if ((isset($list[$field]) && in_array($enum_atom,$aFieldValue))
				or (!isset($list[$field]) && ($row_table_def['Null'] ?? 'YES') != 'YES'
								 && $enum_atom == $row_table_def['Default'])){
				$return .=	' selected="selected"';
			}
			$return .=	'>'	. htmlspecialchars($enum_atom) . "</option>\n";
		} // end for
		
		$list[$field.'_option'] = $return;
	} // end for
	$table_def->free();
} // end function

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
