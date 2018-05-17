<?php
defined('BASEPATH') OR die('No direct script access allowed');

class Tags_model extends CI_model {

    function __construct() {
        parent::__construct();
    }

    /**
    * 添加标签
    */
    public function addTag($name, $status) {
        $time = time();
        $data = array(
            'name' => $name,
            'status' => $status,
            'created' => $time,
            'updated' => $time
        );

        $this->db->insert('tags', $data);
        if($this->db->affected_rows() == 1) {
            return true;
        }else {
            return false;
        }
    }

    /**
    * 标签列表
    */
    public function tagList($page, $size) {
        $page = $page - 1;

        $this->db->select('tid, name, status, count, created, updated');
        $this->db->from('tags');
        $this->db->where('status !=', 0);
        $this->db->limit($size);
        $this->db->offset($page*$size);
        $originalData = $this->db->get()->result_array();

        $formatData = [];
        foreach ($originalData as $tag) {
            switch ($tag['status']) {
                case 1:
                    $tag['status'] = '正常';
                    break;
                case 2:
                    $tag['status'] = '暂停使用';
                    break;
            }
            $tag['created'] = date('Y-m-d H:i:s', $tag['created']);
            $tag['updated'] = date('Y-m-d H:i:s', $tag['updated']);
            $formatData[] = $tag;
        }

        $this->db->select('tid');
        $this->db->from('tags');
        $this->db->where('status !=', 0);
        $count = $this->db->count_all_results();

        $data['count'] = $count;
        $data['tagList'] = $formatData;
        return $data;
    }

    /**
    * 编辑标签
    */
    public function updateTag($tid, $name, $status) {
        $time = time();
        $data = array(
            'name' => $name,
            'status' => $status
        );

        $this->db->where('tid', $tid);
        $this->db->update('tags', $data);

        if($this->db->affected_rows() == 1) {
            return true;
        }else {
            return false;
        }
    }

    /**
    * 删除标签
    */
    public function deleteTag($tid) {
        $count = count($tid);
        $time = time();
        $data = array(
            'status' => 0,
            'updated' => $time
        );

        $this->db->where_in('tid', $tid);
        $this->db->update('tags', $data);

        if($this->db->affected_rows() == $count) {
            return true;
        }else {
            return false;
        }
    }

    /**
    * 验证是否重名
    */
    public function checkTagName($tid, $name) {
        $this->db->select('name');
        $this->db->from('tags');
        $this->db->where('tid !=', $tid);
        $this->db->where('name', $name);

        $checkName = $this->db->get()->result_array();
        if(empty($checkName)) {
            return false;
        }else {
            return true;
        }
    }
}