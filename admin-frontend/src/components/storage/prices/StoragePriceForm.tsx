import React from 'react';
import {DateInput, NumberInput, SimpleForm} from 'react-admin';

export const StoragePriceForm: React.FC = (props) => {
    return (
        <SimpleForm>
            <NumberInput source="size" label="Velikost" />
            <NumberInput source="priceCzk" label="Cena [Kč]" />
            <NumberInput source="percentageDiscount" label="Sleva [%]" defaultValue={0} />
            <DateInput source="discountExpirationAt" label="Expirace slevy" />
        </SimpleForm>
    );
};
