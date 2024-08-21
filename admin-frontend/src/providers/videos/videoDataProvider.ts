import { fetchJsonWithAuth } from '../authProvider';
import { stringify } from 'query-string';

const apiUrl = import.meta.env.VITE_API_URL;

export const videoDataProvider = {
    getList: async (resource: any, params: any) => {
        const { page, perPage } = params.pagination;
        const { field, order } = params.sort;

        const filter = {
            ...params.filter,
        };

        const query: any = {
            limit: perPage,
            offset: (page - 1) * perPage,
            sort: field,
            order: order
        };

        if (Object.keys(filter).length > 0) {
            query.filter = JSON.stringify(filter);
        }

        return fetchJsonWithAuth(`${apiUrl}/v1/admin/videos?${stringify(query)}`)
            .then(response => {
                const { data, totalCount } = response.data.payload;

                const transformedData = data.map((video: any) => {
                    const { conversions, md5, account, ...rest } = video;
                    return {
                        ...rest,
                        md5: md5.md5,
                        email: account.email,
                    };
                });

                return {
                    data: transformedData,
                    total: totalCount,
                };
            });
    },

    getOne: async (resource: any, params: any) => {
        return fetchJsonWithAuth(`${apiUrl}/v1/admin/videos/${params.id}`)
            .then(response => ({
                data: response.data.payload.data,
            }));
    },

    getMany: async (resource: any, params: any) => {
        return Promise.reject(new Error(`getMany is not supported for resource: ${resource}`));
    },

    getManyReference: async (resource: any, params: any) => {
        return Promise.reject(new Error(`getManyReference is not supported for resource: ${resource}`));
    },

    update: async (resource: any, params: any) => {
        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/videos/${params.id}`, {
            method: 'PUT',
            data: JSON.stringify(params.data),
        });

        return ({
            data: response.data.payload.data,
        });
    },

    updateMany: async (resource: any, params: any) => {
        return Promise.reject(new Error(`updateMany is not supported for resource: ${resource}`));
    },

    create: async (resource: any, params: any) => {
        return Promise.reject(new Error(`create is not supported for resource: ${resource}`));
    },

    delete: async (resource: any, params: any) => {
        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/videos/${params.id}`, {
            method: 'DELETE',
        });

        return ({
            data: response.data.payload,
        });
    },

    deleteMany: async (resource: any, params: any) => {
        const query = {
            filter: JSON.stringify({ids: params.ids}),
        };

        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/videos?${stringify(query)}`, {
            method: 'DELETE',
        });

        return ({
            data: response.data.payload.data,
        });
    },
};
