<template>
    <div class="ui vertical huge menu inverted fluid panel-users">

        <div v-if="swapped && selectedProducts.length == 0"
                v-on:click="hintProducts()"
                class="ui active dimmer">
            <div class="ui text inverted">
                {{ __('pages.kiosk.firstSelectProduct') }}

                <div class="ui hidden divider"></div>
                <div class="ui horizontal divider hidden"></div>
                <div class="ui hidden divider"></div>

                <a v-on:click.stop.prevent="swap()"
                        class="ui big button basic primary inverted dimmer-swap"
                        href="#">
                    <i class="halflings halflings-reflect-y"></i>
                    {{ __('pages.kiosk.userMode') }}
                </a>
            </div>
        </div>

        <h5 class="ui item header">
            <span v-if="isSelectMode()">{{ __('pages.kiosk.selectUser') }}</span>
            <span v-else>{{ __('pages.kiosk.addToUser') }}</span>

            <div class="header-actions">
                <a v-on:click.stop.prevent="toggleIndex()"
                        v-bind:class="{ active: showIndex }"
                        href="#"
                        :title="__('pages.kiosk.toggleLetterIndex')">
                    <i class="halflings halflings-sort-by-alphabet"></i>
                </a><!--

                --><a v-if="!swapped"
                        v-on:click.stop.prevent="swap()"
                        href="#"
                        :title="__('pages.kiosk.swapColumns')">
                    <i class="halflings halflings-reflect-y"></i>
                </a><!--

                --><a v-if="isSelectMode() && selectedUsers.length && !buying"
                        v-on:click.stop.prevent="reset(); query = ''"
                        href="#"
                        class="negative">
                    <i class="halflings halflings-remove"></i>
                </a><!--

                --><a v-if="!isSelectMode() && _getTotalCartQuantity() > 0 && !buying"
                        v-on:click.stop.prevent="_removeAllUserCarts(); query = ''"
                        href="#"
                        class="negative">
                    <i class="halflings halflings-trash"></i>
                </a>
            </div>
        </h5>

        <div class="item">
            <div class="ui transparent icon input">
                <input v-model="query"
                        @input="e => query = e.target.value"
                        @focus="e => { e.target.select(); showIndex = false; }"
                        id="user-search"
                        type="search"
                        :placeholder="__('pages.kiosk.searchUsers') + '...'"
                        autocomplete="off" />
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
                v-if="!showIndex && !users.some(u => u.id == user.id)"
                v-on:click.prevent.stop="onItemClick(user)"
                v-bind:class="{ disabled: buying, active: isUserSelected(user) }"
                href="#"
                class="green item kiosk-select-item">
            <div class="item-text">
                <span v-if="!user.registered" class="halflings halflings-tag"></span>

                {{ user.name || __('misc.unknownUser') }}
            </div>

            <div class="item-label">
                <span v-if="getQuantity(user) > 0" class="active subtle quantity">
                    {{ getQuantity(user) }}×
                </span>

                <span v-if="isUserSelected(user)"
                        class="item-icon glyphicons glyphicons-chevron-right"></span>
            </div>

            <div class="item-buttons" v-if="getQuantity(user) > 0">
                <div class="ui two buttons">
                    <a href="#"
                            v-if="swapped"
                            v-on:click.stop.prevent="viewUserCart(user)"
                            v-bind:class="{ disabled: buying }"
                            class="ui large button black">
                        <i class="glyphicons glyphicons-shopping-cart"></i>
                    </a>

                    <a href="#"
                            v-on:click.stop.prevent="_removeUserCart(user)"
                            v-bind:class="{ black: isSelectMode(), red: !isSelectMode(), disabled: buying }"
                            class="ui large button">
                        <i class="glyphicons glyphicons-remove"></i>
                    </a>
                </div>
            </div>
        </a>

        <a v-for="user in users"
                v-if="!showIndex"
                v-on:click.prevent.stop="onItemClick(user)"
                v-bind:class="{ disabled: buying || (!user.registered && !isUserSelected(user)), active: isUserSelected(user) }"
                href="#"
                class="green item kiosk-select-item">
            <div class="item-text">
                <span v-if="!user.registered" class="halflings halflings-tag"></span>

                {{ user.name || __('misc.unknownUser') }}
            </div>

            <div class="item-label">
                <span v-if="getQuantity(user) > 0" class="active subtle quantity">
                    {{ getQuantity(user) }}×
                </span>

                <span v-if="isUserSelected(user)"
                        class="item-icon glyphicons glyphicons-chevron-right"></span>
            </div>

            <div class="item-buttons" v-if="getQuantity(user) > 0">
                <div class="ui two buttons">
                    <a href="#"
                            v-if="swapped"
                            v-on:click.stop.prevent="viewUserCart(user)"
                            v-bind:class="{ disabled: buying }"
                            class="ui large button black">
                        <i class="glyphicons glyphicons-shopping-cart"></i>
                    </a>

                    <a href="#"
                            v-on:click.stop.prevent="_removeUserCart(user)"
                            v-bind:class="{ black: isSelectMode(), red: !isSelectMode(), disabled: buying }"
                            class="ui large button">
                        <i class="glyphicons glyphicons-remove"></i>
                    </a>
                </div>
            </div>
        </a>

        <!-- Always show users having a cart on bottom if not part of query results -->
        <a v-for="user in cart.map(c => c.user)"
                v-if="!showIndex && !users.some(u => u.id == user.id) && !selectedUsers.some(u => u.id == user.id)"
                v-on:click.prevent.stop="onItemClick(user)"
                v-bind:class="{ disabled: buying || (!user.registered && !isUserSelected(user)), active: isUserSelected(user) }"
                href="#"
                class="green item kiosk-select-item">
            <div class="item-text">
                <span v-if="!user.registered" class="halflings halflings-tag"></span>

                {{ user.name || __('misc.unknownUser') }}
            </div>

            <div class="item-label">
                <span v-if="getQuantity(user) > 0" class="active subtle quantity">
                    {{ getQuantity(user) }}×
                </span>

                <span v-if="isUserSelected(user)"
                        class="item-icon glyphicons glyphicons-chevron-right"></span>
            </div>

            <div class="item-buttons" v-if="getQuantity(user) > 0">
                <div class="ui two buttons">
                    <a href="#"
                            v-if="swapped"
                            v-on:click.stop.prevent="viewUserCart(user)"
                            v-bind:class="{ disabled: buying }"
                            class="ui large button black">
                        <i class="glyphicons glyphicons-shopping-cart"></i>
                    </a>

                    <a href="#"
                            v-on:click.stop.prevent="_removeUserCart(user)"
                            v-bind:class="{ black: isSelectMode(), red: !isSelectMode(), disabled: buying }"
                            class="ui large button">
                        <i class="glyphicons glyphicons-remove"></i>
                    </a>
                </div>
            </div>
        </a>

        <div class="ui grid container index" v-if="showIndex">
            <div class="four column row">
                <a class="column button" v-for="i in 26"
                        v-on:click.prevent.stop="selectIndex(String.fromCharCode(96 + i))"
                        href="#">
                    {{ String.fromCharCode(64 + i) }}
                </a>
            </div>
        </div>
        <a class="item kiosk-select-item"
                v-if="showIndex"
                v-on:click.prevent.stop="toggleIndex()"
                href="#">
            <div class="item-text">
                {{ __('general.close') }}
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
                showIndex: false,
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
            '_removeUserCart',
            '_removeAllUserCarts',
            '_getTotalCartQuantity',
        ],
        watch: {
            query: function() {
                this.search(this.query);
                this.showIndex = false;
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

            // Swap columns and view the given users cart
            viewUserCart(user) {
                this.swap();
                this.reset();
                this.toggleSelectUser(user);
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

            // Show letter index
            toggleIndex() {
                this.showIndex = !this.showIndex;
            },

            // Select an index character
            selectIndex(char) {
                this.query = '^' + char;
                this.showIndex = false;
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

    .active.green {
        color: #21ba45 !important;
    }

    /* Right aligned buttons */
    .kiosk-select-item .item-buttons {
        overflow: hidden;
        flex-shrink: 0;
        display: flex;
        flex-direction: row;
        align-items: stretch;
    }

    .kiosk-select-item .item-buttons .button {
        text-align: center;
        padding: .92857143em 1.125em;
        line-height: 1.1;
        border-radius: 0 !important;
    }

    .button .glyphicons {
        vertical-align: middle;
    }

    .button .glyphicons::before {
        padding: 0;
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

<style lang="scss">
    .header-actions {
        position: absolute;
        top: 0px;
        right: 0px;
        height: 40px;
        display: block;

        a {
            /* color: rgba(0, 0, 0, .87); */
            color: #fff;
            display: inline-block;
            width: 40px;
            text-align: center;
            margin: 0;
            padding: 13px 5px 13px 5px;
            border-left: 1px solid rgba(255, 255, 255, .08);
            transition: background .1s ease, color .1s ease;

            &:hover {
                /* color: rgba(0, 0, 0, .87); */
                /* background: rgba(0, 0, 0, .08) !important; */
                background: #2d2e2f !important;
            }

            &.active {
                /* TODO: find proper color code here */
                background: #0f617c !important;
            }

            .glyphicons,
            .halflings {
                top: 2px;
                width: 30px;
                height: 14px;
                margin: -1px 0 0 0;
            }
        }

        .negative {
            color: red;
        }
    }

    .index {

        border-top: 1px solid rgba(255, 255, 255, .08);
        border-bottom: none;
        margin: 0 !important;
        border-bottom: none;

        .row {
            padding: 0 !important;
            border-bottom: none !important;
        }

        .button {
            display: flex !important;
            align-items:center;
            justify-content:center;

            color: #fff;
            text-align: center;
            margin: 0;
            padding: 1em;
            font-weight: bold;
            aspect-ratio: 1;

            border-bottom: 1px solid rgba(255, 255, 255, .08);
            border-right: 1px solid rgba(255, 255, 255, .08);
            transition: background .1s ease, color .1s ease;

            &:hover {
                /* color: rgba(0, 0, 0, .87); */
                /* background: rgba(0, 0, 0, .08) !important; */
                background: #2d2e2f !important;
            }
        }
    }
</style>

<style scoped>
    .ui.vertical.menu .halflings {
        line-height: 0.6;
        margin-right: 0.5em;
    }

    .ui.button.dimmer-swap {
        padding-left: 1em;
        padding-right: 1em;

        .halflings {
            top: 2px;
        }
    }

    /* Inverted colors */
    .menu.inverted .item .ui.button.black {
        background: #1b1c1d80;

        &:hover,
        &:active,
        &:focus {
            background: #1b1c1db0;
        }
    }

    .kiosk-select-item.disabled:not(.active) .item-text,
    .kiosk-select-item.disabled:not(.active) .item-label .ui.label.black {
        color: #686869 !important;
    }

    .ui.text.inverted {
        color: #fff;
    }

    .ui.input input,
    .subtle.quantity {
        color: lightgray;
    }
</style>
