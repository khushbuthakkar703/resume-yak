<template>
  <div>
    <section class="page-resume-editor page-login-info">
      <div class="container">
        <div class="row">
          <div class="col-md-12 grid-container">
            <img
              :src="EnvPath + 'images/logo/logo.png'"
              alt="Resume Yak"
              class="logo_login_page"
            />
            <div class="resume-editor-div" style="padding: 34px 25px">
              <h2>Login</h2>
              <div
                class="alert alert-warning text-center"
                v-if="loginError && errors.message"
              >
                <span>{{ errors.message[0] }}</span>
              </div>
                <p v-if="RequiredErrors.length">
                  <p v-for="(error,index) in RequiredErrors" :key='index' class="alert alert-warning text-center" style="font-size: 14px;">{{ error }}</p>
                </p>
              <div class="login_input_field">
                <input
                  type="email"
                  class="input_text_info mouseLeave"
                  v-model="email"
                  @mouseleave="mouseLeaveInput('emailAddress')"
                />
                <!-- <span class="highlight"></span> -->
                <span class="bar"></span>
                <label
                  class="label_name label_name_color"
                  :class="
                    isMouseLeaveEmailAddress && email != ''
                      ? 'mouseleavefill'
                      : ''
                  "
                  >Email Address:</label
                >
              </div>
              <div class="login_input_field">
                <input
                  type="password"
                  class="input_text_info mouseLeave"
                  v-model="password"
                  @mouseleave="mouseLeaveInput('password')"
                />
                <!-- <span class="highlight"></span> -->
                <span class="bar"></span>
                <label
                  class="label_name label_name_color"
                  :class="
                    isMouseLeavePassword && password != ''
                      ? 'mouseleavefill'
                      : ''
                  "
                  >Password</label
                >
              </div>
              <button
                class="btnOk btn-rounded"
                style="margin-top: 6px"
                @click="login()"
              >
                Login
              </button>
            </div>
            <div class="resume-bottom">
              <div class="row mt-3">
                <div class="col-md-12 create_reset_button">
                  <a  :href="EnvPath + 'login/#/forgotpassword'">Forgot Password?</a
                  ><a href="#">Create Account</a>
                </div>
                <div class="col-md-12 col-sm-12">
                  <ul style="padding-left: 0px">
                    <li>
                      <a href=""><strong>Sitemap</strong></a>
                      <i
                        class="fas fa-circle"
                        style="color: rgb(102, 105, 109); font-size: 25%"
                      ></i>
                    </li>
                    <li>
                      <a href=""><strong>Privacy Policy</strong></a>
                      <i
                        class="fas fa-circle"
                        style="color: rgb(102, 105, 109); font-size: 25%"
                      ></i>
                    </li>
                    <li>
                      <a href=""><strong>Terms of Use</strong></a>
                      <i
                        class="fas fa-circle"
                        style="color: rgb(102, 105, 109); font-size: 25%"
                      ></i>
                    </li>
                    <li>
                      <a href=""><strong>About Us</strong></a>
                    </li>
                  </ul>
                </div>
                <div class="col-md-12 col-sm-12">
                  <p class="text-center">
                    2019 Resume Yak. All rights reserved
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <!-- More Custom Script -->
</template>

<script>
import store from "../../store";
import { environment_api } from "../../env";

export default {
  name: "login",
  data() {
    return {
      EnvPath: environment_api.api_url,
      email: "",
      password: "",
      loginError: false,
      RequiredErrors:[],
      errors: {},
      isProgress: false,
      isMouseLeaveEmailAddress: false,
      isMouseLeavePassword: false,      
    };
  },
  mounted() {
    $("#aLogout").parent().removeClass("resume-visited-page");
    $("#aLogout").parent().removeClass("current-page");
  },
  methods: {
    login() {
      if (
        this.email != "" &&
        this.password != ""
      ) {
        if (!this.validEmail(this.email)) {
          this.errors = [];
          this.RequiredErrors = [];
          this.RequiredErrors.push("Valid email required");
        } else {
          window.axios
            .post(this.EnvPath+"auth/login", {
              email: this.email,
              password: this.password,
            })
            .then((response) => {
              if (response.data.success == true) {
                setTimeout(() => {
                  this.isProgress = false;
                  store.commit("LoginUser", response.data);
                  store.commit("ResumeNameData", response.data);
                  store.commit("EducationEnhancementData", response.data);
                  response.data.data.map(function (value, key) {
                      localStorage.setItem('resume_id',value.resume_id);
                      console.log(value.resume_id);
                  });
                  if (response.data.user.page == 1) {
                    window.location.href = this.EnvPath+"contact/#/new";
                  } else if (response.data.user.page == 2) {
                    window.location.href = this.EnvPath+"contact/#/store";
                  } else if (response.data.user.page == 3) {
                    window.location.href = this.EnvPath+"contact/#/workadd";
                  } else if (response.data.user.page == 4) {
                    window.location.href = this.EnvPath+"contact/#/myresume";
                  } else if (response.data.user.page == 5) {
                    window.location.href = this.EnvPath+"contact/#/experience-finder";
                  } else if (response.data.user.page == 6) {
                    window.location.href = this.EnvPath+"contact/#/education_enhancement";
                  } else if (response.data.user.page == 7) {
                    window.location.href = this.EnvPath+"contact/#/job_descriptions";
                  }
                }, 2000);
              } else {
                this.RequiredErrors = [];
                this.isProgress = true;
                setTimeout(() => {
                  this.isProgress = false;
                  this.loginError = true;
                  this.errors = response.data.errors;
                }, 1000);
              }
            })
            .catch((error) => {
              this.RequiredErrors = [];
              this.isProgress = false;
              this.loginError = true;
              this.errors = error.response.data.errors;
            });
        }
      } else {
        this.errors = [];
        this.RequiredErrors = [];
        this.RequiredErrors.push("Please complete all of the fields");
      }
    },
    mouseLeaveInput(inputName) {
      if (inputName === "emailAddress") {
        this.isMouseLeaveEmailAddress = true;
      }
      if (inputName === "password") {
        this.isMouseLeavePassword = true;
      }
    },
    validEmail: function (email) {
      var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(email);
    },
  },
};
</script>

