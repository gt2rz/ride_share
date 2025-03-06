import axios from 'axios'

const baseURL = 'http://localhost:80'

const http = (url) => {

    let options = {
        headers: {
            'Content-Type': 'application/json',
        },
        baseURL: url || baseURL,
    }

    if (localStorage.getItem('token')) {
        options.headers.Authorization = `Bearer ${localStorage.getItem('token')}`   
    }

    return axios.create(options)
}

export default http