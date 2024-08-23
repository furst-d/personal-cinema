import React from 'react';
import {Create} from 'react-admin';
import {StoragePriceForm} from "./StoragePriceForm";

export const StoragePriceCreate: React.FC = (props) => {
    return (
        <Create {...props}>
            <StoragePriceForm />
        </Create>
    )
};
