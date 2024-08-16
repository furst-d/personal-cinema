import React, {useEffect, useState} from "react";
import {Typography, Table, TableBody, TableRow} from "@mui/material";
import {AccountStats, fetchAccountStats} from "../../service/accountService";
import {TableCellStyle, TableContainerStyle, TableHeadCellStyle} from "../../styles/table/Table";
import Loading from "../loading/Loading";
import {formatDate} from "../../utils/formatter";

const ProfileStats: React.FC = () => {
    const [stats, setStats] = useState<AccountStats | null>(null);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        fetchAccountStats()
            .then((data) => {
                setStats(data);
            }).catch((error) => {
            console.error('Error loading storage prices:', error);
        }).finally(() => {
            setLoading(false);
        });
    }, []);

    if (loading) {
        return <Loading />;
    }

    return (
        <>
            <Typography variant="h5" gutterBottom>
                Uživatelské statistiky
            </Typography>
            {stats && (
                <TableContainerStyle>
                    <Table>
                        <TableBody>
                            <TableRow>
                                <TableHeadCellStyle sx={{ width: '30%' }}>Účet vytvořen</TableHeadCellStyle>
                                <TableCellStyle sx={{ textAlign: 'right' }}>{formatDate(stats.created)}</TableCellStyle>
                            </TableRow>
                            <TableRow>
                                <TableHeadCellStyle sx={{ width: '30%' }}>Využité úložiště</TableHeadCellStyle>
                                <TableCellStyle sx={{ textAlign: 'right' }}>{stats.storageUsedGB} GB</TableCellStyle>
                            </TableRow>
                            <TableRow>
                                <TableHeadCellStyle sx={{ width: '30%' }}>Limit úložiště</TableHeadCellStyle>
                                <TableCellStyle sx={{ textAlign: 'right' }}>{stats.storageLimitGB} GB</TableCellStyle>
                            </TableRow>
                            <TableRow>
                                <TableHeadCellStyle sx={{ width: '30%' }}>Počet navýšení úložiště</TableHeadCellStyle>
                                <TableCellStyle sx={{ textAlign: 'right' }}>{stats.storageUpgradeCount}</TableCellStyle>
                            </TableRow>
                            <TableRow>
                                <TableHeadCellStyle sx={{ width: '30%' }}>Počet videí</TableHeadCellStyle>
                                <TableCellStyle sx={{ textAlign: 'right' }}>{stats.videosCount}</TableCellStyle>
                            </TableRow>
                            <TableRow>
                                <TableHeadCellStyle sx={{ width: '30%' }}>Počet složek</TableHeadCellStyle>
                                <TableCellStyle sx={{ textAlign: 'right' }}>{stats.foldersCount}</TableCellStyle>
                            </TableRow>
                            <TableRow>
                                <TableHeadCellStyle sx={{ width: '30%' }}>Počet sdílených videí</TableHeadCellStyle>
                                <TableCellStyle sx={{ textAlign: 'right' }}>{stats.sharedVideosCount}</TableCellStyle>
                            </TableRow>
                            <TableRow>
                                <TableHeadCellStyle sx={{ width: '30%' }}>Počet sdílených složek</TableHeadCellStyle>
                                <TableCellStyle sx={{ textAlign: 'right' }}>{stats.sharedFoldersCount}</TableCellStyle>
                            </TableRow>
                        </TableBody>
                    </Table>
                </TableContainerStyle>
            )}
        </>
    );
}

export default ProfileStats;
