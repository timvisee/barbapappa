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

        <a v-for="user in users" v-on:click.prevent.stop="addSelected(user)" href="#" class="item">
            {{ user.first_name }} {{ user.last_name }}
        </a>

        <i v-if="!searching && users.length == 0" class="item">No users found for {{query}}...</i>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                query: '',
                searching: true,
                users: [],
            };
        },
        watch: {
            query: function() {
                this.search(this.query);
            },
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

            // Search users with the given query
            search(query = '') {
                this.searching = true;
                // TODO: set fixed URL here
                // TODO: handle search failures!
                fetch(`./buy/users?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(res => {
                        this.users = res;
                        this.searching = false;
                    });
            },
        },
        mounted: function() {
            this.search();
        },
        props: [
            'selected',
            'cart',
        ],
    }
</script>
