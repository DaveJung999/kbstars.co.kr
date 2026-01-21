<?php
//=======================================================
// 설	명 : 심플리스트
// 책임자 : 박선민 (sponsor@new21.com), 검수: 05/01/25
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 05/01/25 박선민 마지막 수정
// 24/05/20 Gemini PHP 7 마이그레이션
// 24/05/20 Gemini 사용자 요청에 따라 정렬, 통계 계산, 디자인 로직 추가
//=======================================================
$HEADER = array(
	'priv' => '', // 인증유무 (비회원,회원,운영자,서버관리자)
	'usedb2' => 1, // DB 커넥션 사용
	'useSkin' => 1, // 템플릿 사용
	'useApp' => 1, // cut_string()
	'useBoard2' => 1 // 보드관련 함수 포함
);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	// 1 . 넘어온값 체크

	// 2 . 기본 URL QueryString
	$qs_basic	= 'goto='.$_SERVER['PHP_SELF'];

	// 3 . $dbinfo 가져오기
	include_once('config.php');
	// PHP 7 호환성: $dbinfo가 정의되지 않았을 경우를 대비해 빈 배열로 초기화
	if (!isset($dbinfo)) {
		$dbinfo = [];
	}
	// PHP 7 호환성: $_GET['html_skin'] 값 할당 시 isset() 체크
	if (isset($_GET['html_skin'])) {
		$dbinfo['html_skin'] = $_GET['html_skin'];
	}

	// 4 . 권한 체크
	if(!privAuth($dbinfo, 'priv_list',1)) back('페이지를 보실 권한이 없습니다.');

	// 5 . URL Link...
	$href['list']	= $_SERVER['PHP_SELF'].'?'.href_qs('page=',$qs_basic);
	$href['write']	= 'write.php?'	. href_qs('mode=write',$qs_basic);	// 글쓰기

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// 템플릿 기반 웹 페이지 제작
$skinfile=basename(__FILE__,'.php').'.html';

if( !is_file('skin/'.($dbinfo['skin'] ?? 'basic').'/'.$skinfile) ) $dbinfo['skin']='basic';
$tpl = new phemplate('skin/'.($dbinfo['skin'] ?? 'basic')); // 템플릿 시작
$tpl->set_file('html',$skinfile,TPL_BLOCK);
// 템플릿 기본 할당
// PHP 7 호환성: $tpl->tie_var()는 변수를 참조로 받으므로, 임시 변수를 사용
$get = $_GET;
$dbinfo_tied = $dbinfo ?? [];
$href_tied = $href ?? [];
$tpl->tie_var('get', $get);	// get값으로 넘어온것들
$tpl->tie_var('dbinfo', $dbinfo_tied);	// dbinfo 정보 변수
$tpl->tie_var('href', $href_tied);	// 게시판 각종 링크

// SQL문 실행
// PHP 7 호환성: 테이블명에 중괄호 사용, JOIN 구문 사용
$sql = "SELECT A.* FROM {$dbinfo['table']} as A, {$dbinfo['table_season']} as B WHERE A.sid = B.sid ORDER BY	B.s_start desc";
$rs_list = db_query($sql);

