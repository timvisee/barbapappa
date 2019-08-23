<template>
    <div class="ui vertical menu fluid">
        <!-- TODO: translate this somehow -->
        <h5 class="ui item header">Add selected to cart for</h5>

        <div class="item">
            <div class="ui transparent icon input">
                <input v-model="query" type="text" placeholder="Search users..." />
                <div v-if="searching" class="ui active inline tiny loader"></div>
                <i v-if="!searching" v-on:click.prevent.stop="search(query)" class="icon glyphicons glyphicons-search link"></i>
            </div>
        </div>

        <a v-for="user in users"
                v-on:click.prevent.stop="addSelected(user)"
                v-bind:class="{ disabled: buying }"
                href="#"
                class="item">
            {{ user.first_name }} {{ user.last_name }}
            <span v-if="user.me" class="subtle">(Me)</span>
        </a>

        <i v-if="!searching && users.length == 0" class="item">No users found for {{query}}...</i>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                query: '',
                searching: true,
                users: [],
            };
        },
        props: [
            'selected',
            'cart',
            'buying',
        ],
        watch: {
            query: function() {
                this.search(this.query);
            },
        },
        methods: {
            addSelected(user) {
                // Do not add products when currently buying
                if(this.buying)
                    return;

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

            // Search users with the given query
            search(query = '') {
                // Create a list of current products, to prioritize the user list
                let products = JSON.stringify(this.selected.map(p =>
                    p.product.id));

                // Fetch the list of users, set searching state
                // TODO: set fixed URL here
                this.searching = true;
                axios.get(`./buy/users?q=${encodeURIComponent(query)}&product_ids=${encodeURIComponent(products)}`)
                    .then(res => this.users = res.data)
                    .catch(err => {
                        alert('An error occurred while listing users');
                        console.error(err);
                    })
                    .finally(() => this.searching = false);
            },
        },
        mounted: function() {
            this.search();
        },
    }
</script>
