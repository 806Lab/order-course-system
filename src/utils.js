/**
 * Created by kalen on 16/11/30.
 */


export const parseTeachers = (json) => {
  let teachers = JSON.parse(json);
  let result = '';
  for (let i = 0; i < teachers.length; i++) {
    result += teachers[i].name;
    if (i != teachers.length - 1) {
      result += ','
    }
  }
  return result;
};


export const encodeTeachers = (teacherSelect, teachers) => {
  var teachersArray = [];
  for (let i = 0; i < teacherSelect.length; i++) {
    let selected = teacherSelect[i];
    for (let j = 0; j < teachers.length; j++) {
      let teacher = teachers[j];
      if (selected == teacher.id) {
        teachersArray.push(teacher);
      }
    }
  }
  return JSON.stringify(teachersArray);
};

