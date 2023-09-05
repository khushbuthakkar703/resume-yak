<template>
    <div class="experience_finder_right_container">
      <div class="row">
        <div class="col-md-2">
          <div class="sidebar experience_finder_sidebar">
              <resumeSidemenu v-bind:page="8"/>
          </div>
        </div>
        <div class="col-md-10 grid-container experience_finder_rightside">
          <div class="resume-editor-div">
            <h2><span class="icon"><img :src="EnvPath+'images/logo/skills_icon.png'" /></span> Skills </h2>
            <div class="row" style="max-width: 800px; margin:0px auto !important;">
              <div class="col-md-7 col-sm-12 group mr-2 skills_selection_center" style="margin-top:20px;">
                <div class="education_enhancement_sections">
                  <div class="edu_section_content">
                    <div class="edu_fields_section scrollbar scrollbar-indigo bordered-indigo thin">
                    <div class="force-overflow">
                      <!-- <div class="resposibilities_items_heading" style="background:none; cursor: auto;">
                        <p style="padding:10px 0px 7px 0px;">Skills Relevant to {{this.$store.state.ResumeName_Data}}</p>
                      </div> -->
                      <div class="resposibilities_items_listing"  v-for="(skill, k) in SkillsData" :key="k">
                        <div v-on:click="addUserSkill(skill.id)">
                          <p>{{skill.name}}</p>
                          <span class="icon"><i class="fas fa-plus"></i></span>
                        </div>	
                      </div>
                      <!-- <div class="resposibilities_items_listing">
                      <p>Salesforce software</p>
						            <span class="icon"><i class="fas fa-plus"></i></span>	
                      </div>
                      <div class="resposibilities_items_listing">
                      <p>Quickbooks Professional</p>
						            <span class="icon"><i class="fas fa-plus"></i></span>	
                      </div>
                      <div class="resposibilities_items_listing">
                      <p>Other Software</p>
						            <span class="icon"><i class="fas fa-plus"></i></span>	
                      </div>
                      <div class="resposibilities_items_listing">
                      <p>ADP software</p>
						            <span class="icon"><i class="fas fa-plus"></i></span>	
                      </div>
                      <div class="resposibilities_items_listing">
                      <p>ADP Workforce Now</p>
						            <span class="icon"><i class="fas fa-plus"></i></span>	
                      </div>
                      <div class="resposibilities_items_listing">
                      <p>Quickbooks Professional</p>
						            <span class="icon"><i class="fas fa-plus"></i></span>	
                      </div>
                      <div class="resposibilities_items_listing">
                      <p>Other Software</p>
						            <span class="icon"><i class="fas fa-plus"></i></span>	
                      </div> -->
                      </div>
                    </div>
                  </div>
                </div>
                </div>
                <div class="col-md-4 col-sm-12 group mr-2 skills_selected_section" style="margin-top:20px;">
                  <div class="education_enhancement_sections">
                    <div class="edu_section_heading">Skills</div>
                    <div class="edu_section_content">
                      <div class="skill_selected_item" v-for="(userSkill, x) in UserSkillData" :key="x">
                        {{ userSkill.name }} <span class="icon" @click="deleteUserSkill(userSkill.id,userSkill.user_skill_id)"><i class="fas fa-times"></i></span>
                      </div>
                    </div>
                  </div>
                  <div class="custom_skill_field_div">
                    <input type="text" class="add_custom_skill_text_field" placeholder="+Add Custom Skill" v-model="customSkill" v-on:keyup.enter="enterCustomSkill()"
                    style="border-radius: 10px;border: 1px solid #d4d4d5;
                    box-shadow: none;height: 36px;padding: 5px;font-size: 16px !important;font-family: 'Hind', sans-serif;font-weight: 400;color: #767676; margin-top: 20px; margin-bottom: 15px;text-align:left;float:left; width: calc(100% - (50px * 1)) !important;" /><span class="icon" style="float:right;" @click="addCustomSkill()"><i class="fas fa-plus"></i></span>
                  </div>
                  <p v-if="errors.length">
                  <p v-for="(error, index) in errors" :key="index" class="alert alert-warning text-center" style="margin-bottom: -10px;font-size: 14px;">{{ error }}</p>
                </div>
                <div class="text-center" style="display: block; margin: 25px auto 15px;">
                  <button class="btn1 btn-rounded btn-next mt-4"><span style="margin-left: -14px;" @click="IsClick()">Next</span> <span class="pl-5"><i class="next-icon fas fa-chevron-right"></i></span></button>
                </div>
                <!-- <button data-v-c64f6442="" type="button" class="btn btn-block" style="border: 3px solid rgb(244, 244, 245); color: rgb(223, 223, 223); padding: 11px 0px; font-size: 18px; margin-top:20px; margin-bottom:5px; border-radius: 6px;">+Add New</button>--> 
            </div>
          </div>
          <div class="resume-bottom">
            <div class="row mt-3">
              <div class="col-md-7 col-sm-12">
                <ul>
                  <li><a href=""><strong>Sitemap</strong></a> <i class="fas fa-circle" style="color: rgb(102, 105, 109); font-size: 25%;"></i></li>
                  <li><a href=""><strong>Privacy Policy</strong></a> <i class="fas fa-circle" style="color: rgb(102, 105, 109); font-size: 25%;"></i></li>
                  <li><a href=""><strong>Terms of Use</strong></a> <i class="fas fa-circle" style="color: rgb(102, 105, 109); font-size: 25%;"></i></li>
                  <li><a href=""><strong>About Us</strong></a></li>
                </ul>
              </div>
              <div class="col-md-5 col-sm-12">
                <p class="text-center">2019 Resume Yak. All rights reserved</p>
              </div>
            </div>
          </div>
        </div>

        <!-- popup next button -->
        <div class="popup-container"  v-if="NextClick">
          <div class="poup-sub-container">
            <div class="pop-content-section pop_confused_content">
              <div class="container">
                <div class="row">
                <div class="col-4"><img :src="EnvPath+'images/logo/Confused.png'" alt="Confused" /></div>
                <div class="col-8">
                <h4>Are You Sure?</h4>
                <p>You haven't added any skills. Are you sure you want to skip this step?.</p>
                <button class="btnOption btn-rounded" @click="cancelClick()" style="float:left;">Cancel</button>
                <button class="btnOption btn_color_option btn-rounded" @click="showNextPage()" style="float:right;">Yes</button>
                </div>
                </div>
                </div>
            </div>
          </div>
        </div>
        <!-- popup -->
      </div>
    </div>
