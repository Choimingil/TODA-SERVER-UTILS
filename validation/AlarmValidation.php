<?php

function isExistToken($userID){
    $pdo = pdoSqlConnect();
    $query = "select EXISTS(select * from Notification where userID = ? and status not like 0) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$userID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;$pdo = null;
    return intval($res[0]['exist']);
}

function isExistOnlyToken($token){
    $pdo = pdoSqlConnect();
    $query = "select EXISTS(select * from Notification where token = ? and status not like 0) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$token]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;$pdo = null;
    return intval($res[0]['exist']);
}

function isJustExistToken($id,$token){
    $pdo = pdoSqlConnect();
    $query = "select EXISTS(select * from Notification where userID = ? and token = ?) AS exist;";
    $st = $pdo->prepare($query);
    $st->execute([$id,$token]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;$pdo = null;
    return intval($res[0]['exist']);
}

function getTokenAllowedByDevice($userID,$token){
    $pdo = pdoSqlConnect();
    $query = 'select isAllowed from Notification where userID = ? and token = ?';
    $st = $pdo->prepare($query);
    $st->execute([$userID,$token]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;$pdo = null;
    if($res[0]['isAllowed']=='Y') return true;
    else return false;
}

function getRemindAllowedByDevice($userID,$token){
    $pdo = pdoSqlConnect();
    $query = 'select isRemindAllowed from Notification where userID = ? and token=?';
    $st = $pdo->prepare($query);
    $st->execute([$userID,$token]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;$pdo = null;
    if(empty($res)) return false;
    if($res[0]['isRemindAllowed']=='Y') return true;
    else return false;
}

function getEventAllowedByDevice($userID,$token){
    $pdo = pdoSqlConnect();
    $query = 'select isEventAllowed from Notification where userID = ? and token=?';
    $st = $pdo->prepare($query);
    $st->execute([$userID,$token]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;$pdo = null;
    if($res[0]['isEventAllowed']=='Y') return true;
    else return false;
}