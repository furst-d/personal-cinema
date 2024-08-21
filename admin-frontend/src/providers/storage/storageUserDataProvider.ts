import { fetchJsonWithAuth } from '../authProvider';
import { stringify } from 'query-string';

const apiUrl = import.meta.env.VITE_API_URL;

export const storageUserDataProvider = {
    getList: async (resource: any, params: any) => {
        const data = [
            { id: 1, name: 'Test A' },
            { id: 2, name: 'Test B' },
            { id: 3, name: 'Test C' },
        ];

        return {
            data: data,
            total: data.length,
        };
    },

    getOne: async (resource: any, params: any) => {
        return Promise.reject(new Error(`getOne is not supported for resource: ${resource}`));
    },

    getMany: async (resource: any, params: any) => {
        return Promise.reject(new Error(`getMany is not supported for resource: ${resource}`));
    },

    getManyReference: async (resource: any, params: any) => {
        return Promise.reject(new Error(`getManyReference is not supported for resource: ${resource}`));
    },

    update: async (resource: any, params: any) => {
        return Promise.reject(new Error(`update is not supported for resource: ${resource}`));
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