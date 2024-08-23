import { fetchJsonWithAuth } from '../authProvider';
import { stringify } from 'query-string';
import {getListQuery} from "../../components/utils/QueryBuilder";

const apiUrl = import.meta.env.VITE_API_URL;

export const storageUpgradeDataProvider = {
    getList: async (resource: any, params: any) => {
        const query = getListQuery(params, true);

        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/storage/upgrade?${stringify(query)}`);
        const {data, totalCount} = response.data.payload;

        const transformedData = data.map((storage: any) => {
            const { account, storageCardPayment, ...rest } = storage;
            return {
                ...rest,
                email: account.email,
                stripePaymentIntent: storage.storageCardPayment?.paymentIntent,
            };
        });

        return {
            data: transformedData,
            total: totalCount,
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
        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/storage/upgrade`, {
            method: 'POST',
            data: JSON.stringify(params.data),
        });

        return ({
            data: {...params.data, id: response.data.payload.id},
        });
    },

    delete: async (resource: any, params: any) => {
        return Promise.reject(new Error(`delete is not supported for resource: ${resource}`));
    },

    deleteMany: async (resource: any, params: any) => {
        const query = {
            filter: JSON.stringify({ids: params.ids}),
        };

        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/storage/upgrade?${stringify(query)}`, {
            method: 'DELETE',
        });

        return ({
            data: response.data.payload.data,
        });
    },

    getPaymentTypes: async () => {
        let response = await fetchJsonWithAuth(`${apiUrl}/v1/admin/storage/upgrade/payment-types`);
        return response.data.payload.data;
    },
};