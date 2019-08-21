<template>
    <div class="ui vertical menu fluid">
        <!-- TODO: translate this somehow -->
        <h5 class="ui item header">Add selected to cart for</h5>

        <div class="item">
            <div class="ui transparent icon input">
                <input type="text" name="q" placeholder="Search users..." />
                <i class="icon glyphicons glyphicons-search link"></i>
            </div>
        </div>

        <a v-for="user in users" v-on:click.prevent.stop="addSelected(user)" href="#" class="item">
            {{ user.name }}
        </a>
    </div>
</template>

<script>
    export default {
        // TODO: should this be located here?
        data() {
            return {
                users: [
                    {
                        id: 51,
                        name: 'Tim Visée',
                    },
                    {
                        // TODO: specify a correct ID here
                        id: 52,
                        name: 'Other User',
                    },
                    {
                        // TODO: specify a correct ID here
                        id: 53,
                        name: 'And another user',
                    },
                ],
            };
        },
        methods: {
            addSelected(user) {
                // Find the user object, or create a new one
                let item = this.cart.filter(i => i.user.id == user.id);
                if(item.length <= 0) {
                    this.cart.push({
                        user,
                        products: JSON.parse(JSON.stringify(this.selected)),
                    });
                    return;
                }

                // Merge selected items one by one into existing list, update quantities
                this.selected.forEach(product => {
                    let i = item[0].products.filter(p => p.id == product.id);
                    if(i.length > 0)
                        i[0].quantity += product.quantity;
                    else
                        item[0].products.push(JSON.parse(JSON.stringify(product)));
                });
            },
        },
        props: [
            'selected',
            'cart',
        ],
    }
</script>
