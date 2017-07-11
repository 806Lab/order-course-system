<?php
/**
 * Created by PhpStorm.
 * User: kalen
 * Date: 16/11/24
 * Time: 上午11:46
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Teacher extends CI_Controller {

    private $uname;
    private $id;

    function __construct()
    {
        parent::__construct();
        $this->load->model("Response_Model", "response_model");
        if( $this->session->userdata('type') != TYPE_TEACHER ){
            $this->response_model->show(2, "你还没有登陆");die();
        }
        $this->uname = $this->session->userdata('username');
        $this->load->model("Common_Model", "common_model");
        $this->load->model("Teacher_Model", "teacher_model");
    }

    function index() {
        $this->info();
    }

    function info() {

    }


    function get_courses_page_num() {

    }

    function get_courses() {
        $page = $this->input->get_post('page');
    }

    function get_comments() {
        $course_id = $this->input->get_post('course_id', true);



    }


}
