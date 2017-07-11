<?php
/**
 * Created by PhpStorm.
 * User: kalen
 * Date: 16/12/2
 * Time: 下午10:12
 */

ini_set('date.timezone','Asia/Shanghai');

require_once './vendor/autoload.php';


$reader = \PHPExcel_IOFactory::createReader('Excel2007');
$PHPExcel = $reader->load('./student.xlsx'); // 载入excel文件
$sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
$highestRow = $sheet->getHighestRow(); // 取得总行数
$highestColumm = $sheet->getHighestColumn(); // 取得总列数

$db = new \MysqliDb ('127.0.0.1', 'root', 'root', 'order-course-system');

/** 循环读取每个单元格的数据 */
for ($row = 2; $row <= $highestRow; $row++){//行数是以第1行开始

    $name = '';
    $username = '';
    $unit_info = '';
    $mobile = 'NULL';

    for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
        $value = $sheet->getCell($column.$row)->getValue();
        switch($column) {
            case 'A':
                $username = $value."";
                break;
            case 'B':
                $name = $value."";
                break;
            case 'C':
                $unit_info = $value."";
                break;
            #case 'D':
            #    $mobile = $value;
            #    break;
        }
    }
    echo $username.'-'.$name.'-'.$unit_info.'-'.$mobile."\n";
    // $name = '';
    // $username = '';
    // $unit_info = '';
    // $mobile = 'NULL';
    $data = [
        "username" => $username,
        "name" => $name,
        "unit_info" => $unit_info,
        "mobile" => $mobile,
    ];
    // var_dump($data);
    $id = $db->insert('student', $data);
    if($id){
        echo '用户:' . $username.' 创建成功!'."\n";
    }
    else {
        echo '用户:' . $username.' 创建失败!'."\n";
    }
}
