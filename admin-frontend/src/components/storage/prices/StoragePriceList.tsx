import * as React from 'react';
import {List, Datagrid, TextField, ListProps} from 'react-admin';
import SizeField from "../../fields/storage/SizeField";
import PriceField from "../../fields/storage/PriceField";
import PercentField from "../../fields/storage/PercentField";
import ExpirationDateField from "../../fields/ExpirationDateField";

export const StoragePriceList: React.FC<ListProps> = (props) => (
    <List {...props}>
        <Datagrid>
            <TextField source="id" label="ID" />
            <SizeField source="size" label="Velikost" />
            <PriceField source="priceCzk" label="Cena" />
            <PercentField source="percentageDiscount" label="Sleva" />
            <ExpirationDateField source="discountExpirationAt" label="Expirace slevy" />
        </Datagrid>
    </List>
);