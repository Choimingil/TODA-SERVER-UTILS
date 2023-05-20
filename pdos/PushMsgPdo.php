<?php

// 다이어리 초대 & 수락

function sendDiaryFriend($data){
    $user = $data['user'];
    $tokenIOS = array();
    $tokenAOS = array();
    $receivename = ''; $sendname = ''; $usercode = ''; $diaryname = '';



    foreach($user as $i=>$value){
        if($data['userCode']==$user[$i]['code']){
            $receivename = $user[$i]['name'];
            $diaryname = $user[$i]['diaryName'];
            if(codeToDeviceType($user[$i]['status']/100)=="Android") array_push($tokenAOS,$user[$i]['token']);
            else array_push($tokenIOS,$user[$i]['token']);
        }
        else{
            $sendname = $user[$i]['name'];
            $usercode = $user[$i]['code'];
        }
    }

    $NotificationArray= array();
    $NotificationArray['body'] = $sendname."님(".$usercode.")이 ".$diaryname."에 초대합니다:)";
    $NotificationArray['title'] = "To. ".$receivename."님";
    $NotificationArray['sound'] = 'default';
    $NotificationArray['type'] = 'addDiaryFriend';
    $dataArray = array();
    $dataArray["data"] = (int)$data['pathVar'];

    sendFcm($tokenIOS, $NotificationArray,$dataArray,"IOS");
    sendFcm($tokenAOS, $NotificationArray,$dataArray,"Android");
}

function acceptDiaryFriend($data){
    $user = $data['user'];
    $tokenIOS = array();
    $tokenAOS = array();
    $receivename = ''; $sendname = ''; $usercode = ''; $diaryname = '';

    foreach($user as $i=>$value){
        if($data['userCode']==$user[$i]['code']){
            $receivename = $user[$i]['name'];
            $diaryname = $user[$i]['diaryName'];
            if(codeToDeviceType($user[$i]['status']/100)=="Android") array_push($tokenAOS,$user[$i]['token']);
            else array_push($tokenIOS,$user[$i]['token']);
        }
        else{
            $sendname = $user[$i]['name'];
            $usercode = $user[$i]['code'];
        }
    }

    $NotificationArray= array();
    $NotificationArray['body'] = $sendname."님(".$usercode.")이 ".$diaryname."초대에 수락하셨습니다:)";
    $NotificationArray['title'] = "To. ".$receivename."님";
    $NotificationArray['sound'] = 'default';
    $NotificationArray['type'] = 'acceptDiaryFriend';
    $dataArray = array();
    $dataArray['data'] = '';

    sendFcm($tokenIOS, $NotificationArray,$dataArray,"IOS");
    sendFcm($tokenAOS, $NotificationArray,$dataArray,"Android");

    $NotificationArray = null;
    $dataArray = null;
}

function pushPost($data){
    $sendname = $data['sendname'];
    $postID = $data['postID'];
    $receive = $data['receive'];

    $tokenIOS = array();
    $tokenAOS = array();

    $NotificationArray= array(
        'body' => $sendname."님이 일기를 남겼습니다:)",
        'title' => "투다에서 알림이 왔어요!",
        'sound' => 'default',
        'type' => 'addPost'
    );

    $tmp = array();
    $tmp['diaryID'] = (int)$data['diary'];
    $tmp['postID'] = (int)$postID;
    $dataArray = array('data' => $tmp);

    foreach($receive as $i=>$value){
        if(codeToDeviceType($receive[$i]['status']/100)=="Android") array_push($tokenAOS,$receive[$i]['token']);
        else array_push($tokenIOS,$receive[$i]['token']);
    }

    sendFcm($tokenIOS, $NotificationArray,$dataArray,"IOS");
    sendFcm($tokenAOS, $NotificationArray,$dataArray,"Android");
}

function pushLike($data){
    $tokenIOS = array();
    $tokenAOS = array();
    $receive = $data['receive'];

    $sendname = $data['sendname'];
    $receivename = $receive[0]['name'];
    foreach ($receive as $i=>$value) {
        if(codeToDeviceType($receive[$i]['status']/100)=="Android") array_push($tokenAOS,$receive[$i]['token']);
        else array_push($tokenIOS,$receive[$i]['token']);
    }

    // 알림 data에 보낼 데이터
    $tmp = array();
    $tmp['diaryID'] = (int)$receive[0]['diaryID'];
    $tmp['postID'] = (int)$data['pathVar'];

    $NotificationArray= array();
    $NotificationArray['body'] = $sendname."님이 ".$receivename."님의 일기를 좋아합니다:)";
    $NotificationArray['title'] = "To. ".$receivename."님";
    $NotificationArray['sound'] = 'default';
    $NotificationArray['type'] = 'postLike';
    $dataArray = array();
    $dataArray['data'] = $tmp;

    sendFcm($tokenIOS, $NotificationArray,$dataArray,"IOS");
    sendFcm($tokenAOS, $NotificationArray,$dataArray,"Android");
}

function pushComment($data){
    $commenttext = $data['reply'];
    $sendname = $data['sendname'];
    $result = $data['result'];

    $tokenIOS = array();
    $tokenAOS = array();
    foreach ($result as $i=>$value) {
        if(codeToDeviceType($result[$i]['status']/100)=="Android") array_push($tokenAOS,$result[$i]['token']);
        else array_push($tokenIOS,$result[$i]['token']);
    }

    $NotificationArray= array();
    $NotificationArray['body'] = $commenttext;
    $NotificationArray['title'] = $sendname."님이 댓글을 남겼습니다:)";
    $NotificationArray['sound'] = 'default';
    $NotificationArray['type'] = 'postComment';
    $dataArray = array();
    $dataArray['data'] = (int)$data['post'];

    sendFcm($tokenIOS, $NotificationArray,$dataArray,"IOS");
    sendFcm($tokenAOS, $NotificationArray,$dataArray,"Android");
}

function pushReComment($data){
    $commenttext = $data['reply'];
    $sendname = $data['sendname'];
    $result = $data['result'];

    $tokenIOS = array();
    $tokenAOS = array();

    $NotificationArray= array(
        'body' => $commenttext,
        'title' => $sendname."님이 대댓글을 남겼습니다:)",
        'sound' => 'default',
        'type' => 'postComment'
    );

    $dataArray = array('data' => (int)$data['post']);

    foreach($result as $i=>$value){
        //알림 데이터 채우기
        if(codeToDeviceType($result[$i]['status']/100)=="Android") array_push($tokenAOS,$result[$i]['token']);
        else array_push($tokenIOS,$result[$i]['token']);
    }
    
    sendFcm($tokenIOS, $NotificationArray,$dataArray,"IOS");
    sendFcm($tokenAOS, $NotificationArray,$dataArray,"Android");
}