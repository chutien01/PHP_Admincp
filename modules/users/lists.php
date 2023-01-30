<?php
if(!defined('_INCODE')) die('Access Deined...');
//Hiển thị danh sách người dùng
$data =[
    'pageTitle' => 'Quản lý người dùng'
];
layout('header', $data);

//Xử lý lọc dữ liệu
$filter ='';
if(isGet()){
    $body=getBody();

    //Xử lý lọc status
    if(!empty($body['status'])){
        $status =$body['status'];

        if($status==2){
            $statusSql=0; // trong sql status = 0 :Chưa kích hoạt
        }else{
            $statusSql=$status;
        }
        if(!empty($filter) && strpos($filter, 'WHERE')>=0){
            $operator ='AND';
        }else{
            $operator =' WHERE';
        }
        $filter .="WHERE status=$statusSql";
    }

    //Xử lý lọc theo từ khóa
    if(!empty($body['keyword'])){
        $keyword=$body['keyword'];

        if(!empty($filter) && strpos($filter, 'WHERE')>=0){
            $operator ='AND';
        }else{
            $operator =' WHERE';
        }
        $filter .= " $operator fullname LIKE '%$keyword%'";
    }
}

//Xử lý phân trang

$allUserNum= getRows("SELECT id FROM users $filter");

//1. Xác định được số lượng bản ghi trên 1 trang
$perPage =5; //Mỗi trang có 3 bản ghi
//2.Tính số trang
$maxPage =ceil($allUserNum / $perPage); // làm tròn đến số nguyên gần nhất
//3. Xử lý số trang dựa vào phương thức GET
if(!empty(getBody()['page'])){
    $page = getBody()['page'];
    if($page <1 && $page >$maxPage){ // Điều kiện sai
        $page =1;
    }
}else{
    $page =1;
}
//4. Tính toán offset trong limit dựa vào biến $page
//$page =1 => offset =0
//$page =2 => offset =3
//$page =3 => offset =6
// => Công thức tổng quát : ($page -1)*$perPage
$offset = ($page -1)*$perPage;

//Truy vấn lấy tất cả bản ghi
$listAllUser = getRaw("SELECT * FROM users $filter ORDER BY createAt DESC LIMIT $offset, $perPage");

//Xử lý query string tìm kiếm với phân trang
if(!empty($_SERVER['QUERY_STRING'])){
    $queryString = $_SERVER['QUERY_STRING'];
    $queryString = str_replace('module=users', '' , $queryString);
    $queryString = str_replace('&page='.$page, '' , $queryString);
    $queryString = trim($queryString, '&');
    $queryString = '&'.$queryString;
}

$msg = getFlashData('msg');
$msgType= getFlashData('msg_type');
?>
<div class="container">
    <hr>
    <h3><?php echo $data['pageTitle']; ?></h3>
    <p>
        <a href="?module=users&action=add" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Thêm người dùng</a>
    </p>
    <form action="" method="get">
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <select name="status" class="form-control">
                        <option value="0">Chọn trạng thái</option>
                        <option value="1"<?php echo (!empty($status) && $status==1)?'selected':false ?>>
                            Kích hoạt
                        </option>
                        <option value="2" <?php echo (!empty($status) && $status==2)?'selected':false ?> >
                            Chưa kích hoạt
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-6">
                <input type="search" class="form-control" name="keyword" placeholder="Từ khóa tìm kiếm"
                    value="<?php echo (!empty($keyword))?$keyword:false; ?>"
                >
            </div>
            <div class="col-3">
                <button type="submit" class="btn btn-primary btn-block">Tìm kiếm</button>
            </div>
            <input type="hidden" name="module" value="users">
        </div>
    </form>
    <?php
            getMsg($msg ,$msgType);
    ?>
    <table class="table table-bordered">
        <thead>
            <th width="5%">STT</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Số điện thoại</th>
            <th>Trạng thái</th>
            <th width="5%">Sửa</th>
            <th width="5%">Xóa</th>
        </thead>
        <tbody>
            <?php
                if(!empty($listAllUser)):
                    $count =0;//Hiển thị số thứ tự
                    foreach ($listAllUser as $item):
                        $count++;
            ?>
            <tr>
                <td><?php echo $count; ?></td>
                <td><?php echo $item['fullname']; ?></td>
                <td><?php echo $item['email']; ?></td>
                <td><?php echo $item['phone']; ?></td>
                <td><?php echo $item['status'] ==1 ?
                    '<button type="button" class="btn btn-success btn-sm">Kích hoạt</button>' :
                    '<button type="button" class="btn btn-warning btn-sm">Chưa kích hoạt</button>'; ?></td>
                <td><a href="<?php echo _WEB_HOST_ROOT.'?module=users&action=edit&id='.$item['id']; ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a></td>
                <td><a href="<?php echo _WEB_HOST_ROOT.'?module=users&action=delete&id='.$item['id']; ?>" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a></td>
            </tr>
            <?php endforeach; else: ?>
                <tr>
                    <td colspan="7">
                        <div class="alert alert-danger text-center">Không có người dùng</div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <?php
            if($page >1){
                $pervPage =$page -1;
                echo '<li class="page-item"><a class="page-link" href="'._WEB_HOST_ROOT.'?module=users'.$queryString.'&page='. $pervPage.'">Trước</a></li>';
            }
            ?>
            <?php 
            $begin = $page -2;
            if($begin <1){
                $begin =1;
            }
            $end = $page+2; // tối đa hiển thị 5 trang
            if($end >$maxPage){
                $end =$maxPage;
            }
            for ($index = $begin; $index <= $end; $index++) 
            {?>
            <li class="page-item <?php echo ($index==$page)?'active':false; ?>">
                <a class="page-link" href="<?php echo _WEB_HOST_ROOT.'?module=users'.$queryString.'&page='.$index; ?>"><?php 
                echo $index;?></a></li>
            <?php } ?>
            <?php
            if($page < $maxPage){
                $nextPage = $page +1;
                echo '<li class="page-item"><a class="page-link" href="'._WEB_HOST_ROOT.'?module=users'.$queryString.'&page='. $nextPage.'">Sau</a></li>';
            } 
            ?>
        </ul>
    </nav>
    <hr>
</div>
<?php
layout('footer');