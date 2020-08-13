import axios from 'axios'

export default {
    methods: {
        /**
         * Return the CSRF token on the page.
         *
         * @returns {string}
         */
        getToken() {
            return document.head.querySelector('meta[name="csrf-token"]').content
        },

        /**
         * Create a base request.
         *
         * @returns {AxiosInstance}
         */
        request() {
            let instance = axios.create()

            instance.defaults.headers.common['X-CSRF-TOKEN'] = this.getToken()
            instance.defaults.baseURL = '/' + Dashboard.path + '/api'

            const requestHandler = request => {
                // Add any request modifiers...
                return request
            }

            const errorHandler = error => {
                // Add any error modifiers...
                if(error.response.data.message) {
                    this.$store.dispatch('SHOW_SNACKBAR', {
                        color: 'error',
                        message: error.response.data.message
                      })
                }
                switch (error.response.status) {
                    case 405:
                        window.location.href = '/' + Dashboard.path + '/home';
                        break;
                    case 401:
                        this.logout()
                        break
                    default:
                        break
                }

                return Promise.reject({...error})
            }

            const successHandler = response => {
                // Add any response modifiers...
                if(response.status !== 202 && response.data.message) {
                        this.$store.dispatch('SHOW_SNACKBAR', {
                            color: 'success',
                            message: response.data.message
                          })
                }
                return response
            }

            instance.interceptors.request.use(request =>
                requestHandler(request)
            )

            instance.interceptors.response.use(
                response => successHandler(response),
                error => errorHandler(error)
            )

            return instance
        },

        /**
         * Log out of the application.
         *
         * @returns void
         */
        logout() {
            let instance = axios.create()

            instance.defaults.headers.common['X-CSRF-TOKEN'] = this.getToken()
            instance.defaults.baseURL = '/'

            instance.post('/logout').then(response => {
                window.location.href = '/'
            })
        }
    },
}
