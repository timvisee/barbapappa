<template>
    <div id="product-quantity-modal" class="ui basic modal">
        <div class="ui icon header">
            <i class="glyphicons glyphicons-hash logo"
                    @click="quantity = 1 + Math.floor(Math.random() * 10);"></i>
        </div>
        <div class="content">

            <div class="ui grid display-inline-block">
                <div class="row">
                    <div class="column">
                        <div class="ui huge form">
                            <div class="quantity-ticker">
                                <button class="ui left attached huge negative button"
                                        v-bind:class="{ disabled: quantity <= 0 }"
                                        @click="changeQuantity(-1)">
                                    <i class="glyphicons glyphicons-minus"></i>
                                </button>
                                <input
                                        type="number"
                                        inputmode="numeric"
                                        pattern="[0-9]*"
                                        min="0"
                                        max="100"
                                        v-model="quantity"
                                        class="ui fluid huge attached"
                                        @focus="e => e.target.select()"
                                        @keyup.enter.stop.prevent="hide">
                                <button class="ui right attached huge positive button"
                                        @click="changeQuantity(+1)">
                                    <i class="glyphicons glyphicons-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="four column row" v-for="qq in quantities">
                    <div class="column" v-for="q in qq">
                        <button
                                class="fluid ui huge inverted basic primary button quantity-button"
                                v-on:click.prevent.stop="quantity = q"
                                v-on:dblclick.prevent.stop="hide()">
                            {{ q }}
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="column">
                        <div class="fluid ui primary ok huge inverted button"
                                @click.prevent.stop="hide()">
                            <i class="checkmark icon"></i>
                            {{ __('pages.kiosk.changeQuantity') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                quantities: [
                    [1, 2, 3, 4],
                    [5, 6, 12, 24],
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
                        duration: 0,
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
        font-size: 3em;
        display: block;
        margin: 0.2em;
    }

    .quantity-button {
        text-align: center;
        padding-left: 1em !important;
        padding-right: 1em !important;
    }

    .quantity-ticker {
        display: flex;
        justify-content: stretch;
    }

    .quantity-ticker .button {
        flex-shrink: 0;
        padding-left: 1em !important;
        padding-right: 1em !important;
    }

    .quantity-ticker input {
        flex-grow: 1;
        border-radius: 0 !important;
        width: 8em !important;
        text-align: center;
        background: #1b1c1d !important;
        color: #fff !important;
        font-family: sans-serif;
    }

    /* Chrome, Safari, Edge, Opera */
    .quantity-ticker input::-webkit-outer-spin-button,
    .quantity-ticker input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    .quantity-ticker input[type=number] {
        -moz-appearance: textfield !important;
    }
</style>
