<?php
/**
 * Created by PhpStorm.
 * User: kalen
 * Date: 16/11/24
 * Time: 上午11:57
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Student extends CI_Controller {

    private $uname;
    private $id;
    const ORDER_LIMIT = 25;
    
    function __construct()
    {
        parent::__construct();
        $this->load->model("Response_Model", "response_model");
        if( $this->session->userdata('type') != TYPE_STUDENT ){
            $this->response_model->show(2, "你还没有登陆");die();
        }
        $this->uname = $this->session->userdata('username');
        $this->load->model("Common_Model", "common_model");
        $this->load->model("Student_Model", "student_model");
        $info = $this->student_model->get_info($this->uname);
        $this->id = $info->id;
    }

    function index() {
        $this->info();
    }

    function info() {
        $info = $this->student_model->get_info($this->uname);
        $this->response_model->show(0, 'success', $info);
    }

    function get_orders_page_num() {
        $count = $this->student_model->get_orders_num($this->id);

        $page_num = round($count / self::ORDER_LIMIT) + 1;

        $this->response_model->show(0, 'success', [
            'page_num' => $page_num
        ]);
    }

    function get_orders() {
        $page = $this->input->get_post('page');
        $this->common_model->check_out_args($page);

        if (!is_numeric($page) && $page < 1) {
            $this->response_model->show_invalid('页数必须为大于0的数字');die();
        }

        $offset = ($page - 1) * self::ORDER_LIMIT;

        $orders = $this->student_model->get_orders($this->id, $offset, self::ORDER_LIMIT);

        $this->response_model->show(0, 'success', $orders);
    }


    function add_order() {
        $course_id = $this->input->get_post('course_id', true);
        $reason = $this->input->get_post('reason', true);
        $this->common_model->check_out_args($course_id, $reason);
        
        if (!$this->student_model->is_course_exist($course_id)) {
            $this->response_model->show_error('课程不存在');die();
        }

        if (!$this->student_model->is_course_current($course_id)) {
            $this->response_model->show_error('无法预约历史课程');die();
        }
        
        if ($this->student_model->is_order_exist($this->id, $course_id)) {
            $this->response_model->show_error('无法重复添加');die();
        }
        
        $this->student_model->add_order($course_id, $this->id, $reason);
        $this->response_model->show_success('添加成功');
    }
    
    

    function get_subjects() {
        $subjects = $this->student_model->get_subjects();
        $this->response_model->show(0, 'success', $subjects);
    }

    function get_courses() {
        $subject_id = $this->input->get_post('subject_id', true);
        $this->common_model->check_out_args($subject_id);

        $courses = $this->student_model->get_courses_by_subject_id($subject_id);
        $this->response_model->show(0, 'success', $courses);
    }

    function get_course_message() {
        $order_id = $this->input->get_post('order_id');
        $this->common_model->check_out_args($order_id);

        $order = $this->student_model->get_order($order_id);

        if ($order == false) {
            $this->response_model->show_error('预约不存在');die();
        }

        if ($order->student_id != $this->id) {
            $this->response_model->show_error('你没有权限查看此信息');die();
        }
        

        $course = $this->student_model->get_course($order->course_id);

        $this->response_model->show(0, 'success', $course->message);
    }


}
