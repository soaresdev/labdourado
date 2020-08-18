<template>
    <v-container fluid>
        <v-row>
            <v-col cols="12" md="4">
                <v-text-field label="2 - Nº Guia no Prestador" placeholder="Nº Guia no Prestador" v-model="provider_number"></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
                <v-text-field label="3 - Nº Guia Principal" placeholder="Nº Guia Principal" v-model="main_number"></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
                <v-text-field type="date" label="4 - Data da Autorização" v-model="permission_date"></v-text-field>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" md="4">
                <v-text-field label="5 - Senha" placeholder="Senha" v-model="password"></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
                <v-text-field type="date" label="6 - Validade da Senha" v-model="password_expiration"></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
                <v-text-field label="7- Nº Guia Atribuído pela Operadora" placeholder="Nº Guia Atribuído pela Operadora" v-model="guide_operator_number"></v-text-field>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {
    name: 'header-guide-sadt',
    props: {
        operator: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            provider_number: '',
            main_number: '',
            permission_date: '',
            password: '',
            password_expiration: '',
            guide_operator_number: ''
        }
    }
}
</script>

<style>

</style>
