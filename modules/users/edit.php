<?php
if(!defined('_INCODE')) die('Access Deined...');
//Sửa người dùng

$data =[
    'pageTitle' => 'Sửa người dùng'
];
layout('header', $data);

//Lấy dữ liệu cũ của người dùng
$body = getBody();
if(!empty($body['id'])){
    $userId = $body['id'];

    //Kiểm tra userId có tồn tại trong Database hay không?
    //Nếu tồn tại =>Lấy ra thông tin
    //Nếu không tồn tại => Chuyển hướng về trang list
    $userDetail = firstRaw("SELECT * FROM users WHERE id=$userId");
    if(!empty($userDetail)){
        //Gán giá trị userDetail vào flashData
        setFlashData('userDetail', $userDetail);
    }else{
        redirect('?module=users');
    }
}else{
    redirect('?module=users');
}

//Xử lý sửa người dùng
if(isPost()){
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
            $sql = "SELECT id FROM users WHERE email='$email' AND id<>$userId";
            if(getRows($sql) >0){
                $errors['email']['unique']='Địa chỉ email đã tồn tại';
            }
        }
    }


    //Validate nhập lại mật khẩu: Bắt buộc phải nhập, giống mật khẩu đã nhập
    if(!empty(trim($body['password']))){
        if(empty(trim($body['confirm_password']))){
            $errors['confirm_password']['required']='Nhập lại mật khẩu không được để trống';
        }else{
            if(trim($body['password']) != trim($body['confirm_password'])){
                $errors['confirm_password']['match']='Hai mật khẩu không khớp nhau';
            }
        }
    }
    
    //Kiểm tra mảng errors
    if(empty($errors)){
        //Không có lỗi xảy ra
        $dataUpdate=[
            'email' => $body['email'],
            'fullname' => $body['fullname'],
            'phone' => $body['phone'],
            'status' => $body['status'],
            'updateAt' => date('Y-m-d H:i:s') 
        ];

        if(!empty(trim($body['password']))){
            $dataUpdate['password'] = password_hash($body['password'], PASSWORD_DEFAULT);
        }

        //$condition = "id=$userId";
        $updateStatus = update('users', $dataUpdate,'id='.$userId);
        if($updateStatus){
                setFlashData('msg','Cập nhật người dùng thành công.');
                setFlashData('msg_type','success');
                
            }else{
                setFlashData('msg','Hệ thống đang gặp sự cố.Vui lòng thử lại sau!');
                setFlashData('msg_type','danger');
            }
    }else{
        //Có lỗi xảy ra
        setFlashData('msg', 'Vui lòng kiểm tra lại dữ liệu nhập vào');
        setFlashData('msg_type','danger');
        setFlashData('errors', $errors); // Lưu errors trước khi load lại trang
        setFlashData('old', $body); // Lưu dữ liệu vừa nhập vào textbox
        
    }
        redirect('?module=users&action=edit&id='.$userId); //Load lại trang thêm người dùng
}
$msg = getFlashData('msg');
$msgType= getFlashData('msg_type');
$errors = getFlashData('errors'); // Lưu errors trước khi load lại trang
$old = getFlashData('old');//Lưu dữ liệu vừa nhập vào textbox
$userDetail =getFlashData('userDetail');
if(!empty($userDetail)){
    $old = $userDetail;
}
?>
<div class="container">
    <hr>
    <h3><?php echo $data['pageTitle']; ?></h3>
    <?php
            getMsg($msg ,$msgType);
    ?>
    <form action="" method="post">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="">Họ tên</label>
                    <input type="text" class="form-control" name="fullname" placeholder="Họ tên ..."
                        value="<?php echo old('fullname', $old); ?>"
                    >
                    <?php echo form_error('fullname',$errors,'<span class="errors">','</span>') ?>
                </div>
                <div class="form-group">
                    <label for="">Số điện thoại</label>
                    <input type="text" class="form-control" name="phone" placeholder="Số điện thoại ..."
                        value="<?php echo old('phone', $old); ?>"
                    >
                    <?php echo form_error('phone',$errors,'<span class="errors">','</span>') ?>
                </div>
                <div class="form-group">
                    <label for="">Email</label>
                    <input type="text" class="form-control" name="email" placeholder="Email ..."
                        value="<?php echo old('email', $old); ?>"
                    >
                    <?php echo form_error('email',$errors,'<span class="errors">','</span>') ?>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="">Mật khẩu</label>
                    <input type="password" class="form-control" name="password" placeholder="Mật khẩu ...">
                    <?php echo form_error('password',$errors,'<span class="errors">','</span>') ?>
                </div>
                <div class="form-group">
                    <label for="">Nhập lại mật khẩu</label>
                    <input type="password" class="form-control" name="confirm_password" placeholder="Nhập lại mật khẩu ...">
                    <?php echo form_error('confirm_password',$errors,'<span class="errors">','</span>') ?>
                </div>
                <div class="form-group">
                    <label for="">Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="0" <?php echo (old('status', $old)==0)?'selected':false ?>>Chưa kích hoạt</option>
                        <option value="1" <?php echo (old('status', $old)==1)?'selected':false ?>>Kích hoạt</option>
                    </select>
                </div>
            </div>
        </div>
        <hr>
        <button type="submit" class="btn btn-primary">Cập nhật người dùng người dùng</button>
        <a href="?module=users" class="btn btn-success">Quay lại</a>
        <input type="hidden" name="id" value="<?php echo $userId ?>">
    </form>
</div>
<?php
layout('footer');