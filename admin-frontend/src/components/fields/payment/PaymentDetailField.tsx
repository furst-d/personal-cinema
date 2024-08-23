import React from 'react';
import { useRecordContext } from 'react-admin';
import { Box, Typography } from '@mui/material';
import {FieldProps} from "../../types/field/FieldProps";

const PaymentDetailsField: React.FC<FieldProps> = ({ label }) => {
    const record = useRecordContext();
    if (!record) return null;

    return (
        <Box>
            <Typography variant="body2">
                <strong>Způsob platby:</strong> {record.paymentType?.label || 'N/A'}
            </Typography>
            {record.paymentType.name === "CARD" && (
                <Typography variant="body2">
                    <strong>Stripe session ID:</strong> {record.stripeSessionId}
                </Typography>
            )}
        </Box>
    );
};

export default PaymentDetailsField;
