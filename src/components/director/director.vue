<template>
  <Navbar></Navbar>


  <h3 style=" margin:30px;padding-top: 50px; ">添加课程</h3>
  <div id="addCourse" style="padding-top:10px">

    <div class="check">
      <p>选择老师</p>
      <Checkbox-group :model.sync="teachersSelect" v-for="item in teachers">
        <Checkbox :value="item.id" >{{ item.name}}</Checkbox>
      </Checkbox-group>
    </div>

    <div class="select">
      <p>日期</p>
      <i-select :model.sync="dateSelect" style="">
        <i-option v-for="item in dates" :value = "item.day">{{ item.day}}</i-option>
      </i-select>
    </div>

    <div class="select">
      <p>时间</p>
      <i-select :model.sync="timeSelect" style="">
        <i-option v-for="item in times" :value = "item.id">{{ item.info }}</i-option>
      </i-select>
    </div>


    <div class="remark">
      <p>备注(哪个教室)</p>
      <i-input :value.sync="message" type="textarea" placeholder="请输入..." style="width: 400px"></i-input>
    </div>

    <div class="button">
      <i-button class="btn" v-on:click="addCourse" type="info" shape="circle" >添加一门课</i-button>
    </div>
  </div>


  <div class="course">
    <h3 style=" float:left; margin:15px ">本周课程</h3>
    <div id="courseList">
      <table class="table table-hover table-striped">
        <thead>
          <tr>
            <th>日期</th>
            <th>时间</th>
            <th>老师</th>
            <th>未处理</th>
            <th>总数</th>
            <th><center>操作</center></th>
          </tr>
        </thead>

        <tr v-for="item in currentCourses">
          <td>{{ item.date }}  </td>
          <td>{{ course_times[item.time_id - 1] }}  </td>
          <td>{{ parseTeachers(item.teachers) }}</td>
          <td>{{ item.unhandled_num }}人</td>
          <td>{{ item.accepted_num }}人</td>
          <td>
            <center>
              <Row>
                <i-button @click="onManageButtonClicked(item.id)" shape="circle" type="info">管理</i-button>
                <i-button @click="onDeleteButtonClicked(item.id)" shape="circle" type="warning">删除</i-button>
              </Row>
            </center>
          </td>
        </tr>
      </table>

      <Modal
        :visible.sync="deleteModal"
        title="警告"
        @on-ok="deleteCourse">
        <p>你确定要删除这门课吗?</p>
      </Modal>
    </div>
  </div>
.
</template>

<script>
  import $ from 'jquery'
  import { Message, iButton, iSelect, iOption, RadioGroup, CheckboxGroup, Checkbox,
    iInput, Modal, Row } from 'iview';
  import Actions from './actions'
  import { encodeTeachers, parseTeachers } from '../../utils'
  import Navbar from './navbar.vue'
  import { course_times } from '../../consts'


  export default {
    name: 'Director',
    components: {
      Navbar, iButton, iSelect, iOption, RadioGroup, CheckboxGroup, Checkbox, iInput, Modal, Row
    },
    data () {
      return {
        teacherName :[],
        teachers: [],
        teachersSelect:[],
        times: [],
        timeSelect:'',
        dates: [],
        dateSelect:'',
        message:'',
        currentCourses:[],
        deleteModal: false,
        deleteId: '',
        course_times: course_times
      }
    },
    ready(){
      if (Actions.getInfo() == null) {
        this.$route.router.go('/login');
        return;
      }
      this.getTimes();
      this.getTeachers();
      this.getDates();
      this.getCurrentCourses();
    },
    methods: {
      getTeachers:function(){
        this.teachers = Actions.getTeachers();
      },

      getTimes: function(){
        this.times = Actions.getTimes();
      },


      getDates: function(){
        this.dates = Actions.getDates();
      },

      addCourse(){
        if (this.teachersSelect.length == 0) {
          Message.error('请至少选择一个教师');
          return;
        }

//        console.log(encodeTeachers(this.teachersSelect, this.teachers));

        let data = Actions.addCourse( encodeTeachers(this.teachersSelect, this.teachers),
              this.timeSelect, this.dateSelect, this.message);

        if (data != null) {
          Message.info(data.msg);
          this.getCurrentCourses();
        }
      },

      getCurrentCourses(){
        let courses = Actions.getCurrentCourses();
        this.deleteModals = [];
        for (let course in courses) {
          this.deleteModals.push(false);
        }
        this.currentCourses = courses;
      },

      deleteCourse() {
        let data = Actions.deleteCourse(this.deleteId);
        if (data != null) {
          Message.info(data.msg);
          this.getCurrentCourses();
        }
      },

      onDeleteButtonClicked(id) {
        this.deleteId = id;
        this.deleteModal = true;
      },

      parseTeachers(json) {
        return parseTeachers(json);
      },

      onManageButtonClicked(id) {
        this.$route.router.go('/director_manage/' + id);
      }


    }
  }
</script>

<style scoped>
#addCourse{
  text-align: center;
  display: flex;
  flex-wrap: wrap;
  justify-content:center;
  margin: 20px

}
.check{
    flex:1;
    margin: 30px;
    flex-basis:300px;


}
.select{
  flex: 1;
      margin: 30px;
        flex-basis:300px;

}
.remark{
  flex:1;
  margin: 30px;
  flex-basis:100px;
}
.button{
  flex: 1;
  flex-basis:100px;
  margin: 30px;
}.btn{
  margin: inherit;
}

.course{
  margin: 20px;
  width: 100%
}

</style>


