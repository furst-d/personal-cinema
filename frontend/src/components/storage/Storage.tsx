import React, { useEffect, useState } from "react";
import { Container, Typography, Box } from "@mui/material";
import StorageMeter from "./StorageMeter";
import { fetchStorageInfo } from "../../service/storageService";
import StoragePriceList from "./StoragePriceList";

const Storage: React.FC = () => {
    const [totalStorage, setTotalStorage] = useState<number>(0);
    const [usedStorage, setUsedStorage] = useState<number>(0);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        fetchStorageInfo()
            .then(data => {
                setTotalStorage(data.totalStorage);
                setUsedStorage(data.usedStorage);
            })
            .catch(error => {
                console.error('Error loading storage info:', error);
            })
            .finally(() => {
                setLoading(false);
            });
    }, []);

    return (
        <Container>
            <Typography variant="h4" gutterBottom>Správa úložiště</Typography>
            <StorageMeter totalStorage={totalStorage} usedStorage={usedStorage} loading={loading} />
            <Box mt={6}>
                <StoragePriceList />
            </Box>
        </Container>
    )
}

export default Storage;
