import axiosPrivate from "../api/axiosPrivate";

export const fetchStorageInfo = async (): Promise<{ totalStorage: number; usedStorage: number }> => {
    const response = await axiosPrivate.get(`/v1/personal/storage`);
    return response.data.payload.data;
};

export const fetchStoragePrices = async (): Promise<{ id: number, sizeInGB: number, priceCzk: number, activePercentageDiscount: number, discountedPriceCzk: number }[]> => {
    const response = await axiosPrivate.get(`/v1/personal/storage/upgrade/price`);
    return response.data.payload.data;
}

export const fetchCheckoutSession = async (storagePriceId: number): Promise<{ checkoutSessionId: string }> => {
    const response = await axiosPrivate.get('/v1/personal/storage/upgrade/payment/session', {
        params: {
            storagePriceId: storagePriceId,
        }
    });
    return response.data.payload.data;
}

export const fetchUserUpgrades = async (): Promise<{ sizeInGB: number, priceCzk: number, paymentType: any, createdAt: string }[]> => {
    const response = await axiosPrivate.get('/v1/personal/storage/upgrade');
    return response.data.payload.data;
}

export const upgradeStorage = async (checkoutSessionId: string): Promise<void> => {
    await axiosPrivate.post('/v1/personal/storage/upgrade', {
        checkoutSessionId: checkoutSessionId,
    });
}