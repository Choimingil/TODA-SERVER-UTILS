<?php
$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"), true);

try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
        * API Name : 오류 메일 발송
        * 마지막 수정 날짜 : 23.05.16
        */
        case 'sendError':
            http_response_code(200);
            sendError($req);
            echo 'success';
            break;

        /*
        * API No. 7-2
        * API Name : 임시 비밀번호 발급 API
        * 마지막 수정 날짜 : 20.08.21
        */
        case 'sendMail':
            http_response_code(200);
            sendMail($req);
            echo 'success';
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
