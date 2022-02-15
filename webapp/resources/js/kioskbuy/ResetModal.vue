<template>
    <div id="reset-modal" class="ui basic modal">
        <div class="ui icon header">
            <i class="glyphicons glyphicons-hourglass logo"></i>
        </div>
        <div class="content">
            <p>
                <slot name="header">
                    <div class="ui huge inverted form">
                        <span v-html="__('pages.kiosk.cartTimeoutDescription')"></span>
                    </div>
                </slot>
            </p>
        </div>
        <div class="actions">
            <div class="ui green huge button" @click="hide">
                <i class="shopping cart icon"></i>
                {{ __('pages.kiosk.viewCart') }}
            </div>
            <div class="ui red huge inverted button" @click="reset">
                <i class="close icon"></i>
                {{ __('pages.kiosk.resetCart') }}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                showing: false,
                modal: undefined,
            };
        },
        watch: {
            // Trigger modal when showing is set to true
            showModal: function(nowShowing) {
                if(nowShowing)
                    this.show();
                else
                    this.hide();
            },
        },
        props: [
            'showModal',
        ],
        mounted: function() {
            this.modal = $('#reset-modal');
        },
        methods: {
            show() {
                // Already showing
                if(this.showing)
                    return;

                // Show modal
                this.showing = true;
                this.modal.modal({
                        closable: true,
                        duration: 0,
                        onHide: () => {
                            this.showing = false;
                            this.$emit('onHide');
                        },
                    })
                    .modal('show');
            },

            hide() {
                let wasShowing = this.showing;
                this.showing = false;
                if(wasShowing)
                    this.modal.modal('hide');
            },

            reset() {
                this.hide();
                this.$emit('onReset');
            },
        },
    }
</script>

<style>
    .glyphicons.logo {
        font-size: 3em;
        display: block;
        margin: 0.2em;
    }

    .ui.modal > .actions {
        padding-top: 0 !important;
        text-align: center !important;
    }

    .ui.modal .content {
        text-align: center;
        margin: 2em 0;
    }

    .actions .button {
        display: block;
        margin: 1em 0 0 !important;
    }
</style>
