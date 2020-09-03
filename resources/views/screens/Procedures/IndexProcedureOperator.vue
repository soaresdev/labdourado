<template>
    <p>{{ !!data[name][0] ? convertToString(data[name][0].procedure_operator[meta.column]) : '' }}</p>
</template>

<script>
export default {
    name: "IndexProcedureOperator",
    props: {
        data: {},
        meta: {},
        name: {}
    },
}
</script>

<style scoped>

</style>
