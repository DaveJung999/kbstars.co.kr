<?php
####################################################################################
//					준비
####################################################################################
if(preg_match("/:\/\//",$path)){echo "n@log error : Unknown path name";exit;}

if(!$path){$path=".";}
else{$path=@preg_replace("/^\/|\/$/","",$path);}
if($id && !$counter){$counter=$id;}

@include "$path/nalog_connect.php";
@include "$path/lib.php";
@include "$path/nalog_info.php";
$version=$nalog_info['version'];

####################################################################################
//					기본설정
####################################################################################
$set=@nalog_config("$counter");
if(!$set){echo "n@log error : Unknown counter name";exit;}
$total=$set['total'];

####################################################################################
//					시간설정
####################################################################################
if($set['time_zone2']){
	if($set['time_zone1']){$time_zone=$set['time_zone2']*3600;}
	else{$time_zone=$set['time_zone2']*3600*(-1);}
}else{
	$time_zone=0;
}

$time=time()+$time_zone;
$yy=date('Y',$time);
$mm=date('m',$time);$mm=preg_replace("/^0/","",$mm);
$dd=date('d',$time);$dd=preg_replace("/^0/","",$dd);
$hh=date('H',$time);$hh=preg_replace("/^0/","",$hh);
$week=date('w',$time);

####################################################################################
//					접속자설정
####################################################################################
$member_id=$set['member_id'];
$member_id=$_COOKIE[$member_id];
$ip=$_SERVER['REMOTE_ADDR'];

#######################################################################################################
//					현재접속자
#######################################################################################################
if($set['now_check']){
####################################################################################
//					정리
####################################################################################
	$temp=$time-$set['connecting'];
	$input="delete from nalog3_now_$counter where time<$temp";
	@mysqli_query($connect,$input);

####################################################################################
//					넣기
####################################################################################
	$query="insert into nalog3_now_$counter (ip,id,time) values ('$ip','$member_id','$time')";
	@mysqli_query($connect,$query);

####################################################################################
//					접속자구하기
####################################################################################
	$query="select count(*) from nalog3_now_$counter";
	$connector=@mysqli_fetch_array(@mysqli_query($connect,$query));
	$now=$connector["count(*)"];

####################################################################################
//					최고접속자
####################################################################################
	if($set['peak']<$now){
		$set['peak']=$now;
	}
}
#######################################################################################################
//					현재접속자끝
#######################################################################################################

####################################################################################
//					전체방문자넣기
####################################################################################
$input="update nalog3_config_$counter set peak='{$set['peak']}',total='$total' where no=1";
@mysqli_query($connect,$input);

####################################################################################
//					오늘방문자
####################################################################################
$query="select * from nalog3_data where yy='$yy' and mm='$mm' and dd='$dd' and counter='$counter'";
$today=@mysqli_fetch_array(@mysqli_query($connect,$query));
$today=$today['hit'];

####################################################################################
//					최대방문자저장
####################################################################################
$query="select max(hit) from nalog3_data where counter='$counter'";
$day_peak=@mysqli_fetch_array(@mysqli_query($connect,$query));
$day_peak=$day_peak[0];

####################################################################################
//					어제방문자
####################################################################################
$time=$time-(24*3600);
$yy=date('Y',$time);
$mm=date('m',$time);
$dd=date('d',$time);
$query="select * from nalog3_data where yy='$yy' and mm='$mm' and dd='$dd' and counter='$counter'";
$yester=@mysqli_fetch_array(@mysqli_query($connect,$query));
$yester=$yester['hit'];
if(!$yester){$yester=0;}

####################################################################################
//					스킨적용
####################################################################################
if(!$today){$today=0;}
if(!$yester){$yester=0;}
if(!$total){$total=0;}
if(!$now){$now=0;}
if(!$peak){$peak=0;}
if(!$day_peak){$day_peak=0;}

$peak=$set['peak'];
$today_image=$today_text=$today;
$yester_image=$yester_text=$yester;
$total_image=$total_text=$total;
$now_image=$now_text=$now;
$peak_image=$peak_text=$peak;
$day_peak_image=$day_peak_text=$day_peak;

for($i=0;$i<=9;$i++){$today_image=str_replace("$i","<img src=★★★/${i}.gif border=0 align=absmiddle ☆☆☆>",$today_image);}
for($i=0;$i<=9;$i++){$yester_image=str_replace("$i","<img src=★★★/${i}.gif border=0 align=absmiddle ☆☆☆>",$yester_image);}
for($i=0;$i<=9;$i++){$total_image=str_replace("$i","<img src=★★★/${i}.gif border=0 align=absmiddle ☆☆☆>",$total_image);}
for($i=0;$i<=9;$i++){$now_image=str_replace("$i","<img src=★★★/${i}.gif border=0 align=absmiddle ☆☆☆>",$now_image);}
for($i=0;$i<=9;$i++){$peak_image=str_replace("$i","<img src=★★★/${i}.gif border=0 align=absmiddle ☆☆☆>",$peak_image);}
for($i=0;$i<=9;$i++){$day_peak_image=str_replace("$i","<img src=★★★/${i}.gif border=0 align=absmiddle ☆☆☆>",$day_peak_image);}

$today_image=@str_replace("★★★",$path."/skin/{$set['skin']}",$today_image);
$yester_image=@str_replace("★★★",$path."/skin/{$set['skin']}",$yester_image);
$total_image=@str_replace("★★★",$path."/skin/{$set['skin']}",$total_image);
$now_image=@str_replace("★★★",$path."/skin/{$set['skin']}",$now_image);
$peak_image=@str_replace("★★★",$path."/skin/{$set['skin']}",$peak_image);
$day_peak_image=@str_replace("★★★",$path."/skin/{$set['skin']}",$day_peak_image);

$alt="alt='n@log analyzer $version' onclick=window.open(\"$path/admin_counter.php?counter=$counter\") style=cursor:hand";
$today_image=@str_replace("☆☆☆",$alt,$today_image);
$yester_image=@str_replace("☆☆☆",$alt,$yester_image);
$total_image=@str_replace("☆☆☆",$alt,$total_image);
$now_image=@str_replace("☆☆☆",$alt,$now_image);
$peak_image=@str_replace("☆☆☆",$alt,$peak_image);
$day_peak_image=@str_replace("☆☆☆",$alt,$day_peak_image);

####################################################################################
//					스킨패턴적용
####################################################################################
if($set['skin_check']){
	$skin="$path/skin/{$set['skin']}";
	include"$skin/skin.php";
}

@mysqli_close($connect);
?>