<template>
    <v-container fluid>
        <v-row align="center" justify="center">
            <v-img :src="logo" contain></v-img>
        </v-row>
    </v-container>
</template>

<script>
export default {
    name: 'Home',
    data() {
        return {
            logo: Dashboard.app_url + '/images/logo.png'
        }
    }
}
</script>

<style>

</style>
