export const getListQuery = (params: any, includeFilter: boolean = false) => {
    const {page, perPage} = params.pagination;
    const {field, order} = params.sort;

    const query: any = {
        limit: perPage,
        offset: (page - 1) * perPage,
        sort: field,
        order: order
    };

    if (includeFilter) {
        const filter = {
            ...params.filter,
        };

        if (Object.keys(filter).length > 0) {
            query.filter = JSON.stringify(filter);
        }
    }

    return query;
}