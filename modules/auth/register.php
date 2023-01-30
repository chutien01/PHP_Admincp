<?php
if(!defined('_INCODE')) die('Access Deined...');
//Chứa chức năng đăng ký
$data =[
    'pageTitle' => 'Đăng ký tài khoản'
];
layout('header-login',$data);//require_once _WEB_PATH_TEMPALTE.'/layouts/header_login.php';

//Xử lý đăng ký
if(isPost()){
    //Validate form
    $body = getBody(); //Lấy tất cả dữ liệu trong form

    $errors = []; //Mảng lưu trữ các lỗi

    //Validate họ tên : Bắt buộc nhập , >= 5 ký tự
    if (empty(trim($body['fullname']))){
        $errors['fullname']['required'] = 'Họ tên bắt buộc phải nhập';
    }else{
        if(strlen(trim($body['fullname'])) <5){
            $errors['fullname']['min'] = 'Họ tên phải >= 5 kí tự';
        }
    }

    //Validate điện thoại: Bắt buộc phải nhập, định dạng số điện thoại
    if (empty(trim($body['phone']))){
        $errors['phone']['required'] ='Số điện thoại bắt buộc phải nhập';
    }else{
        if(!isPhone(trim($body['phone']))){
            $errors['phone']['isPhone'] ='Số điện thoại không hợp lệ';
        }
    }

    //Validate email: Bắt buộc phải nhập, định dạng email, email duy nhất
    if(empty(trim($body['email']))){
        $errors['email']['required'] ='Email bắt buộc phải nhập';
    }else{
        //Kiểm tra email không hợp lệ
        if(!isEmail(trim($body['email']))){
            $errors['email']['isEmail'] ='Email không hợp lệ';
        }else{
            //Kiểm tra email có tồn tại trong database hay không
            $email = trim($body['email']);
            $sql = "SELECT id FROM users WHERE email='$email'";
            if(getRows($sql) >0){
                $errors['email']['unique']='Địa chỉ email đã tồn tại';
            }
        }
    }

    //Validate mật khẩu: Bắt buộc phải nhập, >= 8 ký tự
    if(empty(trim($body['password']))){
        $errors['password']['required'] ='Mật khẩu không được để trống';
    }else{
        if(strlen(trim($body['password'])) <8){
            $errors['password']['min'] ='Mật khẩu không được nhỏ hơn 8 ký tự';
        }
    }

    //Validate nhập lại mật khẩu: Bắt buộc phải nhập, giống mật khẩu đã nhập 
    if(empty(trim($body['confirm_password']))){
        $errors['confirm_password']['required']='Nhập lại mật khẩu không được để trống';
    }else{
        if(trim($body['password']) != trim($body['confirm_password'])){
            $errors['confirm_password']['match']='Hai mật khẩu không khớp nhau';
        }
    }
    
    //Kiểm tra mảng errors
    if(empty($errors)){
        //Không có lỗi xảy ra
        $activeToken= sha1(uniqid().time());
        $dataInsert=[
            'email' => $body['email'],
            'fullname' => $body['fullname'],
            'phone' => $body['phone'],
            'password' => password_hash($body['password'], PASSWORD_DEFAULT),
            'activeToken' =>$activeToken,
            'createAt' => date('Y-m-d H:i:s')
        ];
        $insertStatus = insert('users', $dataInsert);
        if($insertStatus){

            //Tạo link kích hoạt
            $linkActive =_WEB_HOST_ROOT.'?module=auth&action=active&token='.$activeToken;
            //Thiết lập gửi email
            $subject =$body['fullname'].' Vui lòng kích hoạt tài khoản';
            $content ='Chào bạn: '.$body['fullname']. '<br/>';
            $content.='Vui lòng click vào link dưới đây để kích hoạt tài khoản: <br/>';
            $content.=$linkActive.'<br/>';
            $content.='Trân trọng!';

            //Tiến hành gửi email
            $sendStatus = sendMail($body['email'],$subject,$content);
            if($sendStatus){
                setFlashData('msg','Đăng ký tài khoản thành công.Vui lòng kiểm tra email để kích hoạt tài khoản.');
                setFlashData('msg_type','success');
            }else{
                setFlashData('msg','Hệ thống đang gặp sự cố.Vui lòng thử lại sau!');
                setFlashData('msg_type','danger');
            }
        }else{
            setFlashData('msg','Hệ thống đang gặp sự cố.Vui lòng thử lại sau!');
            setFlashData('msg_type','danger');
        }
        redirect('?module=auth&action=register');
    }else{
        //Có lỗi xảy ra
        setFlashData('msg', 'Vui lòng kiểm tra lại dữ liệu nhập vào');
        setFlashData('msg_type','danger');
        setFlashData('errors', $errors); // Lưu errors trước khi load lại trang
        setFlashData('old', $body); // Lưu dữ liệu vừa nhập vào textbox
        redirect('?module=auth&action=register'); //Load lại trang đăng ký
    }
}

$msg = getFlashData('msg');
$msgType= getFlashData('msg_type');
$errors = getFlashData('errors'); // Lưu errors trước khi load lại trang
$old = getFlashData('old');//Lưu dữ liệu vừa nhập vào textbox
?> 
<div class="row">
    <div class="col-6" style="margin: 20px auto">
        <h3 class="text-center text-uppercase">Đăng ký tài khoản</h3>
        <?php
            getMsg($msg ,$msgType);
        ?>
        <form action="" method="post">
            <div class="form-group">
                <label for="">Họ tên</label>
                <input type="text" name="fullname" class="form-control" placeholder="Họ tên..."
                    value="<?php echo old('fullname', $old); ?>"
                >
                <?php echo form_error('fullname',$errors,'<span class="errors">','</span>') ?>
            </div>
            <div class="form-group">
                <label for="">Số điện thoại</label>
                <input type="text" name="phone" class="form-control" placeholder="Số điện thoại..."
                    value="<?php echo old('phone', $old); ?>"
                >
                <?php echo form_error('phone',$errors,'<span class="errors">','</span>') ?>
            </div>
            <div class="form-group">
                <label for="">Email</label>
                <input type="text" name="email" class="form-control" placeholder="Địa chỉ email"
                    value="<?php echo old('email', $old); ?>"
                >
                <?php echo form_error('email',$errors,'<span class="errors">','</span>') ?>
            </div>
            <div class="form-group">
                <label for="">Mật khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="Mật khẩu....">
                <?php echo form_error('password',$errors,'<span class="errors">','</span>') ?>
            </div>
            <div class="form-group">
                <label for="">Nhập lại mật khẩu</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu....">
                <?php echo form_error('confirm_password',$errors,'<span class="errors">','</span>') ?>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Đăng ký</button>
            <hr>
            <p class="text-center"><a href="?module=auth&action=login">Đăng nhập hệ thống</a></p>
        </form>
    </div>
</div>
<?php
layout('footer-login');//require_once _WEB_PATH_TEMPALTE.'/layouts/footer_login.php';