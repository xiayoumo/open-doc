<template>
  <div class="hello">
    <el-tabs tab-position="top" class="member-table-box">
      <el-tab-pane :label="$t('member_manage')">
          <el-button class="add-member" @click="dialogFormVisible = true"><span class="iconfont icon-24gf-portraitMalePlus"></span> {{$t('add_member')}}</el-button>
          <!-- 单个成员列表 -->
          <el-table align="left"
                    v-if="members.length>0"
                    :data="members"
                    class="member-table">
            <el-table-column
              prop="username"
              :label="$t('member_username')"
              width="100">
            </el-table-column>
            <el-table-column
              prop="name"
              :label="$t('name')"
            >
            </el-table-column>
            <el-table-column
              prop="addtime"
              :label="$t('add_time')"
              width="160">
            </el-table-column>
            <el-table-column
              prop="member_group"
              :label="$t('authority')">
            </el-table-column>
            <el-table-column
              prop=""
              :label="$t('operation')">
              <template slot-scope="scope">
                <el-button @click="delete_member(scope.row.item_member_id)" type="text" size="small">{{$t('delete')}}</el-button>
              </template>
            </el-table-column>
          </el-table>
          <el-empty v-if="members.length==0" :description="$t('empty_data')" :image-size="250"  class="edit-model-box-empty"></el-empty>
      </el-tab-pane>
      <el-tab-pane :label="$t('team_mamage')">
          <el-button class="add-member" @click="dialogFormTeamVisible = true"><span class="iconfont icon-duoren"></span> {{$t('add_team')}}</el-button>
          <!-- 团队列表 -->
          <el-table align="left"
                    v-if="teamItems.length>0"
                    :data="teamItems"
                    class="member-table">
            <el-table-column
              prop="team_name"
              :label="'团队名'"
            >
            </el-table-column>
            <el-table-column
              prop="addtime"
              :label="$t('add_time')"
            >
            </el-table-column>

            <el-table-column
              prop=""
              :label="$t('operation')">
              <template slot-scope="scope">
                <el-button @click="getTeamItemMember(scope.row.team_id)" type="text" size="small">{{$t('member_authority')}}</el-button>
                <el-button @click="deleteTeam(scope.row.id)" type="text" size="small">{{$t('delete')}}</el-button>
              </template>
            </el-table-column>
          </el-table>
          <el-empty v-if="teamItems.length==0" :description="$t('empty_data')" :image-size="250"  class="edit-model-box-empty"></el-empty>
      </el-tab-pane>
    </el-tabs>

      <!-- 添加单个成员弹窗 -->
      <el-dialog custom-class="add-member-dialog" :visible.sync="dialogFormVisible" :modal="false" >
        <el-form >
            <el-form-item label="" >
              <el-autocomplete
                :placeholder="$t('input_target_member')" v-model="MyForm.username"
                :fetch-suggestions="getAllUser"
              ></el-autocomplete>
            </el-form-item>
            <el-form-item label="" class="readonly-checkbox" >
              <el-checkbox v-model="MyForm.is_readonly">{{$t('readonly')}}</el-checkbox>
            </el-form-item>
        </el-form>
        <p class="tips">
          {{$t('member_authority_tips')}}
        </p>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">{{$t('cancel')}}</el-button>
          <el-button type="primary" @click="MyFormSubmit" >{{$t('confirm')}}</el-button>
        </div>
      </el-dialog>

      <!-- 添加团队弹窗 -->
      <el-dialog :visible.sync="dialogFormTeamVisible" :modal="false" top="10vh">
        <el-form >
            <el-form-item label="选择团队" >
              <el-select  class="" v-model="MyForm2.team_id">
                <el-option  v-for="team in teams " :key="team.team_name" :label="team.team_name" :value="team.id"></el-option>
              </el-select>

            </el-form-item>
            <router-link to="/team/index" target="_blank">{{$t('go_to_new_an_team')}}</router-link>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormTeamVisible = false">{{$t('cancel')}}</el-button>
          <el-button type="primary" @click="addTeam" >{{$t('confirm')}}</el-button>
        </div>
      </el-dialog>

      <!-- 成员权限弹窗 -->
      <el-dialog :visible.sync="dialogFormTeamMemberVisible" :modal="false" top="10vh" :title="$t('adjust_member_authority')" width="90%">

           <el-table align="left"
                :empty-text="$t('team_member_empty_tips')"
                :data="teamItemMembers"
                style="width: 100%">
                <el-table-column
                  prop="member_username"
                  :label="$t('username')"
                 >
                </el-table-column>
                <el-table-column
                  prop="member_group_id"
                  :label="$t('authority')"
                  width="130"
                  >
                  <template slot-scope="scope">
                    <el-select size="mini" v-model="scope.row.member_group_id" @change="changeTeamItemMemberGroup($event,scope.row.id)" :placeholder="$t('please_choose')">
                      <el-option
                        v-for="item in authorityOptions"
                        :key="item.value"
                        :label="item.label"
                        :value="item.value"

                        >
                      </el-option>
                    </el-select>
                  </template>
                </el-table-column>
                <el-table-column
                  prop="addtime"
                  :label="$t('add_time')"
                  >
                </el-table-column>

              </el-table>
        <br>
        <p class="tips">
          {{$t('team_member_authority_tips')}}
        </p>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormTeamMemberVisible = false">{{$t('close')}}</el-button>
        </div>
      </el-dialog>

  </div>