</template>


<script>
import store from "../../store";
import { environment_api } from "../../env";
import resumeSidemenu from "./resumeSidemenu";

export default {
  name: "skills",
  data() {
    return {
      EnvPath: environment_api.api_url,
      SkillsData:[],
      UserSkillData:[],
      customSkill: "",
      errors: [],
      NextClick:false,
      IsValid: 0
    };
  },
  components: {
    resumeSidemenu
  },
  methods:{
    loadSkills() {
      axios.post(this.EnvPath + "get-skills", {resume_id : localStorage.getItem('resume_id')}).then(({ data }) => {
        if (data.status == 1) {
          store.commit("SkillsData", data);
          this.SkillsData = data.data;
        } else {
        
        }
      });
    },
    loadUserSkills() {
      axios.post(this.EnvPath + "get-user-skills", {resume_id : localStorage.getItem('resume_id')}).then(({ data }) => {
        if (data.status == 1) {
          store.commit("UserSkillData", data);
          this.UserSkillData = data.data;
          if(data.data != '') {
            this.IsValid = 0;
          }
          else {
            this.IsValid = 1;
          }
        } else {
        }
      });
    },
    addUserSkill(skill_id) {
      axios.post(this.EnvPath + "add-user-skill", {skill_id : skill_id, resume_id : localStorage.getItem('resume_id')}).then(({ data }) => {
        if (data.status == 1) {
          this.IsValid = 0;
          this.loadSkills();
          this.loadUserSkills();
        } else {
        }
      });
    },
    deleteUserSkill(skill_id,user_skill_id) {
      axios.post(this.EnvPath + "delete-user-skill", {skill_id : skill_id, user_skill_id : user_skill_id, resume_id : localStorage.getItem('resume_id')}).then(({ data }) => {
        if (data.status == 1) {
          this.loadSkills();
          this.loadUserSkills();
        } else {
        
        }
      });
    },
    addCustomSkill() {
      if(this.customSkill != "") {
        var querystring = require("querystring");
        axios.post(
          this.EnvPath + "add-custom-skill",
          querystring.stringify({
            custom_skill: this.customSkill,
            resume_id: localStorage.getItem('resume_id'),
          })
        ).then(({ data }) => {
          if (data.status == 1) {
            this.IsValid = 0;
            this.customSkill = "";
            this.loadSkills();
            this.loadUserSkills();
          } else {
          }
        });
      }
      else {
        this.errors = [];
        this.errors.push(
          "Please complete the required fields with the *"
        );
      }
    },
    enterCustomSkill: function() {
      this.addCustomSkill();
    },
    IsClick(){
      if(this.IsValid == 0) {
        this.$router.push({ name: "endorsements" });
      }
      else {
        this.NextClick = true;
      }
    },
    cancelClick(){
      this.NextClick = false; 
    },
    showNextPage(){
      this.$router.push({ name: "endorsements" });
    },
  },
  mounted(){
    localStorage.setItem('page',8);
    $(".my_resumes").removeClass("active_nav");
    $(".has_sub_nav_my_profile").removeClass("active_nav");
    this.loadSkills();
    this.loadUserSkills();
  },
};
</script>
