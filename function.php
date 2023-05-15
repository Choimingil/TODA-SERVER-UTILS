<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendFcm($registration_ids,$notification,$data,$device_type) {
    $url = 'https://fcm.googleapis.com/fcm/send';

    if($device_type == "Android"){
        $NotificationArray= array();
        $NotificationArray['body'] = $notification['body'];
        $NotificationArray['title'] = $notification['title'];
        $NotificationArray['data'] = $data['data'];
        $NotificationArray['type'] = $notification['type'];

        $fields = array(
            'registration_ids' => $registration_ids,
            'data' => $NotificationArray
        );
    } else {
        $fields = array(
            'registration_ids' => $registration_ids,
            'notification' => $notification,
            'data' => $data
        );
    }

    // Your Firebase Server API Key
    $headers = array(FCM_TOKEN,FCM_CONTENT_TYPE);
    // Open curl connection
    $ch = curl_init();
    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
//      echo json_encode($fields);
      echo $result;
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }
    curl_close($ch);
}

function codeToDeviceType($code){
    switch ($code){
        case 1:
            return "IOS";
        case 2:
            return "Android";
    }
}

// 여기는 기본 함수들
function getSQLErrorException($errorLogs, $e, $req)
{
    $res = (Object)Array();
    http_response_code(500);
    $res->code = 500;
    $res->message = "SQL Exception -> " . $e->getTraceAsString();
    echo json_encode($res);
    addErrorLogs($errorLogs, $res, $req);
    echo json_encode($res);

    $title = '투다 오류 발송';
    $content = json_encode($res);
    mailFunction(MAIL_USER,MAIL_PW,MY_EMAIL,$title,$content,$content);
}

function mailFunction($sendUser,$sendPW,$receiveUser,$title,$content,$altBody){
    // PHPMailer 선언
    $mail = new PHPMailer(true);
// 디버그 모드(production 환경에서는 주석 처리한다.)
//    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
// SMTP 서버 세팅
    $mail->isSMTP();
    try {
// 구글 smtp 설정
        $mail->Host = 'smtp.naver.com';
// SMTP 암호화 여부
        $mail->SMTPAuth = true;
// SMTP 포트
        $mail->Port = 465;
// SMTP 보안 프로토콜
        $mail->SMTPSecure = 'ssl';
// gmail 유저 아이디
        $mail->Username = 'withtoda';
//// gmail 패스워드
        $mail->Password = $sendPW;
// 인코딩 셋
        $mail->CharSet = 'utf-8';
        $mail->Encoding = "base64";
// 보내는 사람
        $mail->setFrom($sendUser, 'TODA');
// 받는 사람
        $mail->AddAddress($receiveUser);
// 본문 html 타입 설정
        $mail->isHTML(true);
// 제목
        $mail->Subject = $title;
// 본문 (HTML 전용)
        $mail->Body = $content;
// 본문 (non-HTML 전용)
        $mail->AltBody = $altBody;
        $mail->Send();
        return;
    } catch (phpmailerException $e) {
        echo $e->errorMessage();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function getTodayByTimeStamp()
{
    return date("Y-m-d H:i:s");
}

function checkAndroidBillingReceipt($credentialsPath, $token, $pid)
{

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsPath);
    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->addScope("https://www.googleapis.com/auth/androidpublisher");
    $client->setSubject("USER_ID.iam.gserviceaccount.com");


    $service = new Google_Service_AndroidPublisher($client);
    $optParams = array('token' => $token);

    return $service->purchases_products->get("PACKAGE_NAME", $pid, $token);
}


function addAccessLogs($accessLogs, $body)
{
    $logData["GET"] = $_GET;
    $logData["BODY"] = $body;
    $logData["REQUEST_METHOD"] = $_SERVER["REQUEST_METHOD"];
    $logData["REQUEST_URI"] = $_SERVER["REQUEST_URI"];
//    $logData["SERVER_SOFTWARE"] = $_SERVER["SERVER_SOFTWARE"];
    $logData["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];
    $logData["HTTP_USER_AGENT"] = $_SERVER["HTTP_USER_AGENT"];
    $accessLogs->addInfo(json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

}

function addErrorLogs($errorLogs, $res, $body)
{
    $req["GET"] = $_GET;
    $req["BODY"] = $body;
    $req["REQUEST_METHOD"] = $_SERVER["REQUEST_METHOD"];
    $req["REQUEST_URI"] = $_SERVER["REQUEST_URI"];
//    $req["SERVER_SOFTWARE"] = $_SERVER["SERVER_SOFTWARE"];
    $req["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];
    $req["HTTP_USER_AGENT"] = $_SERVER["HTTP_USER_AGENT"];

    $logData["REQUEST"] = $req;
    $logData["RESPONSE"] = $res;

    $errorLogs->addError(json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

//        sendDebugEmail("Error : " . $req["REQUEST_METHOD"] . " " . $req["REQUEST_URI"] , "<pre>" . json_encode($logData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "</pre>");
}


function getLogs($path)
{
    $fp = fopen($path, "r", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$fp) echo "error";

    while (!feof($fp)) {
        $str = fgets($fp, 10000);
        $arr[] = $str;
    }
    for ($i = sizeof($arr) - 1; $i >= 0; $i--) {
        echo $arr[$i] . "<br>";
    }
//        fpassthru($fp);
    fclose($fp);
}
