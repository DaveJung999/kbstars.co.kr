<?php
//=======================================================
// 설	명 : 회원 가입 폼(join.php)
// 책임자 : 박선민 , 검수: 05/01/25
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 05/01/25 박선민 마지막 수정
//=======================================================
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	// 실명확인 후 넘어왔다면
	if ($_SESSION['kidscamp'] == "2011kidscamp"){
		switch((int)$_POST['result']){
			case 1:
				break;
			case 2:
				back('이름과 주민번호가 잘못되었습니다.','/2011_kidscamp/apply.php');
				break;
			case 3:
				back('입력된 주민번화와 성명은 실명확인을 할 수 없습니다.','/2011_kidscamp/apply.php');
				break;
			default : 
				back('이름과 주민번호를 정확히 입력하여주세요','/2011_kidscamp/apply.php');
		}
		
		$_SESSION['kidscamp_result']	= $_POST['result'];
/*		$_SESSION['kidscamp_jumin1']	= $_POST['jumin1'];
		$_SESSION['kidscamp_jumin2']	= $_POST['jumin2'];
		$_SESSION['kidscamp_name']	= $_POST['name'];*/
		
		go_url('/2011_kidscamp/apply.php');
		
	} else {
		switch((int)$_POST['result']){
			case 1:
				break;
			case 2:
				back('이름과 주민번호가 잘못되었습니다.','/sjoin/');
				break;
			case 3:
				back('입력된 주민번화와 성명은 실명확인을 할 수 없습니다.','/sjoin/');
				break;
			default : 
				back('이름과 주민번호를 정확히 입력하여주세요','/sjoin/');
		}
		
		if(substr($_POST['jumin2'],0,1) < 3) 
			$_POST['jumin1'] = '19'.$_POST['jumin1'];
		else
			$_POST['jumin1'] = '20'.$_POST['jumin1'];
			
		$_SESSION['join_result']	= $_POST['result'];
/*		$_SESSION['join_jumin1']	= $_POST['jumin1'];
		$_SESSION['join_jumin2']	= $_POST['jumin2'];
		$_SESSION['join_name']	= $_POST['name'];*/
		
		/*
		if ($_SERVER['REMOTE_ADDR'] == '61.35.254.195'){
			echo substr($_POST['jumin2'],0,1);
			echo "<br><br>";
			print_r($_SESSION);
			echo "<br><br>";
			print_r($_POST);
			exit;
		}
		*/
		
		go_url('join.php');
	} 

?>
