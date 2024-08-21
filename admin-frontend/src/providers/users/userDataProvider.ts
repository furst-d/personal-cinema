import { fetchJsonWithAuth } from '../authProvider';
import { stringify } from 'query-string';

const apiUrl = import.meta.env.VITE_API_URL;

export const userDataProvider = {
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

        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/users?${stringify(query)}`);
        const {data, totalCount} = response.data.payload;

        return {
            data,
            total: totalCount,
        };
    },

    getOne: async (resource: any, params: any) => {
        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/users/${params.id}`);
        const userData = response.data.payload.data;
        const roles = userData.roles.map((role: any) => role.key);

        return {
            data: {
                ...userData,
                roles: roles,
            },
        };
    },

    getMany: async (resource: any, params: any) => {
        return Promise.reject(new Error(`getMany is not supported for resource: ${resource}`));
    },

    getManyReference: async (resource: any, params: any) => {
        return Promise.reject(new Error(`getManyReference is not supported for resource: ${resource}`));
    },

    update: async (resource: any, params: any) => {
        const updatedData = {
            active: params.data.isActive,
            roles: params.data.roles,
            email: params.data.email
        };

        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/users/${params.id}`, {
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
        const updatedData = {
            email: params.data.email,
            password: params.data.password,
            active: params.data.isActive,
            roles: params.data.roles,
        };

        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/users`, {
            method: 'POST',
            data: JSON.stringify(updatedData),
        });

        return ({
            data: {...params.data, id: response.data.payload.id},
        });
    },

    delete: async (resource: any, params: any) => {
        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/users/${params.id}`, {
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

        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/users?${stringify(query)}`, {
            method: 'DELETE',
        });

        return ({
            data: response.data.payload.data,
        });
    },

    getRoles: async () => {
        const url = `${apiUrl}/v1/admin/users/roles`;
        let response = await fetchJsonWithAuth(url);
        return response.data.payload.data.map((role: any) => ({
            id: role.keyName,
            name: role.name,
        }));
    },
};