</template>

<script>


export default {
  name: 'Login',
  components : {

  },
  data () {
    return {
      MyForm:{
        username:'',
        is_readonly:false
      },
      MyForm2:{
        team_id:'',
      },
      members:[],
      dialogFormVisible:false,
      dialogFormTeamVisible:false,
      dialogFormTeamMemberVisible:false,
      teams:[],
      teamItems:[],
      teamItemMembers:[],
      authorityOptions:[
          {
            label:"编辑",
            value:'1',
          },
          {
            label:"只读",
            value:'0',
          }
      ]
    }

  },
  methods: {

      get_members(){
        var that = this ;
        var url = DocConfig.server+'/api/member/getList';
        var params = new URLSearchParams();
        params.append('item_id',  that.$route.params.item_id);
        that.axios.post(url, params)
          .then(function (response) {
            if (response.data.error_code === 0 ) {
              var Info = response.data.data
              that.members =  Info;
            }else{
              that.$alert(response.data.error_message);
            }

          });
      },
      get_teams(){
        var that = this ;
        var url = DocConfig.server+'/api/team/getList';
        var params = new URLSearchParams();
        that.axios.post(url, params)
          .then(function (response) {
            if (response.data.error_code === 0 ) {
              var Info = response.data.data
              that.teams =  Info;
            }else{
              that.$alert(response.data.error_message);
            }

          });
      },
      getTeamItem(){
        var that = this ;
        var url = DocConfig.server+'/api/teamItem/getList';
        var params = new URLSearchParams();
        params.append('item_id',  that.$route.params.item_id);
        that.axios.post(url, params)
          .then(function (response) {
            if (response.data.error_code === 0 ) {
              var Info = response.data.data
              that.teamItems =  Info;
            }else{
              that.$alert(response.data.error_message);
            }

          });
      },
      getTeamItemMember(team_id){
        var that = this ;
        this.dialogFormTeamMemberVisible = true;
        var url = DocConfig.server+'/api/teamItemMember/getList';
        var params = new URLSearchParams();
        params.append('item_id',  that.$route.params.item_id);
        params.append('team_id',  team_id);
        that.axios.post(url, params)
          .then(function (response) {
            if (response.data.error_code === 0 ) {
              var Info = response.data.data
              that.teamItemMembers =  Info;
            }else{
              that.$alert(response.data.error_message);
            }

          })
      },
      MyFormSubmit() {
          var that = this ;
          var url = DocConfig.server+'/api/member/save';

          var params = new URLSearchParams();
          params.append('item_id',  that.$route.params.item_id);
          params.append('username', this.MyForm.username);
          var member_group_id = 1 ;
          if (this.MyForm.is_readonly) {
              member_group_id = 0
          };
          params.append('member_group_id',member_group_id );

          that.axios.post(url, params)
            .then(function (response) {
              if (response.data.error_code === 0 ) {
                that.dialogFormVisible = false;
                that.get_members() ;
                that.MyForm.username = '';
                that.MyForm.is_readonly = false;
              }else{
                that.$alert(response.data.error_message);
              }

            })
            .catch(function (error) {
              console.log(error);
            });
      },
      addTeam() {
          var that = this ;
          var url = DocConfig.server+'/api/teamItem/save';

          var params = new URLSearchParams();
          params.append('item_id',  that.$route.params.item_id);
          params.append('team_id', this.MyForm2.team_id);

          that.axios.post(url, params)
          .then(function (response) {
              if (response.data.error_code === 0 ) {
                that.dialogFormTeamVisible = false;
                that.getTeamItem() ;
                that.MyForm.team_id = '';
              }else{
                that.$alert(response.data.error_message);
              }

          });
      },
      delete_member(item_member_id){
          var that = this ;
          var url = DocConfig.server+'/api/member/delete';

          this.$confirm(that.$t('confirm_delete'), ' ', {
            confirmButtonText: that.$t('confirm'),
            cancelButtonText: that.$t('cancel'),
            type: 'warning'
          }).then(() => {
            var params = new URLSearchParams();
            params.append('item_id',  that.$route.params.item_id);
            params.append('item_member_id', item_member_id);

            that.axios.post(url, params)
            .then(function (response) {
              if (response.data.error_code === 0 ) {
                that.get_members() ;
              }else{
                that.$alert(response.data.error_message);
              }
            });
          })

      },
      deleteTeam(id){
          var that = this ;
          var url = DocConfig.server+'/api/teamItem/delete';

          this.$confirm(that.$t('confirm_delete'), ' ', {
            confirmButtonText: that.$t('confirm'),
            cancelButtonText: that.$t('cancel'),
            type: 'warning'
          }).then(() => {
            var params = new URLSearchParams();
            params.append('id',  id);
            that.axios.post(url, params)
            .then(function (response) {
              if (response.data.error_code === 0 ) {
                that.getTeamItem() ;
              }else{
                that.$alert(response.data.error_message);
              }
            });
          })
      },
      changeTeamItemMemberGroup(member_group_id,id){
          var that = this ;
          var url = DocConfig.server+'/api/teamItemMember/save';

          var params = new URLSearchParams();
          params.append('member_group_id',  member_group_id);
          params.append('id', id);

          that.axios.post(url, params)
          .then(function (response) {
              if (response.data.error_code === 0 ) {
                 that.$message('权限保存成功');
              }else{
                that.$alert(response.data.error_message);
              }

          });
      },
      getAllUser(queryString,cb){
        var that = this ;
        var url = DocConfig.server+'/api/user/allUser';
        var params = new URLSearchParams();
        if (!queryString) {queryString = ''};
        params.append('username', queryString);
        that.axios.post(url, params)
          .then(function (response) {
            if (response.data.error_code === 0 ) {
              var Info = response.data.data
              cb(Info);
            }else{
              that.$alert(response.data.error_message);
            }

          });
      },
  },

  mounted(){
    this.get_members();
    this.get_teams();
    this.getTeamItem();
  }
}
</script>

<style lang="scss">
@import '~@/components/common/base.scss';
.add-member-dialog{
  margin-top: 4vh !important;
}
.el-dialog__wrapper{
  @include scroll-bar-box
}
</style>
<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
@import '~@/components/common/base.scss';

.member-table{
  height: 400px;
  width: 100%;
  background: $theme-grey-color;
}
.member-table-box{
  padding: 4rem;
}
.hello{
  text-align: left;
}

.add-member{
  float: right;
  margin-bottom: 10px;
}

.tips{
  font-size: 12px;
  margin-bottom: 0px;
  margin-top: 0px;
}
</style>
