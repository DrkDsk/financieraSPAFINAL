<template>
    <v-app>
        <v-navigation-drawer 
        app 
        color="#0D47A1"
        absolute
        :value="drawerOpen"
        dark>
        <navigation></navigation>
        </v-navigation-drawer>

        <v-app-bar app 
        dark
        color="#1565C0">
            <v-app-bar-nav-icon @click="drawerToggle"></v-app-bar-nav-icon>
            <v-toolbar-title>
                Financiera
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-menu offset-y>
                <template v-slot:activator="{ on }">
                    <v-btn text v-on="on">
                        {{ user.name }}
                        <v-icon right dark>mdi-chevron-down</v-icon>
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item @click="logout">
                        <v-list-item-icon>
                            <v-icon>mdi-exit-to-app</v-icon>
                        </v-list-item-icon>
                        <v-list-item-title>
                            Logout
                        </v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-app-bar>

        <v-content>
            <v-container fluid>
                <router-view></router-view>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
import Navigation from '@/js/components/Navigation';
export default {
    components: {
        navigation: Navigation
    },
    computed: {
        drawerOpen () {
            return this.$store.state.drawerOpen;
        },
        user () {
            return this.$store.state.user || { name: '' };
        }
    },
    methods: {
        drawerToggle () {
            this.$store.dispatch('drawerToggle');
        },
        async logout() {
            await this.$store.dispatch('logout');
            this.$router.push('/login');
        }
    }
}
</script>
