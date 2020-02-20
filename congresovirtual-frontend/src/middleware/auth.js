// src/middleware/auth.js
export default function auth({ next, router }) {
    if (localStorage.access_token == undefined) {
        return router.push({ name: 'login' });
    }

    return next();
}
