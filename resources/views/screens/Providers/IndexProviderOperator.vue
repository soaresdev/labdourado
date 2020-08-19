<template>
    <div>
        <div class="text-center" v-for="operator in data[name]" :key="operator.provider_operator.operator_id">
            <p>{{operator.name}} - {{ operator.provider_operator[meta.column] }}</p>
        </div>
    </div>
</template>

<script>
export default {
    name: "IndexProviderOperator",
    props: {
        data: {},
        name: {},
        meta: {}
    }
}
</script>

<style scoped>

</style>
