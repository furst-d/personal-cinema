import React from 'react';
import { Box, Typography } from '@mui/material';
import { useRecordContext } from 'react-admin';
import {FieldProps} from "../../types/field/FieldProps";

const FillBarField: React.FC<FieldProps> = ({ source = 0, label }) => {
    const record = useRecordContext();
    const fillSize = record ? parseFloat(record[source]) : 0;

    const getColor = (fillSize: number) => {
        if (fillSize < 33) {
            return '#00ff00';
        } else if (fillSize > 66) {
            return '#ff0000';
        } else {
            return '#ffa500';
        }
    };

    return (
        <Box width="100%" display="flex" alignItems="center">
            <Box width="100%" mr={1} position="relative">
                <Box
                    bgcolor={getColor(fillSize)}
                    width={`${fillSize}%`}
                    height={24}
                    borderRadius={2}
                />
                <Typography
                    variant="body2"
                    style={{ position: 'absolute', top: 3, left: '50%', transform: 'translateX(-50%)', color: 'white' }}
                >
                    {`${fillSize.toFixed(2)}%`}
                </Typography>
            </Box>
        </Box>
    );
};

export default FillBarField;
