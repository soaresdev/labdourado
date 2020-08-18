<template>
    <div>
        <div class="text-center" v-for="operator in data['operators']" :key="operator.provider_operator.provider_operator_number">
            <p>{{ operator.provider_operator[name] }}</p>
        </div>
    </div>
</template>

<script>
export default {
name: "IndexProviderOperator",
    props: {
        data: {},
        name: {}
    },
}
</script>

<style scoped>

</style>
