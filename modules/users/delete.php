<?php
if(!defined('_INCODE')) die('Access Deined...');
//Xóa người dùng
$body = getBody();
if(!empty($body['id'])){
    $userId = $body['id'];
    $userDetail = getRows("SELECT id FROM users WHERE 'id='.$userId");
    if($userDetail > 0){
        //Thực hiện xóa
        //1.Xóa dữ liệu trong csdl:  login_token
        $deleteToken = delete('login_token', "userId=$userId");
        if($deleteToken){
            //2. Xóa dữ liệu trong csdl: users
            $deleteUser= delete('users', "id=$userId");
            if($deleteUser){
                setFlashData('msg', 'Xóa người dùng thành công');
                setFlashData('msg_type', 'success');
            }else{
                setFlashData('msg', 'Lỗi hệ thống!Vui lòng thử lại sau');
                setFlashData('msg_type', 'danger');
            }
        }else{
            setFlashData('msg', 'Lỗi hệ thống!Vui lòng thử lại sau');
            setFlashData('msg_type', 'danger');
        }
    }else{
        setFlashData('msg', 'Người dùng không tồn tại trong hệ thống');
        setFlashData('msg_type', 'danger');
    }
}else{
    setFlashData('msg', 'Liên kết không tồn tại');
    setFlashData('msg_type', 'danger');
}
redirect('?module=users');

