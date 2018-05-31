<?php
defined('BASEPATH') OR die('No direct script access allowed');

class Comments_model extends CI_model {

    function __construct() {
        parent::__construct();
    }

    /**
    * 评论列表
    */
    public function getCommentList($page, $size, $status, $uid) {
        $page = $page - 1;

        $this->db->select('c.cmtid, p.title, u1.nickname, u2.nickname as received_name, c.content, c.status, c.created, c.pass_time');
        $this->db->from('comments c');
        $this->db->join('posts p', 'c.pid=p.pid', 'left');
        $this->db->join('users u1', 'c.uid=u1.uid', 'left');
        $this->db->join('users u2', 'c.to_uid=u2.uid', 'left');

        if($status == 3) {
            if($uid == 0) {
                $this->db->where('c.status !=', 0);
            }else {
                $this->db->where('u1.uid', $uid);
            }
        }else {
            if($uid == 0) {
                $this->db->where('c.status', $status);
            }else {
                $this->db->where('c.status', $status);
                $this->db->where('u1.uid', $uid);
            }
        }

        $this->db->order_by('c.created', 'DESC');
        $this->db->limit($size);
        $this->db->offset($page*$size);
        $originalData = $this->db->get()->result_array();

        $formatData = [];
        foreach ($originalData as $comment) {
            switch ($comment['status']) {
                case 1:
                    $comment['status'] = '正常';
                    break;
                case 2:
                    $comment['status'] = '待审核';
                    break;
                case 0:
                    $comment['status'] = '已删除';
                    break;
            }
            $comment['created'] = date('Y-m-d H:i:s', $comment['created']);
            $comment['pass_time'] = date('Y-m-d H:i:s', $comment['pass_time']);
            $formatData[] = $comment;
        }

        $this->db->select('cmtid');
        $this->db->from('comments');

        if($status == 3) {
            if($uid == 0) {
                $this->db->where('status !=', 0);
            }else {
                $this->db->where('uid', $uid);
            }
        }else {
            if($uid == 0) {
                $this->db->where('status', $status);
            }else {
                $this->db->where('status', $status);
                $this->db->where('uid', $uid);
            }
        }

        $count = $this->db->count_all_results();
        $data['count'] = $count;
        $data['commentsList'] = $formatData;
        return $data;
    }

    /**
    * 审核评论
    */
    public function checkComments($cmtid, $pid, $status) {
        $time = time();
        $data = array(
            'status' => $status,
            'pass_time' => $time
        );

        $this->db->where('cmtid', $cmtid);
        $this->db->update('comments', $data);

        if($this->db->affected_rows() == 1) {
//增加或减少posts里的评论数
            $this->db->select('cmt_count');
            $this->db->where('pid', $pid);
            $count = $this->db->get('posts')->result_array();

            if($status == 0) {
                $data = array(
                    'updated' => $time,
                    'cmt_count' => $count[0]['cmt_count'] - 1
                );
            }else {
                $data = array(
                    'updated' => $time,
                    'cmt_count' => $count[0]['cmt_count'] + 1
                );
            }

            $this->db->where('pid', $pid);
            $this->db->update('posts', $data);

            if($this->db->affected_rows() == 1) {
                return true;
            }else {
                return false;
            }
        }else {
            return false;
        }
    }

    /**
    * 回复评论
    */
    public function writeComment($pid, $uid, $toUid, $content) {
        $time = time();
        $data = array(
            'pid' => $pid,
            'uid' => $uid,
            'to_uid' => $toUid,
            'content' => $content,
            'status' => 1,
            'created' => $time,
            'pass_time' => $time
        );

        $this->db->insert('comments', $data);

        if($this->db->affected_rows() == 1) {
            $this->db->select('cmt_count');
            $count = $this->db->get('posts')->result_array();
            $data = array(
                'updated' => $time,
                'cmt_count' => $count[0]['cmt_count'] + 1
            );
            $this->db->where('pid', $pid);
            $this->db->update('posts', $data);
            if($this->db->affected_rows() == 1) {
                return true;
            }
        }else {
            return false;
        }
    }

}
