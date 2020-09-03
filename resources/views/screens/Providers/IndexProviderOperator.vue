<template>
    <p>{{ !!data[name][0] ? data[name][0].provider_operator[meta.column] : '' }}</p>
</template>

<script>
export default {
    name: 'IndexProviderOperator',
    props: {
        data: {},
        name: {},
        meta: {}
    }
}
</script>

<style scoped>

</style>
