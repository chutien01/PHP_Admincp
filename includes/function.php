<?php
if(!defined('_INCODE')) die('Access Deined...');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function layout($layoutName='header', $data =[]){
    if(file_exists(_WEB_PATH_TEMPALTE.'/layouts/'.$layoutName.'.php')){
        require_once _WEB_PATH_TEMPALTE.'/layouts/'.$layoutName.'.php';
    }
}

function sendMail($to, $subject, $content){
    $mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'chuanhtien01@gmail.com';                     //SMTP username
    $mail->Password   = 'rlcixiahjwlfykjt';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('chuanhtien01@gmail.com', 'Test PhpMailer');
    $mail->addAddress($to);     //Add a recipient
    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    
    $mail->CharSet ='UTF-8';
    $mail->Subject = $subject;
    $mail->Body    = $content;

    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    return $mail->send();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
}
//Kiểm tra phương thức POST
function isPost(){
    if($_SERVER['REQUEST_METHOD']=='POST'){
        return true;
    }
    return false;
}
//Kiểm tra phương thức GET
function isGet(){
    if($_SERVER['REQUEST_METHOD']=='GET'){
        return true;
    }
    return false;
}

//Lấy giá trị phương thức POST, GET
function getBody(){
    $bodyArr = [];
    if(isGet()){
        if(!empty($_GET)){
            foreach($_GET as $key => $value){
                if(is_array($value)){
                    $bodyArr[$key] = filter_input(INPUT_GET, $key , FILTER_SANITIZE_SPECIAL_CHARS,FILTER_REQUIRE_ARRAY);
                }else{
                    $bodyArr[$key] = filter_input(INPUT_GET, $key , FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
    }
    if(isPost()){
        if(!empty($_POST)){
            foreach($_POST as $key => $value){
                if(is_array($value)){
                    $bodyArr[$key] = filter_input(INPUT_POST, $key , FILTER_SANITIZE_SPECIAL_CHARS,FILTER_REQUIRE_ARRAY);
                }else{
                    $bodyArr[$key] = filter_input(INPUT_POST, $key , FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
    }
    return $bodyArr;
}
//Kiểm tra email
function isEmail($email){
    $checkEmail =filter_var($email, FILTER_VALIDATE_EMAIL);
    return $checkEmail;
}

//Kiểm tra số nguyên
function isNumberInt($number, $range=[]){
    //$range = ['min_range' =>1, 'max_range' =>20]
    if(!empty($range)){
        $options = ['options' =>$range];
        $checkNumber =filter_var($number,FILTER_VALIDATE_INT,$options);
    }else{
        $checkNumber =filter_var($number,FILTER_VALIDATE_INT);
    }
    return $checkNumber;
}

//Kiểm tra số thực
function isNumberFloat($number, $range=[]){
    //$range = ['min_range' =>1, 'max_range' =>20]
    if(!empty($range)){
        $options = ['options' =>$range];
        $checkNumber =filter_var($number,FILTER_VALIDATE_FLOAT,$options);
    }else{
        $checkNumber =filter_var($number,FILTER_VALIDATE_FLOAT);
    }
    return $checkNumber;
}

//Kiểm tra số điện thoại (Bắt đầu bằng số 0- Nối tiếp là 9 số)
function isPhone($phone){
    $checkFirstZero = false;
    if($phone[0] == '0'){
        $checkFirstZero = true;
        $phone = substr($phone, 1); //lấy dữ liệu từ vị trí 1 đến hết chuỗi
    }

    $checkNumberLast= false;
    
    if(isNumberInt($phone) && strlen($phone) == 9){
        $checkNumberLast =true;
    }
    
    if($checkFirstZero && $checkNumberLast){
        return true;
    }
    return false;
}

//Hàm tạo thông báo 
function getMsg($msg, $type='success'){
    if(!empty($msg)){
        echo '<div class="alert alert-'.$type.'">';
        echo $msg;
        echo '</div>';
    }
}

// Hàm chuyển hướng
function redirect($path='index.php'){
    header("Location: $path");
    exit;
}

//Hàm thông báo lỗi
function form_error($fieldName, $errors, $beforeHtml='', $afterHtml=''){
    return (!empty($errors[$fieldName]))?$beforeHtml.reset($errors[$fieldName]).$afterHtml:null;
}

//Hàm hiển thị dữ liệu cũ
function old($fieldName,$oldData,$default=null){
    return (!empty($oldData[$fieldName]))?$oldData[$fieldName]:$default;
}

//Hàm kiểm tra trạng thái login
function isLogin(){
    $checkLogin=false;
    if(getSession('loginToken')){
        $tokenLogin = getSession('loginToken');
        $queryToken =firstRaw("SELECT userId FROM login_token WHERE token='$tokenLogin'");
        if(!empty($queryToken)){
            //$checkLogin=true;
            $checkLogin = $queryToken;
        }else{
            removeSession('loginToken');
        }
    }
    return $checkLogin;
}

//Tự động đăng xuất sau 15phut 
//(Tự động xóa token login trong csdl bảng login_oken nếu đăng xuất)
function autoRemoveTokenLogin(){
    $allUsers = getRaw(" SELECT * FROM users WHERE status=1");
    if(!empty($allUsers)){
        foreach ($allUsers as $user){
            $now = date('Y-m-d H:i:s'); // Thời gian hiện tại
            $before = $user['lastActivity'];
            $diff = strtotime($now) - strtotime($before); // kết quả ra số giây
            $diff = floor($diff/60); // Đổi ra số phút
            if($diff >= 1){
                delete('login_token', "userId=".$user['id']);
            }
        }
    }
}

//Lưu lại thời gian cuối cùng hoạt động
function saveActivity(){
    $userId = isLogin()['userId'];
    update('users', ['lastActivity' =>date('Y-m-d H:i:s')], "id=$userId");
}

//Lấy thông tin user
function getUserInfo($userId){
    $info = firstRaw("SELECT * FROM users WHERE id =$userId");
    return $info;
}