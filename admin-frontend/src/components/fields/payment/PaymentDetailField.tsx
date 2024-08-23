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
                <strong>Způsob platby:</strong> {record.paymentTypeInfo?.label || 'N/A'}
            </Typography>
            {record.paymentTypeInfo.name === "CARD" && (
                <Typography variant="body2">
                    <strong>Stripe payment intent:</strong> {record.stripePaymentIntent}
                </Typography>
            )}
        </Box>
    );
};

export default PaymentDetailsField;
