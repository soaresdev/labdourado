<template>
    <v-app>
        <v-navigation-drawer app v-model="drawer"
                             dark>
            <v-list
                dense
                nav
                class="py-0"
            >
                <v-img class="mx-auto" :src="dashboard.app_url + '/images/icon/icon-144x144.png'" width="100"></v-img>
                <v-list-item two-line :to="{ name: 'home' }">
                    <v-list-item-content>
                        <v-list-item-title>{{ dashboard.app_name }}</v-list-item-title>
                        <v-list-item-subtitle>{{ dashboard.user.name }}</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>

                <v-divider></v-divider>

                <v-list-item
                    v-for="item in items"
                    :key="item.title"
                    link
                    :to="{ name: item.name }"
                >
                    <v-list-item-icon>
                        <v-icon>{{ item.icon }}</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>{{ item.title }}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
            <template v-slot:append>
                <div class="text-center">
                    <p class="text-white">Versão: {{ dashboard.app_version }}</p>
                </div>
                <div class="pa-2">
                    <v-btn block @click="logout">Sair</v-btn>
                </div>
            </template>
        </v-navigation-drawer>

        <v-app-bar app dense
                   dark>
            <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title>{{dashboard.app_name}}</v-toolbar-title>
            <v-spacer></v-spacer>
        </v-app-bar>

        <!-- Sizes your content based upon application components -->
        <v-main>

            <!-- Provides the application the proper gutter -->
            <v-container fluid>

                <!-- If using vue-router -->
                <router-view></router-view>
            </v-container>
            <v-snackbar v-model="snackbarVisible" :timeout="snackbar.timeout" :top="false" :bottom="true" :right="true"
                        :left="false" :multi-line="false" :vertical="false" :color="snackbar.color">
                {{ snackbar.message }}
                <template v-slot:action="{ attrs }">
                    <v-btn
                        dark
                        text
                        v-bind="attrs"
                        @click.native="setSnackbarVisible"
                    >
                        OK
                    </v-btn>
                </template>
            </v-snackbar>
        </v-main>
    </v-app>
</template>

<script>
export default {
    name: 'App',
    data() {
        return {
            dashboard: Dashboard,
            drawer: true,
            items: [
                {title: 'Usuários', icon: 'mdi-account', name: 'users.index'},
                {title: 'Prestadores', icon: 'mdi-domain', name: 'providers.index'},
                {title: 'Pacientes', icon: 'mdi-account-group', name: 'patients.index'},
                {title: 'Médicos', icon: 'mdi-doctor', name: 'doctors.index'},
                {title: 'Operadoras', icon: 'mdi-domain', name: 'operators.index'},
                {title: 'Lotes', icon: 'mdi-paperclip', name: 'lots.index'},
                {title: 'Guias SP/SADT', icon: 'mdi-clipboard', name: 'guides-sadt.index'},
            ],
        }
    },
    computed: {
        snackbar() {
            return this.$store.getters.snackbar;
        },
        snackbarVisible: {
            get() {
                return this.$store.getters.snackbar.visible;
            },
            set() {
                this.setSnackbarVisible();
            }
        }
    },
    methods: {
        setSnackbarVisible() {
            this.$store.dispatch('HIDE_SNACKBAR');
        }
    }
}
</script>

<style scoped lang="scss">
a:hover {
    text-decoration: none;
}
</style>
