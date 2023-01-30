<?php
if(!defined('_INCODE')) die('Access Deined...');
//Chứa các hàm liên quan đến session

//Hàm gán session
function setSession($key, $value){
    if(!empty(session_id())){
        $_SESSION[$key] = $value;
        return true;
    }
    return false;
}

//Hàm đọc session
function getSession($key=''){
    if(empty($key)){
        return $_SESSION;
    }else{
        if(isset($_SESSION[$key])){
            return $_SESSION[$key];
        }
    }
    return false;
}

//Hàm xóa session
function removeSession($key=''){
    if(empty($key)){
        session_destroy();
        return true;
    }else{
        if(isset($_SESSION[$key])){
            unset($_SESSION[$key]);
            return true;
        }
    }
    return false;
}

//Hàm gán flash data
function setFlashData($key, $value){
    $key = 'flash_'.$key;
    return setSession($key,$value);
}

//Hàm đọc flash data
function getFlashData($key){
    $key = 'flash_'.$key;
    $data = getSession($key); // gán session vào biến data để không bị mất

    removeSession($key); // Xóa session

    return $data; // Hiển thị  biến $data vừa được gán
}