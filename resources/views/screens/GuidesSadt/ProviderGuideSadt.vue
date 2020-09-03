<template>
    <v-container fluid>
        <v-row>
            <v-col class="d-flex" cols="12" md="3">
                <v-autocomplete
                    v-model="provider"
                    :items="providers"
                    filled
                    dense
                    label="Prestadora *"
                    :item-text="providers => providers.name"
                    :item-value="providers => providers"
                    required
                ></v-autocomplete>
            </v-col>
            <v-col cols="12" md="3">
                <v-text-field label="29 - Cód. na Operadora" placeholder="Cód. na Operadora"
                              v-model="provider.provider_operator.provider_operator_number" readonly></v-text-field>
            </v-col>
            <v-col cols="12" md="3">
                <v-text-field label="30 - Nome do Contratado" placeholder="Nome do Contratado" v-model="provider.name"
                              readonly></v-text-field>
            </v-col>
            <v-col cols="12" md="3">
                <v-text-field label="31 - Cód. CNES" placeholder="Cód. CNES" v-model="provider.cnes"
                              readonly></v-text-field>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {
    name: "provider-guide-sadt",
    props: {
        providers: {
            type: Array,
            default: () => ([])
        },
        guide: {
            type: Object
        }
    },
    data() {
        return {
            provider: null
        }
    },
    created() {
        this.verify();
    },
    methods: {
        verify() {
            if (this.guide.id) {
                this.provider = {
                    ...this.guide.provider,
                    provider_operator: this.guide.provider.operators[0].provider_operator
                };
            } else {
                this.provider = this.providers[0];
            }
        }
    }
}
</script>

<style scoped>

</style>
