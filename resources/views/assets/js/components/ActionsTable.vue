<template>
  <div class="text-center">
      <v-btn class="ma-2" small :color="classes.color"  @click="click(data)">
      <v-icon left>{{ classes.icon }}</v-icon> {{ name }}
    </v-btn>
  </div>
</template>

<script>
export default {
    name: 'ActionsTable',
    props: {
        data: {},
        name: {},
        classes: {
            type: Object,
            default: () => ({
                'success': true,
                'icon': 'mdi-pencil'
            }),
        },
        click: {
            type: Function,
            default: () => {}
        },
    }
}
</script>

<style>

</style>
