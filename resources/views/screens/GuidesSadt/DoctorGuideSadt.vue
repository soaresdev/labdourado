<template>
    <v-container fluid>
        <v-row>
            <v-col class="d-flex" cols="12" md="4">
                <v-select
                    v-model="doctor"
                    :items="doctors_select"
                    filled
                    label="Profissional *"
                    :item-text="doctors => doctors.name"
                    :item-value="doctors => doctors"
                    required
                ></v-select>
                <v-btn class="mx-2" fab small @click="open">
                    <v-icon dark>mdi-plus</v-icon>
                </v-btn>
            </v-col>
            <v-col cols="12" md="4" v-if="doctor">
                <v-text-field label="13 - Cód. na Operadora" readonly placeholder="Cod. na Operadora"
                              v-model="doctor.doctor_operator.doctor_operator_number"></v-text-field>
            </v-col>
            <v-col cols="12" md="4" v-if="doctor">
                <v-text-field label="14 - Nome do Contratado" readonly
                              v-model="doctor.name"></v-text-field>
            </v-col>
        </v-row>
        <v-row v-if="doctor">
            <v-col cols="12" md="3">
                <v-text-field label="15 - Nome do Solicitante" readonly
                              v-model="doctor.name"></v-text-field>
            </v-col>
            <v-col class="d-flex" cols="12" md="3">
                <v-select
                    v-model="doctor.cp"
                    :items="cps"
                    filled
                    label="16 - Conselho Profissional"
                    :item-text="cps.text"
                    :item-value="cps.value"
                    readonly
                ></v-select>
            </v-col>
            <v-col cols="12" md="2">
                <v-text-field label="17 - Nº no Conselho" readonly
                              v-model="doctor.advice_number"></v-text-field>
            </v-col>
            <v-col class="d-flex" cols="12" md="2">
                <v-select
                    v-model="doctor.uf"
                    :items="ufs"
                    filled
                    label="18 - UF"
                    :item-text="ufs.text"
                    :item-value="ufs.value"
                    readonly
                ></v-select>
            </v-col>
            <v-col cols="12" md="2">
                <v-text-field label="19 - Cód. CBO" readonly
                              v-model="doctor.cbo"></v-text-field>
            </v-col>
        </v-row>
        <v-dialog
            v-model="dialog"
        >
            <dialog-doctor v-if="dialog" :operators="operators" :doctor="new_doctor" @action="save"></dialog-doctor>
        </v-dialog>
    </v-container>
</template>

<script>
import DialogDoctor from "../Doctors/DialogDoctor"
import {cps, ufs} from "../Doctors/selects"

export default {
    name: "doctor-guide-sadt",
    components: {
        DialogDoctor
    },
    props: {
        operator: {
            type: Object,
            default: () => ({})
        },
        doctors: {
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
        doctors_select() {
            return this.doctors;
        }
    },
    data() {
        return {
            cps,
            ufs,
            dialog: false,
            doctor: null,
            new_doctor: null
        }
    },
    methods: {
        save(data) {
            if (data) {
                this.doctor = data;
                this.doctors.push(this.doctor);
            }
            this.dialog = false;
        },
        open() {
            this.new_doctor = {
                operators: []
            };
            this.dialog = true;
        }
    }
}
</script>

<style scoped>

</style>
