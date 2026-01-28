<?php
    ini_set('display_errors', 0);
    header('Content-Type: application/json; charset=utf-8');
    date_default_timezone_set("Asia/Seoul");
    include '../db_mssql_sh.php';

    $userid  = $_POST['userid']  ?? '';
    $userdno = $_POST['userdno'] ?? '';

    if (!$userid || !$userdno) {
        echo json_encode(['ok'=>false, 'error'=>'필수 파라미터 누락'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $uploadDir = 'C:\\xampp\\htdocs\\sellerhelper\\upload\\userstyleimage\\';

    // 삭제 전 파일명 조회
    $sqlSel = "SELECT ai_ycma_userimage FROM ai_ycm_automate_userstyle
               WHERE ai_ycma_userid = ? AND ai_ycma_userdno = ?";
    $stmt = sqlsrv_query($connsh, $sqlSel, [$userid, (int)$userdno]);

    if ($stmt === false) {
        $errors = sqlsrv_errors();
        $detail = $errors ? $errors[0]['message'] : '알 수 없는 오류';
        echo json_encode(['ok'=>false, 'error'=>'DB 조회 실패: '.$detail], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);

    if (!$row) {
        echo json_encode(['ok'=>false, 'error'=>'해당 스타일을 찾을 수 없습니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // DB 삭제
    $sqlDel = "DELETE FROM ai_ycm_automate_userstyle
               WHERE ai_ycma_userid = ? AND ai_ycma_userdno = ?";
    $stmtDel = sqlsrv_query($connsh, $sqlDel, [$userid, (int)$userdno]);

    if ($stmtDel === false) {
        $errors = sqlsrv_errors();
        $detail = $errors ? $errors[0]['message'] : '알 수 없는 오류';
        echo json_encode(['ok'=>false, 'error'=>'DB 삭제 실패: '.$detail], JSON_UNESCAPED_UNICODE);
        exit;
    }

    sqlsrv_free_stmt($stmtDel);
    sqlsrv_close($connsh);

    // 파일 삭제
    $filePath = $uploadDir . $row['ai_ycma_userimage'];
    if (file_exists($filePath)) {
        @unlink($filePath);
    }

    echo json_encode(['ok'=>true], JSON_UNESCAPED_UNICODE);
    exit;
?>
