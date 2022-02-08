<template>
    <div class="ui vertical huge menu fluid panel-users">

        <div v-if="swapped && selectedProducts.length == 0"
                v-on:click="hintProducts()"
                class="ui inverted active dimmer">
            <div class="ui text">
                {{ __('pages.kiosk.firstSelectProduct') }}

                <div class="ui horizontal divider">{{ __('general.or') }}</div>
                <a v-on:click.stop.prevent="swap()"
                        href="#">
                    {{ __('pages.kiosk.swapColumns').toLowerCase() }}
                </a>
            </div>
        </div>

        <h5 class="ui item header">
            {{ __('pages.kiosk.selectUser') }}

            <a v-if="!swapped"
                    v-on:click.stop.prevent="swap()"
                    href="#"
                    class="action"
                    :title="__('pages.kiosk.swapColumns')">
                <i class="halflings halflings-reflect-y"></i>
            </a>

            <a v-if="selectedUsers.length"
                    v-on:click.stop.prevent="reset(); query = ''"
                    href="#"
                    class="action negative">
                {{ __('pages.kiosk.deselect') }}
            </a>
        </h5>

        <div class="item">
            <div class="ui transparent icon input">
                <input v-model="query"
                        @input="e => query = e.target.value"
                        @focus="e => e.target.select()"
                        id="user-search"
                        type="search"
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
                v-on:click.prevent.stop="onItemClick(user)"
                v-bind:class="{ disabled: buying, active: isUserSelected(user) }"
                href="#"
                class="green item kiosk-select-item">
            <div class="item-text">
                {{ user.name || __('misc.unknownUser') }}
            </div>

            <div class="item-label">
                <span v-if="getQuantity(user) > 0" class="active subtle quantity">
                    {{ getQuantity(user) }}×
                </span>

                <span v-if="isUserSelected(user)"
                        class="item-icon glyphicons glyphicons-chevron-right"></span>
            </div>
        </a>

        <a v-for="user in users"
                v-on:click.prevent.stop="onItemClick(user)"
                v-bind:class="{ disabled: buying, active: isUserSelected(user) }"
                href="#"
                class="green item kiosk-select-item">
            <div class="item-text">
                {{ user.name || __('misc.unknownUser') }}
            </div>

            <div class="item-label">
                <span v-if="getQuantity(user) > 0" class="active subtle quantity">
                    {{ getQuantity(user) }}×
                </span>

                <span v-if="isUserSelected(user)"
                        class="item-icon glyphicons glyphicons-chevron-right"></span>
            </div>
        </a>

        <!-- Always show users having a cart on bottom if not part of query results -->
        <a v-for="user in cart.map(c => c.user)"
                v-if="!users.some(u => u.id == user.id) && !selectedUsers.some(u => u.id == user.id)"
                v-on:click.prevent.stop="onItemClick(user)"
                v-bind:class="{ disabled: buying, active: isUserSelected(user) }"
                href="#"
                class="green item kiosk-select-item">
            <div class="item-text">
                {{ user.name || __('misc.unknownUser') }}
            </div>

            <div class="item-label">
                <span v-if="getQuantity(user) > 0" class="active subtle quantity">
                    {{ getQuantity(user) }}×
                </span>

                <span v-if="isUserSelected(user)"
                        class="item-icon glyphicons glyphicons-chevron-right"></span>
            </div>
        </a>

        <i v-if="searching && users.length == 0 && query != ''" class="item">
            {{ __('pages.kiosk.searchingFor', {term: query}) }}...
        </i>
        <i v-if="searching && users.length == 0 && query == ''" class="item">
            {{ __('misc.loading') }}...
        </i>
        <i v-if="!searching && users.length == 0" class="item">
            {{ __('pages.kiosk.noUsersFoundFor', {term: query}) }}.
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
            'apiUrl',
            'swapped',
            'selectedUsers',
            'selectedProducts',
            'cart',
            'buying',
            '_getUserCart',
            '_mergeCart',
        ],
        watch: {
            query: function() {
                this.search(this.query);
            },
            selectedUsers: function (newSelectedUsers, oldSelectedUsers) {
                // Glow product selection as visual clue
                if(newSelectedUsers.length > 0)
                    this.$emit('highlightProducts');
            },
        },
        methods: {
            // If we're currently in user selection mode.
            isSelectMode() {
                return !this.swapped;
            },

            // Invoked when an user item is clicked.
            onItemClick(user) {
                if(this.isSelectMode())
                    this.toggleSelectUser(user);
                else
                    this.addProductsForUser(user);
            },

            // Toggle user selection.
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

            // Add selected products to the user cart
            addProductsForUser(user) {
                if(this.selectedProducts.length == 0)
                    return;

                // Get selection and user cart
                // TODO: use function to obtain this cart
                let selectCart = this.selectedProducts[0];
                if(selectCart == null || selectCart.products == undefined)
                    return;
                let userCart = this._getUserCart(user, true);

                // Merge
                this._mergeCart(selectCart, userCart);

                // TODO: temporary select clicked item
            },

            // Check whether given user is in given list.
            isUserSelected(user) {
                return this.isSelectMode() && this.selectedUsers.some(u => u.id == user.id);
            },

            // Search users with the given query
            search(query = '') {
                // Fetch the list of users, set searching state
                this.searching = true;
                axios.get(this.apiUrl + `/members?q=${encodeURIComponent(query)}`)
                    .then(res => this.users = res.data)
                    .catch(err => {
                        alert('An error occurred while listing users');
                        console.error(err);
                    })
                    .finally(() => this.searching = false);
            },

            // Get product quantity for user
            getQuantity(user) {
                // Get user cart
                let userCart = this._getUserCart(user);
                if(userCart == null)
                    return 0;

                // Count user products
                return userCart.products.reduce((sum, product) => product.quantity + sum, 0);
            },

            // Reset selection
            reset() {
                this.selectedUsers.splice(0);
            },

            // Hint to select a product first
            hintProducts() {
                if(this.selectedProducts > 0)
                    return;
                this.$emit('highlightProducts');
            },

            // Swap view
            swap() {
                this.$emit('swap');
            },
        },
        mounted: function() {
            this.search();
        },
    }
</script>

<style>
    .item {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .item-icon::before {
        margin-left: 0.3em;
        padding: 0;
    }

    .right {
        float: right;
    }

    .action {
        color: rgba(0,0,0,.87);
        margin-left: 1em;
        float: right;
        line-height: 1 !important;
    }

    .action.negative {
        color: red;
    }

    .action .halflings {
        /* This is a hack, don't occupy space instead */
        margin-top: -3px;
        margin-bottom: -3px;
    }

    .active.green {
        color: #21ba45 !important;
    }

    .quantity,
    .item.active {
        font-weight: bold !important;
    }

    .ui.dimmer .text {
        padding: 1em;
        line-height: 2;
    }

    .ui.dimmer .ui.divider {
        font-weight: normal;
    }
</style>
