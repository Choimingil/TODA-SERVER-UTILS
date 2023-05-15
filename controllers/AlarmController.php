<?php
$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"), true);

try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
        * API Name : IOS 토큰 저장
        * 마지막 수정 날짜 : 21.05.19
        */
        case 'postTokenIOS':
            http_response_code(200);
            $result = $req; // 본 서버에서 보내는 토큰 배열
            checkNotification($result);
            break;

        /*
        * API Name : AOS 토큰 저장
        * 마지막 수정 날짜 : 21.05.19
        */
        case 'postTokenAOS':
            http_response_code(200);
            $result = $req; // 본 서버에서 보내는 토큰 배열
            checkNotificationAndroid($result);
            break;

       /*
        * API No. 7-3
        * API Name : 리마인드 알림 발송 API(IOS)
        * 마지막 수정 날짜 : 21.01.30
        */
        case 'sendRemindAlarm':
            http_response_code(200);
            $time = date("H:i");
            $pdo = pdoSqlConnect();
            $query = 'select distinct token from Notification where time = ? and isRemindAllowed=\'Y\' and status like 100;';
            $st = $pdo->prepare($query);
            $st->execute([$time]);
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $result = $st->fetchAll();
            $st = null;
            $pdo = null;

            $NotificationArray= array();
            $NotificationArray["body"] = remindAlarmText();
            $NotificationArray["title"] = "TODA";
            $NotificationArray["sound"] = "default";
            $NotificationArray["type"] = "getRemindAlarm";
            $dataArray = array('data' => 'getRemindAlarm');

            $sizePage = (int)sizeof($result)/1000;

            for($j=0;$j<=(int)$sizePage;$j++){
                $tokenArray = Array();

                for($i=1000*$j;$i<1000*($j+1);$i++){
                    if(empty($result[$i])) break;
                    array_push($tokenArray,$result[$i]['token']);
                }
                sendFcm($tokenArray,$NotificationArray,$dataArray,"IOS");
            }

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = '성공적으로 발송되었습니다.';
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
        * API No. 7-3-1
        * API Name : 리마인드 알림 발송 API(AOS)
        * 마지막 수정 날짜 : 22.12.23
        */
        case 'sendRemindAlarmAOS':
            http_response_code(200);
            $time = date("H:i");
            $pdo = pdoSqlConnect();
            $query = 'select distinct token from Notification where time = ? and isRemindAllowed=\'Y\' and status like 200;';
            $st = $pdo->prepare($query);
            $st->execute([$time]);
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $result = $st->fetchAll();
            $st = null;
            $pdo = null;

            $NotificationArray= array();
            $NotificationArray["body"] = remindAlarmText();
            $NotificationArray["title"] = "TODA";
            $NotificationArray["sound"] = "default";
            $NotificationArray["type"] = "getRemindAlarm";
            $dataArray = array('data' => 'getRemindAlarm');

            $sizePage = (int)sizeof($result)/1000;

            for($j=0;$j<=(int)$sizePage;$j++){
                $tokenArray = Array();

                for($i=1000*$j;$i<1000*($j+1);$i++){
                    if(empty($result[$i])) break;
                    array_push($tokenArray,$result[$i]['token']);
                }
                sendFcm($tokenArray,$NotificationArray,$dataArray,"Android");
            }

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = '성공적으로 발송되었습니다.';
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
        * API No. 7-4
        * API Name : 이벤트 및 공지 알림 발송 API
        * 마지막 수정 날짜 : 21.01.30
        */
        case 'sendEventAlarm':
            http_response_code(200);
            $pdo = pdoSqlConnect();
            $query = 'select distinct token, status from Notification where isEventAllowed=\'Y\' and status not like 0;';
            $st = $pdo->prepare($query);
            $st->execute();
            $st->setFetchMode(PDO::FETCH_ASSOC);
            $result = $st->fetchAll();
            $st = null;
            $pdo = null;

            $NotificationArray= array();
            $NotificationArray["body"] ="안녕하세요 투다입니다. 현재 버그로 인해 이미지가 올라가지 않는 현상이 발생했습니다. 최대한 빨리 오류를 해결하겠습니다. 불편을 끼쳐 드려 정말 죄송합니다ㅠㅠㅠ";
            $NotificationArray["title"] = "TODA";
            $NotificationArray["sound"] = "default";
            $NotificationArray["type"] = "getEventAlarm";
            $dataArray = array('data' => 'getEventAlarm');

            $sizePage = sizeof($result)/1000;

            for($j=0;$j<=(int)$sizePage;$j++){
                $tokenArray = Array();

                for($i=1000*$j;$i<1000*($j+1);$i++){
                    if(empty($result[$i])) break;
                    array_push($tokenArray,$result[$i]['token']);
                }
                sendFcm($tokenArray,$NotificationArray,$dataArray,"event");
            }

            $res['isSuccess'] = TRUE;
            $res['code'] = 100;
            $res['message'] = '성공적으로 발송되었습니다.';
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
        * API Name : 7-6. 알림 허용 여부 변경 API(3개)
        * 마지막 수정 날짜 : 21.05.19
        */
        case 'updateAlarmVer2':
            http_response_code(200);
            $result = $req; // 본 서버에서 보내는 토큰 배열
            updateAlarmVer2($result);
            break;

        /*
        * API Name : 7-8. 알림 시간 변경 API
        * 마지막 수정 날짜 : 21.05.19
        */
        case 'updateAlarmTime':
            http_response_code(200);
            $result = $req; // 본 서버에서 보내는 토큰 배열
            updateAlarmTime($result);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
