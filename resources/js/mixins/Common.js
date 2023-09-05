import store from "../store";

export default {
    methods: {
        loadResumes() {
            axios.post(this.EnvPath + "get-resume", {
            }).then(({ data }) => {
            if (data.status == 1) {
                store.commit("ResumeNameData", data);
            }
            });
        },
        getEducationEnhancement() {
            axios
            .get(this.EnvPath+"education_enhancement")
            .then((response) => {
                store.commit("EducationEnhancementData", response.data);
            })
            .catch((error) => console.log(error));
          },
    }
}