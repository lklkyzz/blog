<?php
defined('BASEPATH') OR die('No direct script access allowed');

class Post extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function post() {
        $this->smarty->display('post/post.html');
    }

    public function createpost() {
        $this->smarty->display('post/createpost.html');
    }

    public function category() {
        $this->smarty->display('post/category.html');
    }

    public function tag() {
        $this->smarty->display('post/tag.html');
    }

    /**
    * 获取文章列表bystatus
    */
    public function getpostbystatus() {
        $this->load->model('Posts_model');
        $postM = new Posts_model();

        $page = $this->input->post('page');
        $size = $this->input->post('size');
        $status = $this->input->post('status');
        //显示所有文章status为3，已发布为1，草稿为2，回收站0
        $cid = $this->input->post('cid');//0时显所有种类文章

        if(!isset($page) || !isset($size) || !isset($status) || !isset($cid)) {
            miss_params();
        }

        $result = $postM->getPostsByStatus($page, $size, $status, $cid);
        pagination($result['count'], $result['postsList']);
    }

    /**
    * 删除文章
    */
    public function deletepost() {
        $this->load->model('Posts_model');
        $postM = new Posts_model();

        $pid = $this->input->Post('pid');
        if(!isset($pid)) {
            miss_params();
        }

        $result = $postM->deletePost($pid);
        if($result) {
            success_return();
        }else {
            db_error();
        }
    }

    /**
    * 编辑文章
    */
    public function updatepost() {
        $this->load->model('Posts_model');
        $postM = new Posts_model();

        $pid = $this->input->post('pid');
        $title = $this->input->post('title');
        $summary = $this->input->post('summary');
        $content = $this->input->post('content');
        $cid = $this->input->post('cid');
        $tid = $this->input->post('tid');
        $status = $this->input->post('status');
        $level = $this->input->post('level');
        if(!isset($pid) || !isset($title) || !isset($summary) || !isset($content) || !isset($cid) || !isset($tid) || !isset($status) || !isset($level)) {
            miss_params();
        }

        $checkPost = $postM->checkName($pid, $name);
        if ($checkName) {
            db_exist();
        }

        $result = $postM->updatePost($pid, $title, $summary, $content, $cid, $tid, $status, $level);
        if($result) {
            success_return();
        }else {
            db_error();
        }
    }

    /**
    * 添加文章
    */
    public function addpost() {
        $this->load->model('Posts_model');
        $postM = new Posts_model();

        $title = $this->input->post('title');
        $summary = $this->input->post('summary');
        $content = $this->input->post('content');
        $cid = $this->input->post('cid');
        $tid = $this->input->post('tid');
        $status = $this->input->post('status');
        $level = $this->input->post('level');
        if(!isset($title) || !isset($summary) || !isset($content) || !isset($cid) || !isset($tid) || !isset($status) || !isset($level)) {
            miss_params();
        }

        $checkPost = $postM->checkName($pid = null, $name);
        if ($checkName) {
            db_exist();
        }

        $result = $postM->addPost($title, $summary, $content, $cid, $tid, $status, $level);
        if($result) {
            success_return();
        }else {
            db_error();
        }
    }

    /**
    * 文章分类下拉列表
    */
    public function categorydropdownlist() {
        $this->load->model('Categories_model');
        $categoryM = new Categories_model();

        $result = $categoryM->categoryDropDownList();
        $count = count($result);
        pagination($count, $result);
    }

    /**
    * 添加文章分类
    */
    public function addcategory() {
        $this->load->model('Categories_model');
        $categoryM = new Categories_model();

        $name = $this->input->post('name');
        $pcid = $this->input->post('pcid');
        $status = $this->input->post('status');
        if(!isset($name) || !isset($pcid) || !isset($status)) {
            miss_params();
        }

        $checkName = $categoryM->checkCategoryName($cid = null, $name);
        if($checkName) {
            db_exist();
        }

        $result = $categoryM->addCategory($name, $pcid, $status);
        if($result) {
            success_return();
        }else {
            db_error();
        }
    }

    /**
    * 文章分类列表
    */
    public function categorylist() {
        $this->load->model('Categories_model');
        $categoryM = new Categories_model();

        $page = $this->input->post('page');
        $size = $this->input->post('size');
        if(!isset($page) || !isset($size)) {
            miss_params();
        }

        $result = $categoryM->getCategoryList($page, $size);
        pagination($result['count'], $result['categoryList']);
    }

    /**
    * 编辑文章分类
    */
    public function updatecategory() {
        $this->load->model('Categories_model');
        $categoryM = new Categories_model();

        $cid = $this->input->post('cid');
        $name = $this->input->post('name');
        $pcid = $this->input->post('pcid');
        $status = $this->input->post('status');
        if(!isset($cid) || !isset($name) || !isset($pcid) || !isset($status)) {
            miss_params();
        }

        $checkName = $categoryM->checkCategoryName($cid, $name);
        if($checkName) {
            db_exist();
        }

        $result = $categoryM->updateCategory($cid, $name, $pcid, $status);
        if($result) {
            success_return();
        }else {
            db_error();
        }
    }

    /**
    * 删除文章分类
    */
    public function deletecategory() {
        $this->load->model('Categories_model');
        $categoryM = new Categories_model();

        $cid = $this->input->post('cid');
        if(!isset($cid)) {
            miss_params();
        }

        $result = $categoryM->deleteCategory($cid);
        if($result) {
            success_return();
        }else {
            db_error();
        }
    }

    /**
    * 添加标签
    */
    public function addtag() {
        $this->load->model('Tags_model');
        $tagM = new Tags_model();

        $name = $this->input->post('name');
        $status = $this->input->post('status');
        if(!isset($name) || !isset($status)) {
            miss_params();
        }

        $checkName = $tagM->checkTagName($tid = null, $name);
        if ($checkName) {
            db_exist();
        }

        $result = $tagM->addTag($name, $status);
        if($result) {
            success_return();
        }else {
            db_error();
        }
    }

    /**
    * 标签列表
    */
    public function taglist() {
        $this->load->model('Tags_model');
        $tagM = new Tags_model();

        $page = $this->input->post('page');
        $size = $this->input->post('size');
        if(!isset($page) || !isset($size)) {
            miss_params();
        }

        $result = $tagM->tagList($page, $size);
        pagination($result['count'], $result['tagList']);
    }

    /**
    * 编辑标签
    */
    public function updatetag() {
        $this->load->model('Tags_model');
        $tagM = new Tags_model();

        $tid = $this->input->post('tid');
        $name = $this->input->post('name');
        $status = $this->input->post('status');
        if(!isset($tid) || !isset($name) || !isset($status)) {
            miss_params();
        }

        $checkName = $tagM->checkTagName($tid, $name);
        if($checkName) {
            db_exist();
        }

        $result = $tagM->updateTag($tid, $name ,$status);
        if($result) {
            success_return();
        }else {
            db_error();
        }
    }

    /**
    * 删除标签
    */
    public function deletetag() {
        $this->load->model('Tags_model');
        $tagM = new Tags_model();

        $tid = $this->input->post('tid');
        if(!isset($tid)) {
            miss_params();
        }

        $result = $tagM->deleteTag($tid);
        if($result) {
            success_return();
        }else {
            db_error();
        }
    }
}

