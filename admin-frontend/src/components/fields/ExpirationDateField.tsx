import React from 'react';
import { useRecordContext } from 'react-admin';
import { FieldProps } from "../types/field/FieldProps";
import { Typography } from '@mui/material';

const ExpirationDateField: React.FC<FieldProps> = ({ source = "" }) => {
    const record = useRecordContext();
    if (!record || !record[source]) return <span>-</span>;

    const expirationDate = new Date(record[source]);
    const now = new Date();

    const isExpired = expirationDate < now;

    return (
        <Typography
            component="span"
            style={{ color: isExpired ? 'red' : 'green' }}
        >
            {expirationDate.toLocaleDateString()}
        </Typography>
    );
};

export default ExpirationDateField;
