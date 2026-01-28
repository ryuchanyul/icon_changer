<?php
    ini_set('display_errors', 0);
    header('Content-Type: application/json; charset=utf-8');
    date_default_timezone_set("Asia/Seoul");
    include '../db_mssql_sh.php';

    $userid = $_GET['userid'] ?? $_POST['userid'] ?? 'admin';
    $publicUrl = '/upload/userstyleimage';

    $sql = "SELECT ai_ycma_userdno, ai_ycma_userimage
            FROM ai_ycm_automate_userstyle
            WHERE ai_ycma_userid = ?
            ORDER BY ai_ycma_userdno ASC";
    $stmt = sqlsrv_query($connsh, $sql, [$userid]);

    if ($stmt === false) {
        $errors = sqlsrv_errors();
        $detail = $errors ? $errors[0]['message'] : '알 수 없는 오류';
        echo json_encode(['ok'=>false, 'error'=>'DB 조회 실패: '.$detail], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $styles = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $styles[] = [
            'style_key' => 'custom_' . $row['ai_ycma_userdno'],
            'image_url' => $publicUrl . '/' . $row['ai_ycma_userimage']
        ];
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($connsh);

    echo json_encode([
        'ok'     => true,
        'styles' => $styles
    ], JSON_UNESCAPED_UNICODE);
    exit;
?>
