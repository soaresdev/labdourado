<template>
    <p>{{ data[name][0].patient_operator.wallet_number }}
        {{ data[name][0].patient_operator.wallet_expiration ? ` - ${data[name][0].patient_operator.wallet_expiration_formatted}` : '' }}</p>
</template>

<script>
export default {
    name: 'IndexPatientOperator',
    props: {
        data: {},
        name: {}
    }
}
</script>

<style>

</style>
