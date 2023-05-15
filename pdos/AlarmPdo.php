<?php
require './vendor/autoload.php';
require './validation/AlarmValidation.php';

function remindAlarmText(){
//* 리마인드 알림 디폴트 :
//- 오늘 하루를 투다에 기록해보세요 ( ˘ ³˘)♥
//- ((속닥속닥)) 우리 투다에서 비밀얘기해요!
//- 우리만의 이야기로 하루를 채워나가요 :)
//- 수고 많았어요 오늘도 :) 오늘 하루를 투다에 기록해보세요
//
//* 특정 시간에 보내지는 알림 : 일기 쓰기 좋은 시간…오전/오후 00시…☆
//* 밤 : 굿나잇♪♬ 오늘 하루는 어땠나요?
//
//* 일정 기간동안 안들어왔을때 보내는 알림 :
//- 이번주에 어떤 일이 있었나요? 투다한테 알려주세요!
//- 똑똑똑….! 일기 쓸 시간이에요
//- 이번 일기는 작심삼일로 끝나지 않기로 했잖아요ㅠㅠ

    $text1 = '오늘 하루를 투다에 기록해보세요 ( ˘ ³˘)♥';
    $text2 = '((속닥속닥)) 우리 투다에서 비밀얘기해요!';
    $text3 = '우리만의 이야기로 하루를 채워나가요 :)';
    $text4 = '수고 많았어요 오늘도 :) 오늘 하루를 투다에 기록해보세요';

    $text = array();
    array_push($text,$text1);
    array_push($text,$text2);
    array_push($text,$text3);
    array_push($text,$text4);

    $num = mt_rand(0,3);
    return $text[$num];
}

function checkNotification($data){
    $pdo = pdoSqlConnect();
    if(isExistOnlyToken($data['token'])){
        if(isJustExistToken($data['id'],$data['token'])){
            $query = 'UPDATE Notification SET status = 0 WHERE userID in (select * from (select userID from Notification where token = ?) tmp) and token = ?;
                UPDATE Notification SET status = 100, isAllowed = ?, isRemindAllowed = ?, isEventAllowed = ? WHERE userID = ? and token = ?;';
            $st = $pdo->prepare($query);
            $st->execute([$data['token'],$data['token'],$data['isAllowed'],$data['isAllowed'],$data['isAllowed'],$data['id'],$data['token']]);
            $st = null;
        }
        else{
            $query = 'UPDATE Notification SET status = 0 WHERE userID in (select * from (select userID from Notification where token = ?) tmp) and token = ?;
                INSERT INTO Notification (userID, token, isAllowed, isRemindAllowed, isEventAllowed) VALUES (?,?,?,?,?);';
            $st = $pdo->prepare($query);
            $st->execute([$data['token'],$data['token'],$data['id'], $data['token'], $data['isAllowed'], $data['isAllowed'], $data['isAllowed']]);
            $st = null;
        }
    }
    else{
        if(isJustExistToken($data['id'],$data['token'])){
            $query = "UPDATE Notification SET status = 100, isAllowed = ?, isRemindAllowed = ?, isEventAllowed = ? WHERE userID = ? and token = ?;";
            $st = $pdo->prepare($query);
            $st->execute([$data['isAllowed'],$data['isAllowed'],$data['isAllowed'],$data['id'], $data['token']]);
            $st = null;
        }
        else{
            $query = "INSERT INTO Notification (userID, token, isAllowed, isRemindAllowed, isEventAllowed) VALUES (?,?,?,?,?);";
            $st = $pdo->prepare($query);
            $st->execute([$data['id'], $data['token'], $data['isAllowed'], $data['isAllowed'], $data['isAllowed']]);
            $st = null;
        }
    }
    $pdo = null;
    $res['isSuccess'] = TRUE;
    $res['code'] = 100;
    $res['message'] = '토큰이 저장되었습니다.';
    echo json_encode($res, JSON_NUMERIC_CHECK);
}

function checkNotificationAndroid($data){
    $pdo = pdoSqlConnect();
    if(isExistOnlyToken($data['token'])){
        if(isJustExistToken($data['id'],$data['token'])){
            $query = 'UPDATE Notification SET status = 0 WHERE userID in (select * from (select userID from Notification where token = ?) tmp) and token = ?;
                UPDATE Notification SET status = 200, isAllowed = ?, isRemindAllowed = ?, isEventAllowed = ? WHERE userID = ? and token = ?;';
            $st = $pdo->prepare($query);
            $st->execute([$data['token'],$data['token'],$data['isAllowed'],$data['isAllowed'],$data['isAllowed'],$data['id'],$data['token']]);
            $st = null;
        }
        else{
            $query = 'UPDATE Notification SET status = 0 WHERE userID in (select * from (select userID from Notification where token = ?) tmp) and token = ?;
                INSERT INTO Notification (userID, token, isAllowed, isRemindAllowed, isEventAllowed,status) VALUES (?,?,?,?,?,200);';
            $st = $pdo->prepare($query);
            $st->execute([$data['token'],$data['token'],$data['id'], $data['token'], $data['isAllowed'], $data['isAllowed'], $data['isAllowed']]);
            $st = null;
        }
    }
    else{
        if(isJustExistToken($data['id'],$data['token'])){
            $query = "UPDATE Notification SET status = 200, isAllowed = ?, isRemindAllowed = ?, isEventAllowed = ? WHERE userID = ? and token = ?;";
            $st = $pdo->prepare($query);
            $st->execute([$data['isAllowed'],$data['isAllowed'],$data['isAllowed'],$data['id'], $data['token']]);
            $st = null;
        }
        else{
            $query = "INSERT INTO Notification (userID, token, isAllowed, isRemindAllowed, isEventAllowed,status) VALUES (?,?,?,?,?,200);";
            $st = $pdo->prepare($query);
            $st->execute([$data['id'], $data['token'], $data['isAllowed'], $data['isAllowed'], $data['isAllowed']]);
            $st = null;
        }
    }
    $pdo = null;
    $res['isSuccess'] = TRUE;
    $res['code'] = 100;
    $res['message'] = '토큰이 저장되었습니다.';
    echo json_encode($res, JSON_NUMERIC_CHECK);
}

