<template>
  <Navbar></Navbar>

  <div class="course">
    <h3 style=" float:left; margin:15px ">历史课程</h3>
    <div id="courseList">
      <table class="table table-hover table-striped">
        <thead>
        <tr>
          <th>日期</th>
          <th>时间</th>
          <th>老师</th>
          <th>总数</th>
        </tr>
        </thead>

        <tr v-for="item in courses">
          <td>{{ item.date }}  </td>
          <td>{{ course_times[item.time_id - 1] }}  </td>
          <td>{{ parseTeachers(item.teachers) }}</td>
          <td>{{ item.accepted_num }}人</td>
        </tr>
      </table>
    </div>
    <Page :page-size="15" :current="page" :total="total" @on-change="pageChange"></Page>

  </div>
</template>

<script>
  import $ from 'jquery'
  import { Message, iButton, iInput, Modal, Row, iCol, Page } from 'iview';
  import Actions from './actions'
  import { encodeTeachers, parseTeachers } from '../../utils'
  import Navbar from './navbar.vue'
  import { course_times } from '../../consts'

  export default {
    name: 'Director',
    components: {
      Navbar, iButton, iInput, Modal, Row, iCol, Page
    },
    data () {
      return {
        teachers: [],
        times: [],
        dates: [],
        courses:[],
        course_times: course_times,
        page: 1,
        total: 1
      }
    },
    ready(){
      if (Actions.getInfo() == null) {
        this.$route.router.go('/login');
        return;
      }
      this.page = parseInt(this.$route.params.page);
//      this.getTimes();
      this.getTeachers();
      this.getDates();
      this.getHistoryCourses();
    },
    methods: {
      getPageNum() {
        let page_num = Actions.getHistoryCoursePageNum();
        this.total = page_num * 15;
      },
      getTeachers(){
        this.teachers = Actions.getTeachers();
      },

      getDates(){
        this.dates = Actions.getDates();
      },

      getHistoryCourses(){
        console.log(this.page);
        this.courses = Actions.getHistoryCourses(this.page);
        console.log(JSON.stringify(this.courses));
      },

      parseTeachers(json) {
        return parseTeachers(json);
      },

      pageChange(page) {
        console.log(page);
        this.page = page;
        this.getHistoryCourses();
        this.$route.router.go('/director_history/' + page);

      }

    }
  }
</script>

<style scoped>

  .course{
    padding-top: 70px;
    margin: 20px;
    width: 100%
  }

</style>


