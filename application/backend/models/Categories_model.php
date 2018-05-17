<?php
defined('BASEPATH') OR die('No direct script access allowed');

class Categories_model extends CI_model {

    function __construct() {
        parent::__construct();
    }

    /**
    * 文章分类下拉列表
    */
    public function categoryDropDownList() {
        $this->db->select('cid, name');
        $this->db->from('categories');
        $this->db->where('status !=', 0);
        $result = $this->db->get()->result_array();

        return $result;
    }

    /**
    * 添加文章分类
    */
    public function addCategory($name, $pcid, $status) {
        $time = time();
        $data = array(
            'name' => $name,
            'pcid' =>$pcid,
            'status' => $status,
            'created' => $time
        );

        $this->db->insert('categories', $data);
        if($this->db->affected_rows() == 1) {
            return true;
        }else {
            return false;
        }
    }

    /**
    * 文章分类列表
    */
    public function getCategoryList($page, $size) {
        $page = $page - 1;

        $this->db->select('c1.cid, c1.name, c2.name as parent_name, c1.status, c1.count, c1.created, c1.updated');
        $this->db->from('categories c1');
        $this->db->join('categories c2', 'c1.pcid=c2.cid', 'left');
        $this->db->where('c1.status !=', 0);
        $this->db->limit($size);
        $this->db->offset($page*$size);
        $originalData = $this->db->get()->result_array();

        $formatData = [];
        foreach ($originalData as $category) {
            switch ($category['status']) {
                case 1:
                    $category['status'] = '正常';
                    break;
                case 2:
                    $category['status'] = '暂停使用';
                    break;
            }
            $category['created'] = date('Y-m-d H:i:s', $category['created']);
            $category['updated'] = date('Y-m-d H:i:s', $category['updated']);
            $formatData[] = $category;
        }

        $this->db->select('cid');
        $this->db->from('categories');
        $this->db->where('status !=', 0);
        $count = $this->db->count_all_results();

        $data['count'] = $count;
        $data['categoryList'] = $formatData;
        return $data;
    }

    /**
    * 编辑文章分类
    */
    public function updateCategory($cid, $name, $pcid, $status) {
        $time = time();
        $data = array(
            'name' => $name,
            'pcid' =>$pcid,
            'status' => $status,
            'updated' => $time
        );

        $this->db->where('cid', $cid);
        $this->db->update('categories', $data);
        if($this->db->affected_rows() == 1) {
            return true;
        }else {
            return false;
        }
    }

    /**
    * 删除文章分类
    */
    public function deleteCategory($cid) {
        $count = count($cid);
        $time = time();
        $data = array(
            'status' => 0,
            'pcid' => 1,
            'updated' => $time
        );

        $this->db->where_in('cid', $cid);
        $this->db->update('categories', $data);

        if($this->db->affected_rows() == $count) {
            return true;
        }else {
            return false;
        }
    }

    /**
    * 检查是否重名
    */
    public function checkCategoryName($cid, $name) {
        $this->db->select('name');
        $this->db->from('categories');
        $this->db->where('cid !=', $cid);
        $this->db->where('name', $name);
        $checkName = $this->db->get()->result_array();
        if(empty($checkName)) {
            return false;
        }else {
            return true;
        }
    }
}