import $ from 'jquery'
import { Message } from 'iview'

export default {
  get(url, data) {
    $('#loading').css('display','block');
    url = '/api/index.php' + url;
    var result = null;
    $.ajax({
      type: 'get',
      async: false,
      data: data,
      dataType: 'json',
      url: url,
      success: function(data){
        $('#loading').css('display','none');
        if (data.code == 0) {
          result = data;
        } else {
          Message.error(data.msg);
        }
      }
    });
    return result;
  },
  post(url, data) {
    // $('#loading').css('display','block');
    url = '/api/index.php' + url;
    var result = null;
    $.ajax({
      type: 'post',
      async: false,
      data: data,
      dataType: 'json',
      url: '/api/index.php/student/get_courses',
      success:function(data){
        if (data.code == 0) {
          result = data.data;
        } else {
          Message.error(data.msg);
        }
      }
    });
    return result;

  }
}
