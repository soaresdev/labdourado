<template>
    <div>
        <div class="text-center" v-for="operator in data[name]" :key="operator.doctor_operator.operator_id">
            <p>{{ operator.name }} - {{ operator.doctor_operator[meta.column] }}</p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'IndexDoctorOperator',
    props: {
        data: {},
        name: {},
        meta: {}
    },
}
</script>

<style>

</style>
