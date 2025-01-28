<!-- Main buy page component -->

<template>
    <div>
        <div class="ui two item menu">
            <a href="#" class="item" @click.prevent.stop="self = true" v-bind:class="{ active: self }">{{ __('pages.bar.buy.forMe') }}</a>
            <a href="#" class="item" @click.prevent.stop="self = false" v-bind:class="{ active: !self }">{{ __('pages.bar.buy.forOthers') }}</a>
        </div>

        <div v-if="!stateOnline" class="ui message error">
            <span class="halflings halflings-exclamation-sign icon"></span>
            {{ __('pages.bar.bannerNoConnection') }}
        </div>

        <!-- Main UI -->
        <Self ref="self" v-if="self" :apiUrl="apiUrl" :barUrl="barUrl" :updateUserBalance="updateUserBalance" />
        <Other ref="other" v-else :apiUrl="apiUrl" :updateUserBalance="updateUserBalance" />
    </div>
</template>

<script>
    import axios from 'axios';

    const Self = require('./Self.vue').default;
    const Other = require('./Other.vue').default;

    export default {
        components: {
            Self,
            Other,
        },
        data() {
            return {
                self: true,
                stateOnline: navigator.onLine,
            };
        },
        props: [
            'barUrl',
            'apiUrl',
        ],
        watch: {
            stateOnline: function(newState) {
                // Skip if we're now online
                if(!newState) {
                    return;
                }

                // When we come online, try to drain buy queue
                this._buyQueueDrainAllDelayed();
            },
        },
        created() {
            // Listen to network events
            window.addEventListener("online", (e) => this.stateOnline = true);
            window.addEventListener("offline", (e) => this.stateOnline = false);

            // Prevent accidental closing
            window.addEventListener('beforeunload', this.onClose);
        },
        methods: {
            onClose(event) {
                if(this.self)
                    return this.$refs.self.onClose(event);
                else
                    return this.$refs.other.onClose(event);
            },

            // Update user balance in element outside widget
            updateUserBalance(value, text) {
                if(value === undefined || value === null)
                    return;

                let element = document.getElementById('user-balance');
                if(element == null)
                    return;

                // Update element
                var classes = '';
                if(value < 0)
                    classes = 'red';
                else if(value > 0)
                    classes = 'green';
                element.innerHTML = '<div class="ui label ' + classes + '">' + (text || value) + '</div>';

                // Animate element
                $(element)
                    .transition('stop')
                    .transition('pulse');
            }
        },
    }
</script>
