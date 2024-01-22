const responseCache = {};

const cacheMiddleware = (duration) => (req, res, next) => {
    const key = '__express__' + req.originalUrl || req.url;
    const cachedBody = responseCache[key];

    if (cachedBody) {
        console.log('Serving from cache');
        res.send(cachedBody);
        return;
    }

    res.sendResponse = res.send;
    res.send = (body) => {
        responseCache[key] = body;
        setTimeout(() => { delete responseCache[key]; }, duration * 3600); // Cache expiration
        res.sendResponse(body);
    };

    next();
};

module.exports = cacheMiddleware;
