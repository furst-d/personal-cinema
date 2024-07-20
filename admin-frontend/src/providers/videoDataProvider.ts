import { fetchJsonWithAuth } from './authProvider';
import { stringify } from 'query-string';

const apiUrl = import.meta.env.VITE_API_URL;

export const videoDataProvider = {
    getList: (resource, params) => {
        const { page, perPage } = params.pagination;
        const { field, order } = params.sort;
        const query = {
            limit: perPage,
            offset: (page - 1) * perPage,
            sort: field,
            order: order,
        };
        const url = `${apiUrl}/v1/admin/videos?${stringify(query)}`;

        return fetchJsonWithAuth(url).then(response => {
            const { data, totalCount } = response.data.payload;
            return {
                data,
                total: totalCount,
            };
        });
    },
    getOne: (resource, params) => {
        const url = `${apiUrl}/v1/admin/videos/${params.id}`;
        return fetchJsonWithAuth(url).then(response => ({
            data: response.data.payload,
        }));
    },
    getMany: (resource, params) => {
        const query = {
            filter: JSON.stringify({ id: params.ids }),
        };
        const url = `${apiUrl}/v1/admin/videos?${stringify(query)}`;
        return fetchJsonWithAuth(url).then(response => ({
            data: response.data.payload,
        }));
    },
    getManyReference: (resource, params) => {
        const { page, perPage } = params.pagination;
        const { field, order } = params.sort;
        const query = {
            limit: perPage,
            offset: (page - 1) * perPage,
            sort: field,
            order: order,
            filter: JSON.stringify({
                ...params.filter,
                [params.target]: params.id,
            }),
        };
        const url = `${apiUrl}/v1/admin/videos?${stringify(query)}`;

        return fetchJsonWithAuth(url).then(response => ({
            data: response.data.payload,
            total: response.data.payload.length,
        }));
    },
    update: (resource, params) => {
        const url = `${apiUrl}/v1/admin/videos/${params.id}`;
        return fetchJsonWithAuth(url, {
            method: 'PUT',
            data: params.data,
        }).then(response => ({
            data: response.data.payload,
        }));
    },
    updateMany: (resource, params) => {
        const query = {
            filter: JSON.stringify({ id: params.ids }),
        };
        const url = `${apiUrl}/v1/admin/videos?${stringify(query)}`;
        return fetchJsonWithAuth(url, {
            method: 'PUT',
            data: params.data,
        }).then(response => ({
            data: response.data.payload,
        }));
    },
    create: (resource, params) => {
        const url = `${apiUrl}/v1/admin/videos`;
        return fetchJsonWithAuth(url, {
            method: 'POST',
            data: params.data,
        }).then(response => ({
            data: { ...params.data, id: response.data.payload.id },
        }));
    },
    delete: (resource, params) => {
        const url = `${apiUrl}/v1/admin/videos/${params.id}`;
        return fetchJsonWithAuth(url, {
            method: 'DELETE',
        }).then(response => ({
            data: response.data.payload,
        }));
    },
    deleteMany: (resource, params) => {
        const query = {
            filter: JSON.stringify({ id: params.ids }),
        };
        const url = `${apiUrl}/v1/admin/videos?${stringify(query)}`;
        return fetchJsonWithAuth(url, {
            method: 'DELETE',
        }).then(response => ({
            data: response.data.payload,
        }));
    },
};
