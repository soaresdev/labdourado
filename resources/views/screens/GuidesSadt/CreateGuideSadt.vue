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
                <patient-guide-sadt :guide="guide" :operator="lot.operator" :patients="lot.operator.patients"
                                    ref="patient"></patient-guide-sadt>
                <doctor-guide-sadt :guide="guide" :operator="lot.operator" :doctors="lot.operator.doctors"
                                   ref="doctor"></doctor-guide-sadt>
                <request-data :guide="guide" ref="reqdata"></request-data>
                <provider-guide-sadt :guide="guide" :providers="lot.operator.providers"
                                     ref="provider"></provider-guide-sadt>
                <treatment-data :guide="guide" ref="treatment"></treatment-data>
                <procedures-guide-sadt :guide="guide" :procedures="lot.operator.procedures"
                                       ref="procedures_selected"></procedures-guide-sadt>
            </v-row>
            <v-row v-if="lots.length > 0 && lot">
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
import ProceduresGuideSadt from "./ProceduresGuideSadt"

export default {
    name: 'CreateGuideSadt',
    components: {
        HeaderGuideSadt,
        PatientGuideSadt,
        DoctorGuideSadt,
        RequestData,
        ProviderGuideSadt,
        TreatmentData,
        ProceduresGuideSadt
    },
    data() {
        return {
            operators: [],
            lots: [],
            operator: null,
            lot: null,
            guide: {
                procedures: []
            },
            errors: []
        }
    },
    created() {
        this.verify();
    },
    methods: {
        verify() {
            if (!this.$route.params.id) {
                this.getOperators();
            } else {
                this.getGuide();
            }
        },
        getGuide() {
            this.toggleLoading();
            this.request().get(`/guides-sadt/${this.$route.params.id}`).then(response => {
                this.guide = response.data.data;
                this.operators = [this.guide.lot.operator];
                this.operator = this.guide.lot.operator;
                this.getLots();
            }).catch(err => {
                console.log(err);
                this.guide = {
                    procedures: []
                };
                this.operators = [];
                this.operator = null;
            }).finally(() => {
                this.toggleLoading();
            });
        },
        getOperators() {
            this.toggleLoading();
            this.request().get('/operators/select').then(response => {
                this.operators = response.data.data;
                if (this.operators.length > 0) {
                    this.operator = this.operators[0];
                    this.getLots();
                }
            }).catch(err => {
                console.log(err);
                this.operator = null;
                this.operators = [];
            }).finally(() => {
                this.toggleLoading();
            });
        },
        getLots() {
            this.toggleLoading();
            return this.request().get(`/lots/select/${this.operator.id}`).then(response => {
                this.lots = this.guide.id ? response.data.data.filter(lt => lt.id === this.guide.lot.id) : response.data.data;
                if (this.lots.length > 0) {
                    if (this.guide.id) {
                        this.lot = this.lots.find(lt => lt.id === this.guide.lot.id);
                    } else {
                        this.lot = this.lots[this.lots.length - 1];
                    }
                }
            }).catch(err => {
                console.log(err);
                this.lots = [];
                this.lot = null;
            }).finally(() => {
                this.toggleLoading();
            })
        },
        save() {
            this.errors = [];
            const header = {
                provider_number: this.$refs.header.$data.provider_number,
                main_number: this.$refs.header.$data.main_number,
                permission_date: this.$refs.header.$data.permission_date,
                password: this.$refs.header.$data.password,
                password_expiration: this.$refs.header.$data.password_expiration,
                guide_operator_number: this.$refs.header.$data.guide_operator_number
            };
            const patient = {
                patient_id: this.$refs.patient.$data.patient ? this.$refs.patient.$data.patient.id : null,
                rn: this.$refs.patient.$data.rn
            };
            const doctor = {
                doctor_id: this.$refs.doctor.$data.doctor ? this.$refs.doctor.$data.doctor.id : null
            };
            const reqdata = {
                character_treatment: this.$refs.reqdata.$data.character_treatment,
                request_date: this.$refs.reqdata.$data.request_date,
                clinical_indication: this.$refs.reqdata.$data.clinical_indication
            };
            const provider = {
                provider_id: this.$refs.provider.$data.provider ? this.$refs.provider.$data.provider.id : null
            };
            const treatment = {
                type_treatment: this.$refs.treatment.$data.type_treatment,
                accident_indication: this.$refs.treatment.$data.accident_indication,
                total: this.$refs.treatment.$data.total,
                observation: this.$refs.treatment.$data.observation
            };
            const data = {
                lot_id: this.lot.id,
                ...header,
                ...patient,
                ...doctor,
                ...reqdata,
                ...provider,
                ...treatment,
                procedures: this.guide.procedures
            };
            if (!this.guide.id) {
                this.toggleLoading();
                this.request().post('guides-sadt/store', data).then(response => {
                    this.$router.go();
                }).catch(err => {
                    console.log(err);
                    if (err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    }
                }).finally(() => {
                    this.toggleLoading();
                });
            } else {
                this.toggleLoading();
                this.request().put(`guides-sadt/${this.guide.id}/update`, data).then(response => {
                    this.$router.push({name: 'guides-sadt.index'})
                }).catch(err => {
                    console.log(err);
                    if (err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    }
                }).finally(() => {
                    this.toggleLoading();
                });
            }
        }
    }
}
</script>

<style>

</style>
