import store from '../store';

class Auth
{
    login(email, password) {
        return axios.post('/api/login', {
            email,
            password,
        })
            .then((response) => {
                if (response.data.success) {
                    store.commit('login', response.data.user);
                    store.commit('setIsPremium', response.data.premium);
                    return response.data.user;
                } else {
                    return Promise.reject(response.data.reason);
                }
            });
    }

    resetPassword(email) {
        return axios.post('/api/reset-password', {
            email,
        })
            .then((response) => {
                if (response.data.success) {
                    return true;
                } else {
                    return Promise.reject(response.data.reason);
                }
            });
    }
}

export default new Auth();
