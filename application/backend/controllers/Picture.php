<?php
defined('BASEPATH') OR die('No direct script access allowed');

class Picture extends Base_Controller {

    function __construct() {
        parent::__construct();
    }

    /**
    * 上传头像
    */
    public function savepic() {
        $filename = $this->filename;
        $account = $this->account;
        $postFileMd5 = $this->postFileMd5;
        $getTime = $this->getTime;
        $filenameMd5 = md5($filename.$getTime);

        if(!file_exists('static/'.$account)) {
            mkdir('static/'.$account, 0700);
        }
        $upload_path = 'static/'.$account.'/';
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = '*';
        $config['max_size'] = 2048;
        $config['max_width'] = 1920;
        $config['max_height'] = 1080;
        $this->load->library('upload', $config);

        if(!$this->upload->do_upload('file')) {
            $error = array('error' => $this->upload->display_errors());
            upload_error($error);
        }else {
            $data = array('upload_data' => $this->upload->data());
            $tmp = explode('.', $filename);
            $suffix = end($tmp);
            rename($data['upload_data']['full_path'], $upload_path.$filenameMd5.'.'.$suffix);
            $locationFileMd5 = md5_file($upload_path.$filenameMd5.'.'.$suffix);

            if( $postFileMd5 != $locationFileMd5 ) {
                md5_file_error();
            } else {
                $returnData = array(
                    'pic_path' => $this->domain.'/'.$upload_path.$filenameMd5.'.'.$suffix
                );
                success_return($returnData);
            }
        }
    }

    /**
    * 删除旧头像
    */
    public function deletepic() {
        $path = 'static/'.$account.'/';
        $result = unlink($path.$filename);
        if($result) {
            success_return();
        }else {
            delete_fail();
        }
    }

}
