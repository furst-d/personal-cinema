import { FilterList, FilterListItem } from 'react-admin';
import React, { useEffect, useState } from "react";
import PaymentIcon from '@mui/icons-material/Payment';
import { Box, Card, CardContent } from "@mui/material";
import { storageUpgradeDataProvider } from "../../../providers/storage/storageUpgradeDataProvider";

const StorageUpgradeSidebarFilter: React.FC = () => {
    const [paymentTypes, setPaymentTypes] = useState([]);

    useEffect(() => {
        storageUpgradeDataProvider.getPaymentTypes()
            .then(types => {
                setPaymentTypes(types);
            })
            .catch(error => {
                console.error('Error fetching payment types:', error);
            });
    }, []);

    return (
        <Box
            display={{ xs: 'none', sm: 'block' }}
            sx={{
                order: -1,
                width: '15em',
                marginRight: '1em',
                marginTop: '.5em',
            }}
        >
            <Card>
                <CardContent>
                    <FilterList label="Platební metoda" icon={<PaymentIcon />}>
                        {paymentTypes.map((type: any) => (
                            <FilterListItem
                                key={type.id}
                                label={type.label}
                                value={{ 'paymentTypeId': type.id }}
                            />
                        ))}
                    </FilterList>
                </CardContent>
            </Card>
        </Box>
    );
};

export default StorageUpgradeSidebarFilter;
