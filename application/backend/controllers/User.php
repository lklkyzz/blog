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

        if(!isset($page) || !isset($size)) {
            miss_params();
        }
        if(!isset($status)) {
            $status = 4;//4为全部，1为管理员，2普通用户，3禁用，0删除
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
        $avatar= $this->input->post('avatar');
        $status = $this->input->post('status');
        if(!isset($uid) || !isset($nickname) || !isset($pwd) || !isset($email) || !isset($sex) || !isset($avatar) || !isset($status) ) {
            miss_params();
        }

        $res = $userM->checkRepeat($uid, $account = null, $nickname, $email);
        if($res) {
            db_exist();
        }

        $result = $userM->updateUser($uid, $nickname, $pwd, $email, $sex, $avatar, $status);
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

        $account = $this->input->post('account');
        $nickname = $this->input->post('nickname');
        $pwd = $this->input->post('password');
        $email = $this->input->post('email');
        $sex = $this->input->post('sex');
        $avatar= $this->input->post('avatar');
        $status = $this->input->post('status');
        if(!isset($account) || !isset($nickname) || !isset($pwd) || !isset($email) || !isset($sex) || !isset($avatar) || !isset($status) ) {
            miss_params();
        }

        $res = $userM->checkRepeat($uid = null, $account, $nickname, $email);
        if($res) {
            db_exist();
        }

        $result = $userM->addUser($account, $nickname, $pwd, $email, $sex, $avatar, $status);
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
}