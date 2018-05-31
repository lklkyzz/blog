<?php
defined('BASEPATH') OR die('No direct script access allowed');

class Comment extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function comment() {
        $this->smarty->display('comment/comment.html');
    }

    /**
    * 获取评论列表
    */
    public function commentlist() {
        $this->load->model('Comments_model');
        $commentM = new Comments_model();

        $page = $this->input->post('page');
        $size = $this->input->post('size');
        $status = $this->input->post('status');//1正常，2待审，0回收站
        $uid = $this->input->post('uid');//0为所有用户评论

        if(!isset($page) || !isset($size) || !isset($status) || !isset($uid)) {
            miss_params();
        }

        $result = $commentM->getCommentList($page, $size, $status, $uid);
        pagination($result['count'], $result['commentsList']);
    }

    /**
    * 审核与删除评论
    */
    public function checkcomment() {
        $this->load->model('Comments_model');
        $commentM = new Comments_model();

        $cmtid = $this->input->post('cmtid');
        $pid = $this->input->post('pid');
        $status = $this->input->post('status');//1通过，0删除
        if(!isset($cmtid) || !isset($pid) || !isset($status)) {
            miss_params();
        }

        $result = $commentM->checkComments($cmtid, $pid, $status);
        if($result) {
            success_return();
        }else {
            db_error();
        }
    }

    /**
    * 回复评论
    */
    public function writecomment() {
        $this->load->model('Comments_model');
        $commentM = new Comments_model();

        $pid = $this->input->post('pid');
        $uid = $this->input->post('uid');
        $toUid = $this->input->post('to_uid');
        $content = $this->input->post('content');
        if(!isset($pid) || !isset($uid) || !isset($toUid) || !isset($content)) {
            miss_params();
        }

        $result = $commentM->writeComment($pid, $uid, $toUid, $content);
        if($result) {
            success_return();
        }else {
            db_error();
        }
    }


}