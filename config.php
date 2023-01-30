<?php
//if(!defined('_INCODE')) die('Access Deined...');
//Chứa các hằng số cấu hình

date_default_timezone_set('Asia/Ho_Chi_Minh');

const _MODULE_DEFAULT ='home';//Module mặc định
const _ACTION_DEFAUTL ='lists';//Action mặc định

const _INCODE = true;//Ngăn chặn người dùng truy cập trực tiếp vào file

//Thiết lập host
    //Địa chỉ trang chủ(index.php)
define('_WEB_HOST_ROOT', 'http://'.$_SERVER['HTTP_HOST'].'/PhpProject/ThucHanh_Admincp/users_manager');
define('_WEB_HOST_TEMPLATE', _WEB_HOST_ROOT.'/templates');

//Thiết lập path
define('_WEB_PATH_ROOT', __DIR__);
define('_WEB_PATH_TEMPALTE',_WEB_PATH_ROOT.'/templates');

//Thiết lập kết nối database
//Thông tin kết nối
const _HOST = 'localhost';
const _USER = 'root';
const _PASS = ''; //Xampp => pass='';
const _DB = 'phponline'; // Tên CSDL
const _DRIVER = 'mysql';