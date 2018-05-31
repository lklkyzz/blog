<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 成功返回结果
function success_return($data="") {
    $ret = 0;
    $msg = 'ok';
    output_json($ret, $msg, $data);
    exit();
}

// 缺少参数返回结果
function miss_params() {
    $ret = -1;
    $msg = 'missing parameter';
    output_json($ret, $msg);
    exit();
}

// 认证令牌出错
function token_error() {
    $ret = -2;
    $msg = 'token error';
    output_json($ret, $msg);
    exit();
}

// 数据库互动出错
function db_error() {
    $ret = -3;
    $msg = 'db fail';
    output_json($ret, $msg);
    exit();
}

// 数据库已存在
function db_exist() {
    $ret = -4;
    $msg = 'db existed';
    output_json($ret, $msg);
    exit();
}

// 验证码出错
function checkcode_error() {
    $ret = -5;
    $msg = 'checkcode error';
    output_json($ret, $msg);
    exit();
}

// 用户不存在
function user_not_exist() {
    $ret = -6;
    $msg = 'user not exist';
    output_json($ret, $msg);
    exit();
}

// 密码错误
function password_error() {
    $ret = -7;
    $msg = 'password error';
    output_json($ret, $msg);
    exit();
}

// 长度不对
function length_error() {
    $ret = -8;
    $msg = 'length error';
    output_json($ret, $msg);
    exit();
}

// 上传失败
function upload_error($error) {
    $ret = -9;
    $msg = $error;
    output_json($ret, $msg);
    exit();
}

//验证md5失败
function md5_file_error() {
    $ret = -10;
    $msg = 'md5 file check error';
    output_json($ret, $msg);
    exit();
}

//删除图片上败
function delete_fail() {
    $ret = -11;
    $msg = 'delete fail';
    output_json($ret, $msg);
    exit();
}

// 分页返回
function pagination($count, $data="") {
    $output = array(
        'ret' => 0,
        'msg' => 'ok',
        'count' => $count,
        'data' => $data
    );
    echo json_encode($output);
    exit();
}

// 输出帮助函数
function output_json($ret, $msg, $data="") {
    if (empty($data)) {
        $output = array(
            'ret' => $ret,
            'msg' => $msg
        );
    } else {
        $output = array(
            'ret' => $ret,
            'msg' => $msg,
            'data' => $data
        );
    }
    echo json_encode($output);
    exit();
}
