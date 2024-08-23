import React from 'react';
import {
    Typography,
    Table,
    TableBody,
    TableHead,
    TableRow
} from '@mui/material';
import {formatDate} from "../../utils/formatter";
import {TableCellStyle, TableContainerStyle, TableHeadCellStyle} from "../../styles/table/Table";

interface StorageUpgradeListProps {
    upgrades: { sizeInGB: number, priceCzk: number, paymentTypeInfo: any, createdAt: string }[];
}

const StorageUpgradeList: React.FC<StorageUpgradeListProps> = ({ upgrades }) => {

    if (upgrades.length === 0) {
        return null;
    }

    return (
        <>
            <Typography variant="h5" gutterBottom>
                Předchozí navýšení
            </Typography>
            <TableContainerStyle>
                <Table>
                    <TableHead>
                        <TableRow>
                            <TableHeadCellStyle>Datum</TableHeadCellStyle>
                            <TableHeadCellStyle align="right">Velikost (GB)</TableHeadCellStyle>
                            <TableHeadCellStyle align="right">Cena (Kč)</TableHeadCellStyle>
                            <TableHeadCellStyle align="right">Způsob platby</TableHeadCellStyle>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {upgrades.map((upgrade, index) => (
                            <TableRow key={index}>
                                <TableCellStyle>{formatDate(upgrade.createdAt)}</TableCellStyle>
                                <TableCellStyle align="right">{upgrade.sizeInGB}</TableCellStyle>
                                <TableCellStyle align="right">{upgrade.priceCzk}</TableCellStyle>
                                <TableCellStyle align="right">{upgrade.paymentTypeInfo.label}</TableCellStyle>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </TableContainerStyle>
        </>
    );
};

export default StorageUpgradeList;
