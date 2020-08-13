<template>
<div>
    <div class="text-center" v-for="operator in data['operators']" :key="operator.doctor_operator.doctor_operator_number">
    <p>{{ operator.doctor_operator[name] }}</p>
</div>
</div>
</template>

<script>
export default {
    name: 'IndexDoctorOperator',
    props: {
        data: {},
        name: {}
    },
}
</script>

<style>

</style>
