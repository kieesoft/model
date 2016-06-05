<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/3
 * Time: 23:38
 */
define('WEEK',18); //定义学期教学总周数
/**
 * 生成一个GUID值,38位字符
 * @param null
 * @return string  {65034FBB-0526-F03D-5262-5C4314ABD0A7}
 */
function getGUID($sessionId){
    $guid = md5(time().$sessionId);
    $guid = "{".substr($guid,0,4).trim(chunk_split(substr($guid,4,-4),4, "-"),"-").substr($guid,-4)."}";
    return strtoupper($guid);
}
/**获取学年学期
 * @return mixed
 */
function getyearterm(){
    $year=date('Y',time());
    $month=date('m',time());
    if($month>=3&&$month<=8){
        $workyear['term']=2;
        $workyear['year']=$year-1;
    }
    elseif($month<3){
        $workyear['term']=1;
        $workyear['year']=$year-1;
    }
    else
    {
        $workyear['term']=1;
        $workyear['year']=$year;
    }
    return $workyear;
}
function getnextyearterm(){
    $year=date('Y',time());
    $month=date('m',time());
    if($month>=3&&$month<=8){
        $workyear['term']=1;
        $workyear['year']=$year;
    }
    elseif($month<3){
        $workyear['term']=2;
        $workyear['year']=$year-1;
    }
    else
    {
        $workyear['term']=2;
        $workyear['year']=$year;
    }
    return $workyear;
}
/**二进制反序
 * @param $string
 * @return string
 */
function bin_reserve($string){
    return join('',array_reverse(str_split($string)));
}
/**将周次转为二进制，不足位补零
 * @param $decimal
 * @return string
 */
function week_dec2bin($decimal){
    return str_pad(decbin($decimal),WEEK,'0',STR_PAD_LEFT);
}
/**将周次转为二进制，不足位补零, 并反序
 * @param $decimal
 * @return string
 */
function week_dec2bin_reserve($decimal){
    return bin_reserve(str_pad(decbin($decimal),WEEK,'0',STR_PAD_LEFT));
}

/**将一个数据集导出为excel表
 * @param string $filename 保存的文件名
 * @param array $array 包含$filename保存的文件，$sheet表名，$title表格标题，$template字段模板，$data数据集，$string需要以字符存储的数组
 * @throws PHPExcel_Exception
 * @throws PHPExcel_Reader_Exception
 */
function export2excel($filename='',$array=[]){
    import('Vendor.PHPExcel.PHPExcel');
    $PHPExcel = new \PHPExcel();
    set_time_limit(0);
    /*$sheet,$title,$template,$data,$string*/
    $count=count($array);
    $sheetIndex=0;
    for($i=0;$i<$count;$i++){
        $sheetIndex = $i;
        $sheet=$array[$i]['sheet'];
        $title=$array[$i]['title'];
        $template=$array[$i]['template'];
        $data=$array[$i]['data'];
        $string=$array[$i]['string'];
        //设置表名
        if($sheetIndex!=0) $PHPExcel->createSheet(); //不是第一个表就新建一个。
        $PHPExcel->setActiveSheetIndex($sheetIndex);
        $PHPExcel->getActiveSheet()->setTitle($sheet);
        $rowIndex = 1; //行从1开始
        $start = "A" . $rowIndex; //数据区域开始单元格。
        //如果标题不为空设置标题
        $colCount = count($template);
        if ($title != "") {
            $PHPExcel->getActiveSheet($sheetIndex)->mergeCellsByColumnAndRow(0, 1, $colCount - 1, 1);
            $PHPExcel->getActiveSheet($sheetIndex)->setCellValueByColumnAndRow(0, 1, $title);
            //设置font
            $styleArray = array(
                'font' => array('name' => '隶书', 'size' => '14', 'bold' => true),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            );
            $PHPExcel->getActiveSheet($sheetIndex)->getStyle('A1')->applyFromArray($styleArray);
            $rowIndex++;
            $start = "A" . $rowIndex;
        }
        $colIndex = 0;
        //设置列名
        foreach ($template as $v) {
            $PHPExcel->getActiveSheet($sheetIndex)->setCellValueByColumnAndRow($colIndex, $rowIndex, $v);
            $colIndex++;
        }
        $rowIndex++;
        foreach ($data as $row) {
            $colIndex = 0;
            foreach ($template as $k => $v) {
                if (in_array($k, $string)) //字段名在字符串格式化列表中
                    $PHPExcel->getActiveSheet($sheetIndex)->setCellValueExplicitByColumnAndRow($colIndex, $rowIndex, $row[$k], \PHPExcel_Cell_DataType::TYPE_STRING);
                else
                    $PHPExcel->getActiveSheet($sheetIndex)->setCellValueByColumnAndRow($colIndex, $rowIndex, $row[$k]);
                $colIndex++;
            }
            $rowIndex++;
        }
        //加边框
        $PHPExcel->getActiveSheet($sheetIndex)->getStyle($start . ':' . \PHPExcel_Cell::stringFromColumnIndex($colCount - 1) . (count($data) + 2))->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        $PHPExcel->getActiveSheet($sheetIndex)->getStyle($start . ':' . \PHPExcel_Cell::stringFromColumnIndex($colCount - 1) . (count($data) + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $PHPExcel->getActiveSheet($sheetIndex)->getColumnDimension() -> setAutoSize(true);
    }
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.iconv("UTF-8","GB2312",$filename).'"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
    header('Set-Cookie: fileDownload=true; path=/'); //与fileDownload配合，否则无法出发成功事件
    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0
    $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5'); //设置保存的excel
    $objWriter->save('php://output');
    exit;
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}