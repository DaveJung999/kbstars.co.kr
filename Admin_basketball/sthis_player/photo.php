
<?php
function board_up($db_name,$cuid){
	global $SITE, $GAMEINFO, $PlayerCateBoard, $DEBUG; // global 변수 추.
	
	$oldGET = $_GET; 
	$_GET = array( db =>	"$db_name", // 게시물 db 
						cut_length =>	40, // 보일 게시물 수 
						limitno =>	0,
						limitrows =>	8,
						sql_where =>	" 1 ",
						sql_order =>	" rdate DESC ",
						skin =>	"player_photo", // 게시판 스킨 
						html_headpattern => "no" ,// Site 해더 넣지 않음 
						cateuid => "$cuid"
						); 
	include("{$_SERVER['DOCUMENT_ROOT']}/sboard/list.php"); 
	$_GET = $oldGET; 
} 

?>
 
<div id="page_content" style="position:absolute;left:0;top:0;width:100%"> 
<?php board_up("photo", $catuid ) ; ?></div>

<script language="JavaScript1.2"> 
	function iframe_reset(){ 
		dataobj=document.all? document.all.page_content : document.getElementById("page_content") 
		
		dataobj.style.top=0 
		dataobj.style.left=0 

		pagelength=dataobj.offsetHeight 
		pagewidth=dataobj.offsetWidth 

		parent.document.all.iframe_main.height=pagelength 
		parent.document.all.iframe_main.width=pagewidth 
	} 
	window.onload=iframe_reset 
</script> 