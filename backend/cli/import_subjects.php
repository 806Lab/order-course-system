<?php
/**
 * Created by PhpStorm.
 * User: kalen
 * Date: 16/12/2
 * Time: 下午11:18
 */


ini_set('date.timezone','Asia/Shanghai');

require_once './vendor/autoload.php';


$reader = \PHPExcel_IOFactory::createReader('Excel2007');
$PHPExcel = $reader->load('./subjects.xlsx'); // 载入excel文件
$sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
$highestRow = $sheet->getHighestRow(); // 取得总行数
$highestColumm = $sheet->getHighestColumn(); // 取得总列数

$db = new \MysqliDb ('127.0.0.1', 'root', 'root', 'order-course-system');

/** 循环读取每个单元格的数据 */
for ($row = 2; $row <= $highestRow; $row++){//行数是以第1行开始

    $name = '';
    $id = '';

    for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
        $value = $sheet->getCell($column.$row)->getValue();
        echo $column.' '.$value.' ';
        switch($column) {
            case 'A':
                $name = $value;
                break;
            case 'B':
                $id = $value;
                break;
        }
    }
    echo "\n";
    echo $name.' '.$id."\n";
    $data = [
        "id" => $id,
        "name" => $name
    ];
    $id = $db->insert ('subject', $data);
    if($id){
        echo '课程:' . $name.' 创建成功!'."\n";
    }
    else {
        echo '课程:' . $name.' 创建失败!'."\n";
    }
}