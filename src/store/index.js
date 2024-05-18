import { createStore } from 'vuex';
import { actions } from './actions';
import { getters } from './getters';
import { mutations } from './mutations';

export default createStore({
    state: {
        settings: {
            general: {
                firstname: '',
                lastname: '',
                email: ''
            }
        },
        loadingText: 'Save Settings'
    },
    actions,
    getters,
    mutations
});
