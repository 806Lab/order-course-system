<template>
  <Navbar></Navbar>

  <div class="container-fluid" style="padding-top: 70px">
    <div class="row">

      <div class="col-md-6">
        <Row class="" style=" margin-top:20px " >
          <i-col span="3">
            <label>选择科目:</label>
          </i-col>
          <i-col span="10">
            <i-select :model.sync="subjectSelected" style="">
              <i-option v-for="subject in subjects" :value="subject.id">{{ subject.name }}</i-option>
            </i-select>
          </i-col>
        </Row>

        <Row style=" margin-top:22px " >
          <i-col span="3">
            <label>选择课程:</label>
          </i-col>
          <i-col span="10">
            <i-select :model.sync="course_id" style="">
              <i-option v-for="course in courses" :value="course.id">{{ getCourseInfo(course) }}</i-option>
            </i-select>
          </i-col>
        </Row>
      </div>

      <div class="col-md-6" class="box">
        <Row style=" height:90px ;margin-top:22px" >
          <i-col span="3">
            <label >申请原因:</label>
          </i-col>
          <i-col span="10">
            <i-input :value.sync="reason" type="textarea" :rows="4"   placeholder="请输入你想上课学习的内容(包括章节)"></i-input>
          </i-col>
        </Row>
      </div>

    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="text-center" style="margin-top:30px">
          <i-button @click="submit" type="primary" style="">提交</i-button>
        </div>
      </div>
    </div>
  </div>



  <div v-if="orders.length == 0">
    暂无预约
  </div>

  <div style="margin: 8px" v-else>
    <h3>查看预约</h3>
    <table class="table table-striped table-hover">
      <thead>
      <tr>
        <th>日期</th>
        <th>时间</th>
        <th>科目</th>
        <th>上课老师</th>
        <th>原因</th>
        <th><center>状态</center></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="order in orders">
        <td>{{ order.course.date }}</td>
        <td>{{ parseTime(order.course.time_id) }}</td>
        <td>{{ parseSubject(order.course.subject_id) }}</td>
        <td>{{ parseTeachers(order.course.teachers) }}</td>
        <td>{{ order.reason }}</td>
        <td>
          <center>
            {{ parseStatus(order.status) }}
            <div v-if=" order.status == '1' ">
              <i-button @click="showCourseInfo(order.id)" type="info">查看教室</i-button>
            </div>
          </center>
        </td>
      </tr>
      </tbody>
    </table>
  </div>

  <Modal
    :visible.sync="modal"
    title="教室信息">
    <p>上课地点: {{ courseInfo }}</p>
  </Modal>

</template>

<script>
  import $ from 'jquery'
  import { Row, iCol, Message, iSelect, iOption, iInput, iButton, Modal } from 'iview'
  import { course_times } from '../../consts'
  import Actions from './actions'
  import { parseTeachers } from '../../utils'
  import Navbar from './navbar.vue'

  export default {
    components: {
      Row, iCol, iSelect, iOption, iInput, iButton, Navbar, Modal
    },
    data () {
      return {
        subject_id: '',
        course_id: '',
        subjects: [],
        courses: [],
        reason: '',
        orders: [],
        courseInfo: '',
        modal: false
      }
    },
    computed: {
      subjectSelected: {
        get() {
          return this.subject_id;
        },
        set(subject_id) {
          this.getCourses(subject_id);
          this.subject_id = subject_id;
        }
      }
    },
    ready() {
      if (Actions.getInfo() == null) {
        this.$route.router.go('/login');
        return;
      }
      this.getSubjects();
      this.getOrders();
    },
    methods: {
      parseSubject(subject_id) {
        for (let i = 0; i < this.subjects.length; i++) {
          if(this.subjects[i].id == subject_id) {
            return this.subjects[i].name;
          }
        }
        return '未知课程';
      },
      parseTime(time_id) {
        return course_times[parseInt(time_id) - 1];
      },
      parseTeachers(teachers){
        return parseTeachers(teachers);
      },
      getSubjects() {
        this.subjects = Actions.getSubjects();
      },
      getCourses(subject_id) {
        this.courses = Actions.getCourses(subject_id);
      },
      getOrders() {
        this.orders = Actions.getOrders(1);
      },

      getCourseInfo(course) {
        return course.date + ' ' + course_times[course.time_id - 1] + '(' +
          this.parseTeachers(course.teachers) + ')'
      },
      submit() {
        let data = Actions.addOrder(this.course_id, this.reason);
        if (data != null) {
          Message.info(data.msg);
          //提交后刷新
          this.getOrders();
        }
      },
      parseStatus(status) {
        switch (status) {
          case '0': return '待审核';
          case '1': return '已通过';
          case '2': return '未通过';
        }
      },
      showCourseInfo(order_id) {
//        console.log(order_id);
        let info = Actions.getCourseInfo(order_id);
        if (info != null) {
          this.courseInfo = info;
          if (info == '') {
            this.courseInfo = '负责人未填写教室信息';
          }
          this.modal = true;
        }

      }
    }
  }

</script>

<style scoped>

  /* .container{
     display: flex;
     margin: 30px
   }

   .box{
     flex: 1;
   }
   label {
     line-height: 32px;
   }*/
</style>
