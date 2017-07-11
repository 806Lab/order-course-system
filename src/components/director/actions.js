/**
 * Created by kalen on 16/11/30.
 */

import Api from '../../api'

export default {
  getTeachers() {
    let data = Api.get('/director/get_teachers', null);
    if (data != null) {
      return data.data;
    }
    return [];
  },

  getTimes() {
    let data = Api.get('/director/get_times', null);
    if (data != null) {
      return data.data;
    }
    return [];
  },

  getDates() {
    let data = Api.get('/director/get_dates', null);
    if (data != null) {
      return data.data;
    }
    return [];
  },

  addCourse(teachers, time_id, date, message) {
    return Api.get('/director/add_course', {
      teachers: teachers,
      time_id: time_id,
      date: date,
      message: message
    });
  },

  getCurrentCourses() {
    let data = Api.get('/director/get_current_courses', null);
    if (data != null) {
      return data.data;
    }
    return [];
  },

  getHistoryCoursePageNum() {
    let data = Api.get('/director/get_history_courses_page_num');
    if (data != null) {
      return data.data;
    }
    return 1;
  },

  getHistoryCourses(page) {
    let data = Api.get('/director/get_history_courses', {page: page});
    if (data != null) {
      return data.data;
    }
    return [];
  },

  deleteCourse(course_id) {
    return Api.get('/director/delete_course', {
      course_id: course_id
    });
  },

  getCourse(course_id) {
    return Api.get('/director/get_course', {
      course_id: course_id
    })
  },

  updateCourse(course_id, message) {
    return Api.get('/director/update_course', {
      course_id: course_id,
      message: message
    })
  },

  moveCourseToHistory(course_id) {
    return Api.get('/director/move_course_to_history', {
      course_id: course_id
    })
  },

  getOrders(is_operated, course_id) {
    let data = Api.get('/director/get_orders', {
      is_operated: is_operated,
      course_id: course_id
    });
    if (data != null) {
      return data.data;
    }
    return [];
  },

  handleOrder(order_id, status) {
    return Api.get('/director/handle_order', {
      order_id: order_id,
      status: status
    });
  },

  getInfo() {
    return Api.get('/director/info', null);
  }




}
