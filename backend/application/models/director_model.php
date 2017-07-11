<?php
/**
 * Created by PhpStorm.
 * User: kalen
 * Date: 16/11/25
 * Time: 下午2:40
 */

class Director_Model extends CI_Model {

    const TAB_DIRECTOR = 'director';
    const TAB_TEACHER = 'teacher';
    const TAB_STUDENT = 'student';
    const TAB_SUBJECT = 'subject';
    const TAB_COURSE = 'course';
    const TAB_TIME = 'time';
    const TAB_ORDER = 'order';

    function get_info($uname) {
        $row = $this->db->get_where(self::TAB_DIRECTOR, [
            'username' => $uname
        ])->row();
        unset($row->password);

        // 获取课程名称
        $row->subject_name = $this->db->get_where(self::TAB_SUBJECT,
            ['id' => $row->subject_id])->row()->name;
        return $row;
    }

    /**
     * 获取从今天起往后的7天的日期
     */
    function get_dates() {
        $today_id = date("w");
        $res= array();
        for ($i = 0; $i < 10; $i++){
            $day_id = ($today_id + $i) % 7;
            $day = date("Y-m-d",strtotime("+".$i." day"));
            array_push($res, array( "id" => $day_id,"day" => $day ));
        }
        return $res;
    }

    function get_times() {
        return $this->db->get(self::TAB_TIME)->result_array();
    }

    function get_teachers($subject_id) {
        $teachers = $this->db->get_where(self::TAB_TEACHER, [
            'subject_id' => $subject_id
        ])->result();

        $result = [];
        foreach ($teachers as $teacher) {
            array_push($result, [
                'id' => $teacher->id,
                'name' => $teacher->name
            ]);
        }
        return $result;
    }


    function add_course($subject_id, $director_id, $time_id, $date, $teachers, $message){
        $data = array(
            'director_id' => $director_id,
            'subject_id' => $subject_id,
            'date_id' => date("w", strtotime($date)),
            'date' => $date,
            'time_id' => $time_id,
            'status' => STATUS_CURRENT,      //0 为本周课程，1为历史课程
            'message' => $message,
            'teachers' => $teachers,
        );
        $this->db->insert(self::TAB_COURSE, $data);
    }

    function is_course_exist($subject_id, $director_id, $time_id, $date, $teachers) {
        $where = array(
            'director_id' => $director_id,
            'subject_id' => $subject_id,
            'date' => $date,
            'time_id' => $time_id,
            'teachers' => $teachers,
        );
        $count = $this->db->get_where(self::TAB_COURSE, $where)->num_rows();
        if ($count != 0) {
            return true;
        }
        return false;
    }
    
    /**
     * 判断是否有操作一个课程的权限
     * @param $course_id
     * @param $subject_id
     * @param $director_id
     * @return bool
     */
    function can_operate_course($course_id, $subject_id, $director_id) {
        $where = array(
            'id' => $course_id,
            'director_id' => $director_id,
            'subject_id' => $subject_id,
            'status' => STATUS_CURRENT
        );
        $count = $this->db->get_where(self::TAB_COURSE, $where)->num_rows();
        if ($count != 1) {
            return false;
        }
        return true;
    }

    function move_course_to_history($course_id, $subject_id, $director_id) {
        $data = array(
            'status' => STATUS_HISTORY,      //0 为本周课程，1为历史课程
        );
        $this->db->where([
            "id" => $course_id,
            'director_id' => $director_id,
            'subject_id' => $subject_id,
            'status' => STATUS_CURRENT
        ]);
        $this->db->update(self::TAB_COURSE, $data);
    }

    function update_course($course_id, $subject_id, $director_id, $message){
        $data = [
            'message' => $message,
        ];
        $this->db->where([
            "id" => $course_id,
            'director_id' => $director_id,
            'subject_id' => $subject_id,
            'status' => STATUS_CURRENT
        ]);
        $this->db->update(self::TAB_COURSE, $data);
    }

