import { DataProvider } from 'react-admin';
import { userDataProvider } from './providers/userDataProvider';
import { videoDataProvider } from './providers/videoDataProvider';

const combinedDataProvider: DataProvider = {
    getList: (resource, params) => {
        if (resource === 'users') {
            return userDataProvider.getList(resource, params);
        }
        if (resource === 'videos') {
            return videoDataProvider.getList(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },

    getOne: (resource, params) => {
        if (resource === 'users') {
            return userDataProvider.getOne(resource, params);
        }
        if (resource === 'videos') {
            return videoDataProvider.getOne(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },

    getMany: (resource, params) => {
        if (resource === 'users') {
            return userDataProvider.getMany(resource, params);
        }
        if (resource === 'videos') {
            return videoDataProvider.getMany(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },

    getManyReference: (resource, params) => {
        if (resource === 'users') {
            return userDataProvider.getManyReference(resource, params);
        }
        if (resource === 'videos') {
            return videoDataProvider.getManyReference(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },

    update: (resource, params) => {
        if (resource === 'users') {
            return userDataProvider.update(resource, params);
        }
        if (resource === 'videos') {
            return videoDataProvider.update(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },

    updateMany: (resource, params) => {
        if (resource === 'users') {
            return userDataProvider.updateMany(resource, params);
        }
        if (resource === 'videos') {
            return videoDataProvider.updateMany(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },

    create: (resource, params) => {
        if (resource === 'users') {
            return userDataProvider.create(resource, params);
        }
        if (resource === 'videos') {
            return videoDataProvider.create(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },

    delete: (resource, params) => {
        if (resource === 'users') {
            return userDataProvider.delete(resource, params);
        }
        if (resource === 'videos') {
            return videoDataProvider.delete(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },

    deleteMany: (resource, params) => {
        if (resource === 'users') {
            return userDataProvider.deleteMany(resource, params);
        }
        if (resource === 'videos') {
            return videoDataProvider.deleteMany(resource, params);
        }
        return Promise.reject(new Error(`Unknown resource: ${resource}`));
    },
};

export default combinedDataProvider;
