<template>
<div>
    <div class="text-center" v-for="operator in data['operators']" :key="operator.patient_operator.wallet_number">
    <p>{{ operator.patient_operator[name] }}</p>
</div>
</div>
</template>

<script>
export default {
    name: 'IndexPatientOperator',
    props: {
        data: {},
        name: {}
    },
}
</script>

<style>

</style>
