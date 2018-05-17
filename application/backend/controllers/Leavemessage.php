<?php
defined('BASEPATH') OR die('No direct script access allowed');

class Leavemessage extends CI_Controller {

    function __construct() {

        parent::__construct();
    }

    public function leavemessage() {
        $this->smarty->display('leavemessage/leavemessage.html');
    }
    /**
    * 获取留言列表
    */
    public function lvmsglist() {
        $this->load->model('Leavemessages_model');
        $lvmsgM = new Leavemessages_model();
    }
}