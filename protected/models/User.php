<?php
/**
 * Created by JetBrains PhpStorm.
 * User: weijie
 * Date: 13-2-19
 * Time: 上午11:21
 * File: User.php
 * To change this template use File | Settings | File Templates.
 */
/**
 * @property int $id
 * @property int $uid;
 * @property string $username;
 * @property string $password;
 * @property string $email;
 */
class User extends Model implements UC_IUser
{
    public function __construct($scenario='insert'){
        parent::__construct($scenario);
        $this->tableName='user';
    }

    /**
     * 取得用户名
     * @return string
     */
    public function getUserName(){
        return $this->username;
    }
    /**
     * 取得uid
     * @return int
     */
    public function getUid(){
        return $this->uid;
    }
    /**
     * 取得密码
     * @return string;
     */
    public function getPassword(){
        return $this->password;
    }

    /**
     * 取得email
     * @return string
     */
    public function getEmail(){
        return $this->email;
    }

    /**
     * @return array 数组的字段顺序为'uid','username','pwd','email'
     */
    public function getUser(){
        return array('uid'=>$this->uid,'username'=>$this->username,'pwd'=>$this->password,'email'=>$this->email);
    }

    /**
     * 根据uid查询数据
     * @param $uid int
     * @return boolean
     */
    public function findByUid($uid){}

    /**
     * @param $uids
     * @return array the values is UC_IUser object
     */
    public function findAllByUid($uids){}
    /**
     * 根据username查询数据
     * @param $username string
     * @return boolean
     */
    public function findByName($username){
        return $this->find('username=:username',array(':username'=>$username));
    }

    /**
     * 新增会员
     * @param $data array uid,username,password,email
     * @return boolean
     */
    public function add($data){
        $this->uid=$data['uid'];
        $this->uid=$data['username'];
        $this->uid=$data['password'];
        $this->uid=$data['email'];
        return $this->save();
    }

    /**
     * 根据uid修改会员信息
     * @param $uid int
     * @param $data
     * @return mixed
     */
    public function edit($uid,$data){}
}
