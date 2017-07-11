<template>
  <Navbar></Navbar>
  <div id="addCourse" style="padding-top: 70px">
    <h3>修改课程信息</h3>
    <p>已选老师: {{ parseTeachers(course.teachers) }}</p>
    <p>日期: {{ course.date }}</p>
    <p>时间: {{ parseTime(course.time_id - 1) }}</p>
    <p>备注:</p>
    <i-input :value.sync="message" type="textarea" placeholder="请输入..." :rows="8" style="width: 300px"></i-input>
    <br>
    <i-button v-on:click="updateCourse" type="info">修改</i-button>
    <i-button v-on:click="moveCourseToHistory" type="warning">移动到历史</i-button>
  </div>
  <br/>
  <h3>订单信息</h3>
  <center>
    <Radio-group :model.sync="type" type="button">
      <Radio value="未处理"></Radio>
      <Radio value="已处理"></Radio>
    </Radio-group>
  </center>

  <br>
  <div v-if="type == '未处理'">
    <Order :is_operated="0"></Order>
  </div>
  <div v-else>
    <Order :is_operated="1"></Order>
  </div>

</template>

<script>
  import Navbar from './navbar.vue'
  import Order from './order.vue'
  import { Radio, RadioGroup, iButton, iInput, Message } from 'iview'
  import Actions from './actions'
  import { parseTeachers } from '../../utils'
  import { course_times } from '../../consts'


  export default {
    components: {
      Navbar, RadioGroup, iButton, Radio, iInput, Order
    },
    data() {
      return {
        type: '未处理',
        course: '',
        course_id: null,
        message: ''
      }
    },
    created() {
      if (Actions.getInfo() == null) {
        this.$route.router.go('/login');
        return;
      }
      this.course_id = this.$route.params.id;
      let course = Actions.getCourse(this.course_id).data;
      if (course == null) {
        this.$route.router.go('/director');
      }
      console.log(course);
      this.course = course;
      this.message = course.message;

    },
    methods: {
      parseTime(time_id) {
        return course_times[time_id];
      },
      parseTeachers(json) {
        console.log(json);
        return parseTeachers(json);
      },
      updateCourse() {
        let data = Actions.updateCourse(this.course_id, this.message);
        if (data != null) {
          Message.info(data.msg)
        }
      },

      moveCourseToHistory() {
        let data = Actions.moveCourseToHistory(this.course_id);
        if (data != null) {
          Message.info(data.msg)
        }
      }
    }
  }
</script>

<style scoped>
  div {
    margin: 8px;
  }
</style>
