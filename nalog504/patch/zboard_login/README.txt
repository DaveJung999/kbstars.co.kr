###################################################################################
/*
                            zeroboard 4 login patch
  			(Korean zeroboard 4 user only)
*/
###################################################################################

■ zeroboard 4 login patch ?

zeroboard 4의 로그인 방식 문제로 인해, n@log에서 제대로 회원 ID를 파악하지 못하는
문제점을 해결하기 위한 패치 파일 입니다.
위 패치 파일을 설치 하기 전에 zeroboard폴더의 login_check.php파일을 백업 해 두시기 바랍니다.


■ zeroboard 4 login patch 설치방법

먼저 zeroboard 4가 설치된 폴더에 \patch\zboard_login\login_check.php파일을 복사(overwrite)하고,
n@log설정에서 [회원 구분 쿠키 이름]을 na3_member로 설정 한 후 저장 하시면 됩니다.
