<template>
  <div class="body">
    <div class="main">
      <h1>ORDER COURSE SYSTEM</h1>
      <p>用户名</p>
      <input v-model="uname" placeholder="用户名">
      <p>密码</p>
      <input v-model="passwd" type="password" placeholder="密码">
      <p>用户类型</p>
      <select v-model="selected">
        <option value="0">学生</option>
        <option value="1">老师</option>
        <option value="2">负责人</option>
      </select>
      <div>
        <i-button id="ibutton" type="info" v-on:click="submit">提交</i-button>
      </div>
    </div>
  </div>
</template>

<script>
  import $ from 'jquery'
  import {Message,iButton} from 'iview'

  export default {
    components: {
      Message,iButton
    },
    data () {

      return {
        msg: 'Welcome to Your Vue.js App',
        uname: '',
        passwd: '',
        selected: ''
      }
    },
    methods: {
      submit: function () {
        const that = this;
        $.ajax({
          type: 'post',
          async: false,
          data: {
            uname: that.uname,
            passwd: that.passwd,
            type: that.selected
          },
          dataType: 'json',
          url: '/api/index.php/login',
          success:function(data){
            // data = JSON.parse(data);
//            console.log(data);
            Message.info(data.msg);
            if (data.code == 0) {
              if(that.selected == 0){
                that.$route.router.go('/student');
              }
              else if(that.selected == 1){
                that.$route.router.go('/teacher');
              }
              else if(that.selected == 2){
                that.$route.router.go('/director');
              }
            }
          }
        });
      }
    }
  }

</script>

<style scoped>

  .body {
    min-height: 100vh;
    box-sizing: border-box;
    margin: 0;
    padding-top: calc(30vh - 6em);
    font: 150%/1.6 Baskerville, Palatino, serif;
  }
  .body,.main::before {
    background: url("../../static/pic/2233444wmmW.jpg") 0 / cover fixed;
  }

  .main {

    position: relative;
    margin: 0 auto;
    padding: 1em;
    max-width: 23em;
    background: hsla(0,0%,20%,.5) border-box;
    overflow: hidden;
    border-radius: .3em;
    box-shadow: 0 0 0 1px hsla(0,0%,100%,.3) inset,
    0 .5em 1em rgba(0, 0, 0, 0.6);
    text-shadow: 0 1px 1px hsla(0,0%,100%,.3);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;



  }
  /*
  .main::before {
    content: '';
    position: absolute;
    top: 0; right: 0; bottom: 0; left: 0;
    margin: -300px;
    z-index: -1;
    -webkit-filter: blur(5px);
    filter: blur(5px);

  }*/

  blockquote { font-style: italic }
  blockquote cite { font-style: normal; }




  h1{
    margin-top: 20px;
    font-size: 30px;
    color:#DEE3BE;
  }

  #ibutton{
    background-color: #46818E;
  }

  P{
    margin-top: 25PX;
    margin-bottom: 5px;
    font-size: 17px;
    font-weight: 800;
    color: #DEE3BE
  }

  select{
    width: 125px;
  }
  input{
    width: 125px;
    height: 25px
  }

  button{
    margin: 40px;
  }

  ul {
    list-style-type: none;
    padding: 0;
  }

  li {
    display: inline-block;
    margin: 0 10px;
  }

  a {
    color: #42b983;
  }
</style>
