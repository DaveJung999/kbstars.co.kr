<?php
####################################################################################
//					준비
####################################################################################
if(!@include"nalog_connect.php"){echo"<script language='javascript'>alert('Please install n@log first :)')</script>
<meta http-equiv='refresh' content='0;url=install.php'>";exit;}
include "lib.php";
if(!@include "nalog_language.php"){nalog_go("install.php");}
if(!@include"language/$language/language.php"){nalog_go("install.php");}
####################################################################################
//					체크
####################################################################################
nalog_admin_check($_SERVER['HTTP_REFERER']);
####################################################################################
//					생성
####################################################################################
if($mode=="make"){
	if($new_board==""){nalog_error($lang['counter_create_error_name']);}
	$new_board=trim($new_board);
	if(!$new_board){nalog_error($lang['counter_create_error_name']);}
	if(preg_match("/\s/",$new_board)){nalog_error($lang['counter_create_error_blank']);}
	nalog_chk_str($new_board,0,$lang['counter_create_error_char'],"");
	$tables=nalog_list_bd();
	$total=count($tables);
	for($i=0;$i<$total;$i++){
		$board_name=str_replace("nalog3_counter_","",$tables[$i]);
		if($board_name==$new_board){nalog_error($lang['counter_create_error_exist']);}
	}
	include"nalog_schema.php";
####################################################################################
//					-_-a
####################################################################################
	echo"
	<script language='javascript'>
	window.open('example.php?counter=$new_board','ex');
	</script>
	";
}
####################################################################################
//					삭제
####################################################################################
if($mode=="drop"){
	nalog_drop("nalog3_counter_".$new_board);
	nalog_drop("nalog3_config_".$new_board);
	nalog_drop("nalog3_now_".$new_board);
	nalog_drop("nalog3_log_".$new_board);
	nalog_drop("nalog3_dlog_".$new_board);
	$query="delete from nalog3_data where counter='$new_board'";
	$result=@mysqli_query($connect,$query);
	$query="delete from nalog3_os where counter='$new_board'";
	$result=@mysqli_query($connect,$query);
}
####################################################################################
//					비우기
####################################################################################
if($mode=="del"){
	nalog_drop("nalog3_counter_".$new_board);
	nalog_drop("nalog3_log_".$new_board);
	nalog_drop("nalog3_dlog_".$new_board);
	include"nalog_schema.php";
	$query="delete from nalog3_data where counter='$new_board'";
	$result=@mysqli_query($connect,$query);
	$query="delete from nalog3_os where counter='$new_board'";
	$result=@mysqli_query($connect,$query);
}
####################################################################################
//					이동
####################################################################################
mysqli_close($connect);
nalog_go("admin.php");
?>