import http from 'k6/http';
import { sleep } from 'k6';

export const options = {
    vus: 300,
    duration: '30s',
};

export default function () {
    http.get('http://51.83.36.122/~s315-cube/articles/categories/51');
    sleep(1);
}
