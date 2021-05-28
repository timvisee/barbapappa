<template>
    <div>
        <!-- TODO: we should not need this when position sticky works -->
        <br>
        <br>

        <div class="fluid ui big buttons stick-bottom">
            <a href="#"
                    class="ui button"
                    v-on:click.prevent.stop="cancel()">
                {{
                    confirmingCancel
                        ? __('pages.bar.advancedBuy.pressToConfirm')
                        : __('general.cancel')
                }}
            </a>

            <div class="or" :data-text="__('general.or')"></div>

            <a class="ui primary button"
                    v-if="cart.length > 0"
                    v-on:click.prevent.stop="buy()"
                    v-bind:class="{ disabled: buying, loading: buying }"
                    href="#">
                {{
                    confirming
                        ? __('pages.bar.advancedBuy.pressToConfirm')
                        : (cart.length <= 1
                            ? langChoice('pages.bar.advancedBuy.buyProducts#', quantity())
                            : langChoice('pages.bar.advancedBuy.buyProductsUsers#', quantity(), {users: this.cart.length}))
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
        ],
        data: function() {
            return {
                confirming: false,
                confirmingCancel: false,
                confirmingTimer: null,
                confirmingCancelTimer: null,
            };
        },
        watch: {
            cart: function() {
                this.setConfirming(false);
                this.setConfirmingCancel(false);
            },
        },
        methods: {
            quantity() {
                return this.cart
                    .map(i => i.products
                        .map(p => p.quantity)
                        .reduce((a, b) => a + b)
                    )
                    .reduce((a, b) => a + b);
            },

            buy() {
                if(this.confirming !== true) {
                    this.setConfirming(true);
                    return;
                }

                this.$emit('buy');
                this.setConfirming(false);
            },

            cancel() {
                if(this.confirmingCancel !== true) {
                    this.setConfirmingCancel(true);
                    return;
                }

                this.resetCart();
                this.setConfirmingCancel(false);
            },

            setConfirming(confirming = true) {
                // Cancel any pending confirming timers
                if(this.confirmingTimer != null)
                    clearTimeout(this.confirmingTimer);

                // Set confirming state, add reset timer
                this.confirming = !!confirming;
                if(this.confirming)
                    this.confirmingTimer = setTimeout(() => {
                        this.confirming = false;
                        this.confirmingTimer = null;
                    }, 4000);

                // Unset confirming cancel
                if(!!confirming)
                    this.setConfirmingCancel(false);
            },

            setConfirmingCancel(confirming = true) {
                // Cancel any pending confirming timers
                if(this.confirmingCancelTimer != null)
                    clearTimeout(this.confirmingCancelTimer);

                // Set confirming state, add reset timer
                this.confirmingCancel = !!confirming;
                if(this.confirmingCancel)
                    this.confirmingCancelTimer = setTimeout(() => {
                        this.confirmingCancel = false;
                        this.confirmingCancelTimer = null;
                    }, 4000);

                // Unset confirming buy
                if(!!confirming)
                    this.setConfirming(false);
            },

            // Reset carts
            resetCart() {
                this.cart.splice(0);

                // TODO: call reset function in parent instead
                // TODO: - reset selected users
                // TODO: - refresh lists

                // Call function for this in Users component instead
                this.selectedUsers.splice(0);
            },
        }
    }
</script>

<style>
    .stick-bottom {
        position: fixed;
        bottom: 14px;
        z-index: 1001;
        box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.15) !important;

        /* TODO: do not use this hack! */
        width: calc(100% - 26px) !important;
    }
</style>
