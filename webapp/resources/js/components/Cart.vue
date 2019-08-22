<template>
    <div>
        <div class="ui vertical menu fluid">
            <!-- TODO: translate this somehow -->
            <h5 class="ui item header">In cart</h5>

            <div v-for="item in cart">
                <i class="ui item">For {{ item.user.first_name }} {{ item.user.last_name }}</i>
                <div v-for="product in item.products" class="item">
                    <span class="subtle">{{ product.quantity }}×</span>

                    {{ product.product.name }}

                    <a v-on:click.stop.prevent="discard(item.user, product)"
                            class="ui red label" href="#">×</a>

                    <div class="ui blue label">{{ product.product.price_display }}</div>
                </div>
            </div>

            <a class="ui bottom attached primary button"
                    v-on:click.prevent.stop="buy()"
                    v-bind:class="{ disabled: buying }"
                    href="#">
                <span v-if="!buying">
                    Buy
                    <span v-if="cart.length > 0">{{ quantity() }} product(s)</span>
                </span>
                <div v-else class="ui active inline inverted tiny loader"></div>
            </a>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                buying: false,
            };
        },
        props: [
            'selected',
            'cart',
        ],
        methods: {
            discard(user, product) {
                // Find the user item
                let itemArr = this.cart.filter(i => i.user.id == user.id);
                if(itemArr.length <= 0)
                    return;
                let item = itemArr[0];

                // Remove the product
                item.products.splice(item.products.findIndex(p => p.id == product.id), 1);

                // Remove the whole user if there are no products left
                if(item.products.length <= 0)
                    this.cart.splice(this.cart.findIndex(i => i.user.id == user.id), 1);
            },

            quantity() {
                return this.cart
                    .map(i => i.products
                        .map(p => p.quantity)
                        .reduce((a, b) => a + b)
                    )
                    .reduce((a, b) => a + b);
            },

            // Commit the current cart as purchase
            buy() {
                // Do not buy if already buying
                if(this.buying)
                    return;
                this.buying = true;

                axios.post('./buy', this.cart)
                    .then(res => {
                        // TODO: show success message

                        // Clear the selected & cart
                        this.selected.splice(0);
                        this.cart.splice(0);
                    })
                    .catch(err => {
                        alert('Failed to purchase products, an error occurred');
                        console.error(err);
                    })
                    .finally(() => this.buying = false);
            },
        }
    }
</script>
