<template>
    <div class="ui vertical large menu fluid panel-users">
        <h5 class="ui item header">
            {{ __('pages.kiosk.selectUser') }}

            <a v-if="selectedUsers.length"
                    v-on:click.stop.prevent="reset(); query = ''"
                    href="#"
                    class="reset">
                {{ __('pages.kiosk.deselect') }}
            </a>
        </h5>

        <div class="item">
            <div class="ui transparent icon input">
                <input v-model="query"
                        @input="e => query = e.target.value"
                        @focus="e => e.target.select()"
                        id="user-search"
                        type="text"
                        :placeholder="__('pages.kiosk.searchUsers') + '...'" />
                <div v-if="searching" class="ui active inline tiny loader"></div>
                <i v-if="!searching && !query" v-on:click.prevent.stop="search(query)" class="icon link">
                    <span class="glyphicons glyphicons-search"></span>
                </i>
                <i v-if="!searching && query" v-on:click.prevent.stop="query = ''" class="icon link">
                    <span class="glyphicons glyphicons-remove"></span>
                </i>
            </div>
        </div>

        <!-- Always show selected user on top if not part of query results -->
        <a v-for="user in selectedUsers"
                v-if="!users.some(u => u.id == user.id)"
                v-on:click.prevent.stop="toggleSelectUser(user)"
                v-bind:class="{ disabled: buying, active: isUserSelected(user) }"
                href="#"
                class="green item">
            {{ user.name || __('misc.unknownUser') }}

            <div class="right">
                <span v-if="getQuantity(user) > 0" class="active green">{{ getQuantity(user) }}×</span>

                <span v-if="isUserSelected(user)"
                        class="item-icon glyphicons glyphicons-chevron-right"></span>
            </div>
        </a>

        <a v-for="user in users"
                v-on:click.prevent.stop="toggleSelectUser(user)"
                v-bind:class="{ disabled: buying, active: isUserSelected(user) }"
                href="#"
                class="green item">
            {{ user.name || __('misc.unknownUser') }}

            <div class="right">
                <span v-if="getQuantity(user) > 0" class="active green">{{ getQuantity(user) }}×</span>

                <span v-if="isUserSelected(user)"
                        class="item-icon glyphicons glyphicons-chevron-right"></span>
            </div>
        </a>

        <!-- Always show users having a cart on bottom if not part of query results -->
        <a v-for="user in cart.map(c => c.user)"
                v-if="!users.some(u => u.id == user.id) && !selectedUsers.some(u => u.id == user.id)"
                v-on:click.prevent.stop="toggleSelectUser(user)"
                v-bind:class="{ disabled: buying, active: isUserSelected(user) }"
                href="#"
                class="green item">
            {{ user.name || __('misc.unknownUser') }}

            <div class="right">
                <span v-if="getQuantity(user) > 0" class="active green">{{ getQuantity(user) }}×</span>

                <span v-if="isUserSelected(user)"
                        class="item-icon glyphicons glyphicons-chevron-right"></span>
            </div>
        </a>

        <i v-if="!searching && users.length == 0" class="item">
            {{ __('pages.kiosk.noUsersFoundFor', {term: query}) }}...
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
            'selectedUsers',
            'cart',
            'buying',
        ],
        watch: {
            query: function() {
                this.search(this.query);
            },
            selectedUsers: function (newSelectedUsers, oldSelectedUsers) {
                // Glow product selection as visual clue
                if(newSelectedUsers.length > 0)
                    $('.panel-products')
                        .transition('stop')
                        .transition('glow');
            },
        },
        methods: {
            toggleSelectUser(user) {
                // Assert we have maximum of one user in the list
                if(this.selectedUsers.length > 1)
                    throw 'selected user list cannot have multiple users';

                // Remove from list if already in it
                if(this.isUserSelected(user)) {
                    this.selectedUsers.splice(
                        this.selectedUsers.findIndex(u => u.id == user.id),
                        1,
                    );
                    return;
                }

                // Add user to list
                this.selectedUsers.splice(0);
                this.selectedUsers.push(user);
            },

            // Check whether given user is in given list.
            isUserSelected(user) {
                return this.selectedUsers.some(u => u.id == user.id);
            },

            // Search users with the given query
            search(query = '') {
                // TODO: include users already having products
                // // Create a list of current products, to prioritize the user list
                // let products = JSON.stringify(this.selected.map(p =>
                //     p.product.id));

                // Fetch the list of users, set searching state
                // TODO: set fixed URL here
                this.searching = true;
                axios.get(window.location.href + `/api/members?q=${encodeURIComponent(query)}`)
                    .then(res => this.users = res.data)
                    .catch(err => {
                        alert('An error occurred while listing users');
                        console.error(err);
                    })
                    .finally(() => this.searching = false);
            },

            // Get cart instance for user
            getUserCart(user) {
                return this.cart.filter(c => c.user.id == user.id)[0] || null;
            },

            // Get product quantity for user
            getQuantity(user) {
                // Get user cart
                let userCart = this.getUserCart(user);
                if(userCart == null)
                    return 0;

                // Count user products
                return userCart.products.reduce((sum, product) => product.quantity + sum, 0);
            },

            // Reset selection
            reset() {
                this.selectedUsers.splice(0);
            },
        },
        mounted: function() {
            this.search();
        },
    }
</script>

<style>
    .right {
        float: right;
    }

    .item-icon::before {
        padding: 0;
    }

    .reset {
        color: red;
        float: right;
        line-height: 1 !important;
    }

    .active.green {
        color: #21ba45 !important;
    }
</style>
