<?php
    ini_set('display_errors', 0);
    header('Content-Type: application/json; charset=utf-8');
    date_default_timezone_set("Asia/Seoul");
    include '../db_mssql_sh.php';

    // ✅ 수정: 불필요한 '$p_userid;', 'use PhpMyAdmin\Index;' 제거
    // ✅ 수정: 미사용 변수 '$currentDate', '$currentTime' 제거

    $userid = $_POST['userid'] ?? 'admin';

    $uploadDir = 'C:\\xampp\\htdocs\\sellerhelper\\upload\\userstyleimage\\';
    $publicUrl = '/upload/userstyleimage';

    if (!isset($_FILES['image'])) {
        echo json_encode(['ok'=>false,'error'=>'image 파일이 없습니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ✅ 폴더 없으면 생성
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }

    $file = $_FILES['image'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['ok'=>false,'error'=>'업로드 오류: '.$file['error']], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ✅ 추가: 서버측 파일 크기 검증 (5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['ok'=>false,'error'=>'파일 크기는 5MB 이하만 가능합니다.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ✅ 추가: MIME 타입 서버측 검증 (확장자 위조 방지)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mimeType, $allowedMimes, true)) {
        echo json_encode(['ok'=>false,'error'=>'허용되지 않은 파일 형식입니다. (JPG, PNG, WEBP만 가능)'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /* 안전한 파일명 생성 */
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext === 'jpeg') $ext = 'jpg';

    $allowed = ['jpg','png','webp'];
    if (!in_array($ext, $allowed, true)) {
        echo json_encode(['ok'=>false,'error'=>'허용되지 않은 확장자'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $storedName = $userid.'_'.date('Ymd_His').'_'.bin2hex(random_bytes(6)).'.'.$ext;
    $destPath = $uploadDir . $storedName;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        echo json_encode(['ok'=>false,'error'=>'파일 저장 실패'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /* userdno 계산 */
    $updatetime = date("Y-m-d H:i:s");
    $sqlMax = "SELECT ISNULL(MAX(ai_ycma_userdno),0) AS maxno
               FROM ai_ycm_automate_userstyle
               WHERE ai_ycma_userid = ?";
    $stmt = sqlsrv_query($connsh, $sqlMax, [$userid]);

    if ($stmt === false) {
        $errors = sqlsrv_errors();
        $detail = $errors ? $errors[0]['message'] : '알 수 없는 오류';
        @unlink($destPath);
        echo json_encode(['ok'=>false,'error'=>'DB 조회 실패: '.$detail], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $styledno = ((int)$row['maxno']) + 1;

    // ✅ 수정: 첫 번째 쿼리 리소스 해제
    sqlsrv_free_stmt($stmt);

    /* INSERT */
    $sqlIns = "INSERT INTO ai_ycm_automate_userstyle
               (ai_ycma_userid, ai_ycma_userdno, ai_ycma_userimage, ai_ycma_inputtime)
               VALUES (?, ?, ?, CONVERT(datetime, ?, 120))";
    $stmtIns = sqlsrv_query($connsh, $sqlIns, [$userid, $styledno, $storedName, $updatetime]);

    if ($stmtIns === false) {
        $errors = sqlsrv_errors();
        $detail = $errors ? $errors[0]['message'] : '알 수 없는 오류';
        @unlink($destPath);
        echo json_encode(['ok'=>false,'error'=>'DB 저장 실패: '.$detail], JSON_UNESCAPED_UNICODE);
        exit;
    }

    sqlsrv_free_stmt($stmtIns);
    sqlsrv_close($connsh);

    /* JS가 바로 쓰는 JSON 응답 */
    echo json_encode([
        'ok' => true,
        'image_url' => $publicUrl.'/'.$storedName,
        'style_key' => 'custom_'.$styledno
    ], JSON_UNESCAPED_UNICODE);
    exit;
?>
