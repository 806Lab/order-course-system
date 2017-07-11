import Vue from 'vue'
import App from './App'
import Hello from './components/Hello.vue'
import Teacher from './components/teacher/teacher.vue'
import Student from './components/student/student.vue'
import Director from './components/director/director.vue'
import DirectorManage from './components/director/director_manage.vue'
import DirectorHistory from './components/director/director_history.vue'
import Login from './components/Login.vue'
import VueRouter from 'vue-router'
import 'iview/dist/styles/iview.css'

import { Message } from 'iview';

Message.config({
  top: 70,
  duration: 3
});

Vue.use(VueRouter);

const router = new VueRouter();

router.map({
	'/hello': {
		component: Hello
	},
	'/director': {
		component: Director
	},
	'/teacher': {
		component: Teacher
	},
	'/student': {
		component: Student
	},
	'/login': {
		component: Login
	},
  '/director_manage/:id': {
    component: DirectorManage
  },
  '/director_history/:page': {
    component: DirectorHistory
  }
});

router.redirect({
  '/director_history': '/director_history/1',
	'/director_manage': '/director',
	'*': '/login'
});

router.start(App, '#app');
