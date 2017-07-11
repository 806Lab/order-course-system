<?php
/**
 * Created by PhpStorm.
 * User: kalen
 * Date: 16/11/24
 * Time: 上午11:57
 */
defined('BASEPATH') OR exit('No direct script access allowed');


class Login extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model("Login_Model", "login_model");
        $this->load->model("Common_Model", "common_model");
        $this->load->model("Response_Model", "response_model");
    }

    /**
     * 登陆接口
     */
    function index(){
        $username = $this->input->get_post('uname', TRUE);
        $password = $this->input->get_post('passwd', TRUE);
        $type = $this->input->get_post('type', TRUE);
        $this->common_model->check_out_args($username, $password, $type);
        if (!is_numeric($type)) {
            $this->response_model->show_error('类型不存在');die();
        }

        switch ($type) {
            case TYPE_STUDENT: // 学生
                $this->handle_student_login($username, $password);
                break;
            case TYPE_TEACHER: // 老师
                $this->handle_teacher_login($username, $password);
                break;
            case TYPE_DIRECTOR: // 负责人
                $this->handle_director_login($username, $password);
                break;
            default:
                $this->response_model->show_error('类型不存在');die();
        }
    }
    
    private function handle_student_login($uname, $passwd) {
        if (!$this->login_model->is_student_exist($uname)){
            $this->response_model->show_error("用户不存在");
        } else if ($uname != $passwd) {
            $this->response_model->show_error("密码错误");
        } else {
            $session['type'] = TYPE_STUDENT;
            $session['username'] = $uname;
            $this->session->set_userdata($session);
            $this->response_model->show_success('登陆成功');
        }
    }

    private function handle_director_login($uname, $passwd) {
        if (!$this->login_model->is_director_exist($uname)){
            $this->response_model->show_error("用户不存在");
        } else if ( !$this->login_model->is_director_password_correct($uname, $passwd) ) {
            $this->response_model->show_error("密码错误");
        } else {
            $session['type'] = TYPE_DIRECTOR;
            $session['username'] = $uname;
            $this->session->set_userdata($session);
            $this->response_model->show_success('登陆成功');
        }
    }

    private function handle_teacher_login($uname, $passwd) {
        if (!$this->login_model->is_teacher_exist($uname)){
            $this->response_model->show_error("用户不存在");
        } else if ( !$this->login_model->is_teacher_password_correct($uname, $passwd) ) {
            $this->response_model->show_error("密码错误");
        } else {
            $session['type'] = TYPE_TEACHER;
            $session['username'] = $uname;
            $this->session->set_userdata($session);
            $this->response_model->show_success('登陆成功');
        }
    }

}
