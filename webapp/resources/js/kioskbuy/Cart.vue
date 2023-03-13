<template>
    <div>
        <!-- TODO: we should not need this when position sticky works -->

        <br>
        <br>

        <div class="fluid two ui massive buttons stick-bottom">
            <a href="#"
                    class="ui negative button"
                    v-bind:class="{ disabled: buying }"
                    v-on:click.prevent.stop="cancel()">
                {{
                    confirmingCancel
                        ? __('pages.bar.advancedBuy.pressToConfirm')
                        : __('general.cancel')
                }}
            </a>

            <div class="or" :data-text="__('general.or')"></div>

            <a class="ui positive button"
                    v-if="cart.length > 0"
                    v-on:click.prevent.stop="buy()"
                    v-bind:class="{ disabled: buying, loading: buying }"
                    href="#">
                {{
                    confirmingBuy
                        ? __('pages.bar.advancedBuy.pressToConfirm')
                        : (cart.length <= 1
                            ? langChoice('pages.kiosk.buyProducts#', _getTotalCartQuantity())
                            : langChoice('pages.kiosk.buyProductsUsers#', _getTotalCartQuantity(), {users: this.cart.length}))
                }}
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'cart',
            'buying',
            'selectedUsers',
            '_getTotalCartQuantity',
        ],
        data: function() {
            return {
                confirmingBuy: false,
                confirmingCancel: false,
                confirmingBuyTimer: null,
                confirmingCancelTimer: null,
            };
        },
        watch: {
            cart: function() {
                this.setConfirmingBuy(false);
                this.setConfirmingCancel(false);
            },
        },
        methods: {
            buy() {
                if(this.confirmingBuy !== true) {
                    this.setConfirmingBuy(true);
                    return;
                }

                this.$emit('buy');
                this.setConfirmingBuy(false);
            },

            cancel() {
                if(this.confirmingCancel !== true) {
                    this.setConfirmingCancel(true);
                    return;
                }

                this.$emit('cancel');
                this.setConfirmingCancel(false);
            },

            unconfirm() {
                if(this.confirmingBuy)
                    this.setConfirmingBuy(false);
                if(this.confirmingCancel)
                    this.setConfirmingCancel(false);
            },

            setConfirmingBuy(confirming = true) {
                // Cancel any pending confirming timers
                if(this.confirmingBuyTimer != null)
                    clearTimeout(this.confirmingBuyTimer);

                // Set confirming state, add reset timer
                this.confirmingBuy = !!confirming;
                if(this.confirmingBuy)
                    this.confirmingBuyTimer = setTimeout(() => {
                        this.setConfirmingBuy(false);
                        this.confirmingBuyTimer = null;
                    }, 5000);

                // Unset confirming cancel
                if(!!confirming)
                    this.setConfirmingCancel(false);

                // Update global confirming
                this.$emit('confirming', this.confirmingBuy || this.confirmingCancel);
            },

            setConfirmingCancel(confirming = true) {
                // Cancel any pending confirming timers
                if(this.confirmingCancelTimer != null)
                    clearTimeout(this.confirmingCancelTimer);

                // Set confirming state, add reset timer
                this.confirmingCancel = !!confirming;
                if(this.confirmingCancel)
                    this.confirmingCancelTimer = setTimeout(() => {
                        this.setConfirmingCancel(false);
                        this.confirmingCancelTimer = null;
                    }, 5000);

                // Unset confirming buy
                if(!!confirming)
                    this.setConfirmingBuy(false);

                // Update global confirming
                this.$emit('confirming', this.confirmingBuy || this.confirmingCancel);
            },
        }
    }
</script>

<style>
    .stick-bottom {
        position: fixed;
        left: 14px;
        right: 14px;
        bottom: 14px;
        z-index: 1010;
        box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.15) !important;

        /* TODO: do not use this hack! */
        width: calc(100% - 28px) !important;
    }

    .button {
        white-space: nowrap;
    }
</style>
