<template>
  <table class="table table-hover table-striped">
    <thead>
    <tr>
      <th>学生姓名</th>
      <th>手机号</th>
      <th>申请原因</th>
      <th v-if="is_operated == 0"><center>操作</center></th>
      <th v-else>反馈</th>
    </tr>
    </thead>
    <tbody>
    <tr v-for="order in orders">
      <td>{{ order.stu_name }}</td>
      <td>{{ order.stu_mobile }}</td>
      <td>{{ order.reason }}</td>
      <td>
        <center v-if="is_operated == 0">
          <Row>
            <i-button type="primary" @click="onAcceptClicked(order.id)">同意</i-button>
            <i-button type="error" @click="onDenyClicked(order.id)">拒绝</i-button>
          </Row>
        </center>

        <span v-else>
          {{ parseStatus(order.status) }}
        </span>
      </td>
    </tr>
    </tbody>
  </table>

  <Modal
    :visible.sync="acceptModal"
    title="警告"
    @on-ok="accept">
    <p>你确定同意这个预约吗?</p>
  </Modal>

  <Modal
    :visible.sync="denyModal"
    title="警告"
    @on-ok="deny">
    <p>你确定拒绝这个预约吗?</p>
  </Modal>

</template>

<script>
  import { iButton, Row, iCol, Modal, Message } from 'iview'
  import Actions from './actions'

  export default {
    data() {
      return {
        orders: [],
        course_id: '',
        acceptedId: '',
        deniedId: '',
        acceptModal: false,
        denyModal: false
      }
    },
    components: {
      Row, iButton, iCol, Modal
    },
    props: {
      is_operated: Number
    },
    created() {
      this.course_id = this.$route.params.id;
      this.getOrders();
    },
    methods: {
      onAcceptClicked(id) {
        this.acceptedId = id;
        this.acceptModal = true;
      },
      onDenyClicked(id) {
        this.deniedId = id;
        this.denyModal = true;
      },
      accept() {
        let data = Actions.handleOrder(this.acceptedId, 1);
        if (data != null) {
          Message.info(data.msg);
          this.getOrders();
        }
      },

      deny() {
        let data = Actions.handleOrder(this.deniedId, 2);
        if (data != null) {
          Message.info(data.msg);
          this.getOrders();
        }
      },

      parseStatus(status) {
        if (status == '1') {
          return '已同意';
        }
        return '已拒绝';
      },

      getOrders() {
        this.orders = Actions.getOrders(this.is_operated, this.course_id);
      }

    }

  }
</script>
