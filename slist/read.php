<?php
//=======================================================
// 설	명 : 심플리스트읽기(read.php)
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
	'useApp' => 1 // replace_string()
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
	
	//==================== 
	// 4 . 해당 게시물 읽음
	//==================== 
	$sql = "SELECT * FROM {$dbinfo['table']} WHERE uid='{$_GET['uid']}' LIMIT 1";
	$list=db_arrayone($sql) or back('데이터가 없습니다.');

	//==================== 
	// 5 . 해당 게시물 처리
	//==================== 
	// 인증 체크(자기 글이면 무조건 보기)
	// - 게시물에 priv값이 있으면, 해당 권한으로 변경
	if($list['priv_read']) $dbinfo['priv_read']=$list['priv_read'];
	if(!privAuth($dbinfo, 'priv_read',1)){
		if(!$list['bid'] or $list['bid'] != $_SESSION['seUid'] or 'nobid' == substr($dbinfo['priv_read'],0,5) )
			back('이용이 제한되었습니다.(레벨부족)');
	} // end if
	
	$list['rdate_date']	= date('Y/m/d', $list['rdate']);
	$list['title']		= htmlspecialchars($list['title'],ENT_QUOTES);
	$list['content']	= replace_string($list['content'], $list['docu_type']);	// 문서 형식에 맞추어서 내용 변경

	// 6 . URL Link...
	$href['list']	= 'list.php?'.href_qs('uid=',$qs_basic);
	$href['write']	= 'write.php?'.href_qs('mode=write',$qs_basic);
	$href['modify']	= 'write.php?'.href_qs("mode=modify&uid={$list['uid']}&num={$list['num']}",$qs_basic);
	$href['delete']	= 'ok.php?'.href_qs('mode=delete&uid='.$list['uid'],$qs_basic);

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// 템플릿 기반 웹 페이지 제작
$skinfile=basename(__FILE__,'.php').'.html';
if( !is_file('skin/'.$dbinfo['skin'].'/'.$skinfile) ) $dbinfo['skin']='basic';
$tpl = new phemplate('skin/'.$dbinfo['skin']); // 템플릿 시작
$tpl->set_file('html',$skinfile,TPL_BLOCK);
// 템플릿 마무리 할당
$tpl->tie_var('list'			,$list);	// 게시물 할당

$tpl->tie_var('get'				,$_GET);	// get값으로 넘어온것들
$tpl->tie_var('dbinfo'			,$dbinfo);	// dbinfo 정보 변수
$tpl->tie_var('href'			,$href);

// 블럭 : 글쓰기
if(privAuth($dbinfo, 'priv_write')) $tpl->process('WRITE','write');
else $tpl->process('WRITE','nowrite');

// 블럭 : 글수정,삭제
if(privAuth($dbinfo, 'priv_modify') or $list['bid'] == $_SESSION['seUid'] or $list['bid'] == 0){
	$tpl->process('MODIFY','modify');
}
if(privAuth($dbinfo, 'priv_delete') or $list['bid'] == $_SESSION['seUid'] or $list['bid'] == 0){
	$tpl->process('DELETE','delete');
}

// 마무리
$tpl->echoHtml($dbinfo, $SITE); 
?>
