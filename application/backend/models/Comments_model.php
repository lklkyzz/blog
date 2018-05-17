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

        $this->db->select('c.cmtid, p.title, u1.nickname, u2.nickname as received_name, c.content, c.status, c.count');
        $this->db->from('comments c');
        $this->db->join('posts p', 'c.pid=p.pid');
        $this->db->join('users u1', 'c.uid=u1.uid');
        $this->db->join('users u2', 'c.to_uid=u2.uid');

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

        $this->db->order_by('updated', 'DESC');
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
            $comment['updated'] = date('Y-m-d H:i:s', $comment['updated']);
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
}