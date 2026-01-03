
<?php
	$thisPath	= dirname(__FILE__);
	$table	= $SITE['th'] . "slist_player_league";

	// SQL문 where절 정리
	$sql_where=" 1 ";

	
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// 템플릿 기반 웹 페이지 제작
$tpl = new phemplate("stpl/yboard_album/");
$tpl->set_file('html',"precode.htm",1); // here 1 mean extract blocks

// Limit로 필요한 게시물만 읽음.
$rs_list = db_query("SELECT * from {$table} WHERE $sql_where ORDER BY rdate DESC");
if(!$total=db_count()) {	// 게시물이 하나도 없다면...
	$tpl->process('LIST', 'nolist');
}
else{
	$count['total']=$total;
	for($i=0; $i<$total; $i++){
		$list = db_array($rs_list);
		
			// 템플릿 YESRESULT 값들 입력
		$tpl->set_var('list',$list);

		$tpl->process('LIST','list',TPL_APPEND);
	} // end for (i)
} // end if (게시물이 있다면...)

// 템플릿 마무리 할당
// 마무리; ?>
