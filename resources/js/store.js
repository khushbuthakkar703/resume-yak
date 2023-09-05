import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'
 
Vue.use(Vuex)
 
export default new Vuex.Store({
    plugins: [createPersistedState({
        storage: window.sessionStorage,
    })],
    state: {
        isLoggedIn: !!localStorage.getItem('token'),
        token: localStorage.getItem('token'),
        countriesData:[],
        statesData:[],  
        contact_Data:[], 
        work_Hisory:[],
        position_Data:[],
        education_History:[],
        grad_Date: '',
        tips: [],
        userData:[],
        AC2:false,
        isGrade:false,
        SchoolData:[],
        minorDate:[],
        ResumeName_Data:[],
        load_Questions:[],
        get_job:[],
        Skills_Data:[],
        User_Skills_Data:[],
        Subscription_Data:[]
    },
    mutations: {
        LoginUser (state, data) {
            state.isLoggedIn = true;
            let token = data.access_token;
            state.token = token;
            localStorage.setItem('token', token)
        },
        LogoutUser (state) {
            state.isLoggedIn = false;
            state.token = localStorage.removeItem('token')
        },
        tokenStored (state) {
            state.token = localStorage.getItem('token')
        },
        LocationData (state, data) {
            if ('states' in data) {
                state.statesData=data.states;
            } else {
                state.countriesData=data.countries;
            }
        },
        contactData(state, data){
            state.contact_Data=data.user[0];
        },
        workHisory(state, data){
            state.work_Hisory=data;
        },
        positionData(state, data){
            state.position_Data=data;
        },
        educationHistory(state, data){
            state.education_History=data;
        },
        EducationEnhancementData(state,data){
            state.grad_Date = data.user.isgradDate;
            state.tips = data.user.tips;
            state.userData = data.user;
            state.isGrade = data.user.isGrade;
            state.SchoolData = data.user.educationData;
            state.minorDate = data.user.minorDatePush;
            if(data.user.academic_achievement !== null){
                state.AC2 = true;
            }
            else if(data.user.clubs_organizations !== null){
                state.AC2 = true;
            }
            else if(data.user.credits_completed !== null && data.user.majorgpa !== null && data.user.overallgpa !== null){
                state.AC2 = true;
            }
        },
        ResumeNameData(state,data){
            state.ResumeName_Data = data;
        },
        loadQuestions(state,data){
           state.load_Questions = data;
        },
        GetJob(state,data){
            state.get_job = data;
        },
        SkillsData(state,data){
            state.Skills_Data = data;
        },
        UserSkillData(state,data){
            state.User_Skills_Data = data;
        },
        SubscriptionData(state,data){
            state.Subscription_Data = data;
        },
    }
})