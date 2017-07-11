/**
 * Created by kalen on 16/11/30.
 */

import Api from '../../api'


export default {
  getSubjects() {
    let data = Api.get('/student/get_subjects', null);
    if (data != null) {
      return data.data;
    }
    return [];
  },

  getCourses(subject_id) {
    let data = Api.get('/student/get_courses', {subject_id: subject_id});
    if (data != null) {
      return data.data;
    }
    return [];
  },

  getOrders(page) {
    let data = Api.get('/student/get_orders', {page: page});
    if (data != null) {
      return data.data;
    }
    return [];
  },

  addOrder(course_id, reason) {
    return Api.get('/student/add_order', {
      course_id: course_id,
      reason : reason
    });
  },

  getInfo() {
    return Api.get('/student/info', null);
  },

  getCourseInfo(order_id) {
    let data = Api.get('/student/get_course_message', {
      order_id: order_id
    });
    if (data != null) {
      return data.data;
    }
    return null;
  }

}
