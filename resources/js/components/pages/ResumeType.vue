<template>
    <section class="page-resume-editor page-contact-info subscription_main_page">
        <h2 class="subscription_page_heading" style="font-weight: 500; padding: 0px 30px;"> Upgrade Your Subscription </h2>
        <p style="max-width: 850px; width: 90%; margin: 0px auto; text-align: left; color: rgb(118, 118, 118);">To use the functional resume format, you will need to upgrade to either the Plus or Pro subscription plans. The functional resume format is ideal for highlighting your experience on the first page of your resume.</p><br>
        <p v-if="this.msg != ''" class="alert alert-warning text-center" style="margin-bottom: -20px;font-size: 14px;">{{ this.msg }}</p> 
        <div class="container">
            <div class="row">
                <div class="subscription_plans_list">
                    <div class="col-md-4 col-sm-12" :class="(subscriptions.month_price == 0) ? 'subscription_free' : ''" v-for="(subscriptions,index) in subscriptionList" :key="'subscriptions'+index">
                        <div class="subscription_header">
                            <h3 style="text-transform:uppercase">{{subscriptions.name}}</h3> <h4 v-if="subscriptions.month_price == 0">Free</h4>
                            <h4 v-else>${{subscriptions.month_price}} <span class="small_dur">/ Month</span><br> <span style="margin-left: -28px; font-size: 18px;">or</span> ${{subscriptions.year_price}} <span class="small_dur">/ Year</span></h4>
                        </div> 
                        <div class="subscription_body">
                            <ul class="subscription_list_options" v-if="plan_count == subscriptions.option_count">
                                <li v-for="(option1,index) in subscriptions.subscription_option" :key="'option1'+index"><span><i class="fa fa-angle-right"></i></span> {{option1.content}}</li>
                            </ul>
                            <ul class="subscription_list_options" v-else>
                                <li v-for="(option2,index) in subscriptions.subscription_option" :key="'option2'+index"><span><i class="fa fa-angle-right"></i></span> {{option2.content}}</li>
                                <li v-for="(option3,index) in plan_count - subscriptions.option_count" :key="'option3'+index"></li>
                            </ul>
                            <button v-if="subscriptions.month_price == 0" class="btnonlyBorder btn-rounded mt-4" @click="revertCancleClick()">{{subscriptions.text}}</button>

                            <button v-else class="btnonlyBorder_FillSolid btn-rounded mt-4" @click="addSubscriptionPlan(subscriptions.id)">{{subscriptions.text}}</button>
                        </div>
                    </div>
                    
                </div>

                <div class="col-md-12 grid-container resume_type_no_sidebar_container subscription_plans_form_container">
                    <div class="resume-editor-div" style="display: none;">
                        <div class="row" style="max-width: 780px; margin: 0px auto !important;">
                            <div id="plan_detail" class="col-sm-12 inputP55 billing-detail" style="max-width: 320px; margin: 0px auto;display: none;">
                                <!-- Select drop down appear here  -->
                            </div>
                            <div class="col-12 choose_subscription_form billing-detail" style="display: none;z-index: 0;">
                                <h5>Choose Your Billing Cycle</h5> 
                                <div class="choose_subscription_duration">
                                    <div class="more_checkbox_section" id="plusBillingCycle">
                                        <div class="custom-control custom-radio form-check form-check-inline finder_job_check">
                                            <input type="radio" v-model="billing_plan" id="billingPlan1" value="1" name="billingPlan1" class="custom-control-input form-check-input" style="cursor: pointer;" @click="selectBillingCycle()"> 
                                            <label for="billingPlan1" class="custom-control-label form-check-label"><p>Monthly<br><span>${{this.month_price}} / month</span></p></label>
                                        </div> 
                                        <div class="custom-control custom-radio form-check form-check-inline finder_job_check">
                                            <input type="radio" v-model="billing_plan" id="billingPlan2" value="2" name="billingPlan2" class="custom-control-input form-check-input" style="cursor: pointer;" @click="selectBillingCycle()"> 
                                            <label for="billingPlan2" class="custom-control-label form-check-label"><p>Annual<br><span>${{this.year_price}} / year </span></p><span class="best_value">Best Value</span></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 choose_subscription_form" style="display: none;">
                                <h5>Credit Card Details</h5> 
                                <div class="choose_subscription_fields">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12 group mr-2 inputP55 input_yak_logo_label_shrink">
                                            <input maxlength="100" name="name" type="text" class="input mouseLeave" v-model="card_name" autocomplete="off"> 
                                            <span class="bar"></span> 
                                            <label class="label_name" :class="(card_name!='') ?'mouseleavefill':''">Name on Card</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12 group mr-2 inputP55 input_yak_logo_label_shrink">
                                            <input type="text" name="card_number" class="input mouseLeave" v-model="card_number" v-mask="'#### #### #### ####'" > 
                                            <span class="bar"></span> 
                                            <label class="label_name" :class="(card_number!='') ?'mouseleavefill':''">Credit or Debit Card Number</label>
                                        </div> 
                                        <div class="col-md-6 col-sm-12 group mr-2 inputP55 custom_expiration_security_div" style="padding: 0px; display: inherit; margin-bottom: 0px;">
                                            <div class="col-md-6 col-sm-12 group mr-2 inputP55 input_yak_logo_label_shrink">
                                                <input type="text" name="expiration" class="input mouseLeave" v-model="expiration" v-mask="'##/##'">
                                                <span class="bar"></span> 
                                                <label class="label_name" :class="(expiration!='') ?'mouseleavefill':''">Expiration: MM/YY</label>
                                            </div> 
                                            <div class="col-md-6 col-sm-12 group mr-2 inputP55 input_yak_logo_label_shrink">
                                                <input type="text" name="security_code" class="input mouseLeave" v-model="security_code" v-mask="'###'">
                                                <span class="bar"></span> 
                                                <label class="label_name" :class="(security_code!='') ?'mouseleavefill':''">Security Code:</label>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12 group mr-2 inputP55">
                                            <select name="country" id="country" class="select-item input mouseLeave" style="appearance: none;" v-model="country">
                                                <option v-for="(country,index) in countries" :key="index" :value="index" >{{ country }}</option>
                                            </select>
                                            <span class="bar"></span> 
                                            <label class="select-label" :class="(country!='') ?'mouseleavefill':''">Country:</label>
                                        </div> 
                                        <div class="col-md-6 col-sm-12 group mr-2 inputP55 input_yak_logo_label_shrink">
                                            <input maxlength="100" name="postal_code" type="text" class="input mouseLeave" v-model="postal_code" autocomplete="off"> 
                                            <span class="bar"></span> 
                                            <label class="label_name" :class="(postal_code!='') ?'mouseleavefill':''">Postal Code</label>
                                        </div>
                                    </div> 

                                    <!-- Error msg -->
                                    <p v-if="errors.length">
                                        <p v-for="(error, index) in errors" :key="index" class="alert alert-warning text-center" style="margin-bottom: -10px;font-size: 14px;">{{ error }}</p>
                                    </p><br>
                                    <!-- End -->

                                    <div class="sub_buttons_container">
                                        <button class="btnOption btn_color_option btn-rounded" @click="addCard()">Upgrade</button> 
                                        <button class="btnGray btn-rounded" @click="revertCancleClick()">Cancel</button>
                                    </div>
                                </div>
                            </div>

                            <div class="sub_buttons_container cancel-btn" style="max-width: 650px;margin: 10px auto;"> 
                                <button class="btnGray btn-rounded" style="width: 150px;" @click="revertCancleClick()">Cancel</button>
                            </div>
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
                <div class="popup-container"  v-if="CreditCard">
                <div class="poup-sub-container">
                    <div class="pop-content-section pop_confused_content">
                    <div class="container">
                        <div class="row">
                        <div class="col-4"><img :src="EnvPath+'images/bg/yak-port.png'" alt="Confused" /></div>
                        <div class="col-8">
                        <h4>Whoa There!</h4>
                        <p>You must complete all of the fields before upgrading Button</p>
                        <button class="btnOption btn_color_option btn-rounded" @click="okClick()" style="float:right;">Ok</button>
                        </div>
                        </div>
                        </div>
                    </div>
                </div>
                </div>
                <!-- popup -->

            </div>
        </div>
    </section>
