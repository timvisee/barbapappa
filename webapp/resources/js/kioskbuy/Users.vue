<template>
    <div class="ui vertical menu fluid">
        <h5 class="ui item header">
            {{ __('pages.kiosk.selectUser') }}
        </h5>

        <div class="item">
            <div class="ui transparent icon input">
                <input v-model="query"
                        :placeholder="__('pages.kiosk.searchUsers') + '...'"
                        id="user-search"
                        type="text" />
                <div v-if="searching" class="ui active inline tiny loader"></div>
                <i v-if="!searching" v-on:click.prevent.stop="search(query)" class="icon link">
                    <span class="glyphicons glyphicons-search"></span>
                </i>
            </div>
        </div>

        <a v-for="user in users"
                v-on:click.prevent.stop="toggleSelectUser(user)"
                v-bind:class="{ disabled: buying, active: isUserSelected(user) }"
                href="#"
                class="green item">
            {{ user.name || __('misc.unknownUser') }}
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
                // Create a list of current products, to prioritize the user list
                let products = JSON.stringify(this.selected.map(p =>
                    p.product.id));

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
        },
        mounted: function() {
            this.search();
        },
    }
</script>
