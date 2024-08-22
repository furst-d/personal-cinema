import { fetchJsonWithAuth } from '../authProvider';
import { stringify } from 'query-string';

const apiUrl = import.meta.env.VITE_API_URL;

export const settingDataProvider = {
    getList: async (resource: any, params: any) => {
        const { page, perPage } = params.pagination;
        const { field, order } = params.sort;

        const query: any = {
            limit: perPage,
            offset: (page - 1) * perPage,
            sort: field,
            order: order
        };

        return fetchJsonWithAuth(`${apiUrl}/v1/admin/settings?${stringify(query)}`)
            .then(response => {
                const { data, totalCount } = response.data.payload;

                return {
                    data: data,
                    total: totalCount,
                };
            });
    },

    getOne: async (resource: any, params: any) => {
        return fetchJsonWithAuth(`${apiUrl}/v1/admin/settings/${params.id}`)
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
        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/settings/${params.id}`, {
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
        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/settings`, {
            method: 'POST',
            data: JSON.stringify(params.data),
        });

        return ({
            data: {...params.data, id: response.data.payload.id},
        });
    },

    delete: async (resource: any, params: any) => {
        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/settings/${params.id}`, {
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

        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/settings?${stringify(query)}`, {
            method: 'DELETE',
        });

        return ({
            data: response.data.payload.data,
        });
    },
};