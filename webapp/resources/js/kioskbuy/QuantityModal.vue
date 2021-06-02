<template>
    <div id="product-quantity-modal" class="ui basic modal">
        <div class="ui icon header">
            <i class="glyphicons glyphicons-hash logo"
                    @click="quantity = 1 + Math.floor(Math.random() * 10);"></i>
            {{ __('pages.kiosk.productQuantity') }}:
        </div>
        <div class="content">
            <p>
                <slot name="header">
                    <div class="ui huge inverted form">
                        <div class="quantity-ticker">
                            <button class="ui left attached inverted huge negative button"
                                    v-bind:class="{ disabled: quantity <= 0 }"
                                    @click="changeQuantity(-1)">
                                <i class="glyphicons glyphicons-minus"></i>
                            </button>
                            <input type="text" v-model="quantity" class="attached" autofocus>
                            <button class="ui right attached inverted huge positive button"
                                    @click="changeQuantity(+1)">
                                <i class="glyphicons glyphicons-plus"></i>
                            </button>
                        </div>
                    </div>
                </slot>
            </p>

            <div style="text-align: center;">
                <div v-for="qq in quantities">
                    <button v-for="q in qq" class="ui huge inverted basic primary button quantity-button" v-on:click.prevent.stop="quantity = q">
                        {{ q }}
                    </button>
                </div>
            </div>
        </div>
        <div class="actions">
            <div class="ui green ok huge inverted button">
                <i class="checkmark icon"></i>
                {{ __('general.accept') }}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                quantities: [
                    [1,  3,  5,  8],
                    [10, 15, 20, 25],
                    [30, 35, 40, 45],
                ],
                showing: false,
                modal: undefined,
                quantity: 0,
            };
        },
        watch: {
            // Initial quantity as prop, triggers modal on set
            initialQuantity: function(newQuantity, oldQuantity) {
                if(newQuantity != null && oldQuantity == null) {
                    this.quantity = newQuantity;
                    this.show();
                } else if(newQuantity == null && oldQuantity != null)
                    this.hide();
            },
            quantity: function(newQuantity) {
                // Clamp quantity to 0 minimum
                if((newQuantity != '' && newQuantity != null) && (isNaN(newQuantity) || newQuantity < 0))
                    this.quantity = 1;
            },
        },
        props: [
            'initialQuantity',
            'onSubmit',
        ],
        mounted: function() {
            this.modal = $('#product-quantity-modal');
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
                        onHide: () => {
                            this.showing = false;
                            if(this.quantity !== undefined && this.quantity !== null && this.quantity !== '' && !isNaN(this.quantity))
                                this.$emit('onSubmit', Number.parseInt(this.quantity) || 0);
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

            changeQuantity(diff) {
                this.quantity = (Number.parseInt(this.quantity) || 0) + diff;
            },
        },
    }
</script>

<style>
    .glyphicons.logo {
        font-size: 3.5em;
        display: block;
        margin: 0.2em;
    }

    .quantity-button {
        text-align: center;
        width: 3em;
        padding-left: 0 !important;
        padding-right: 0 !important;
        margin: 0.5em !important;
    }

    .quantity-ticker {
        display: flex;
        justify-content: center;
    }

    .quantity-ticker .button {
        flex-shrink: 0;
        padding-left: 1em !important;
        padding-right: 1em !important;
    }

    .quantity-ticker input {
        border-radius: 0 !important;
        width: 8em !important;
        text-align: center;
    }

    .actions {
        text-align: center !important;
    }

    .actions .button {
        margin: 1rem !important;
    }
</style>
