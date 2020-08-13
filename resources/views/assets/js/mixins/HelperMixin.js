import moment from 'moment-timezone'
export default {
    methods: {
        format(value, type = 'YYYY-MM-DD', to = 'L'){
            return moment(value, type).format(to)
        }
    }
}
