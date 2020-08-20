<template>
    <div>
        <div class="text-center" v-for="operator in data['operators']" :key="operator.patient_operator.operator_id">
            <p>{{ meta.name ? operator.name + ' - ' : '' }}{{ operator.patient_operator[meta.column] }}</p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'IndexPatientOperator',
    props: {
        data: {},
        meta: {}
    },
}
</script>

<style>

</style>
