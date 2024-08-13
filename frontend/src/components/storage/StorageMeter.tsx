import React from "react";
import { Box, Typography } from "@mui/material";
import { useTheme } from "styled-components";
import { formatBytesToGigabytes } from "../../utils/formatter";
import {MeterBar, MeterContainer, MeterText} from "../../styles/storage/Meter";
import Loading from "../loading/Loading";

interface StorageMeterProps {
    totalStorage: number;
    usedStorage: number;
    loading: boolean;
}

const StorageMeter: React.FC<StorageMeterProps> = ({ totalStorage, usedStorage, loading }) => {
    const theme = useTheme();
    const percentageFree = ((totalStorage - usedStorage) / totalStorage) * 100;

    if (loading) {
        return <Loading />;
    }

    return (
        <Box>
            <MeterContainer theme={theme}>
                <MeterBar $percentage={percentageFree} />
                <MeterText variant="body2">
                    {percentageFree.toFixed(2)}% volného místa
                </MeterText>
            </MeterContainer>
            <Typography variant="body2" sx={{ marginTop: '8px' }}>
                Využité úložiště: {formatBytesToGigabytes(usedStorage)} GB / Celkové úložiště: {formatBytesToGigabytes(totalStorage)} GB
            </Typography>
        </Box>
    );
}

export default StorageMeter;
