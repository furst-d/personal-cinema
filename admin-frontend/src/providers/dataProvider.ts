import { DataProvider } from 'react-admin';
import { userDataProvider } from './userDataProvider';
import { videoDataProvider } from './videoDataProvider';

const dataProviderMap: Record<string, DataProvider> = {
    users: userDataProvider,
    videos: videoDataProvider,
};

const combinedDataProvider: DataProvider = {
    getList: (resource, params) => {
        const provider = dataProviderMap[resource];
        if (provider && provider.getList) {
            return provider.getList(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },

    getOne: (resource, params) => {
        const provider = dataProviderMap[resource];
        if (provider && provider.getOne) {
            return provider.getOne(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },


    getMany: (resource, params) => {
        const provider = dataProviderMap[resource];
        if (provider && provider.getMany) {
            return provider.getMany(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },

    getManyReference: (resource, params) => {
        const provider = dataProviderMap[resource];
        if (provider && provider.getManyReference) {
            return provider.getManyReference(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },

    update: (resource, params) => {
        const provider = dataProviderMap[resource];
        if (provider && provider.update) {
            return provider.update(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },

    updateMany: (resource, params) => {
        const provider = dataProviderMap[resource];
        if (provider && provider.updateMany) {
            return provider.updateMany(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },

    create: (resource, params) => {
        const provider = dataProviderMap[resource];
        if (provider && provider.create) {
            return provider.create(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },

    delete: (resource, params) => {
        const provider = dataProviderMap[resource];
        if (provider && provider.delete) {
            return provider.delete(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },

    deleteMany: (resource, params) => {
        const provider = dataProviderMap[resource];
        if (provider && provider.deleteMany) {
            return provider.deleteMany(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },
};

export default combinedDataProvider;