    function delete_course($course_id){
        //先删除所有的order
        $this->db->where('course_id', $course_id);
        $this->db->delete(self::TAB_ORDER);

        //再删除课程
        $this->db->where('id', $course_id);
        $this->db->delete(self::TAB_COURSE);
    }


    function get_order($id) {
        $query = $this->db->get_where(self::TAB_ORDER, ['id' => $id]);
        if ($query->num_rows() != 1) {
            return false;
        }
        return $query->row();
    }


    function get_orders($course_id, $is_operated){
        $status_query = "status";
        if ($is_operated != STATUS_UNHANDLED) {
            $status_query .= " !=";
        }
        $this->db->order_by("id", "desc");
        $this->db->where(array(
            $status_query => STATUS_UNHANDLED,
            'course_id' => $course_id
        ));
        $query = $this->db->get(self::TAB_ORDER);
        $res = array();
        foreach ($query->result() as $v){
            $stu_info = $this->get_student_info_by_id($v->student_id);
            $val['id'] = $v->id;
            $val['stu_name'] = $stu_info->name;
            $val['stu_mobile'] = $stu_info->mobile;
            $val['reason'] = $v->reason;
            $val['status'] = $v->status;
            array_push($res, $val);
        }
        return $res;
    }

    private function get_student_info_by_id($id) {
        return $this->db->get_where(self::TAB_STUDENT, ['id' => $id])->row();
    }


    /**
     * 能否操作order
     * @param $order_id
     * @param $course_id
     * @param $subject_id
     * @return bool
     */
    function can_operate_order($order_id, $course_id, $subject_id) {
        $sid = $this->get_course_subject_id($course_id);
        //首先判断是不是此负责人管的
        if ($sid != $subject_id) {
            return false;
        }
        
        $query = $this->db->get_where(self::TAB_ORDER, ['id' => $order_id]);
        if ($query->num_rows() != 1) {
            return false;
        }
        
        return $course_id == $query->row()->course_id;
    }


    private function get_course_subject_id($course_id) {
        $query = $this->db->get_where(self::TAB_COURSE, ['id' => $course_id]);
        if ($query->num_rows() != 1) {
            return false;
        }

        return $query->row()->subject_id;
    }


    function update_order($order_id, $status, $feedback){
        if ($feedback === FALSE || is_null($feedback)){
            $feedback = '';
        }
        $data = array(
            'status' => $status,
            'feedback' => $feedback,
            'handle_time' => time()
        );
        $this->db->where(array("id" => $order_id));
        $this->db->update(self::TAB_ORDER, $data);
    }

    function get_courses_num($subject_id, $director_id, $status, $offset = null, $limit = null) {
        $this->db->where([
            'subject_id' => $subject_id,
            'director_id' => $director_id,
            'status' => $status
        ]);
        if ( !is_null($offset) && !is_null($limit)) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get(self::TAB_COURSE);
        return $query->num_rows();
    }

    function get_course($id) {
        $query = $this->db->get_where(self::TAB_COURSE, ['id' => $id]);
        if ($query->num_rows() != 1) {
            return false;
        }
        return $query->row();
    }

    function get_courses($subject_id, $director_id, $status, $offset = null, $limit = null) {

        $this->db->where([
            'subject_id' => $subject_id,
            'director_id' => $director_id,
            'status' => $status
        ]);
        $this->db->order_by("id", "desc");
        if ( !is_null($offset) && !is_null($limit)) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get(self::TAB_COURSE);
        $result = [];
        foreach ($query->result() as $v) {
            $val = $v;
            $val->unhandled_num = $this->get_order_num($v->id, STATUS_UNHANDLED);
            $val->accepted_num = $this->get_order_num($v->id, STATUS_ACCEPTED);
            array_push($result, $val);
        }
        return $result;
    }

    function get_order_num($course_id, $status) {
        $query = $this->db->get_where(self::TAB_ORDER, [
            'course_id' => $course_id,
            'status' => $status
        ]);
        return $query->num_rows();
    }
    
}