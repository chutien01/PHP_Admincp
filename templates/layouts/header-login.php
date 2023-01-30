<?php
if(!defined('_INCODE')) die('Access Deined...');
autoRemoveTokenLogin();
//saveActivity();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link type="text/css" rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATE;?>/css/bootstrap.min.css">
        <link type="text/css" rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATE;?>/css/font-awesome.min.css">
        <link type="text/css" rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATE;?>/css/style.css?ver=<?php echo rand(); ?>">
        <title><?php echo !empty($data['pageTitle'])?$data['pageTitle']:'Unicode'; ?></title>
    </head>
<body>