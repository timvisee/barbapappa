<template>
    <div class="ui vertical menu fluid">

        <!-- TODO: translate this somehow -->
        <h5 class="ui item header">Select products</h5>

        <div class="item">
            <div class="ui transparent icon input">
                <!-- TODO: translate this somehow -->
                <input type="text" name="q" placeholder="Search products..." />
                <i class="icon glyphicons glyphicons-search link"></i>
            </div>
        </div>

        <a v-for="product in products"
                v-on:click.stop.prevent="select(product)"
                href="#"
                class="green inverted item"
                v-bind:class="{ active: getQuantity(product) > 0 }">
            <span v-if="getQuantity(product) > 0" class="subtle">{{ getQuantity(product) }}×</span>

            {{ product.name }}

            <div v-if="getQuantity(product)"
                    v-on:click.stop.prevent="deselect(product)"
                    class="ui red label">×</div>

            <div class="ui blue label">{{ product.price }}</div>
        </a>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                products: [
                    {
                        id: 1,
                        name: 'Product A',
                        price: '€1,00',
                        quantity: 0,
                    },
                    {
                        id: 2,
                        name: 'Product B',
                        price: '€0,70',
                        quantity: 0,
                    },
                    {
                        id: 3,
                        name: 'Product C',
                        price: '€0,70',
                        quantity: 0,
                    },
                ],
            };
        },
        methods: {
            select(product) {
                let item = this.selected.filter(p => p.id == product.id);
                if(item.length > 0)
                    item[0].quantity += 1;
                else
                    this.selected.push({
                        id: product.id,
                        quantity: 1,
                        product,
                    });
            },
            deselect(product) {
                this.selected.splice(
                    this.selected.findIndex(p => p.id == product.id),
                    1,
                );
            },
            getQuantity(product) {
                let item = this.selected.filter(p => p.id == product.id);
                return item.length > 0 ? item[0].quantity : 0;
            },
        },
        props: [
            'selected',
        ]
    }
</script>
