<template>
    <div>
        <v-container fluid>
            <v-row>
                <v-col class="d-flex" cols="12" md="4">
                    <v-autocomplete
                        v-model="operator"
                        dense
                        :items="operators"
                        filled
                        label="Operadora *"
                        :item-text="operators => operators.name"
                        :item-value="operators => operators"
                        @change="getLots"
                        required
                    ></v-autocomplete>
                </v-col>
                <v-col class="d-flex" cols="12" md="4" v-if="operator">
                    <v-autocomplete
                        v-model="lot"
                        :items="lots"
                        dense
                        filled
                        label="Lote *"
                        :item-text="lots => lots.number"
                        :item-value="lots => lots"
                        required
                    ></v-autocomplete>
                </v-col>
                <v-col cols="12" md="4" v-if="operator">
                    <v-text-field label="Registro ANS" readonly placeholder="Selecione uma operadora"
                                  v-model="operator.ans"></v-text-field>
                </v-col>
            </v-row>
            <v-row v-if="lots.length > 0 && lot">
                <header-guide-sadt :guide="guide" ref="header"></header-guide-sadt>
                <v-divider></v-divider>
                <patient-guide-sadt :guide="guide" :operator="lot.operator" :patients="lot.operator.patients"
                                    ref="patient"></patient-guide-sadt>
                <v-divider></v-divider>
                <doctor-guide-sadt :guide="guide" :operator="lot.operator" :doctors="lot.operator.doctors"
                                   ref="doctor"></doctor-guide-sadt>
                <v-divider></v-divider>
                <request-data :guide="guide" ref="reqdata"></request-data>
                <v-divider></v-divider>
                <provider-guide-sadt :guide="guide" :providers="lot.operator.providers" ref="provider"></provider-guide-sadt>
                <v-divider></v-divider>
                <treatment-data :guide="guide" ref="treatment"></treatment-data>
            </v-row>
            <v-row>
                <v-col cols="12" class="text-center">
                    <v-btn class="ma-2" color="success" @click="save">
                        <v-icon left>mdi-plus</v-icon>
                        Salvar
                    </v-btn>
                </v-col>
            </v-row>
            <v-row v-if="errors">
                <v-col cols="12">
                    <v-alert
                        dense
                        border="left"
                        type="warning"
                        v-for="error in errors"
                        :key="error"
                    >
                        {{ error }}
                    </v-alert>
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
            operators: [],
            lots: [],
            operator: null,
            lot: null,
            guide: null,
            errors: []
        }
    },
    created() {
        this.verify();
    },
    methods: {
        verify() {
            if(!this.$route.params.id) {
                this.getOperators();
            } else {
                this.getGuide();
            }
        },
        getGuide() {
            this.request().get(`/guides-sadt/${this.$route.params.id}`).then(response => {
                this.guide = response.data.data[0];
                this.operators = [this.guide.lot.operator];
                this.operator = this.guide.lot.operator;
                this.getLots();
            }).catch(err => {
                this.guide = null;
                this.operators = [];
                this.operator = null;
                console.log(err);
            })
        },
        getOperators() {
            this.request().get('/operators/select').then(response => {
                this.operators = response.data.data;
                if (this.operators.length > 0) {
                    this.operator = this.operators[0];
                    this.getLots();
                }
            }).catch(err => {
                console.log(err);
                this.operators = [];
            })
        },
        getLots() {
            return this.request().get(`/lots/select/${this.operator.id}`).then(response => {
                this.lots = this.guide ? response.data.data.filter(lt => lt.id === this.guide.lot.id) : response.data.data;
                if (this.lots.length > 0) {
                    if(this.guide) {
                        this.lot = this.lots.find(lt => lt.id === this.guide.lot.id);
                    } else {
                        this.lot = this.lots[this.lots.length - 1];
                    }
                }
            }).catch(err => {
                console.log(err);
                this.lots = [];
            });
        },
        save() {
            this.errors = [];
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
            let provider = {
                provider_id: this.$refs.provider.provider ? this.$refs.provider.provider.id : null
            }
            if(!this.guide) {
                this.request().post('guides-sadt/store', {
                    lot_id: this.lot.id,
                    ...header,
                    ...patient,
                    ...doctor,
                    ...reqdata,
                    ...treatment,
                    ...provider
                }).then(response => {
                    this.$router.go();
                }).catch(err => {
                    console.log(err);
                });
            } else {
                this.request().put(`guides-sadt/${this.guide.id}/update`, {
                    lot_id: this.lot.id,
                    ...header,
                    ...patient,
                    ...doctor,
                    ...reqdata,
                    ...treatment,
                    ...provider
                }).then(response => {
                    this.$router.push({name: 'guides-sadt.index'})
                }).catch(err => {
                    console.log(err);
                });
            }
        }
    }
}
</script>

<style>

</style>
