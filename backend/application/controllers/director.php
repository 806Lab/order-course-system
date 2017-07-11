<?php
/**
 * Created by PhpStorm.
 * User: kalen
 * Date: 16/11/25
 * Time: 上午10:22
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Director extends CI_Controller {
    
    private $uname;
    private $subject_id;
    private $director_id;
    const   COURSE_LIMIT = 15;

    function __construct()
    {
        parent::__construct();
        $this->load->model("Response_Model", "response_model");
//        echo $this->session->userdata('type');
        if( $this->session->userdata('type') != TYPE_DIRECTOR ) {
            $this->response_model->show(2, "你还没有登陆");die();
        }
        $this->uname = $this->session->userdata('username');

        $this->load->model("Common_Model", "common_model");
        $this->load->model("Director_Model", "director_model");

        $info = $this->director_model->get_info($this->uname);
        $this->subject_id = $info->subject_id;
        $this->director_id = $info->id;
    }

    function update_password() {
        $old_password = $this->input->get_post('old_password', true);
        $new_password = $this->input->get_post('new_password', true);
    }

    function index() {
        $this->info();
    }

    function info() {
        $info = $this->director_model->get_info($this->uname);
        $this->response_model->show_json(0, 'success', $info);
    }
    

    function get_teachers() {
        $teachers = $this->director_model->get_teachers($this->subject_id);
        $this->response_model->show_json(0, 'success', $teachers);
    }


    function get_times() {
        $times = $this->director_model->get_times();
        $this->response_model->show(0, "success", $times);
    }

    /**
     * 获取七天内的日期和星期
     */
    function get_dates() {
        $dates = $this->director_model->get_dates();
        $this->response_model->show(0, "success", $dates);
    }

    
    function add_course() {
        $teachers = $this->input->get_post('teachers', TRUE);
        $time_id = $this->input->get_post('time_id', TRUE);
        $date = $this->input->get_post('date', TRUE);
        $message = $this->input->get_post('message', TRUE);

        $this->common_model->check_out_args($teachers, $time_id, $date, $message);

        //判断是否是合法日期
        if (!$this->common_model->checkDateIsValid($date)){
            $this->response_model->show_invalid("日期不合法");die();
        }

        if (is_null(json_decode($teachers))) {
            $this->response_model->show_invalid('教师格式不合法');die();
        }

        if ($this->director_model->is_course_exist($this->subject_id, $this->director_id,
            $time_id, $date, $teachers)){
            $this->response_model->show_error('无法重新添加课程');die();
        }

        $this->director_model->add_course($this->subject_id, $this->director_id,
            $time_id, $date, $teachers, $message);
        $this->response_model->show_success('添加成功');
    }

    function update_course() {
        $course_id = $this->input->get_post('course_id', TRUE);
        $message = $this->input->get_post('message', TRUE);

        $this->common_model->check_out_args($course_id, $message);

        // 判断是否有权限操作此课程
        $can = $this->director_model->can_operate_course($course_id, $this->subject_id, $this->director_id);
        if (!$can) {
            $this->response_model->show_invalid("你没有权限修改这门课程");die();
        }

        //更新数据库
        $this->director_model->update_course($course_id, $this->subject_id, $this->director_id, $message);
        $this->response_model->show_success('更新成功');
    }

    function delete_course() {
        $course_id = $this->input->get_post('course_id', TRUE);
        $this->common_model->check_out_args($course_id);

        // 判断是否有权限操作此课程
        $can = $this->director_model->can_operate_course($course_id, $this->subject_id, $this->director_id);
        if (!$can) {
            $this->response_model->show_invalid("你没有权限修改这门课程");die();
        }

        //更新数据库
        $this->director_model->delete_course($course_id);
        $this->response_model->show_success('删除成功');
    }

    /**
     * 把本周的课程加入历史
     */
    function move_course_to_history() {
        $course_id = $this->input->get_post('course_id');
        $this->common_model->check_out_args($course_id);

        // 判断是否有权限操作此课程
        $can = $this->director_model->can_operate_course($course_id, $this->subject_id, $this->director_id);
        if (!$can) {
            $this->response_model->show_invalid("你没有权限修改这门课程");die();
        }

        //更新数据库
        $this->director_model->move_course_to_history($course_id, $this->subject_id, $this->director_id);
        $this->response_model->show_success();
    }

    /**
     * 获取本周课程
     */
    function get_current_courses() {
        $status = STATUS_CURRENT;
        $data = $this->director_model->get_courses($this->subject_id, $this->director_id, $status);
        $this->response_model->show(0, 'success', $data);
    }

    /**
     * 获取历史课程的页数
     */
    function get_history_courses_page_num() {
        $status = STATUS_HISTORY;
        $num_rows = $this->director_model->get_courses_num($this->subject_id, $this->director_id,
            $status);
        $page_num = round($num_rows / self::COURSE_LIMIT) + 1;
        $this->response_model->show(0, 'success', [
            'page_num' => $page_num
        ]);
    }

    /**
     * 获取历史课程
     */
    function get_history_courses() {
        $page = $this->input->get_post('page');
        $status = STATUS_HISTORY;
        $this->common_model->check_out_args($page);

        if (!is_numeric($page) && $page < 1) {
            $this->response_model->show_invalid('页数必须为大于0的数字');die();
        }

        $offset = ($page - 1) * self::COURSE_LIMIT;

        $data = $this->director_model->get_courses($this->subject_id, $this->director_id,
            $status, $offset, self::COURSE_LIMIT);
        $this->response_model->show(0, 'success', $data);
    }

    /**
     * 获取订单
     */
    function get_orders() {
        $course_id = $this->input->get_post('course_id');
        $is_operated = $this->input->get_post('is_operated');
        
        $this->common_model->check_out_args($course_id, $is_operated);
        if ( !in_array($is_operated, [0, 1])){
            $this->response_model->show_invalid('参数格式错误');die();
        }

        // 判断是否有权限操作此课程
        $can = $this->director_model->can_operate_course($course_id, $this->subject_id, $this->director_id);
        if (!$can) {
            $this->response_model->show_invalid("你没有权限查看这门课程");die();
        }

        //获取数据
        $orders = $this->director_model->get_orders($course_id, $is_operated);
        $this->response_model->show(0, 'success', $orders);
    }

    function get_course() {
        $course_id = $this->input->get_post('course_id', true);
        $this->common_model->check_out_args($course_id);

        // 判断是否有权限操作此课程
        $can = $this->director_model->can_operate_course($course_id, $this->subject_id, $this->director_id);
        if (!$can) {
            $this->response_model->show_invalid("你没有权限处理这门课程");die();
        }

        $course = $this->director_model->get_course($course_id);
        if ($course == false) {
            $this->response_model->show_error('课程不存在');die();
        }
        $this->response_model->show(0, 'success', $course);

    }

    /**
     * 处理订单
     */
    function handle_order() {
        $order_id = $this->input->get_post('order_id');
        $status = $this->input->get_post('status');
        $feedback = $this->input->get_post('feedback');
        $this->common_model->check_out_args($order_id, $status);

        if ( !in_array($status, [1, 2]) ) {
            $this->response_model->show_invalid('请求参数无效');die();
        }

        $order = $this->director_model->get_order($order_id);
        if ($order == false) {
            $this->response_model->show_invalid('订单不存在');die();
        }

        if ($order->status != STATUS_UNHANDLED) {
            $this->response_model->show_error('已处理的订单无法重新处理');die();
        }

        $course_id = $order->course_id;

        // 判断是否有权限操作此课程
        $can = $this->director_model->can_operate_course($course_id, $this->subject_id, $this->director_id);
        if (!$can) {
            $this->response_model->show_invalid("你没有权限处理这门课程");die();
        }

        // 判断是否有权限操作此课程
        $can2 = $this->director_model->can_operate_order($order_id, $course_id, $this->subject_id);
        if (!$can2) {
            $this->response_model->show_invalid("你没有权限处理这个预约");die();
        }

        $this->director_model->update_order($order_id, $status, $feedback);
        $this->response_model->show_success('操作成功');
    }

}
