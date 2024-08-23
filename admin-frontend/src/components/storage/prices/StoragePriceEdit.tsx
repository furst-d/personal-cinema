import React from 'react';
import {Edit} from 'react-admin';
import {StoragePriceForm} from "./StoragePriceForm";

export const StoragePriceEdit: React.FC = (props) => {
    return (
        <Edit {...props}>
            <StoragePriceForm />
        </Edit>
    );
};
