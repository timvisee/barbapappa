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

            <a class="ui bottom attached primary button" href="#">
                Buy

                <span v-if="cart.length > 0">{{ quantity() }} product(s)</span>
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
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
            }
        }
    }
</script>
