<?php
defined('BASEPATH') OR die('No direct script access allowed');

class Upload extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function uploadfile() {
        $this->smarty->display('upload/upload.html');
    }

    public function uploadpic() {
        $time = '1527127559';
        $filename = $_FILES['file']['name'];
        $webSecrect = 'qweASDzxc123!@#';
        $token = md5($time.$filename.$webSecrect);
        $url = 'http://apollo.backend.me/picture/savepic';
        $params = '?token='.$token."&filename=".$filename."&time=".$time;
        $t = md5_file($_FILES['file']['tmp_name']);

        $cfile = new CURLFile(realpath($_FILES['file']['tmp_name']));
        $post_data = array(
            'file_md5' => md5_file($_FILES['file']['tmp_name']),
            'file' => $cfile
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url.$params);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        $ret = curl_exec($curl);

        return $ret;
    }
}