<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Base_Controller extends CI_Controller {

    function __construct() {
        parent::__construct();

        require_once(APPPATH.'config/global.php');
        $account = $this->input->post('account');
        $postFileMd5 = $this->input->post('file_md5');
        $filename = $this->input->get('filename');
        $getTime = $this->input->get('time');
        if(!isset($account) || !isset($filename) || !isset($getTime) ) {
            miss_params();
        }

        $this->account = $account;
        $this->postFileMd5 = $postFileMd5;
        $this->filename = $filename;
        $this->getTime = $getTime;
        $this->domain = DOMAIN;
        $this->web_secrect = WEB_SECRECT;
        $this->_checkToken($filename, $getTime);
    }

    private function _checkToken($filename, $getTime) {
        $webSecrect = $this->web_secrect;
        $getToken = $this->input->get('token');

        $token = md5($getTime.$filename.$webSecrect);
        if($token != $getToken) {
            token_error();
        }
    }

}

