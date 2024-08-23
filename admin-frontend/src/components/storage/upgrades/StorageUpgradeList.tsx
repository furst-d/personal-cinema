import * as React from 'react';
import {List, Datagrid, TextField, ListProps, DateField, TextInput} from 'react-admin';
import SizeField from "../../fields/storage/SizeField";
import PriceField from "../../fields/payment/PriceField";
import PaymentDetailsField from "../../fields/payment/PaymentDetailField";
import StorageUpgradeSidebarFilter from "./StorageUpgradeSidebarFilter";

const storageUpgradeFilters = [
    <TextInput label="Email" source="email" alwaysOn />,
    <TextInput label="Stripe payment intent" source="stripePaymentIntent" alwaysOn />
];

export const StorageUpgradeList: React.FC<ListProps> = (props) => (
    <List {...props} filters={storageUpgradeFilters} aside={<StorageUpgradeSidebarFilter />}>
        <Datagrid>
            <TextField source="id" label="ID" />
            <TextField source="email" label="Email" />
            <SizeField source="size" label="Velikost" />
            <PriceField source="priceCzk" label="Cena" />
            <DateField showTime={true} source="createdAt" label="Vytvořeno" />
            <PaymentDetailsField label="Platební údaje" sortable={false} />
        </Datagrid>
    </List>
);