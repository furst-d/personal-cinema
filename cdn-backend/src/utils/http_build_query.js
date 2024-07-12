/**
 * Function to build a query string from an object
 * @param obj
 * @param prefix
 * @returns {string}
 */
module.exports = function http_build_query(obj, prefix){
    const str = [];
    for (const p in obj) {
        if (obj.hasOwnProperty(p)) {
            const k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
            str.push((v !== null && typeof v === "object") ?
                http_build_query(v, k) :
                encodeURIComponent(k) + "=" + encodeURIComponent(v));
        }
    }
    return str.join("&");
};