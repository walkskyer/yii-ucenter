<?php
/**
 * Created by JetBrains PhpStorm.
 * User: weijie
 * Date: 13-2-19
 * Time: 上午11:13
 * File: UC_IUser.php
 * To change this template use File | Settings | File Templates.
 */
/**
 * 用户接口
 */
interface UC_IUser
{
    /**
     * 取得用户名
     * @return string
     */
    public function getUserName();
    /**
     * 取得uid
     * @return int
     */
    public function getUid();
    /**
     * 取得密码
     * @return string
     */
    public function getPassword();

    /**
     * 取得email
     * @return string
     */
    public function getEmail();
    /**
     * @return array 数组的字段顺序为'uid','username','pwd','email'
     */
    public function getUser();

    /**
     * 根据uid查询数据
     * @param $uid int
     * @return boolean
     */
    public function findByUid($uid);

    /**
     * @param $uids array
     * @return array the values is UC_IUser object
     */
    public function findAllByUid($uids);
    /**
     * 根据username查询数据
     * @param $username string
     * @return boolean
     */
    public function findByName($username);

    /**
     * 新增会员
     * @param $data array  array uid,username,password,email
     * @return boolean
     */
    public function add($data);

    /**
     * 根据uid修改会员信息
     * @param $uid int
     * @param $data array
     * @return boolean
     */
    public function edit($uid,$data);

    /**
     * 删除会员
     * @return boolean
     */
    public function delete();
}
