import moment from 'moment-timezone'
import createNumberMask from 'text-mask-addons/dist/createNumberMask';

const onlyInteger = createNumberMask({
    prefix: '',
    allowDecimal: false,
    allowNegative: false
});

export default {
    data: () => ({
        isLoading: null,
        onlyInteger
    }),
    methods: {
        toggleLoading() {
            if(!this.isLoading)
                this.isLoading = this.$loading.show();
            else {
                this.isLoading.hide();
                this.isLoading = null;
            }
        },
        format(value, type = 'YYYY-MM-DD', to = 'L') {
            return moment(value, type).format(to)
        },
        convertToString(value) {
            return new Intl.NumberFormat('pt-BR', {style: 'currency', currency: 'BRL'}).format(value);
        },
        convertToPrice(value) {
            return value
                .toFixed(2)
                .replace('.', ',')
                .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
        }
    }
}
