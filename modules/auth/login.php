<?php
if(!defined('_INCODE')) die('Access Deined...');
//Chứa chức năng đăng nhập

$data =[
    'pageTitle' => 'Đăng nhập hệ thống'
];
layout('header-login', $data);//require_once _WEB_PATH_TEMPALTE.'/layouts/header_login.php';

//Kiểm tra trạng thái đăng nhập
if(isLogin()){
    redirect('?module=users');
}

//Xử lý đăng nhập
if(isPost()){
    $body=getBody();
    if(!empty(trim($body['email'])) && !empty(trim($body['password']))){
        //Kiểm tra đăng nhập
        $email =$body['email'];
        $password=$body['password'];

        //Truy vấn lấy thông tin user theo email
        $userQuery =firstRaw("SELECT id,password FROM users WHERE email='$email' AND status =1 ");
        if(!empty($userQuery)){
            $passwordHash= $userQuery['password'];
            $userId =$userQuery['id'];
            if(password_verify($password,$passwordHash)){
                //Tạo token login
                $tokenLogin =sha1(uniqid().time());
                //Insert dữ liệu vào bảng login_token trong csdl
                $dataToken =[
                    'userId' => $userId,
                    'token' => $tokenLogin,
                    'createAt' => date('Y-m-d H:i:s'),
                ];

                //Insert
                $insertTokenStatus = insert('login_token', $dataToken);
                if($insertTokenStatus){
                    //Insert thành công
                    
                    //Lưu loginToken vào session
                    setSession('loginToken',$tokenLogin);

                    //Chuyển hướng qua trang quản lý user
                    redirect('?module=users');
                }else{
                    setFlashData('msg', 'Lỗi hệ thống, bạn không thể đăng nhập vào lúc này');
                    setFlashData('msg_type', 'danger');
                    //redirect('?module=auth&action=login');
                }
            }else{
                setFlashData('msg', 'Mật khẩu không chính xác.');
                setFlashData('msg_type', 'danger');
                //redirect('?module=auth&action=login');
            }
        }else{
            setFlashData('msg', 'Email không tồn tại trong hệ thống hoặc chưa được kích hoạt.');
            setFlashData('msg_type', 'danger');
            //redirect('?module=auth&action=login');
        }
    }else{
        setFlashData('msg', 'Vui lòng nhập email và mật khẩu');
        setFlashData('msg_type', 'danger');
        //redirect('?module=auth&action=login');
    }
    redirect('?module=auth&action=login');
}

//Gán thông báo từ trang active
$msg = getFlashData('msg');
$msgType =getFlashData('msg_type');

?> 
<div class="row">
    <div class="col-6" style="margin: 20px auto">
        <h3 class="text-center text-uppercase">Đăng nhập hệ thống</h3>
        <?php echo getMsg($msg, $msgType) ?>
        <form action="" method="post">
            <div class="form-group">
                <label for="">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Địa chỉ email">
            </div>
            <div class="form-group">
                <label for="">Mật khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="Mật khẩu....">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
            <hr>
            <p class="text-center"><a href="?module=auth&action=fogot">Quên mật khẩu?</a></p>
            <p class="text-center"><a href="?module=auth&action=register">Đăng ký tài khoản</a></p>
        </form>
    </div>
</div>
<?php
layout('footer-login');//require_once _WEB_PATH_TEMPALTE.'/layouts/footer_login.php';