function updateAlarmVer2($data){
    $pdo = pdoSqlConnect();
    if(!isExistToken($data['id'])){
        $res['isSuccess'] = FALSE;
        $res['code'] = 102;
        $res['message'] = '알림 토큰이 저장되어있지 않습니다.';
        echo json_encode($res, JSON_NUMERIC_CHECK);
        return;
    }
    switch ($data['alarmType']){
        case 0:
            if(getTokenAllowedByDevice($data['id'],$data['fcmToken'])){
                $query = 'UPDATE Notification SET isAllowed = ? WHERE userID = ? and token=?';
                $st = $pdo->prepare($query);
                $st->execute(['N',$data['id'],$data['fcmToken']]);
                $st = null;
                $pdo = null;
                $res['isSuccess'] = TRUE;
                $res['code'] = 200;
                $res['message'] = '알림이 해제되었습니다.';
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                $query = 'UPDATE Notification SET isAllowed = ? WHERE userID = ? and token=?';
                $st = $pdo->prepare($query);
                $st->execute(['Y',$data['id'],$data['fcmToken']]);
                $st = null;
                $pdo = null;
                $res['isSuccess'] = TRUE;
                $res['code'] = 100;
                $res['message'] = '알림이 허용되었습니다.';
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;
        case 1:
            if(getRemindAllowedByDevice($data['id'],$data['fcmToken'])){
                $query = 'UPDATE Notification SET isRemindAllowed = ? WHERE userID = ? and token=?';
                $st = $pdo->prepare($query);
                $st->execute(['N',$data['id'],$data['fcmToken']]);
                $st = null;
                $pdo = null;
                $res['isSuccess'] = TRUE;
                $res['code'] = 200;
                $res['message'] = '알림이 해제되었습니다.';
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                $query = 'UPDATE Notification SET isRemindAllowed = ? WHERE userID = ? and token=?';
                $st = $pdo->prepare($query);
                $st->execute(['Y',$data['id'],$data['fcmToken']]);
                $st = null;
                $pdo = null;
                $res['isSuccess'] = TRUE;
                $res['code'] = 100;
                $res['message'] = '알림이 허용되었습니다.';
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;
        case 2:
            if(getEventAllowedByDevice($data['id'],$data['fcmToken'])){
                $query = 'UPDATE Notification SET isEventAllowed = ? WHERE userID = ? and token=?';
                $st = $pdo->prepare($query);
                $st->execute(['N',$data['id'],$data['fcmToken']]);
                $st = null;
                $pdo = null;
                $res['isSuccess'] = TRUE;
                $res['code'] = 200;
                $res['message'] = '알림이 해제되었습니다.';
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            else{
                $query = 'UPDATE Notification SET isEventAllowed = ? WHERE userID = ? and token=?';
                $st = $pdo->prepare($query);
                $st->execute(['Y',$data['id'],$data['fcmToken']]);
                $st = null;
                $pdo = null;
                $res['isSuccess'] = TRUE;
                $res['code'] = 100;
                $res['message'] = '알림이 허용되었습니다.';
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;
    }
}

function updateAlarmTime($data){
    if(!isExistToken($data['id'])){
        $res['isSuccess'] = FALSE;
        $res['code'] = 102;
        $res['message'] = '알림 토큰이 저장되어있지 않습니다.';
        echo json_encode($res, JSON_NUMERIC_CHECK);
        return;
    }
    if(!getRemindAllowedByDevice($data['id'],$data['fcmToken'])){
        $res['isSuccess'] = FALSE;
        $res['code'] = 102;
        $res['message'] = '리마인드 알림이 거절된 상태 혹은 토큰이 존재하지 않은 상태입니다.';
        echo json_encode($res, JSON_NUMERIC_CHECK);
        return;
    }

    $pdo = pdoSqlConnect();
    $query = 'UPDATE Notification SET time = ? WHERE userID = ? and token=?';
    $st = $pdo->prepare($query);
    $st->execute([$data['time'],$data['id'],$data['fcmToken']]);
    $st = null;
    $pdo = null;
    $res['isSuccess'] = TRUE;
    $res['code'] = 100;
    $res['message'] = '시간이 변경되었습니다.';
    echo json_encode($res, JSON_NUMERIC_CHECK);
}