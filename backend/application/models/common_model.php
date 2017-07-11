<?php


/**
 * Class Common_Model
 *
 * @property Response_Model $response_model
 */
class Common_Model extends CI_Model{

    const TAB_TIME = 'time';
    
    /**
     * 检测参数是否缺少
     *
     */
    function check_out_args(){
        $this->load->model("Response_Model", "response_model");
        $args = func_get_args();
        foreach ($args as $v){
            if ($v === false || is_null($v) || $v === ''){
                $this->response_model->show(1, "请求参数缺少");die();
            }
        }
    }
    
    /**
     * 判断日期是否有效
     * @param string $date
     * @param array|string $formats
     * @return boolean
     */
    function checkDateIsValid($date, $formats = array("Y-m-d", "Y/m/d")) {
        $unixTime = strtotime($date);
        if (!$unixTime) { //strtotime转换不对，日期格式显然不对。
            return false;
        }
        //校验日期的有效性，只要满足其中一个格式就OK
        foreach ($formats as $format) {
            if (date($format, $unixTime) == $date) {
                return true;
            }
        }
        return false;
    }
}