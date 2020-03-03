import axios from 'axios';
import { API_URL } from './data_server';

export default axios.create({
    baseURL: API_URL + '/api',
    headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Access-Control-Allow-Credentials': 'true',
        'Access-Control-Allow-Origin': '*'
    }
});