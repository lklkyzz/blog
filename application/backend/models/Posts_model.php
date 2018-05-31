<?php
defined('BASEPATH') OR die('No direct script access allowed');

class Posts_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
    * 获取文章列表
    */
    public function getPostsList($page, $size, $status, $cid) {
        $page = $page - 1;

        $this->db->select('p.pid, p.title, p.summary, p.content, c.name as category_name, t.name as tag_name, p.status, p.level, p.cmt_count, p.created, p.updated');
        $this->db->from('posts p');
        $this->db->join('categories c', 'p.cid=c.cid', 'left');
        $this->db->join('tags t', 'p.tid=t.tid', 'left');

        if($status == 3) {
            if($cid == 0) {
                $this->db->where('p.status !=', 0);
            }else {
                $this->db->where('p.status !=', 0);
                $this->db->where('p.cid', $cid);
            }
        }else {
            $this->db->where('p.status', $status);
            $this->db->where('p.cid', $cid);
        }

        $this->db->order_by('updated', 'DESC');
        $this->db->limit($size);
        $this->db->offset($page*$size);
        $originalData = $this->db->get()->result_array();

        $formatData = [];
        foreach ($originalData as $post) {
            switch ($post['status']) {
                case '1':
                    $post['status'] = '正常';
                    break;
                case '2':
                    $post['status'] = '草稿';
                    break;
                case '0':
                    $post['status'] = '删除';
                    break;
            }
            switch ($post['level']) {
                case '1':
                    $post['level'] = '正常';
                    break;
                case '2':
                    $post['level'] = '置顶';
                    break;
                case '0':
                    $post['level'] = '仅自己可见';
                    break;
            }
            $post['created'] = date('Y-m-d H:i:s', $post['created']);
            $post['updated'] = date('Y-m-d H:i:s', $post['updated']);
            $formatData[] = $post;
        }

        $this->db->select('pid');
        $this->db->from('posts');
        if($status == 3) {
            if($cid == 0) {
                $this->db->where('status !=', 0);
            }else {
                $this->db->where('status !=', 0);
                $this->db->where('cid', $cid);
            }
        }else {
            $this->db->where('status', $status);
            $this->db->where('cid', $cid);
        }

        $count = $this->db->count_all_results();
        $data['count'] = $count;
        $data['postsList'] = $formatData;
        return $data;
    }

    /**
    * 删除文章
    */
    public function deletePost($pid) {
        $count = count($pid);
        $time = time();
        $data = array(
            'status' => 0,
            'updated' => $time
        );

        $this->db->select('cid');
        $this->db->where_in('pid', $pid);
        $tmpData = $this->db->get('posts')->result_array();

        for ($i=0; $i < count($tmpData); $i++) { 
            $this->db->select('post_count');
            $this->db->where('cid', $tmpData[$i]['cid']);
            $postCount = $this->db->get('categories')->result_array();

            $delete = array(
                'updated' => $time,
                'post_count' => $postCount[0]['post_count'] - 1
            );

            $this->db->select('post_count');
            $this->db->where('cid', $tmpData[$i]['cid']);
            $this->db->update('categories', $delete);
        }

        $this->db->where_in('pid', $pid);
        $this->db->update('posts', $data);
        if($this->db->affected_rows() == $count) {
            return true;
        }else {
            return false;
        }

    }

    /**
    * 编辑文章
    */
    public function updatePost($pid, $title, $summary, $content, $cid, $tid, $status, $level) {
        $time = time();
        $data = array(
            'title' => $title,
            'summary' => $summary,
            'content' => $content,
            'cid' => $cid,
            'tid' => $tid,
            'status' => $status,
            'level' => $level,
            'updated' => $time
        );

        $this->db->select('cid');
        $this->db->where('pid', $pid);
        $oldCid = $this->db->get('posts')->result_array();
        if($oldCid[0]['cid'] != $cid) {//也许大概可能需要用到事物
            //减去原来分类的post_count
            $this->db->select('post_count');
            $this->db->where('cid', $oldCid[0]['cid']);
            $count = $this->db->get('categories')->result_array();

            $delete = array(
                'updated' => $time,
                'post_count' => $count[0]['post_count'] - 1
            );

            $this->db->select('post_count');
            $this->db->where('cid', $oldCid[0]['cid']);
            $this->db->update('categories', $delete);

            //增加新分类的post_count
            $this->db->select('post_count');
            $this->db->where('cid', $cid);
            $count = $this->db->get('categories')->result_array();

            $add = array(
                'updated' => $time,
                'post_count' => $count[0]['post_count'] + 1
            );

            $this->db->select('post_count');
            $this->db->where('cid', $cid);
            $this->db->update('categories', $add);
        }

        $this->db->where('pid', $pid);
        $this->db->update('posts', $data);


        if($this->db->affected_rows() == 1) {
            return true;
        }else {
            return false;
        }
    }

    /**
    * 添加文章
    */
    public function addPost($title, $summary, $content, $cid, $tid, $status, $level) {
        $time = time();
        $data = array(
            'title' => $title,
            'summary' => $summary,
            'content' => $content,
            'cid' => $cid,
            'tid' => $tid,
            'status' => $status,
            'level' => $level,
            'updated' => $time,
            'created' => $time
        );

        $this->db->insert('posts', $data);

        if($this->db->affected_rows() == 1) {
            $this->db->select('post_count');
            $this->db->where('cid', $cid);
            $count = $this->db->get('categories')->result_array();

            $add = array(
                'updated' => $time,
                'post_count' => $count[0]['post_count'] + 1
            );

            $this->db->where('cid', $cid);
            $this->db->update('categories', $add);
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
    * 检查是否重名
    */
    public function checkName($pid, $title) {
        $this->db->select('title');
        $this->db->from('posts');
        $this->db->where('pid !=', $pid);
        $this->db->where('title', $title);
        $result = $this->db->get()->result_array();
        if(empty($result)) {
            return false;
        }else {
            return true;
        }
    }
}