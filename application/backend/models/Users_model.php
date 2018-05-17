<?php
defined('BASEPATH') OR die('No direct scrpit access allowed');

class Users_model extends CI_model {

    function __construct() {
        parent::__construct();
    }

    /**
    * 获取用户列表
    */
    public function usersList($page, $size, $status) {
        $page = $page - 1;

        $this->db->select('uid, account, nickname, email, sex, avatar,status, created, updated');
        $this->db->from('users');

        if($status == 4) {
            $this->db->where('status !=', 0);
        }else {
            $this->db->where('status', $status);
        }

        $this->db->limit($size);
        $this->db->offset($page*$size);

        $originalData = $this->db->get()->result_array();

        $formatData = [];
        foreach ($originalData as $user) {
            switch ($user['status']) {
                case 1:
                    $user['status'] = '管理员';
                    break;
                case 2:
                    $user['status'] = '普通用户';
                    break;
                case 3:
                    $user['status'] = '禁用';
                    break;
                case 0:
                    $user['status'] = '已删除';
                    break;
            }
            switch ($user['sex']) {
                case 1:
                    $user['sex'] = '男';
                    break;
                case 2:
                    $user['sex'] = '女';
                    break;
            }
            $user['created'] = date('Y-m-d H:i:s', $user['created']);
            $user['updated'] = date('Y-m-d H:i:s', $user['updated']);
            $formatData[] = $user;
        }

        $this->db->select('uid');
        $this->db->from('users');

        if($status == 4) {
            $this->db->where('status !=', 0);
        }else {
            $this->db->where('status', $status);
        }

        $count = $this->db->count_all_results();

        $data['count'] = $count;
        $data['usersList'] = $formatData;
        return $data;
    }

    /**
    * 编辑用户
    */
    public function updateUser($uid, $nickName, $pwd, $email, $sex, $avatar, $status) {
        $this->load->helper('password');

        $time = time();
        $pwdSalt = md5($email.$time.mt_rand(100000, 999999));
        $pwdHash = getPasswordHash($pwd, $pwdSalt);
        $data = array(
            'nickname' => $nickName,
            'pwd_salt' => $pwdSalt,
            'pwd_hash' => $pwdHash,
            'email' => $email,
            'sex' => $sex,
            'avatar' => $avatar,
            'status' => $status,
            'updated' => $time
        );

        $this->db->where('uid', $uid);
        $this->db->update('users', $data);
        if($this->db->affected_rows() == 1) {
            return true;
        }else {
            return false;
        }
    }

    /**
    * 添加用户
    **/
    public function addUser($account, $nickName, $pwd, $email, $sex, $avatar, $status) {
        $this->load->helper('password');

        $time =time();
        $pwdSalt = md5($email.$time.mt_rand(100000, 999999));
        $pwdHash = getPasswordHash($pwd, $pwdSalt);
        $data = array(
            'account' => $account,
            'nickname' => $nickName,
            'pwd_salt' => $pwdSalt,
            'pwd_hash' => $pwdHash,
            'email' => $email,
            'sex' => $sex,
            'avatar' => $avatar,
            'status' => $status,
            'created' => $time,
            'updated' => $time
        );

        $this->db->insert('users', $data);
        if($this->db->affected_rows() == 1) {
            return true;
        }else {
            return false;
        }
    }

    /**
    * 删除用户
    **/
    public function deleteUser($uid) {
        $time = time();
        $count = count($uid);
        $data = array(
            'status' => 0,
            'updated' => $time
        );

        $this->db->where_in('uid', $uid);
        $this->db->update('users', $data);

        if($this->db->affected_rows() == $count) {
            return true;
        }else {
            return false;
        }
    }

    /**
    * 检查账号，昵称，邮箱是否重复
    **/
    public function checkRepeat($uid, $account, $nickname, $email) {
        $this->db->select('account, nickname, email');
        $this->db->from('users');

        if(!$account) {
        $where = "(nickname = '$nickname' OR email = '$email')";
    }else {
        $where = "(account = '$account' OR nickname = '$nickname' OR email = '$email')";
    }

        $this->db->where('uid !=', $uid);
        $this->db->where($where);

        $checkRepeat = $this->db->get()->result_array();

        if(empty($checkRepeat)) {
            return false;
        }else {
            return true;
        }
    }

}