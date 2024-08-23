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
                Informace o účtu
            </Typography>
            {stats && (
                <TableContainerStyle>
                    <Table>
                        <TableBody>
                            <TableRow>
                                <TableHeadCellStyle sx={{ width: '200px' }}>E-mail</TableHeadCellStyle>
                                <TableCellStyle sx={{ textAlign: 'right' }}>{stats.email}</TableCellStyle>
                            </TableRow>
                            <TableRow>
                                <TableHeadCellStyle sx={{ width: '200px' }}>Účet vytvořen</TableHeadCellStyle>
                                <TableCellStyle sx={{ textAlign: 'right' }}>{formatDate(stats.created)}</TableCellStyle>
                            </TableRow>
                            <TableRow>
                                <TableHeadCellStyle sx={{ width: '200px' }}>Využité úložiště</TableHeadCellStyle>
                                <TableCellStyle sx={{ textAlign: 'right' }}>{stats.storageUsedGB.toFixed(2)} GB</TableCellStyle>
                            </TableRow>
                            <TableRow>
                                <TableHeadCellStyle sx={{ width: '200px' }}>Limit úložiště</TableHeadCellStyle>
                                <TableCellStyle sx={{ textAlign: 'right' }}>{stats.storageLimitGB.toFixed(2)} GB</TableCellStyle>
                            </TableRow>
                            <TableRow>
                                <TableHeadCellStyle sx={{ width: '200px' }}>Počet navýšení úložiště</TableHeadCellStyle>
                                <TableCellStyle sx={{ textAlign: 'right' }}>{stats.storageUpgradeCount}</TableCellStyle>
                            </TableRow>
                            <TableRow>
                                <TableHeadCellStyle sx={{ width: '200px' }}>Počet videí</TableHeadCellStyle>
                                <TableCellStyle sx={{ textAlign: 'right' }}>{stats.videosCount}</TableCellStyle>
                            </TableRow>
                            <TableRow>
                                <TableHeadCellStyle sx={{ width: '200px' }}>Počet složek</TableHeadCellStyle>
                                <TableCellStyle sx={{ textAlign: 'right' }}>{stats.foldersCount}</TableCellStyle>
                            </TableRow>
                            <TableRow>
                                <TableHeadCellStyle sx={{ width: '200px' }}>Počet sdílených videí</TableHeadCellStyle>
                                <TableCellStyle sx={{ textAlign: 'right' }}>{stats.sharedVideosCount}</TableCellStyle>
                            </TableRow>
                            <TableRow>
                                <TableHeadCellStyle sx={{ width: '200px' }}>Počet sdílených složek</TableHeadCellStyle>
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
