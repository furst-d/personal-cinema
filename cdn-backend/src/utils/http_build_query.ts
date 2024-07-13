/**
 * Function to build a query string from an object
 * @param obj - The object to be serialized into a query string
 * @param prefix - The prefix for nested objects
 * @returns {string}
 */
const http_build_query = (obj: Record<string, any>, prefix?: string): string => {
    const str: string[] = [];
    for (const p in obj) {
        if (obj.hasOwnProperty(p)) {
            const k = prefix ? `${prefix}[${p}]` : p;
            const v = obj[p];
            str.push((v !== null && typeof v === "object") ?
                http_build_query(v, k) :
                `${encodeURIComponent(k)}=${encodeURIComponent(v)}`);
        }
    }
    return str.join("&");
};

export default http_build_query;
