<template>
    <div>
        <v-container fluid>
        <v-row>
            <v-col class="d-flex" cols="12" md="4">
                <v-select
                v-model="lot"
                :items="lots"
                filled
                label="Lote *"
                :item-text="lots => lots.number"
                :item-value="lots => lots"
                required
                ></v-select>
              </v-col>
              <v-col class="d-flex" cols="12" md="4" v-if="operators">
                  <v-select
                      v-model="operator"
                      :items="operators"
                      filled
                      label="Operadora *"
                      :item-text="operators => operators.name"
                      :item-value="operators => operators"
                      required
                  ></v-select>
            </v-col>
            <v-col cols="12" md="4" v-if="operator">
                <v-text-field label="Registro ANS" readonly placeholder="Selecione uma operadora" v-model="operator.ans"></v-text-field>
            </v-col>
        </v-row>
        <v-row v-if="operator">
            <header-guide-sadt :operator="operator" ref="header"></header-guide-sadt>
            <v-divider></v-divider>
            <patient-guide-sadt :operator="operator" :patients="operator.patients" ref="patient"></patient-guide-sadt>
            <v-divider></v-divider>
            <doctor-guide-sadt :operator="operator" :doctors="operator.doctors" ref="doctor"></doctor-guide-sadt>
            <v-divider></v-divider>
            <request-data ref="reqdata"></request-data>
            <v-divider></v-divider>
            <provider-guide-sadt :providers="operator.providers" ref="provider"></provider-guide-sadt>
            <v-divider></v-divider>
            <treatment-data ref="treatment"></treatment-data>
            <v-col cols="12" class="text-center">
                <v-btn class="ma-2" color="success"  @click="save">
                    <v-icon left>mdi-plus</v-icon> Salvar
                </v-btn>
            </v-col>
        </v-row>
    </v-container>
    </div>
</template>

<script>
import HeaderGuideSadt from './HeaderGuideSadt'
import PatientGuideSadt from './PatientGuideSadt'
import DoctorGuideSadt from "./DoctorGuideSadt"
import RequestData from "./RequestData"
import ProviderGuideSadt from "./ProviderGuideSadt"
import TreatmentData from "./TreatmentData"
export default {
    name: 'CreateGuideSadt',
    components: {
        HeaderGuideSadt,
        PatientGuideSadt,
        DoctorGuideSadt,
        RequestData,
        ProviderGuideSadt,
        TreatmentData
    },
    data() {
        return {
            lots: [],
            lot: null,
            operator: null
        }
    },
    created() {
        this.getLots();
    },
    computed: {
        operators() {
            if(this.lot) {
                this.operator = this.lot.operators[0];
            }
            return this.lot ? this.lot.operators : null;
        }
    },
    methods: {
        getLots() {
            this.request().get('/lots/select').then(response => {
                this.lots = response.data.data;
            }).catch(err => {
                console.log(err);
            })
        },
        save() {
            let header = {
                provider_number: this.$refs.header.provider_number,
                main_number: this.$refs.header.main_number,
                permission_date: this.$refs.header.permission_date,
                password: this.$refs.header.password,
                password_expiration: this.$refs.header.password_expiration,
                guide_operator_number: this.$refs.header.guide_operator_number
            };
            let patient = {
                patient_id: this.$refs.patient.patient ? this.$refs.patient.patient.id : null,
                rn: this.$refs.patient.rn
            };
            let doctor = {
                doctor_id: this.$refs.doctor.doctor ? this.$refs.doctor.doctor.id : null
            };
            let reqdata = {
                character_treatment: this.$refs.reqdata.character_treatment,
                request_date: this.$refs.reqdata.request_date,
                clinical_indication: this.$refs.reqdata.clinical_indication
            };
            let treatment = {
                type_treatment: this.$refs.treatment.type_treatment,
                accident_indication: this.$refs.treatment.accident_indication,
                total: this.$refs.treatment.total
            }
            this.request().post('guides-sadt/store', {
                lot_id: this.lot.id,
                ...header,
                ...patient,
                ...doctor,
                ...reqdata,
                ...treatment
            }).then(response => {
                console.log(response.data);
            }).catch(err => {
                console.log(err);
            })
        }
    }
}
</script>

<style>

</style>
