<?php
$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"), true);

try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
        * API No. 12-0
        * API Name : 다이어리 친구 요청 보내기 API
        * 마지막 수정 날짜 : 20.09.03
        */
        case 'sendDiaryFriend':
            http_response_code(200);
            sendDiaryFriend($req);
            break;

        /*
        * API No. 12-1
        * API Name : 다이어리 친구 요청 수락 API
        * 마지막 수정 날짜 : 20.09.03
        */
        case 'acceptDiaryFriend':
            http_response_code(200);
            acceptDiaryFriend($req);
            break;

        /*
        * API No. 16-2
        * API Name : 게시물 작성 API(날짜 및 폰트 추가)
        * 마지막 수정 날짜 : 21.05.11
        */
        case 'pushPost':
            http_response_code(200);
            pushPost($req);
            break;

        /*
        * API No. 28
        * API Name : 좋아요 API
        * 마지막 수정 날짜 : 20.09.11
        */
        case 'pushLike':
            http_response_code(200);
            pushLike($req);
            break;

        /*
        * API No. 30-0
        * API Name : 댓글 작성 API
        * 마지막 수정 날짜 : 20.09.11
        */
        case 'pushComment':
            http_response_code(200);
            pushComment($req);
            break;

        /*
        * API No. 30-1
        * API Name : 대댓글 작성 API
        * 마지막 수정 날짜 : 20.09.11
        */
        case 'pushReComment':
            http_response_code(200);
            pushReComment($req);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
