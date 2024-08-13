import React, { useEffect, useState } from "react";
import {Grid, Typography} from "@mui/material";
import StoragePriceItem from "./StoragePriceItem";
import { fetchStoragePrices } from "../../service/storageService";
import Loading from "../loading/Loading";

const StoragePriceList: React.FC = () => {
    const [prices, setPrices] = useState<{ id: number, sizeInGB: number, priceCzk: number, activePercentageDiscount: number, discountedPriceCzk: number }[]>([]);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        fetchStoragePrices()
            .then(data => {
                setPrices(data);
            })
            .catch(error => {
                console.error('Error loading storage prices:', error);
            })
            .finally(() => {
                setLoading(false);
            });
    }, []);

    if (loading) {
        return <Loading />;
    }

    return (
        <div>
            <Typography variant="h5" gutterBottom>
                Navyšte si své úložiště
            </Typography>
            <Grid container spacing={4}>
                {prices.map((price, index) => (
                    <Grid item xs={12} sm={6} md={4} lg={3} key={price.id}>
                        <StoragePriceItem
                            key={price.id}
                            id={price.id}
                            sizeGB={price.sizeInGB}
                            priceCzk={price.priceCzk}
                            discountedPriceCzk={price.discountedPriceCzk}
                            percentageDiscount={price.activePercentageDiscount}
                            isBestValue={index === prices.length - 1}
                        />
                    </Grid>
                ))}
            </Grid>
        </div>
    );
};

export default StoragePriceList;
