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
        if($result) {
            success_return();
        }else {
            db_error();
        }
    }

    /**
    * 审核评论
    */
    public function deleteComment() {
        $this->load->model('Comments_model');
        $commentM = new Comments_model();

        $cmtid = $this->input->post('cmtid');
        $status = $this->input->post('status');
        if(!isset($cmtid) || !isset($status)) {
            miss_params();
        }

        $result = $commentM->checkComment($cmtid, $status);
        if($result) {
            success_return();
        }else {
            db_error();
        }
    }

    /**
    * 回复评论
    */
    public function replycomment() {
        $this->load->model('Comments_model');
        $commentM = new Comments_model();

        $pid = $this->input->post('pid');
        $uid = $this->input->post('uid');
        $toUid = $this->input->post('to_uid');
        $content = $this->input->post('content');
        if(!isset($pid) || !isset($uid) || !isset($toUid) || $content) {
            miss_params();
        }

        $result = $commentM->replyComment($pid, $uid, $toUid, $content);
        if($result) {
            success_return();
        }else {
            db_error();
        }
    }


}