<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 密码哈希计算帮助函数
function getPasswordHash($password, $passSalt) {

    $passHash = md5($password);
    for ( $i=0; $i<100; $i++ ) {
        $passHash = hash('sha256', $passHash.$passSalt);
    }
    return $passHash;

}
