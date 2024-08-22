import { fetchJsonWithAuth } from '../authProvider';
import { stringify } from 'query-string';

const apiUrl = import.meta.env.VITE_API_URL;

export const storageUserDataProvider = {
    getList: async (resource: any, params: any) => {
        const {page, perPage} = params.pagination;
        const {field, order} = params.sort;

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

        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/storage?${stringify(query)}`);
        const {data, totalCount} = response.data.payload;

        const transformedData = data.map((storage: any) => {
            const { account, ...rest } = storage;
            return {
                ...rest,
                email: account.email,
            };
        });

        return {
            data: transformedData,
            total: totalCount,
        };
    },

    getOne: async (resource: any, params: any) => {
        return fetchJsonWithAuth(`${apiUrl}/v1/admin/storage/${params.id}`)
            .then(response => {
                const transformedData = (data: any) => {
                    const { account, ...rest } = data;
                    return {
                        ...rest,
                        email: account.email,
                    };
                };

                return {
                    data: transformedData(response.data.payload.data),
                };
            });
    },

    getMany: async (resource: any, params: any) => {
        return Promise.reject(new Error(`getMany is not supported for resource: ${resource}`));
    },

    getManyReference: async (resource: any, params: any) => {
        return Promise.reject(new Error(`getManyReference is not supported for resource: ${resource}`));
    },

    update: async (resource: any, params: any) => {
        const updatedData = {
            maxStorage: params.data.maxStorage,
        };

        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/storage/${params.id}`, {
            method: 'PUT',
            data: JSON.stringify(updatedData),
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
        return Promise.reject(new Error(`delete is not supported for resource: ${resource}`));
    },

    deleteMany: async (resource: any, params: any) => {
        return Promise.reject(new Error(`deleteMany is not supported for resource: ${resource}`));
    },
};