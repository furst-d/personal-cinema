import { fetchUtils, DataProvider } from 'react-admin';
import { stringify } from 'query-string';

const apiUrl = import.meta.env.VITE_API_URL;
const httpClient = fetchUtils.fetchJson;

export const userDataProvider: DataProvider = {
    getList: (resource, params) => {
        const { page, perPage } = params.pagination;
        const { field, order } = params.sort;
        const query = {
            limit: perPage,
            offset: (page - 1) * perPage,
            sort: field,
            order: order,
        };
        const url = `${apiUrl}/v1/admin/users?${stringify(query)}`;

        return httpClient(url).then(({ headers, json }) => ({
            data: json.payload.data,
            total: json.payload.totalCount,
        }));
    },

    getOne: (resource, params) =>
        httpClient(`${apiUrl}/v1/admin/users/${params.id}`).then(({ json }) => ({ data: json })),

    getMany: (resource, params) => {
        const query = {
            filter: JSON.stringify({ id: params.ids }),
        };
        const url = `${apiUrl}/v1/admin/users?${stringify(query)}`;
        return httpClient(url).then(({ json }) => ({ data: json.payload.data }));
    },

    getManyReference: (resource, params) => {
        const { page, perPage } = params.pagination;
        const { field, order } = params.sort;
        const query = {
            sort: JSON.stringify([field, order]),
            range: JSON.stringify([(page - 1) * perPage, page * perPage - 1]),
            filter: JSON.stringify({
                ...params.filter,
                [params.target]: params.id,
            }),
        };
        const url = `${apiUrl}/v1/admin/users?${stringify(query)}`;
        return httpClient(url).then(({ json }) => ({ data: json.payload.data }));
    },

    update: (resource, params) =>
        httpClient(`${apiUrl}/v1/admin/users/${params.id}`, {
            method: 'PUT',
            body: JSON.stringify(params.data),
        }).then(({ json }) => ({ data: json })),

    updateMany: (resource, params) => {
        const query = {
            filter: JSON.stringify({ id: params.ids }),
        };
        return httpClient(`${apiUrl}/v1/admin/users?${stringify(query)}`, {
            method: 'PUT',
            body: JSON.stringify(params.data),
        }).then(({ json }) => ({ data: json.payload.data }));
    },

    create: (resource, params) =>
        httpClient(`${apiUrl}/v1/admin/users`, {
            method: 'POST',
            body: JSON.stringify(params.data),
        }).then(({ json }) => ({
            data: { ...params.data, id: json.id },
        })),

    delete: (resource, params) =>
        httpClient(`${apiUrl}/v1/admin/users/${params.id}`, {
            method: 'DELETE',
        }).then(({ json }) => ({ data: json })),

    deleteMany: (resource, params) => {
        const query = {
            filter: JSON.stringify({ id: params.ids }),
        };
        return httpClient(`${apiUrl}/v1/admin/users?${stringify(query)}`, {
            method: 'DELETE',
        }).then(({ json }) => ({ data: json.payload.data }));
    },
};
