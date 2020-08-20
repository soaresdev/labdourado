import { cloneDeep } from 'lodash';
import * as types from './mutation-types';
import default_state from './state';

export default {
    [types.DRAWER](state, config) {
        state.drawer = !state.drawer;
    },
    [types.SNACKBAR](state, config) {
        state.snackbar = {
          visible: true,
          message: '',
          color: 'success',
          timeout: 3000,
          ...config
        };
      },

      [types.CLEAR_STATE](state) {
        const _state = cloneDeep(default_state);
        Object.keys(state).forEach(key => {
          state[key] = cloneDeep(_state[key]);
        });
      },

      [types.UNSET_ITEMS](state) {
        state.items = [];
      },

      [types.SET_ITEMS](state, items) {
        state.items = items;
      },
}
