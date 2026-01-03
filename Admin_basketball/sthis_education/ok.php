<?php
//=======================================================
// 설	명 : 게시판 처리(ok.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 04/08/03
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 04/08/03 박선민 마지막 수정
// 24/05/18 Gemini	PHP 7 마이그레이션 및 db_* 함수 적용
//=======================================================
// 앞으로 : 게시물 삭제시 메모로 삭제되도록...
$HEADER=array(
		'priv' =>	"운영자,뉴스관리자", // 인증유무 (비회원,회원,운영자,서버관리자)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useCheck' => 1, // check_email()
		'useApp' => 1, // file_upload()
		'useClassSendmail' =>	1, // mime_mail
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
page_security("", $_SERVER['HTTP_HOST']);

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
$thisPath		= dirname(__FILE__);
$thisUrl	= "/Admin_basketball/sthis_education"; // 마지막 "/"이 빠져야함
include_once("./dbinfo.php"); // $dbinfo, $table 값 정의

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

// 기본 URL QueryString
$table_dbinfo	= $dbinfo['table'];

if($_REQUEST['mode'] == "delete") $_REQUEST['puid'] = $_GET['p_uid'];
if(!$_REQUEST['puid']) back("고유번호가 없습니다.");

// 기본 URL QueryString
$qs_basic = "db=" . ($_REQUEST['db'] ?? $table) .			//table 이름
			"&mode=" . ($_REQUEST['mode'] ?? '') .		// mode값은 list.php에서는 당연히 빈값
			"&cateuid=" . ($_REQUEST['cateuid'] ?? '') .		//cateuid
			"&team=" . ($_REQUEST['team'] ?? '') .				// 페이지당 표시될 게시물 수
			"&pern=" . ($_REQUEST['pern'] ?? '') .				// 페이지당 표시될 게시물 수
			"&sc_column=" . ($_REQUEST['sc_column'] ?? '') .	//search column
			"&sc_string=" . urlencode(stripslashes(isset($sc_string) ? $sc_string : '')) . //search string
			"&team=" . ($_REQUEST['team'] ?? '').
			"&html_headtpl=" . (isset($html_headtpl) ? $html_headtpl : '').
			"&pid=" . ($_REQUEST['pid'] ?? '').
			"&pname=" . ($_REQUEST['pname'] ?? '').
			"&page=" . ($_REQUEST['page'] ?? '');
			
if(($_REQUEST['getinfo'] ?? '') == "cont")
	$qs_basic .= "&html_headpattern=" . ($_REQUEST['html_headpattern'] ?? '') . "&html_headtpl=" . ($_REQUEST['html_headtpl'] ?? '') . "&skin=" . ($_REQUEST['skin'] ?? '');
$qs_basic		= href_qs($qs_basic); // 해당값 초기화

$table_logon	= ($SITE['th'] ?? '') . "logon";
	
// 공통적으로 사용할 $qs
$qs=array(
			'title' =>	"post,trim,notnull=" . urlencode("제목을 입력하시기 바랍니다."),
			'content' =>	"post,trim"
	);
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// mode값에 따른 함수 호출
switch($_REQUEST['mode']){
	case 'write':
		$uid = write_ok($dbinfo, $qs);
			back_close('',"{$thisUrl}/list.php?p_uid={$_REQUEST['puid']}&" . href_qs($qs_basic));
		break;
	case 'modify':
		modify_ok($dbinfo,$qs,'uid');
			back_close('',"{$thisUrl}/list.php?p_uid={$_REQUEST['puid']}&" . href_qs($qs_basic));
		break;
	case 'delete':
		$goto = "{$thisUrl}/list.php?p_uid={$_REQUEST['puid']}&" . href_qs($qs_basic);
		delete_ok($dbinfo,'uid',$goto);
		go_url($goto);
	default :
		back("잘못된 웹 페이지에 접근하였습니다");
} // end switch
//=======================================================
// User functions.. . (사용자 함수 정의)
//=======================================================
function reply_ok(&$dbinfo, $qs){
	global $db_conn;

	// $qs 추가, 체크후 값 가져오기
	$qs['uid']	= "post,tirm,notnull=" . urlencode("답변할 게시물의 고유넘버가 넘어오지 않았습니다.");
	$qs=check_value($qs);

	// 부모글 가져오기
	$sql = "SELECT * FROM {$dbinfo['table']} WHERE uid={$qs['uid']}";
	$list = db_arrayone($sql) or back("답변할 DB가 없습니다");
	if($list['type'] == 'info') back("공지글에는 답변글을 올리실 수 없습니다.");	
	
	//////////////////////////////////////
	// 카테고리 정보 가져와 dbinfo 재 설정
	if(($dbinfo['enable_cate'] ?? '') == 'Y' and ($dbinfo['enable_cateinfo'] ?? '') == 'Y' and $list['cateuid']){
		$dbinfo['table_cate']	= {$dbinfo['table']} . "_cate";

		$sql = "select * from {$dbinfo['table_cate']} where uid='{$list['cateuid']}' LIMIT 1";
		$cateinfo = db_arrayone($sql);

		if(($cateinfo['bid'] ?? 0) > 0) $dbinfo['cid'] = $cateinfo['bid'];
		// 나머지 dbinfo값 일괄 변경
		$tmp = array('priv_reply','enable_userid','enable_upload','enable_uploadextension','enable_uploadmust','enable_adm_mail','enable_rec_mail');
		foreach($tmp as $tmp_field){
			if(isset($cateinfo[$tmp_field]) && $cateinfo[$tmp_field] !== null) $dbinfo[$tmp_field]	= $cateinfo[$tmp_field];
		}
	} // end if
	//////////////////////////////////////
	
	// 권한 검사
	if(!privAuth($dbinfo, 'priv_reply')) back("이용이 제한되었습니다(레벨부족) . 확인바랍니다.");
		
	/////////////////////////////////
	// 추가되어 있는 테이블 필드 포함
	// type 제외
	$skip_fields = array('type', 'uid', 'upfiles', 'upfiles_totalsize', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip', 'rdate');
	$sql_set = '';
	if($fieldlist = userGetAppendFields($table, $skip_fields)){
		foreach($fieldlist as $value){
			// 해당 필드 데이터값 확정
			switch($value){
				// board2 reply
				case 'cateuid' : // catelist에서 선택한 값을
					$qs['cateuid'] = $list['cateuid'];
					break;
				case 'catetitle' :
					$qs['catetitle'] =	$list['catetitle'];
					break;					
				case 'priv_level' : // 정수값으로
					$qs['priv_level'] = (int)($_POST['priv_level'] ?? 0);
					break;
				case 'docu_type' : // html값이 아니면 text로
					if(strtolower($_POST['docu_type'] ?? '') != "html") $_POST['docu_type']="text";
					break;
				case 'ip' :
					$qs['ip'] = remote_addr();
					break;
				case 'num' :
					$qs['num'] = $list['num'];
					break;
				case 're' :
					$qs['re'] = userReplyRe($dbinfo['table'], $list['num'], $list['re']); // re값 구하는 함수 호출
				case 'bid' :
					$qs['bid']	= $_SESSION['seUid'];
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
					if(isset($_POST['email'])) $qs['email']	= check_email($_POST['email']);
					elseif($_SESSION['seUid']) $qs['email']	= $_SESSION['seEmail'];
					break;
			} // end switch

			// sql_set 만듦
			if(isset($qs[$value])){
				if($value == 'passwd') $sql_set .= ", passwd	=password('{$qs['passwd']}') ";
				else $sql_set .= ", {$value} = '" . db_escape($qs[$value]) . "' ";
			} elseif(isset($_POST[$value])){
				if($value == 'passwd') $sql_set .= ", passwd	=password('".db_escape($_POST['passwd']) . "') ";
				else $sql_set .= ", {$value} = '" . db_escape($_POST[$value]) . "' ";
			}
		} // end foreach
	} // end if
	////////////////////////////////
	/////////////////////////////////
	// 파일업로드 처리-추가(03/10/20)
	/////////////////////////////////
	$sql_set_file = '';
	if(($dbinfo['enable_upload'] ?? 'N') != 'N' and isset($_FILES)){
		$updir = $dbinfo['upload_dir'] . "/" . (int)($_SESSION['seUid'] ?? 0);

		// 사용변수 초기화
		$upfiles=array();
		$upfiles_totalsize=0;
		if($dbinfo['enable_upload'] == 'Y'){
			if(isset($_FILES['upfile']['name'])) { // 파일이 업로드 되었다면
				if(isset($dbinfo['enable_uploadextension'])) { // 특정 확장자만 사용가능하면
					$allow_extension = explode(',',$dbinfo['enable_uploadextension']);
					$ext = strtolower(substr(strrchr($_FILES['upfile']['name'],"."), 1)); //확장자
					if(in_array($ext,$allow_extension)){
						$upfiles['upfile']=file_upload("upfile",$updir);
						$upfiles_totalsize = $upfiles['upfile']['size'];
					}
				} else {
					$upfiles['upfile']=file_upload("upfile",$updir);
					$upfiles_totalsize = $upfiles['upfile']['size'];
				}
			}
		} else {
			foreach($_FILES as $key =>	$value){
				if(isset($value['name'])) { // 파일이 업로드 되었다면
					if(isset($dbinfo['enable_uploadextension'])){
						$allow_extension = explode(',',$dbinfo['enable_uploadextension']);
						$ext = strtolower(substr(strrchr($value['name'],"."), 1)); //확장자
						if(!in_array($ext,$allow_extension)) continue;
					}
					if( $dbinfo['enable_upload'] == 'image'
						AND !is_array(getimagesize($_FILES[$key]['tmp_name'])) )
						continue;
					$upfiles[$key]=file_upload($key,$updir);
					$upfiles_totalsize += $upfiles[$key]['size'];
				}
			} // end foreach
		} // end if . . esle ..
		if(($dbinfo['enable_uploadmust'] ?? 'N') == 'Y' and sizeof($upfiles) == 0){
			if( $dbinfo['enable_upload'] == 'image')
				back("이미지파일을 선택하여 업로드하여 주시기 바랍니다");
			else back("파일이 업로드 되지 않았습니다");
		}
		$sql_set_file = ", upfiles='".db_escape(serialize($upfiles)) . "', upfiles_totalsize='{$upfiles_totalsize}' ";
		unset($upfiles);
	} // end if
	/////////////////////////////////

	$sql="INSERT INTO {$dbinfo['table']} SET
				rdate	= UNIX_TIMESTAMP()
				{$sql_set_file}
				{$sql_set}
		";
	db_query($sql);
	$uid = db_insert_id();

	// E-Mail 전송
	if( ($dbinfo['enable_adm_mail'] ?? 'N') == 'Y' or ($dbinfo['enable_rec_mail'] ?? 'N') == 'Y' ){
		if($dbinfo['enable_rec_mail'] == 'Y'){
			// dbinfo['email']에 부모글 메일 주소 추가
			if($list['email'] = check_email($list['email'])){
				if(isset($dbinfo['email'])) $dbinfo['email'] .= ",{$list['email']}";
				else $dbinfo['email'] = $list['email'];
			}
		}
					
		$mail = new mime_mail;

		$mail->from		= $qs['email'];
		$mail->name		= "게시판 자동메일";
		$mail->to		= $dbinfo['email'];
		$mail->subject	= "[게시판-답변] {$qs['title']}";
		if(($qs['docu_type'] ?? '') == "html"){
			$mail->body	= "[{$list['userid']}]님께서 다음과 같은 답변 게시물을 남겼습니다.]<br><hr>제목:{$list['title']}<hr>{$list['content']}<hr>예상되는게시판주소:http://{$_SERVER['HTTP_HOST']}/sboard2/read.php?db={$_REQUEST['db']}&uid={$uid}";
			$mail->html	= 1;
		} else {
			$mail->body	= "[{$list['userid']}]님께서 다음과 같은 답변 게시물을 남겼습니다.]\n
제목:{$list['title']}\n
--------------------------------------------\n
{$list['content']}\n
--------------------------------------------
\n\n\n
예상되는게시판URL: http://{$_SERVER['HTTP_HOST']}/sboard2/read.php?db={$_REQUEST['db']}&uid={$uid}";
			$mail->html	= 0;
		}
		$mail->send();
	}

	return $uid;
} // end func reply_ok()
function write_ok(&$dbinfo, $qs){
	global $db_conn;

	//////////////////////////////////////
	// 카테고리 정보 가져와 dbinfo 재 설정
	if(($dbinfo['enable_cate'] ?? 'N') == 'Y' and ($dbinfo['enable_cateinfo'] ?? 'N') == 'Y' and isset($_REQUEST['catelist'])){
		$dbinfo['table_cate']	= {$dbinfo['table']} . "_cate";

		$sql = "select * from {$dbinfo['table_cate']} where uid='".db_escape($_REQUEST['catelist']) . "' LIMIT 1";
		$cateinfo = db_arrayone($sql);

		if(($cateinfo['bid'] ?? 0) > 0) $dbinfo['cid'] = $cateinfo['bid'];
		// 나머지 dbinfo값 일괄 변경
		$tmp = array('priv_writeinfo','priv_write','enable_userid','enable_upload','enable_uploadextension','enable_uploadmust','enable_adm_mail');
		foreach($tmp as $tmp_field){
			if(isset($cateinfo[$tmp_field]) && $cateinfo[$tmp_field] !== null) $dbinfo[$tmp_field]	= $cateinfo[$tmp_field];
		}
	} // end if
	//////////////////////////////////////

	// 권한 검사
	if(($dbinfo['enable_type'] ?? 'N') == 'Y' and ($_POST['type'] ?? '') == 'info')
		if(!privAuth($dbinfo, 'priv_writeinfo')) back("이용이 제한되었습니다(레벨부족) . 확인바랍니다.");
	else
		if(!privAuth($dbinfo, 'priv_write')) back("이용이 제한되었습니다(레벨부족) . 확인바랍니다.");
	
	// $qs 추가, 체크후 값 가져오기
	$qs=check_value($qs);

	/////////////////////////////////
	// 추가되어 있는 테이블 필드 포함
	$skip_fields = array('uid', 'upfiles', 'upfiles_totalsize', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip', 'rdate');
	$sql_set = '';
	if($fieldlist = userGetAppendFields($table, $skip_fields)){
		foreach($fieldlist as $value){
			// 해당 필드 데이터값 확정
			switch($value){
				// board2 write
				case 'cateuid' : // catelist에서 선택한 값을
					$qs['cateuid'] = $_POST['catelist'] ?? '';
					break;
				case 'catetitle' :
					if(isset($_POST['catelist'])){
						$sql="select title from {$dbinfo['table_cate']} where uid='".db_escape($_POST['catelist']) . "'";
						$qs['catetitle'] = db_resultone($sql,0,'title');
					}
					break;
				case 'priv_level' : // 정수값으로
					$qs['priv_level'] = (int)($_POST['priv_level'] ?? 0);
					break;
				case 'docu_type' : // html값이 아니면 text로
					if(strtolower($_POST['docu_type'] ?? '') != "html") $_POST['docu_type']="text";
					break;
				case 'ip' :
					$qs['ip'] = remote_addr();
					break;
				case 'num' :
					$sql = "SELECT max(num) FROM {$dbinfo['table']}";
					$qs['num'] = (db_resultone($sql,0,"max(num)") ?? 0) + 1;	
					break;
				case 'bid' :
					$qs['bid']	= $_SESSION['seUid'] ?? 0;
				case 'userid' :
					if(isset($_SESSION['seUid'])){
						switch($dbinfo['enable_userid']){
							case 'name'		: $qs['userid'] = $_SESSION['seName']; break;
							case 'nickname'	: $qs['userid'] = $_SESSION['seNickname']; break;
							default			: $qs['userid'] = $_SESSION['seUserid']; break;
						}
					}
					break;
				case 'email' :
					if(isset($_POST['email'])) $qs['email']	= check_email($_POST['email']);
					elseif(isset($_SESSION['seUid'])) $qs['email']	= $_SESSION['seEmail'];
					break;
			} // end switch

			// sql_set 만듦
			if(isset($qs[$value])){
				if($value == 'passwd') $sql_set .= ", passwd	=password('{$qs['passwd']}') ";
				else $sql_set .= ", {$value} = '" . db_escape($qs[$value]) . "' ";
			} elseif(isset($_POST[$value])){
				if($value == 'passwd') $sql_set .= ", passwd	=password('".db_escape($_POST['passwd']) . "') ";
				else $sql_set .= ", {$value} = '" . db_escape($_POST[$value]) . "' ";
			}
		} // end foreach
	} // end if
	////////////////////////////////
	/////////////////////////////////
	// 파일업로드 처리-추가(03/10/20)
	/////////////////////////////////
	$sql_set_file = '';
	if(($dbinfo['enable_upload'] ?? 'N') != 'N' and isset($_FILES)){
		$updir = $dbinfo['upload_dir'] . "/" . (int)($_SESSION['seUid'] ?? 0);

		// 사용변수 초기화
		$upfiles=array();
		$upfiles_totalsize=0;
		if($dbinfo['enable_upload'] == 'Y'){
			if(isset($_FILES['upfile']['name'])) { // 파일이 업로드 되었다면
				if(isset($dbinfo['enable_uploadextension'])) { // 특정 확장자만 사용가능하면
					$allow_extension = explode(',',$dbinfo['enable_uploadextension']);
					$ext = strtolower(substr(strrchr($_FILES['upfile']['name'],"."), 1)); //확장자
					if(in_array($ext,$allow_extension)){
						$upfiles['upfile']=file_upload("upfile",$updir);
						$upfiles_totalsize = $upfiles['upfile']['size'];
					}
				} else {
					$upfiles['upfile']=file_upload("upfile",$updir);
					$upfiles_totalsize = $upfiles['upfile']['size'];
				}
			}
		} else {
			foreach($_FILES as $key =>	$value){
				if(isset($value['name'])) { // 파일이 업로드 되었다면
					if(isset($dbinfo['enable_uploadextension'])){
						$allow_extension = explode(',',$dbinfo['enable_uploadextension']);
						$ext = strtolower(substr(strrchr($value['name'],"."), 1)); //확장자
						if(!in_array($ext,$allow_extension)) continue;
					}
					if( $dbinfo['enable_upload'] == 'image'
						AND !is_array(getimagesize($_FILES[$key]['tmp_name'])) )
						continue;
					$upfiles[$key]=file_upload($key,$updir);
					$upfiles_totalsize += $upfiles[$key]['size'];
				}
			} // end foreach
		} // end if . . esle ..
		if(($dbinfo['enable_uploadmust'] ?? 'N') == 'Y' and sizeof($upfiles) == 0){
			if( $dbinfo['enable_upload'] == 'image')
				back("이미지파일을 선택하여 업로드하여 주시기 바랍니다");
			else back("파일이 업로드 되지 않았습니다");
		}
		$sql_set_file = ", upfiles='".db_escape(serialize($upfiles)) . "', upfiles_totalsize='{$upfiles_totalsize}' ";
		unset($upfiles);
	} // end if
	/////////////////////////////////

	$sql="INSERT INTO {$dbinfo['table']} SET
				rdate	= UNIX_TIMESTAMP()
				{$sql_set_file}
				{$sql_set}
		";
	db_query($sql);
	$uid = db_insert_id();

	// E-Mail 전송
	if( ($dbinfo['enable_adm_mail'] ?? 'N') == 'Y' and isset($dbinfo['email']) ){
		$mail = new mime_mail;

		$mail->from		= $dbinfo['email'];
		$mail->name		= "게시판 자동메일";
		$mail->to		= $dbinfo['email'];
		$mail->subject	= "[게시판자동메일] {$qs['title']}";
		if(($qs['docu_type'] ?? '') == "html"){
			$mail->body	= "[" . ($list['userid'] ?? '') . "]님께서 다음과 같은 게시물을 남겼습니다.]<br><hr>제목:" . ($list['title'] ?? '') . "<hr>" . ($list['content'] ?? '') . "<hr>예상되는게시판주소:http://{$_SERVER['HTTP_HOST']}/sboard2/read.php?db={$_REQUEST['db']}&uid=$uid";
			$mail->html	= 1;
		} else {
			$mail->body	= "[" . ($list['userid'] ?? '') . "]님께서 다음과 같은 답변 게시물을 남겼습니다.]\n
제목:" . ($list['title'] ?? '') . "\n
--------------------------------------------\n
" . ($list['content'] ?? '') . "\n
--------------------------------------------
\n\n\n
예상되는게시판URL: http://{$_SERVER['HTTP_HOST']}/sboard2/read.php?db={$_REQUEST['db']}&uid={$uid}";
			$mail->html	= 0;
		}
		$mail->send();
	}

	return $uid;
} // end func

function modify_ok(&$dbinfo,$qs,$field){
	// $qs 추가, 체크후 값 가져오기
	$qs["$field"]	= "post,trim,notnull=" . urlencode("고유번호가 넘어오지 않았습니다");
	$qs=check_value($qs);

	// 수정할 글 읽기
	$sql = "SELECT *,password('".db_escape($_POST['passwd'] ?? '') . "') as postpasswd FROM {$dbinfo['table']} WHERE {$field}='".db_escape($qs[$field]) . "'";
	$list=db_arrayone($sql) or back("게시물이 없거나 수정할 권한이 없습니다");

	//////////////////////////////////////
	// 카테고리 정보 가져와 dbinfo 재 설정
	if(($dbinfo['enable_cate'] ?? 'N') == 'Y' and ($dbinfo['enable_cateinfo'] ?? 'N') == 'Y' and isset($list['cateuid'])){
		$dbinfo['table_cate']	= {$dbinfo['table']} . "_cate";

		$sql = "select * from {$dbinfo['table_cate']} where uid='".db_escape($list['cateuid']) . "' LIMIT 1";
		$cateinfo = db_arrayone($sql);

		if(($cateinfo['bid'] ?? 0) > 0) $dbinfo['cid'] = $cateinfo['bid'];
		// 나머지 dbinfo값 일괄 변경
		$tmp = array('priv_delete','enable_userid','enable_upload','enable_uploadextension','enable_uploadmust');
		foreach($tmp as $tmp_field){
			if(isset($cateinfo[$tmp_field]) && $cateinfo[$tmp_field] !== null) $dbinfo[$tmp_field]	= $cateinfo[$tmp_field];
		}
	} // end if
	//////////////////////////////////////
	// 수정 권한 체크
	if(!privAuth($dbinfo,"priv_delete") ){
		if(privAuth($dbinfo,"priv_writer")) { // 작성자에게 권한이 있으면
			if(isset($list['bid'])){ // 회원의 글이라면,
				if($list['bid'] != ($_SESSION['seUid'] ?? '')) back('회원님이 작성한 게시물이 아님니다.');
			} else { // 비회원의 글이라면
				if($list['postpasswd'] != $list['passwd']) back('패스워드가 틀렸습니다.');
			}
		}
		else back('수정하실 권한이 없습니다.');
	} // end if

	/////////////////////////////////
	// 추가되어 있는 테이블 필드 포함
	// bid, nume, re, passwd, type는 수정 불가
	$skip_fields = array( 'bid', 'num', 're', 'passwd', 'type', 'uid', 'upfiles', 'upfiles_totalsize', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip', 'rdate');
	$sql_set = '';
	if($fieldlist = userGetAppendFields($table, $skip_fields)){
		foreach($fieldlist as $value){
			// 해당 필드 데이터값 확정
			switch($value) {
				// board2 modify
				case 'cateuid' : // catelist에서 선택한 값을
					// 답변이 아닌 경우에만 카테고리 수정 가능
					if( isset($_POST['catelist']) and strlen($list['re']) == 0 ){
						$qs['cateuid'] = $_POST['catelist'];
						// 해당 카테고리가 있는지 체크
						if($qs['cateuid']){
							$sql="select * from {$dbinfo['table_cate']} where uid='".db_escape($qs['cateuid']) . "'";
							if(!db_arrayone($sql)) back('선택한 카테고리가 없습니다.');
						}
					}
					else $qs['cateuid'] = $list['cateuid'];
					break;
				case 'catetitle' :
					// 답변이 아닌 경우에만 카테고리 수정 가능
					if( isset($_POST['catelist']) and strlen($list['re']) == 0 ){
						$sql="select title from {$dbinfo['table_cate']} where uid='".db_escape($_POST['catelist']) . "'";
						$qs['catetitle'] = db_resultone($sql,0,'title');
					}
					break;
				case 'priv_level' : // 정수값으로
					$qs['priv_level'] = (int)($_POST['priv_level'] ?? 0);
					break;
				case 'docu_type' : // html값이 아니면 text로
					if(strtolower($_POST['docu_type'] ?? '') != "html") $qs['docu_type']="text";
					else $qs['docu_type'] = "html";
					break;
				case 'ip' :
					$qs['ip'] = remote_addr();
					break;
				case 'userid' :
					if($list['bid'] == ($_SESSION['seUid'] ?? '')) { // 관리자권한으로 수정했으면 변경불가
						switch($dbinfo['enable_userid']){
							case 'name'		: $qs['userid'] = $_SESSION['seName']; break;
							case 'nickname'	: $qs['userid'] = $_SESSION['seNickname']; break;
							default			: $qs['userid'] = $_SESSION['seUserid']; break;
						}
					}
					break;
				case 'email' :
					if(isset($_POST['email'])) $qs['email']	= check_email($_POST['email']);
					elseif($list['bid'] == ($_SESSION['seUid'] ?? '')) // 관리자권한으로 수정했으면 변경불가
						$qs['email']	= $_SESSION['seEmail'];
					break;
			} // end switch

			// sql_set 만듦
			if(isset($qs[$value])) $sql_set .= ", {$value} = '" . db_escape($qs[$value]) . "' ";
			elseif(isset($_POST[$value])) $sql_set .= ", {$value} = '" . db_escape($_POST[$value]) . "' ";
		} // end foreach
	} // end if
	////////////////////////////////

	///////////////////////////////
	// 파일 업로드 - 변경(03/10/20)
	///////////////////////////////
	$sql_set_file = '';
	if( ($dbinfo['enable_upload'] ?? 'N') != 'N' and isset($_FILES) ){
		// 파일 업로드 드렉토리
		$updir = $dbinfo['upload_dir'] . "/" . (int)$list['bid'];

		// 기존 업로드 파일 정보 읽어오기
		$upfiles = @unserialize($list['upfiles']);
		if(!is_array($upfiles)) { // 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
			$upfiles = [];
			$upfiles['upfile']['name']=$list['upfiles'];
			$upfiles['upfile']['size']=(int)$list['upfiles_totalsize'];
		}
		$upfiles_totalsize=(int)$list['upfiles_totalsize'];

		// 파일을 올리지 않고, 해당 파일을 삭제하고자 하였을때
		if(is_array($upfiles) and count($upfiles)>0){
			foreach($upfiles as $key =>	$value){
				if(isset($_REQUEST["del_{$key}"])) {
						// 해당 파일 삭제
						if( is_file($dbinfo['upload_dir'] . "/{$list['bid']}/" . $upfiles[$key]['name']) ){
							@unlink($dbinfo['upload_dir'] . "/{$list['bid']}/" . $upfiles[$key]['name']);
							@unlink($dbinfo['upload_dir'] . "/{$list['bid']}/" . $upfiles[$key]['name'].".thumb.jpg"); // thumbnail 삭제
						}
						elseif( is_file($dbinfo['upload_dir'] . "/" . $upfiles[$key]['name']) ){
							@unlink($dbinfo['upload_dir'] . "/" . $upfiles[$key]['name']);
							@unlink($dbinfo['upload_dir'] . "/" . $upfiles[$key]['name'].".thumb.jpg"); // thumbnail 삭제
						}

						$upfiles_totalsize -= $upfiles[$key]['size'];
						unset($upfiles[$key]);
				}
			}
		}

		// 업로드 파일 처리
		if($dbinfo['enable_upload'] == 'Y') { // 파일 하나 업로드라면
			if(isset($_FILES['upfile']['name'])) {	// 파일이 업로드 되었다면
				$ok_upload =0;
				if(isset($dbinfo['enable_uploadextension'])){
					$allow_extension = explode(',',$dbinfo['enable_uploadextension']);
					$ext = strtolower(substr(strrchr($_FILES['upfile']['name'],"."), 1)); //확장자
					if(in_array($ext,$allow_extension)) $ok_upload = 1;
				}
				else $ok_upload = 1;

				if($ok_upload){
					// 기존 업로드 파일이 있다면 삭제
					if( isset($upfiles['upfile']['name']) && is_file($dbinfo['upload_dir'] . "/{$list['bid']}/" . $upfiles['upfile']['name']) ){
						@unlink($dbinfo['upload_dir'] . "/{$list['bid']}/" . $upfiles['upfile']['name']);
						@unlink($dbinfo['upload_dir'] . "/{$list['bid']}/" . $upfiles['upfile']['name'].".thumb.jpg"); // thumbnail 삭제
					}
					elseif( isset($upfiles['upfile']['name']) && is_file($dbinfo['upload_dir'] . "/" . $upfiles['upfile']['name']) ){
						@unlink($dbinfo['upload_dir'] . "/" . $upfiles['upfile']['name']);
						@unlink($dbinfo['upload_dir'] . "/" . $upfiles['upfile']['name'].".thumb.jpg"); // thumbnail 삭제
					}

					// 업로드
					$upfiles_tmp=file_upload("upfile",$updir);
					$upfiles_totalsize	= $upfiles_tmp['size'];
					$upfiles['upfile']	= $upfiles_tmp;
					unset($upfiles_tmp);
				}
			}
		} else { // 복수 업로드라면,
			foreach($_FILES as $key =>	$value){
				if(isset($value['name'])) { // 파일이 업로드 되었다면
					if(isset($dbinfo['enable_uploadextension'])){
						$allow_extension = explode(',',$dbinfo['enable_uploadextension']);
						$ext = strtolower(substr(strrchr($value['name'],"."), 1)); //확장자
						if(!in_array($ext,$allow_extension)) continue;
					}
					if( $dbinfo['enable_upload'] == 'image'
						AND !is_array(getimagesize($_FILES[$key]['tmp_name'])) )
						continue;

					// 기존 업로드 파일이 있다면 삭제
					if( isset($upfiles[$key]['name']) && is_file($dbinfo['upload_dir'] . "/{$list['bid']}/" . $upfiles[$key]['name']) ){
						@unlink($dbinfo['upload_dir'] . "/{$list['bid']}/" . $upfiles[$key]['name']);
						@unlink($dbinfo['upload_dir'] . "/{$list['bid']}/" . $upfiles[$key]['name'].".thumb.jpg"); // thumbnail 삭제
					}
					elseif( isset($upfiles[$key]['name']) && is_file($dbinfo['upload_dir'] . "/" . $upfiles[$key]['name']) ){
						@unlink($dbinfo['upload_dir'] . "/" . $upfiles[$key]['name']);
						@unlink($dbinfo['upload_dir'] . "/" . $upfiles[$key]['name'].".thumb.jpg"); // thumbnail 삭제
					}

					// 업로드
					$upfiles_tmp=file_upload($key,$updir);
					$upfiles_totalsize = $upfiles_totalsize - ($upfiles[$key]['size'] ?? 0) + $upfiles_tmp['size'];
					$upfiles[$key]=$upfiles_tmp;
					unset($upfiles_tmp);
				}
			} // end foreach
		} // end if . . else ..
		if(($dbinfo['enable_uploadmust'] ?? 'N') == 'Y' and sizeof($upfiles) == 0){
			if(isset($dbinfo['enable_uploadextension']))
				back("다음의 파일 확장자만 업로드 가능합니다.\\n{$dbinfo['enable_uploadextension']}");
			if( $dbinfo['enable_upload'] == 'image')
				back("이미지파일을 선택하여 업로드하여 주시기 바랍니다");
			else back("파일이 업로드 되지 않았습니다");
		}
		$sql_set_file = ", upfiles='".db_escape(serialize($upfiles)) . "', upfiles_totalsize='{$upfiles_totalsize}' ";
	} // end if
	///////////////////////////////

	$sql = "UPDATE {$dbinfo['table']} SET
				rdate	=UNIX_TIMESTAMP()
				{$sql_set_file}
				{$sql_set}
			WHERE
				{$field}='".db_escape($qs[$field]) . "'
		";
	db_query($sql);

	// 만일 카테고리가 변경되었다면, 그 이하 답변글들 역시 cateuid값 변경함
	if( (int)($qs['cateuid'] ?? 0) <> (int)$list['cateuid'] ){
		db_query("update {$dbinfo['table']} set cateuid='".db_escape($qs['cateuid']) . "',catetitle='".db_escape($qs['catetitle']) . "' where num='".db_escape($list['num']) . "'");
	} // end if
	
	return true;
} // end func.
// 삭제
function delete_ok(&$dbinfo,$field,$goto){
	global $qs_basic, $thisUrl;
	$qs=array(
			"$field" =>	"request,trim,notnull=" . urlencode("고유넘버가 넘어오지 않았습니다."),
			'passwd' =>	"request,trim"
		);
	$qs=check_value($qs);

	// 삭제 권한 체크와 해당 게시물 읽어오기
	$sql = "SELECT *,password('".db_escape($qs['passwd']) . "') as pass FROM {$dbinfo['table']} WHERE uid='".db_escape($qs['uid']) . "'";
	$list = db_arrayone($sql) or back("이미 삭제되었거나 잘못된 요청입니다");
	
	//////////////////////////////////////
	// 카테고리 정보 가져와 dbinfo 재 설정
	if(($dbinfo['enable_cate'] ?? 'N') == 'Y' and ($dbinfo['enable_cateinfo'] ?? 'N') == 'Y' and isset($list['cateuid'])){
		$dbinfo['table_cate']	= {$dbinfo['table']} . "_cate";

		$sql = "select * from {$dbinfo['table_cate']} where uid='".db_escape($list['cateuid']) . "' LIMIT 1";
		$cateinfo = db_arrayone($sql);

		if(($cateinfo['bid'] ?? 0) > 0) $dbinfo['cid'] = $cateinfo['bid'];
		// 나머지 dbinfo값 일괄 변경
		$tmp = array('priv_delete');
		foreach($tmp as $tmp_field){
			if(isset($cateinfo[$tmp_field]) && $cateinfo[$tmp_field] !== null) $dbinfo[$tmp_field]	= $cateinfo[$tmp_field];
		}
	} // end if
	//////////////////////////////////////
	
	if(!privAuth($dbinfo,"priv_delete")) {// 게시판 전체 삭제 권한을 가졌다면
		if(privAuth($dbinfo,"priv_writer")) { // 작성자에게 권한이 있으면
			if($list['bid'] == 0 and $list['passwd'] != $list['pass']){
				if(isset($_SERVER['QUERY_STRING']))
					back("비밀번호를 입력하여 주십시오","{$thisUrl}/delete.php?{$_SERVER['QUERY_STRING']}");
				else back("비밀번호를 정확히 입력하십시오");
			} elseif ($list['bid']>0 and $list['bid'] != ($_SESSION['seUid'] ?? '')) back("삭제할 권한이 없습니다.");
		}
		else back('삭제하실 권한이 없습니다.');
	}

	// 업로드 파일 삭제 준비
	$del_uploadfile = array(); // init
	if(isset($list['upfiles'])){
		$upfiles=@unserialize($list['upfiles']);
		if(!is_array($upfiles)) { // 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
			$upfiles = [];
			$upfiles['upfile']['name']=$list['upfiles'];
			$upfiles['upfile']['size']=(int)$list['upfiles_totalsize'];
		}
		foreach($upfiles as $key =>	$value){
			if(isset($value['name'])){
				if( is_file($dbinfo['upload_dir'] . "/{$list['bid']}/" . $value['name']) )
					$del_uploadfile[] = $dbinfo['upload_dir'] . "/{$list['bid']}/" . $value['name'];
				elseif( is_file($dbinfo['upload_dir'] . "/" . $value['name']) )
					$del_uploadfile[] = $dbinfo['upload_dir'] . "/" . $value['name'];
			} // end if
		} // end foreach
	} // end if

	// 답변글과 파일도 함께 삭제 준비
	if(isset($list['num'])){
		$rs_subre = db_query("SELECT * FROM {$dbinfo['table']} WHERE num='".db_escape($list['num']) . "' AND length(re) > length('".db_escape($list['re']) . "') AND locate('".db_escape($list['re']) . "',re) = 1");
		while($row=db_array($rs_subre)){
			if(isset($row['upfiles'])){
				$upfiles=@unserialize($row['upfiles']);
				if(!is_array($upfiles)) { // 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
					$upfiles = [];
					$upfiles['upfile']['name']=$row['upfiles'];
				}
				foreach($upfiles as $key =>	$value){
					if(isset($value['name'])){
						if( is_file($dbinfo['upload_dir'] . "/{$row['bid']}/" . $value['name']) )
							$del_uploadfile[] = $dbinfo['upload_dir'] . "/{$row['bid']}/" . $value['name'];
						elseif( is_file($dbinfo['upload_dir'] . "/" . $value['name']) )
							$del_uploadfile[] = $dbinfo['upload_dir'] . "/" . $value['name'];
					} // end if
				} // end foreach
			} // end if
		} // end while
	
		// 서브그룹도 삭제
		db_query("DELETE FROM {$dbinfo['table']} WHERE num='".db_escape($list['num']) . "' AND length(re) > length('".db_escape($list['re']) . "') AND locate('".db_escape($list['re']) . "',re) = 1");
	} // end if
	
	// 삭제
	db_query("DELETE FROM {$dbinfo['table']} WHERE {$field}='".db_escape($qs[$field]) . "'");

	if(is_array($del_uploadfile)){
		foreach ( $del_uploadfile as $value){
			@unlink($value);
			@unlink($value.".thumb.jpg"); // thumbnail 삭제
		}
	} // end if

	return true;
} // end func delete_ok()

function vote_ok(&$dbinfo){
	$qs=array(
				'vote' =>	"post,trim,notnull=" . urlencode("앨범 점수를 선택하여 주기 바랍니다."),
				'uid' =>	"post,trim,notnull=" . urlencode("게시물 값이 없습니다.")
		);
	$qs=check_value($qs);

	// 점수 한계선 설정
	if($qs['vote']>5) $qs['vote']=5;
	if($qs['vote']<-5) $qs['vote']=-5;

	// 조회수 증가
	db_query("UPDATE {$dbinfo['table']} SET
					vote	=vote + " . (int)$qs['vote'].",
					voteip	='".db_escape($_SERVER['REMOTE_ADDR']) . "'
				WHERE
					uid='".db_escape($qs['uid']) . "'
				AND
					voteip<>'".db_escape($_SERVER['REMOTE_ADDR']) . "'
				LIMIT 1
				");

	if(db_count())
		return true;
	else
		back("이미 참여하셨습니다.");

} // end func.

function memoWrite_ok(&$dbinfo){
	global $db_conn;

	// 권한 검사
	if(($dbinfo['enable_memo'] ?? 'N') != 'Y' || !privAuth($dbinfo, 'priv_memo'))
		back('메모를 쓸 수 없습니다.');
	
	// $qs 추가, 체크후 값 가져오기
	$qs=array(
			'uid' =>	"post,trim,notnull=" . urlencode("잘못된 요청입니다"),
			'title' =>	"post,trim,notnull=" . urlencode("내용 입력하시기 바랍니다."),
		);
	$qs=check_value($qs);

	/////////////////////////////////
	// 추가되어 있는 테이블 필드 포함
	$skip_fields = array('uid', 'upfiles', 'upfiles_totalsize', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip', 'rdate');
	$sql_set = '';
	if($fieldlist = userGetAppendFields($table, $skip_fields)){
		foreach($fieldlist as $value){
			// 해당 필드 데이터값 확정
			switch($value){
				// memo write
				case 'docu_type' : // html값이 아니면 text로
					if(isset($_POST['docu_type']) and strtolower($_POST['docu_type']) != "html")
						$_POST['docu_type']="text";
					break;
				case 'ip' :
					$qs['ip'] = remote_addr();
					break;
				case 'bid' :
					$qs['bid']	= $_SESSION['seUid'] ?? 0;
				case 'userid' :
					if(isset($_SESSION['seUid'])){
						switch($dbinfo['enable_userid']){
							case 'name'		: $qs['userid'] = $_SESSION['seName']; break;
							case 'nickname'	: $qs['userid'] = $_SESSION['seNickname']; break;
							default			: $qs['userid'] = $_SESSION['seUserid']; break;
						}
					}
					break;
				case 'email' :
					if(isset($_POST['email'])) $qs['email']	= check_email($_POST['email']);
					elseif(isset($_SESSION['seUid'])) $qs['email']	= $_SESSION['seEmail'];
					break;
			} // end switch

			// sql_set 만듦
			if(isset($qs[$value])){
				if($value == 'passwd') $sql_set .= ", passwd	=password('".db_escape($qs['passwd']) . "') ";
				else $sql_set .= ", {$value} = '" . db_escape($qs[$value]) . "' ";
			} elseif(isset($_POST[$value])){
				if($value == 'passwd') $sql_set .= ", passwd	=password('".db_escape($_POST['passwd']) . "') ";
				else $sql_set .= ", {$value} = '" . db_escape($_POST[$value]) . "' ";
			}
		} // end foreach
	} // end if
	////////////////////////////////
	/////////////////////////////////
	// 파일업로드 처리-추가(03/10/20)
	/////////////////////////////////
	$sql_set_file = '';
	if(($dbinfo['enable_upload'] ?? 'N') != 'N' and isset($_FILES)){
		$updir = $dbinfo['upload_dir'] . "/" . (int)($_SESSION['seUid'] ?? 0);

		// 사용변수 초기화
		$upfiles=array();
		$upfiles_totalsize=0;
		if($dbinfo['enable_upload'] == 'Y'){
			if(isset($_FILES['upfile']['name'])) { // 파일이 업로드 되었다면
				if(isset($dbinfo['enable_uploadextension'])) { // 특정 확장자만 사용가능하면
					$allow_extension = explode(',',$dbinfo['enable_uploadextension']);
					$ext = strtolower(substr(strrchr($_FILES['upfile']['name'],"."), 1)); //확장자
					if(in_array($ext,$allow_extension)){
						$upfiles['upfile']=file_upload("upfile",$updir);
						$upfiles_totalsize = $upfiles['upfile']['size'];
					}
				} else {
					$upfiles['upfile']=file_upload("upfile",$updir);
					$upfiles_totalsize = $upfiles['upfile']['size'];
				}
			}
		} else {
			foreach($_FILES as $key =>	$value){
				if(isset($value['name'])) { // 파일이 업로드 되었다면
					if(isset($dbinfo['enable_uploadextension'])){
						$allow_extension = explode(',',$dbinfo['enable_uploadextension']);
						$ext = strtolower(substr(strrchr($value['name'],"."), 1)); //확장자
						if(!in_array($ext,$allow_extension)) continue;
					}
					if( $dbinfo['enable_upload'] == 'image'
						AND !is_array(getimagesize($_FILES[$key]['tmp_name'])) )
						continue;
					$upfiles[$key]=file_upload($key,$updir);
					$upfiles_totalsize += $upfiles[$key]['size'];
				}
			} // end foreach
		} // end if . . esle ..
		if(($dbinfo['enable_uploadmust'] ?? 'N') == 'Y' and sizeof($upfiles) == 0){
			if( $dbinfo['enable_upload'] == 'image')
				back("이미지파일을 선택하여 업로드하여 주시기 바랍니다");
			else back("파일이 업로드 되지 않았습니다");
		}
		$sql_set_file = ", upfiles='".db_escape(serialize($upfiles)) . "', upfiles_totalsize='{$upfiles_totalsize}' ";
		unset($upfiles);
	} // end if
	/////////////////////////////////

	$sql="INSERT INTO {$dbinfo['table']} SET
				rdate	= UNIX_TIMESTAMP()
				{$sql_set_file}
				{$sql_set}
		";
	db_query($sql);
	$uid = db_insert_id();
	
	return $uid;
} // end func memoWrite_ok

function memodelete_ok(&$dbinfo,$field){
	global $qs_basic, $thisUrl;
	$qs=array(
			"$field" =>	"request,trim,notnull=" . urlencode("고유넘버가 넘어오지 않았습니다."),
			'pid' =>	"request,trim,notnull=" . urlencode("고유번호가 넘어오지 않았습니다."),
			'passwd' =>	"request,trim"
		);
	$qs=check_value($qs);

	// 삭제 권한 체크와 해당 게시물 읽어오기
	$sql = "SELECT *,password('".db_escape($qs['passwd']) . "') as pass FROM {$dbinfo['table']} WHERE $field='".db_escape($qs[$field]) . "' and pid='".db_escape($qs['pid']) . "'";
	$list = db_arrayone($sql) or back("이미 삭제되었거나 잘못된 요청입니다");
	if(!privAuth($dbinfo,"priv_delete")) {// 게시판 전체 삭제 권한을 가졌다면
		if($list['bid'] == 0 and $list['passwd'] != $list['pass']){
			if(isset($_SERVER['QUERY_STRING']))
				back("비밀번호를 입력하여 주십시오","{$thisUrl}/delete.php?{$_SERVER['QUERY_STRING']}");
			else back("비밀번호를 정확히 입력하십시오");
		} elseif ($list['bid']>0 and $list['bid'] != ($_SESSION['seUid'] ?? '')) back("삭제할 권한이 없습니다.");
	}

	// 업로드 파일 삭제 준비
	$del_uploadfile = array(); // init
	if(isset($list['upfiles'])){
		$upfiles=@unserialize($list['upfiles']);
		if(!is_array($upfiles)) { // 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
			$upfiles = [];
			$upfiles['upfile']['name']=$list['upfiles'];
			$upfiles['upfile']['size']=(int)$list['upfiles_totalsize'];
		}
		foreach($upfiles as $key =>	$value){
			if(isset($value['name'])){
				if( is_file($dbinfo['upload_dir'] . "/{$list['bid']}/" . $value['name']) )
					$del_uploadfile[] = $dbinfo['upload_dir'] . "/{$list['bid']}/" . $value['name'];
				elseif( is_file($dbinfo['upload_dir'] . "/" . $value['name']) )
					$del_uploadfile[] = $dbinfo['upload_dir'] . "/" . $value['name'];
			} // end if
		} // end foreach
	} // end if

	// 삭제
	db_query("DELETE FROM {$dbinfo['table']} WHERE {$field}='".db_escape($qs[$field]) . "'");

	if(is_array($del_uploadfile)){
		foreach ( $del_uploadfile as $value){
			@unlink($value);
			@unlink($value.".thumb.jpg"); // thumbnail 삭제
		}
	} // end if

	return true;
} // end func memodelete_ok

// 카테고리 새서브 RE값 구함
// 03/10/12
// 04/07/26 board2에 맞추어 수정
function userReplyRe($table, $num, $re){
	global $dbinfo;

	// 한 table에 여러 게시판 생성의 경우
	$sql_where = '';
	if (($dbinfo['table_name'] ?? '') != ($dbinfo['db'] ?? '')) {
		$sql_where = " db='{$dbinfo['db']}' ";
	}
	if (($dbinfo['enable_type'] ?? '') == 'Y') {
		$sql_where = $sql_where ? $sql_where . " and type='docu' " : " type='docu' ";
	}
	if (!$sql_where) {
		$sql_where = " 1 ";
	}

	$sql = "SELECT re, right(re,1) FROM {$table} WHERE $sql_where and num='{$num}' AND length(re)=length('{$re}')+1 AND locate('{$re}', re)=1 ORDER BY re DESC LIMIT 1";
	$row = db_arrayone($sql);

	if ($row) {
		$ord_head = substr($row['re'], 0, -1);
		if (ord($row['right(re,1)']) >= 255) {
			back("더이상 추가하실 수 없습니다");
		}
		$ord_foot = chr(ord($row['right(re,1)']) + 1);
		$re = $ord_head . $ord_foot;
	} else {
		$re .= "1";
	}
	return $re;
} // end func userReplyRe($table, $num, $re)

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