</template>

<script>
import store from "../../store";
import { environment_api } from "../../env";
import Vue from 'vue'
import VueTheMask from 'vue-the-mask';
Vue.use(VueTheMask)

export default {
    name: "resume_type",
    data() {
        return {
            EnvPath: environment_api.api_url,
            //plan_name: '',
            countries: [],
            country: "232",
            card_name: '',
            card_number: '',
            expiration: '',
            security_code: '',
            postal_code: '',
            subscription: '',
            billing_plan: '',
            errors: [],
            CreditCard: false,
            subscriptionList: [],
            plan_count: '',
            month_price: 0,
            year_price: 0,
            msg: ''
        };
    },
    components: {
    },
    mounted(){
        localStorage.setItem('page',11);
        $(".my_resumes").removeClass("active_nav");
        $(".has_sub_nav_my_profile").removeClass("active_nav");
        this.loadCountries();
        this.country = "232";
        this.loadSubscriptions();
    },
    methods: {
        loadSubscriptions() {
            axios.get(this.EnvPath+"subscription_list")
            .then((response) => {
                store.commit("SubscriptionData", response.data.data);
                this.subscriptionList = this.$store.state.Subscription_Data;
                this.plan_count = response.data.count;
            })
            .catch((error) => console.log(error));
        },
        loadCountries() {
            axios.get(this.EnvPath+"countries")
            .then((response) => {
                store.commit("LocationData", response.data);
                this.countries = this.$store.state.countriesData;
            })
            .catch((error) => console.log(error));
        },
        addSubscriptionPlan(id) {
            axios.post(this.EnvPath+"get_subscription_list",{subscription_id : id})
            .then((response) => {
                this.month_price = response.data.data.month_price;
                this.year_price = response.data.data.year_price;
                this.subscription = response.data.data.id;
                $("#plan_detail").html('<select name="subscription_plan_option" id="subscription_plan_option" class="select-item input mouseLeave" style="appearance: none;">'+
                '<option value="'+response.data.data.id+'">'+response.data.data.name+'</option>'+
                '</select>'+
                '<label data-v-7969e6e8="" class="select-label mouseLeave_">Subscription:</label>');
            })
            .catch((error) => console.log(error));
            $(".resume-editor-div").css('display', 'block');
            $(".billing-detail").css('display', 'block');
            $(".subscription_plans_list").css('display', 'none');
        },
        selectBillingCycle() {
            $(".choose_subscription_form").css('display', 'block');
            $(".subscription_plans_list").css('display', 'none');
            $(".cancel-btn").css('display', 'none');
        },
        okClick(){
            this.CreditCard = false; 
        },
        addCard() {
            if (
                this.card_name != "" &&
                this.card_number != "" &&
                this.expiration != "" &&
                this.security_code != "" &&
                this.postal_code != "" &&
                this.country != 0 &&
                this.subscription != 0 &&
                this.billing_plan != 0
            ) {
                axios.post(this.EnvPath + "add-subscription", {
                    card_name : this.card_name,
                    card_number : this.card_number,
                    expiration : this.expiration,
                    cvc : this.security_code,
                    postal_code : this.postal_code,
                    country : this.country,
                    subscription_id : this.subscription,
                    billing_plan : this.billing_plan,
                    price : (this.billing_plan == 1) ? this.month_price : this.year_price,
                }).then(({ data }) => {
                    if (data.status == 1) {
                        if(data.page == 6) {
                            this.$router.push({ name: "education_enhancement" });
                        }
                        else {
                            this.$router.push({ name: "job_descriptions" });
                        }
                    }
                    else {
                        this.msg = data.msg;
                    }
                });
            }
            else {
                if(this.card_name == "" && this.card_number == "" && this.expiration == "" && this.security_code == "" && this.postal_code == "") {
                    this.CreditCard = true;
                }
                else {
                    this.errors = [];
                    this.errors.push(
                        "Please complete all of the required fields with the *"
                    );
                }
            }
        },
        revertCancleClick() {
            axios.post(this.EnvPath + "change-resume-type", {resume_id : localStorage.getItem('resume_id')}).then(({ data }) => {
                if (data.status == 1) {
                    if(data.page == 6) {
                        this.$router.push({ name: "education_enhancement" });
                    }
                    else {
                        this.$router.push({ name: "job_descriptions" });
                    }
                } 
                else {
                }
            });
        }
    },
};
</script>
<style scoped>
.resume-editor-div {
    margin: 50px auto 30px auto;
}
</style>