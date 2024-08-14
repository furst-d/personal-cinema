import React, {useEffect, useRef, useState} from "react";
import { Container, Typography, Box } from "@mui/material";
import StorageMeter from "./StorageMeter";
import {fetchStorageInfo, upgradeStorage} from "../../service/storageService";
import StoragePriceList from "./StoragePriceList";
import {useLocation} from "react-router-dom";
import {toast} from "react-toastify";

const Storage: React.FC = () => {
    const location = useLocation();
    const hasHandledPaymentResult = useRef(false);

    const [totalStorage, setTotalStorage] = useState<number>(0);
    const [usedStorage, setUsedStorage] = useState<number>(0);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        // Ensure that payment result is handled only once
        if (!hasHandledPaymentResult.current) {
            handlePaymentResult();
            hasHandledPaymentResult.current = true;
        }

        handleFetchStorageInfo();
    }, []);

    const handleFetchStorageInfo = () => {
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
    }

    const handlePaymentResult = () => {
        const searchParams = new URLSearchParams(location.search);
        const paymentType = searchParams.get("payment");

        if (paymentType === "success") {
            const sessionId = searchParams.get("session_id");

            if (sessionId) {
                upgradeStorage(sessionId).then(() => {
                    toast.success("Úložiště bylo úspěšně navýšeno.");
                    handleFetchStorageInfo();
                }).catch(error => {
                    if (error.response && error.response.status !== 409) {
                        console.error('Error upgrading storage:', error);
                        toast.error("Nastala chyba při navýšení úložiště.");
                    }
                })
            }

        } else if (paymentType === "failure") {
            toast.error("Platbu nebylo možné dokončit.");
        }
    }

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
