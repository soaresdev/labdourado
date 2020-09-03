<template>
    <v-container fluid>
        <v-row>
            <v-col class="d-flex" cols="12" md="4">
                <v-select
                    v-model="type_treatment"
                    :items="tas"
                    filled
                    label="32 - Tipo de Atendimento *"
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
                    label="33 - Indicação de Acidente *"
                    :item-text="ais.text"
                    :item-value="ais.value"
                    required
                ></v-select>
            </v-col>
            <v-col cols="12" md="4">
                <v-currency-field prefix="R$ " label="65 - Total Geral (R$) *" required
                                  v-model="total"></v-currency-field>
            </v-col>
            <v-col cols="12">
                <v-textarea rows="3" label="58 - Observação/Justificativa" placeholder="Observação/Justificativa"
                            v-model="observation"></v-textarea>
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
            total: '',
            observation: ''
        }
    },
    methods: {
        verify() {
            if (this.guide.id) {
                this.type_treatment = this.guide.type_treatment;
                this.accident_indication = this.guide.accident_indication;
                this.total = this.guide.total;
                this.observation = this.guide.observation;
            }
        }
    }
}
</script>

<style scoped>

</style>
