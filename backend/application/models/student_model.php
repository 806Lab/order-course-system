<?php
/**
 * Created by PhpStorm.
 * User: kalen
 * Date: 16/11/25
 * Time: 下午2:37
 */

class Student_Model extends CI_Model {

    const TAB_STUDENT = 'student';
    const TAB_COURSE = 'course';
    const TAB_SUBJECT = 'subject';
    const TAB_ORDER = 'order';


    function get_info($uname) {
        $row = $this->db->get_where(self::TAB_STUDENT, [
            'username' => $uname
        ])->row();

        return $row;
    }

    function is_course_exist($course_id) {
        return $this->db->get_where(self::TAB_COURSE, ['id' => $course_id])->num_rows() == 1;
    }

    function is_course_current($course_id) {
        $course = $this->db->get_where(self::TAB_COURSE, ['id' => $course_id])->row();
        return $course->status == STATUS_CURRENT;
    }

    function is_order_exist($student_id, $course_id) {
        $query = $this->db->get_where(self::TAB_ORDER, [
            'student_id' => $student_id,
            'course_id' => $course_id
        ]);
        return $query->num_rows() == 1;
    }

    function add_order($course_id, $student_id, $reason) {
        $data = array(
            'course_id' => $course_id,
            'student_id' => $student_id,
            'status' => STATUS_UNHANDLED,
            'reason' => $reason,
            'commit_time' => time(),
            'handle_time' => 0,
        );
        $this->db->insert(self::TAB_ORDER, $data);
    }

    function get_order($order_id) {
        $query = $this->db->get_where(self::TAB_ORDER, ['id' => $order_id]);
        if ($query->num_rows() != 1) {
            return false;
        }
        return $query->row();
    }

    function get_subjects() {
        return $this->db->get(self::TAB_SUBJECT)->result();
    }

    function get_orders_num($stu_id) {
        $this->db->where(['student_id' => $stu_id]);
        $query = $this->db->get(self::TAB_ORDER);
        return $query->num_rows();
    }

    function get_orders($stu_id, $offset, $limit) {
        $this->db->where(['student_id' => $stu_id]);
        $this->db->order_by("id", "desc");
        $this->db->limit($limit, $offset);
        $query = $this->db->get(self::TAB_ORDER);
        $result = [];
        foreach ($query->result() as $value) {
            $val = $value;
            $val->course = $this->get_course($value->course_id);
            array_push($result, $val);
        }
        return $result;
    }

    function get_course($course_id) {
        return $this->db->get_where(self::TAB_COURSE, ['id' => $course_id])->row();
    }

    function get_courses_by_subject_id($subject_id) {
        $courses = $this->db->get_where(self::TAB_COURSE, [
            'subject_id' => $subject_id,
            'status' => STATUS_CURRENT
        ])->result();
        return $courses;
    }


}