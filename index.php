<?php
require './pdos/DatabasePdo.php';
require './pdos/MailPdo.php';
require './pdos/AlarmPdo.php';
require './pdos/PushMsgPdo.php';
require './function.php';

require './vendor/autoload.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

date_default_timezone_set('Asia/Seoul');
//ini_set('default_charset', 'utf8mb4');

//에러출력하게 하는 코드
error_reporting(E_ALL); ini_set("display_errors", 1);

//Main Server API
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('POST', '/mail/error', ['MailController', 'sendError']);                                               //오류 메일 발송
    $r->addRoute('POST', '/mail/pw', ['MailController', 'sendMail']);                                                   //7-2. 임시 비밀번호 발급 API

    $r->addRoute('POST', '/alarm/notification/ios', ['AlarmController', 'postTokenIOS']);                               // IOS 토큰 저장
    $r->addRoute('POST', '/alarm/notification/aos', ['AlarmController', 'postTokenAOS']);                               // AOS 토큰 저장
    $r->addRoute('POST', '/alarm/remind', ['AlarmController', 'sendRemindAlarm']);                                      //7-3. 리마인드 알림 발송 API IOS
    $r->addRoute('POST', '/alarm/remind/aos', ['AlarmController', 'sendRemindAlarmAOS']);                               //7-3-1. 리마인드 알림 발송 API AOS
    $r->addRoute('POST', '/alarm/event', ['AlarmController', 'sendEventAlarm']);                                        //7-4. 이벤트 공지 알림 발송 API
    $r->addRoute('POST', '/alarm/ver2', ['AlarmController', 'updateAlarmVer2']);                                        //7-6. 알림 허용 여부 변경 API(3개)
    $r->addRoute('POST', '/alarm/time', ['AlarmController', 'updateAlarmTime']);                                        //7-8. 알림 시간 변경 API

    $r->addRoute('POST', '/push/diary/send', ['PushMsgController', 'sendDiaryFriend']);                                 //12-0. 친구 초대 알림 발송
    $r->addRoute('POST', '/push/diary/accept', ['PushMsgController', 'acceptDiaryFriend']);                             //12-1. 친구 수락 알림 발송
    $r->addRoute('POST', '/push/post', ['PushMsgController', 'pushPost']);                                              //16. 게시물 작성 알림 발송
    $r->addRoute('POST', '/push/like', ['PushMsgController', 'pushLike']);                                              //28. 좋아요 알림 발송
    $r->addRoute('POST', '/push/comment', ['PushMsgController', 'pushComment']);                                        //30-0. 댓글 작성 알림 발송
    $r->addRoute('POST', '/push/recomment', ['PushMsgController', 'pushReComment']);                                    //30-1. 대댓글 작성 알림 발송
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// 로거 채널 생성
$accessLogs = new Logger('ACCESS_LOGS');
$errorLogs = new Logger('ERROR_LOGS');
// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$accessLogs->pushHandler(new StreamHandler('logs/access.log', Logger::INFO));
$errorLogs->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo "No URL(404)";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        echo "Wrong Method.(405)";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        switch ($routeInfo[1][0]) {
            case 'MailController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/MailController.php';
                break;

            case 'AlarmController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/AlarmController.php';
                break;

            case 'PushMsgController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/PushMsgController.php';
                break;
        }

        break;
}
