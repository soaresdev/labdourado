<template>
    <p>{{ !!data[name][0] ? data[name][0].doctor_operator[meta.column] : '' }}</p>
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

<style scoped>

</style>
