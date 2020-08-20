<template>
    <v-container fluid>
        <v-row>
            <v-col class="d-flex" cols="12" md="4">
                <v-select
                    v-model="character_treatment"
                    :items="cas"
                    filled
                    label="21 - Caráter do Atendimento"
                    :item-text="cas.text"
                    :item-value="cas.value"
                    required
                ></v-select>
            </v-col>
            <v-col cols="12" md="4">
                <v-text-field type="date" label="22 - Data da Solicitação" v-model="request_date"></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
                <v-textarea rows="1" label="23 - Indicação Clínica" placeholder="Indicação Clínica"
                            v-model="clinical_indication"></v-textarea>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import {cas} from "./selects"

export default {
    name: "request-data",
    props: {
        guide: {
            tyoe: Object
        }
    },
    created() {
        this.verify();
    },
    data() {
        return {
            cas,
            character_treatment: '1',
            request_date: '',
            clinical_indication: ''
        }
    },
    methods: {
        verify() {
            if (this.guide) {
                this.character_treatment = this.guide.character_treatment;
                this.request_date = this.guide.request_date;
                this.clinical_indication = this.guide.clinical_indication;
            }
        }
    }
}
</script>

<style scoped>

</style>
