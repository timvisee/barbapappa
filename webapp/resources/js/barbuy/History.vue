<template>
    <div>
        <div v-html="html">
            <div class="ui active centered inline loader"></div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        props: [
            'apiUrl',
            'barUrl',
        ],
        data() {
            return {
                refreshing: true,
                html: null,
            };
        },
        mounted: function() {
            this.refresh();
        },
        methods: {
            // Refresh history list
            refresh() {
                this.refreshing = true;
                this._refreshRequest()
                    .then(body => {
                        this.html = body;
                    })
                    .catch(err => {
                        console.error(err);
                    })
                    .finally(() => {
                        this.refreshing = false;
                    });
            },

            // Do a refresh request
            _refreshRequest(query = '', all = false) {
                let url = new URL(this.barUrl + '/widget/history');
                return axios.get(url.toString()).then(res => res.data);
            },
        },
    }
</script>
