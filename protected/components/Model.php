<?php
/**
 * Created by JetBrains PhpStorm.
 * User: weijie
 * Date: 13-2-20
 * Time: 上午10:54
 * File: Model.php
 * To change this template use File | Settings | File Templates.
 */
class Model extends CActiveRecord{
    public $dbPrefix='tbl_';
    public $tableName=null;

    public function __construct($scenario='insert'){
        parent::__construct($scenario);
    }

    public function tableName(){
        return $this->dbPrefix.$this->tableName;
    }
    public static function model($className=false){
        !$className && $className=get_called_class();
        return parent::model($className);
    }
}