if(!($total=db_count($rs_list))) {	// 게시물이 하나도 없다면...
	$tpl->process('LIST', 'nolist');
} else {
	for($i=0; $i<$total; $i++){
		$list		= db_array($rs_list);
		if($i%2) $list['background'] =	" background='/img/list-bar.gif' ";
		else $list['background'] = "";
		
		$list['no']	= $count['lastnum']--;
		$list['rede']	= strlen($list['re'] ?? '');
		$list['rdate_date']= (isset($list['rdate']) && $list['rdate']) ? date('y/m/d', $list['rdate']) : '';	//	날짜 변환
		if(!($list['title'] ?? null)) $list['title'] = '제목없음…';		
		$list['cut_title'] = cut_string($list['title'], (int)($_GET['cut_length'] ?? 0)); // 제목자름

		$total_result['tr_game'] = ($total_result['tr_game'] ?? 0) + ($list['tr_game'] ?? 0);
		$total_result['tr_win'] = ($total_result['tr_win'] ?? 0) + ($list['tr_win'] ?? 0);
		$total_result['tr_loss'] = ($total_result['tr_loss'] ?? 0) + ($list['tr_loss'] ?? 0);
		$total_result['tr_score'] = ($total_result['tr_score'] ?? 0) + ($list['tr_score'] ?? 0);
		$total_result['tr_2p1'] = ($total_result['tr_2p1'] ?? 0) + ($list['tr_2p1'] ?? 0);
		$total_result['tr_2p2'] = ($total_result['tr_2p2'] ?? 0) + ($list['tr_2p2'] ?? 0);
		$total_result['tr_3p1'] = ($total_result['tr_3p1'] ?? 0) + ($list['tr_3p1'] ?? 0);
		$total_result['tr_3p2'] = ($total_result['tr_3p2'] ?? 0) + ($list['tr_3p2'] ?? 0);
		$total_result['tr_free1'] = ($total_result['tr_free1'] ?? 0) + ($list['tr_free1'] ?? 0);
		$total_result['tr_free2'] = ($total_result['tr_free2'] ?? 0) + ($list['tr_free2'] ?? 0);
		$total_result['tr_re'] = ($total_result['tr_re'] ?? 0) + ($list['tr_re'] ?? 0);
		$total_result['tr_as'] = ($total_result['tr_as'] ?? 0) + ($list['tr_as'] ?? 0);
		$total_result['tr_st'] = ($total_result['tr_st'] ?? 0) + ($list['tr_st'] ?? 0);
		$total_result['tr_blk'] = ($total_result['tr_blk'] ?? 0) + ($list['tr_blk'] ?? 0);
		$total_result['tr_to'] = ($total_result['tr_to'] ?? 0) + ($list['tr_to'] ?? 0);
		$total_result['tr_po'] = ($total_result['tr_po'] ?? 0) + ($list['tr_po'] ?? 0);

		/*$trs1 = substr(($list['tr_season'] ?? ''), 0, 2);
		$trs2 = substr(($list['tr_season'] ?? ''), 2);
		
		$list['tr_season'] = $trs1."<br>".$trs2;*/

		// URL Link...
		$list['href']['read'] = 'read.php?'	. href_qs('uid='.($list['uid'] ?? ''),$qs_basic);
		// PHP 7 호환성: 배열 키에 따옴표 사용
		$href['go']	= 'write.php?'.href_qs("mode=modify&uid={$list['uid']}&num={$list['num']}",$qs_basic);

		// 템플릿 할당
		$tpl->set_var('href'			,$href);	// 게시판 각종 링크
		$tpl->set_var('list'		,$list);

		if(privAuth($dbinfo, "priv_write")) $tpl->process('GO','go');
		else $tpl->process('GO','nogo');
					
		$tpl->set_var('blockloop'	,true);
		$tpl->process('LIST','list',TPL_OPTIONAL|TPL_APPEND);

		if($i == 0){
			$new_list = $list;
			$tpl->set_var('new_list', $new_list);
			// PHP 7 호환성: tie_var에 리터럴 값 대신 변수 전달
			$new_list_tied = $new_list ?? [];
			$tpl->tie_var('new_list', $new_list_tied);
		}
	} // end for (i)
	//	템플릿내장값 지우기
	$tpl->drop_var('blockloop');
	$tpl->drop_var('list',$list);
} // end if (게시물이 있다면...)

$list['wdat'] =	date("m/d");
$list['wdat'] =	$list['wdat']." WKBL 종합순위";
// PHP 7 호환성: set_var에 리터럴 값 대신 변수 전달
$total_result_final = $total_result ?? [];
$tpl->set_var('total_result', $total_result_final);
$tpl->process('LIST','total', TPL_APPEND);

// PHP 7 호환성: set_var에 리터럴 값 대신 변수 전달
$list_wdat_final = $list['wdat'] ?? '';
$tpl->set_var('list.wdat', $list_wdat_final);// dbinfo 정보 변수
// 템플릿 마무리 할당
// PHP 7 호환성: tie_var에 리터럴 값 대신 변수 전달
$total_tied = $total ?? null;
$tpl->tie_var('count.total', $total_tied);	// 게시판 각종 카운트

// 블럭 : 글쓰기
if(privAuth($dbinfo, 'priv_write')) $tpl->process('WRITE','write');
else $tpl->process('WRITE','nowrite');

// 마무리
// PHP 7 호환성: echoHtml에 리터럴 값 대신 변수 전달
$dbinfo_final = $dbinfo ?? [];
$SITE_final = $SITE ?? [];
$tpl->echoHtml($dbinfo_final, $SITE_final);
?>
