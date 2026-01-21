<?php
//=======================================================
// 설	명 : 게시판 삭제 비밀번호 입력 페이지(delete.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 03/10/12
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 03/10/12 박선민 마지막 수정
//=======================================================
$HEADER=array(
		'private' => 1,
		'priv' => '', // 인증유무 (0:모두에게 허용, 숫자가 높을 수록 레벨업)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useBoard2' => 1, // 보드관련 함수 포함
		'html_echo' => ''	// 게시판은 무조건 '0', (boardinfo[html_headtpl]에서 템플릿 설정함
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $HTTP_HOST);

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	$thisPath		= dirname(__FILE__);
	$thisUrl		= "/sboard"; // 마지막 "/"이 빠져야함

	// 기본 URL QueryString
	$qs_basic		= href_qs();
	$table_dbinfo	= $SITE['th'] . "boardinfo";

	// info 테이블 정보 가져와서 $dbinfo로 저장
	if($_GET['db']){
		$sql="SELECT * from {$table_dbinfo} WHERE db='{$_GET['db']}'";
		if(!$dbinfo=db_arrayone($sql)) back("사용하지 않은 DB입니다.");

		$table=$SITE['th'] . "board_" . $dbinfo['table_name'];
	}
	else back("DB 값이 없습니다");

	$form_delete	= " ACTION='{$thisUrl}/ok.php' method='POST'>";
	if($_GET['mode'] == "memo") // memo 삭제 요청의 경우(하지만 메모는 로그인한사람만 쓸 수 있음)
		$form_delete.= substr(href_qs("mode=memodelete",$qs_basic,1),0,-1);
	else 
		$form_delete.= substr(href_qs("mode=delete",$qs_basic,1),0,-1);
	
	// URL Link..
	$href["list"] = "{$thisUrl}/list.php?" . href_qs("",$qs_basic);

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// 템플릿 기반 웹 페이지 제작
$tpl = new phemplate("{$thisPath}/stpl/{$dbinfo['skin']}/");
$tpl->set_file('html',"delete.htm",1); // here 1 mean extract blocks

// 템플릿 마무리 할당
$tpl->set_var('form_delete',$form_delete);
$tpl->set_var('href',$href);

// 마무리
$val="\\1{$thisUrl}/stpl/{$dbinfo['skin']}/images/";
switch($dbinfo['html_headpattern']){
	case "ht":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] == 2;
		if( $dbinfo['html_headtpl'] != "" and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") ) 
			@include_once("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
		else
			@include_once("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo $SITE['head'] . $dbinfo['html_head'];
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html')); // 1 mean loop		
		echo $dbinfo['html_tail'] . $SITE['tail'];
		break;
	case "h":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] == 2;
		if( $dbinfo['html_headtpl'] != "" and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") ) 
			@include_once("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
		else
			@include_once("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo $SITE['head'] . $dbinfo['html_head'];
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html')); // 1 mean loop		
		echo $dbinfo['html_tail'];
		break;
	case "t":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] == 2;
		if( $dbinfo['html_headtpl'] != "" and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") ) 
			@include_once("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
		else
			@include_once("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo $dbinfo['html_head'];
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html')); // 1 mean loop		
		echo $dbinfo['html_tail'] . $SITE['tail'];
		break;
	case "no":
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		break;
	default:
		echo $dbinfo['html_head'];
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html')); // 1 mean loop		
		echo $dbinfo['html_tail'];
} // end switch 
?>
