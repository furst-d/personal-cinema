import { useRecordContext } from 'react-admin';
import ResolutionField from "./ResolutionField";
import React from "react";
import {FieldProps} from "../../types/field/FieldProps";

const VideoInfoField: React.FC<FieldProps> = ({ label }) => {
    const record = useRecordContext();
    if (!record) return null;

    return (
        <div>
            <div><strong>Formát:</strong> {record.extension}</div>
            <div><strong>Kodek:</strong> {record.codec}</div>
            <div><strong>Rozlišení:</strong> {<ResolutionField source="resolution" label="Rozlišení" sortable={false} />}</div>
        </div>
    );
};

export default VideoInfoField;
