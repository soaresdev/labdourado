import * as types from './mutation-types';

export const CLEAR_STATE = ({ commit }) => commit(types.CLEAR_STATE);
export const SHOW_SNACKBAR = ({ commit }, config) => commit(types.SNACKBAR, config);
export const HIDE_SNACKBAR = ({ commit }) => commit(types.SNACKBAR, { visible: false });

export const SET_ITEMS = ({ commit }, items) => commit(types.SET_ITEMS, items);
export const UNSET_ITEMS = ({ commit }) => commit(types.UNSET_ITEMS);
