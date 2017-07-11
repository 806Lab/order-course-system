<?php
class Login_Model extends CI_Model{
    
    const TAB_STUDENT = 'student';
    const TAB_TEACHER = 'teacher';
    const TAB_DIRECTOR = 'director';


    function  is_student_exist($username){
        $query = $this->db->get_where(self::TAB_STUDENT, [
            'username' => $username
        ]);
        if ($query->num_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

    function  is_director_exist($username){
        $query = $this->db->get(self::TAB_DIRECTOR);
        foreach ($query->result() as $row){
            if($row->username == $username){
                return true;
            }
        }
        return false;
    }

    function  is_teacher_exist($username){
        $query = $this->db->get(self::TAB_TEACHER);
        foreach ($query->result() as $row){
            if($row->username == $username){
                return true;
            }
        }
        return false;
    }



    function is_teacher_password_correct($uname, $passwd) {
        if (!$this->is_teacher_exist($uname)) {
            return false;
        }
        $query = $this->db->get_where(self::TAB_TEACHER, array(
            'username' => $uname,
            'password' => md5($passwd)
        ));
        if ($query->num_rows() != 1) {
            return false;
        } else {
            return true;
        }
    }

    function is_director_password_correct($uname, $passwd) {
        if (!$this->is_director_exist($uname)) {
            return false;
        }
        $query = $this->db->get_where(self::TAB_DIRECTOR, array(
            'username' => $uname,
            'password' => md5($passwd)
        ));
        if ($query->num_rows() != 1) {
            return false;
        } else {
            return true;
        }
    }
}