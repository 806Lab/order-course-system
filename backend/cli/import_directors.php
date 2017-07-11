<?php
/**
 * Created by PhpStorm.
 * User: kalen
 * Date: 16/12/2
 * Time: 下午11:27
 */


ini_set('date.timezone','Asia/Shanghai');

require_once './vendor/autoload.php';


$reader = \PHPExcel_IOFactory::createReader('Excel2007');
$PHPExcel = $reader->load('./director.xlsx'); // 载入excel文件
$sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
$highestRow = $sheet->getHighestRow(); // 取得总行数
$highestColumm = $sheet->getHighestColumn(); // 取得总列数

$db = new \MysqliDb ('127.0.0.1', 'root', 'root', 'order-course-system');

/** 循环读取每个单元格的数据 */
for ($row = 2; $row <= $highestRow; $row++){//行数是以第1行开始

    $username = '';
    $name = '';
    $password = '';
    $subject_id = '';

    for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
        $value = $sheet->getCell($column.$row)->getValue();
//        echo $column.' '.$value.' ';
        switch($column) {
            case 'D':
                $username = $value;
                break;
            case 'E':
                $password = md5($value);
                break;
            case 'C':
                $name = $value;
                break;
            case 'B':
                $subject_id = $value;
                break;
        }
    }
//    echo "\n";
    echo $username.' '. $name. ' '.$password.' '.$subject_id."\n";
//    echo $username.' '.$name.' '.$unit_info.' '.$mobile."\n";
    $data = [
        "username" => $username,
        "name" => $name,
        "password" => $password,
        "subject_id" => $subject_id
    ];
    $id = $db->insert ('director', $data);
    if($id){
        echo '负责人:' . $username.' 创建成功!'."\n";
    }
    else {
        echo '负责人:' . $username.' 创建失败!'."\n";
    }
}