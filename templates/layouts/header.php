<?php
if(!defined('_INCODE')) die('Access Deined...');
if(!isLogin()){
    redirect('?module=auth&action=login');
}else{
    $userId= isLogin()['userId'];
    $userDetail = getUserInfo($userId);
}
saveActivity();//Lưu lại hoạt động cuối cùng của users
autoRemoveTokenLogin();

?>
<html>
    <head>
        <meta charset="UTF-8">
        <link type="text/css" rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATE;?>/css/bootstrap.min.css">
        <link type="text/css" rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATE;?>/css/font-awesome.min.css">
        <link type="text/css" rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATE;?>/css/style.css?ver=<?php echo rand() ?>">
        <title>Quản lý người dùng</title>
    </head>
<body>
<header>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="<?php echo _WEB_HOST_ROOT.'?module=users'; ?>">HoangAn Unicode </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ">
            <li class="nav-item active">
                <a class="nav-link" href="<?php echo _WEB_HOST_ROOT.'?module=users'; ?>">Tổng quan</a>
            </li>
            <li class="nav-item dropdown profile">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                Hi, <?php echo $userDetail['fullname']; ?>
                </a>
                <div class="dropdown-menu">
                <a class="dropdown-item" href="#">Thông tin cá nhân</a>
                <a class="dropdown-item" href="#">Đổi mật khẩu</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?php echo _WEB_HOST_ROOT.'?module=auth&action=logout'; ?>">Đăng xuất</a>
                </div>
            </li>
            </ul>
            <!-- <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form> -->
        </div>
        </nav>
    </div>
</header>