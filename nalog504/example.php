<?php
$path = "./";
include "nalog_viewer.php";

if (!nalog_admin_check4()) {
	nalog_go("http://navyism.com");
}

// 언어 파일 로드
if (!@include "nalog_language.php") {
	nalog_go("install.php");
}
if (!@include "language/$language/language.php") {
	nalog_go("install.php");
}

echo $lang['head'];

?>

<?php
// GD 지원 여부 확인 후 테스트 이미지 생성
if (function_exists("imagecreate")) {
	$image = @imagecreate(50, 50); // 이미지 사이즈 50x50
	$color_black = @imagecolorallocate($image, 0x00, 0x00, 0x00); // 검정색
	$color_white = @imagecolorallocate($image, 0xFF, 0xFF, 0xFF); // 흰색

	@imagearc($image, 25, 25, 45, 45, 0, 360, $color_white); // 원 그리기
	@imagefill($image, 25, 25, $color_white); // 내부 채우기

	@imagejpeg($image, "test_gd.jpg"); // 이미지 저장
	@imagedestroy($image); // 메모리 해제

	$test_gd = "test_gd.jpg";
} else {
	$test_gd = "nalog_image/test_gd.jpg";
}
?>

<br><br>

<?php
// 언어별 예제 파일 include
include "language/$language/example.php";
?>

<br><br>
</body>
</html>
