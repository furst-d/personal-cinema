import React from 'react';
import { useRecordContext } from 'react-admin';
import {FieldProps} from "../../types/field/FieldProps";

const PriceField: React.FC<FieldProps> = ({ source = "" }) => {
    const record = useRecordContext();
    if (!record || !record[source] === undefined) return <span>-</span>;

    const price = record[source];

    return <span>{price} Kč</span>;
};

export default PriceField;
