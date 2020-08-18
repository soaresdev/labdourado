<template>
    <v-container fluid>
        <v-row>
            <v-col class="d-flex" cols="12" md="4">
                <v-select
                    v-model="patient"
                    :items="patients_select"
                    filled
                    label="Paciente *"
                    :item-text="patients => patients.name"
                    :item-value="patients => patients"
                    required
                ></v-select>
                <v-btn class="mx-2" fab small @click="open">
                    <v-icon dark>mdi-plus</v-icon>
                </v-btn>
            </v-col>
            <v-col cols="12" md="4" v-if="patient">
                <v-text-field label="8 - Número da Carteira" readonly placeholder="Nº da Carteira"
                              v-model="patient.patient_operator.wallet_number"></v-text-field>
            </v-col>
            <v-col cols="12" md="4" v-if="patient">
                <v-text-field type="date" label="9- Validade da Carteira" readonly
                              v-model="patient.patient_operator.wallet_expiration"></v-text-field>
            </v-col>
        </v-row>
        <v-row v-if="patient">
            <v-col cols="12" md="4">
                <v-text-field label="10 - Nome" readonly v-model="patient.name"></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
                <v-text-field label="11 - Cartão Nacional de Saúde" readonly v-model="patient.cns"></v-text-field>
            </v-col>
            <v-col class="d-flex" cols="12" md="4">
                <v-select
                    v-model="rn"
                    :items="rns"
                    filled
                    label="Atendimento a RN"
                    :item-text="rns.text"
                    :item-value="rns.value"
                    required
                ></v-select>
            </v-col>
        </v-row>
        <v-dialog
            v-model="dialog"
        >
            <dialog-patient v-if="dialog" :operators="operators" :patient="new_patient" @action="save"></dialog-patient>
        </v-dialog>
    </v-container>
</template>

<script>
import {rns} from './selects'
import DialogPatient from "../Patients/DialogPatient"

export default {
    name: 'patient-guide-sadt',
    components: {
        DialogPatient
    },
    props: {
        operator: {
            type: Object,
            default: () => ({})
        },
        patients: {
            type: Array,
            default: () => ([])
        }
    },
    computed: {
        operators() {
            return [
                {
                    text: this.operator.name,
                    value: this.operator.id
                }
            ];
        },
        patients_select() {
            return this.patients;
        }
    },
    data() {
        return {
            dialog: false,
            rns,
            rn: 'N',
            patient: null,
            new_patient: null
        }
    },
    methods: {
        save(data) {
            if(data) {
                this.patient = data;
                this.patients.push(this.patient);
            }
            this.dialog = false;
        },
        open(){
            this.new_patient = {
                operators: []
            };
            this.dialog = true;
        }
    }
}
</script>

<style>

</style>
