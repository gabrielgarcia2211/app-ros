<script>
export default {
    methods: {
        $getEnumsOptions(type) {
            const vm = this;
            return new Promise((resolve, reject) => {
                this.$axios
                    .get(`/enums/option/${type.join(",")}`)
                    .then(function (response) {
                        resolve(response.data);
                    })
                    .catch((error) => {
                        vm.$readStatusHttp(error);
                        reject(error);
                    });
            });
        },
    },
};
</script>
