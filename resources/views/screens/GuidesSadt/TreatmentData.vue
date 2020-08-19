<template>
    <v-container fluid>
        <v-row>
            <v-col class="d-flex" cols="12" md="4">
                <v-select
                    v-model="type_treatment"
                    :items="tas"
                    filled
                    label="32 - Tipo de Atendimento"
                    :item-text="tas.text"
                    :item-value="tas.value"
                    required
                ></v-select>
            </v-col>
            <v-col class="d-flex" cols="12" md="4">
                <v-select
                    v-model="accident_indication"
                    :items="ais"
                    filled
                    label="33 - Indicação de Acidente"
                    :item-text="ais.text"
                    :item-value="ais.value"
                    required
                ></v-select>
            </v-col>
            <v-col cols="12" md="4">
                <v-text-field label="65 - Total Geral (R$)" v-model="total"></v-text-field>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import {tas, ais} from "./selects";

export default {
    name: "treatment-data",
    props: {
        guide: {
            type: Object
        }
    },
    created() {
        this.verify();
    },
    data() {
        return {
            tas,
            ais,
            type_treatment: '05',
            accident_indication: '9',
            total: ''
        }
    },
    methods: {
        verify() {
            if(this.guide) {
                this.type_treatment = this.guide.type_treatment;
                this.accident_indication = this.guide.accident_indication;
                this.total = this.guide.total_guide;
            }
        }
    }
}
</script>

<style scoped>

</style>
