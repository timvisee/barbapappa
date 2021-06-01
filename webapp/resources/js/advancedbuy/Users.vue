<template>
    <div class="ui vertical menu fluid">
        <h5 class="ui item header">
            {{ __('pages.bar.advancedBuy.addToCartFor') }}
        </h5>

        <div class="item">
            <div class="ui transparent icon input">
                <input v-model="query"
                        @input="e => query = e.target.value"
                        @focus="e => e.target.select()"
                        id="user-search"
                        type="text"
                        :placeholder="__('pages.bar.advancedBuy.searchUsers') + '...'" />
                <div v-if="searching" class="ui active inline tiny loader"></div>
                <i v-if="!searching && !query" v-on:click.prevent.stop="search(query)" class="icon link">
                    <span class="glyphicons glyphicons-search"></span>
                </i>
                <i v-if="!searching && query" v-on:click.prevent.stop="query = ''" class="icon link">
                    <span class="glyphicons glyphicons-remove"></span>
                </i>
            </div>
        </div>

        <a v-for="user in users"
                v-on:click.prevent.stop="addSelected(user)"
                v-bind:class="{ disabled: buying, active: user.active }"
                href="#"
                class="green item">
            {{ user.name || __('misc.unknownUser') }}
            <span v-if="user.me" class="subtle">({{ __('misc.me') }})</span>
        </a>

        <i v-if="searching && users.length == 0 && query != ''" class="item">
            {{ __('pages.bar.advancedBuy.searchingFor', {term: query}) }}...
        </i>
        <i v-if="searching && users.length == 0 && query == ''" class="item">
            {{ __('misc.loading') }}...
        </i>
        <i v-if="!searching && users.length == 0" class="item">
            {{ __('pages.bar.advancedBuy.noUsersFoundFor', {term: query}) }}.
        </i>
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

                // Mark user as active for half a second
                user.active = true;
                this.$forceUpdate();
                setTimeout(() => {
                    user.active = false;
                    this.$forceUpdate();
                }, 500);

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
                axios.get(window.location.href + `/members?q=${encodeURIComponent(query)}&product_ids=${encodeURIComponent(products)}`)
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
