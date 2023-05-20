<?php
require './vendor/autoload.php';

function sendMail($data){
        $tmpPW = $data['tmpPW'];
        $email = $data['email'];
        $title = 'TODA에서 편지왔어요 :)';
        $content = '<html lang=\'ko\'>
                <head> <meta charset=\'utf-8\'/> </head>
                <body>
                    <h3>임시 비밀번호를 발급했어요! 이 비밀번호로 로그인하시고 마이페이지 -> 비밀번호 변경 에 들어가셔서 비밀번호를 변경해주세요!<br></h3>
                    <h1>'.$tmpPW.'</h1>
                </body>
            </html>';
        mailFunction(MAIL_USER,MAIL_PW,$email,$title,$content,$content);
}

function sendError($data){
    $title = $data['title'];
    $content = $data['content'];
    mailFunction(MAIL_USER,MAIL_PW,MY_EMAIL,$title,$content,$content);
}