<?php
defined('BASEPATH') OR die('No direct script access allowed');

class User extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function user() {
        $this->smarty->display('user/user.html');
    }

    /**
    * 获取用户列表
    **/
    public function getusersbystatus() {
        $this->load->model('Users_model');
        $userM = new Users_model();

        $page = $this->input->post('page');
        $size = $this->input->post('size');
        $status = $this->input->post('status');
        //4为全部，1为管理员，2普通用户，3禁用，0回收站

        if(!isset($page) || !isset($size) || !isset($status)) {
            miss_params();
        }

        $result = $userM->usersList($page, $size, $status);
        pagination($result['count'], $result['usersList']);
    }

    /**
    * 编辑用户信息
    **/
    public function updateuser() {
        $this->load->model('Users_model');
        $userM = new Users_model();

        $uid = $this->input->post('uid');
        $nickname = $this->input->post('nickname');
        $pwd = $this->input->post('password');
        $email = $this->input->post('email');
        $sex = $this->input->post('sex');
        $status = $this->input->post('status');
        if(!isset($uid) || !isset($nickname) || !isset($pwd) || !isset($email) || !isset($sex) || !isset($status) ) {
            miss_params();
        }

        $checkName = $userM->checkRepeat($uid, $account = null, $nickname, $email);
        if($checkName) {
            db_exist();
        }

        $avatarPath = $userM->getAvatarPath($uid);
        $tmp = explode('/', $avatarPath);
        $avatarName = end($tmp);


        $delete = json_decode($this->deletepic($avatarName));
        if($)
        $upload = $this->uploadpic($picName);

        $result = $userM->updateUser($uid, $nickname, $pwd, $email, $sex, $avatarPath, $status);
        if($result) {
            success_return();
        }else {
            db_error();
        }
    }

    /**
    * 添加用户
    **/
    public function adduser() {
        $this->load->model('Users_model');
        $userM = new Users_model();

        $time = time();
        $filename = $_FILES['file']['name'];
        $tmpPath = $_FILES['file']['tmp_name'];
        $account = $this->input->post('account');
        $nickname = $this->input->post('nickname');
        $pwd = $this->input->post('password');
        $email = $this->input->post('email');
        $sex = $this->input->post('sex');
        $status = $this->input->post('status');
        if(!isset($account) || !isset($nickname) || !isset($pwd) || !isset($email) || !isset($sex) || !isset($status) ) {
            miss_params();
        }

        $checkName = $userM->checkRepeat($uid = null, $account, $nickname, $email);
        if($checkName) {
            db_exist();
        }

        $upload = json_decode($this->uploadpic($account, $filename, $tmpPath, $time), true);
        if($upload['ret'] === 0) {
            $avatarPath = $upload['data']['pic_path'];
        }else {
            $error = $upload['msg'];
            upload_error($error);
        }

        $result = $userM->addUser($account, $nickname, $pwd, $email, $sex, $avatarPath, $status);
        if($result) {
            success_return();
        }else {
            db_error();
        }
    }

    /**
    * 删除用户
    **/
    public function deleteuser() {
        $this->load->model('Users_model');
        $userM = new Users_model();

        $uid = $this->input->post('uid');
        if(!isset($uid)) {
            miss_params();
        }

        $result = $userM->deleteUser($uid);
        if($result) {
            success_return();
        }else {
            db_error();
        }
    }

    /**
    * 上传头像
    **/
    public function uploadpic($account, $filename, $tmpPath, $time) {
        $webSecrect = 'qweASDzxc123!@#';
        $token = md5($time.$filename.$webSecrect);
        $url = 'http://apollo.backend.me/picture/savepic';
        $params = '?token='.$token."&filename=".$filename."&time=".$time;

        $cfile = new CURLFile(realpath($tmpPath));
        $post_data = array(
            'file_md5' => md5_file($tmpPath),
            'file' => $cfile,
            'account' => $account
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url.$params);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        $ret = curl_exec($curl);

        return $ret;
    }

    /**
    * 删除头像
    **/
    public function deletepic($account, $filename, $time) {
    }
}
