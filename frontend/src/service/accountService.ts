import axiosPrivate from "../api/axiosPrivate";

export interface AccountStats {
    email: string;
    created: string;
    storageUsedGB: number;
    storageLimitGB: number;
    storageUpgradeCount: number;
    videosCount: number;
    foldersCount: number;
    sharedVideosCount: number,
    sharedFoldersCount: number,
}

export const fetchAccountStats = async (): Promise<AccountStats> => {
    const response = await axiosPrivate.get('/v1/personal/account/stats');
    return response.data.payload.data;
};