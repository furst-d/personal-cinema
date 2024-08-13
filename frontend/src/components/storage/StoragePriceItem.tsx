import React from "react";
import {Button, Typography} from "@mui/material";
import {
    DiscountedPriceContainerStyle, DiscountedPriceStyle,
    NormalPriceStyle, OriginalPriceStyle,
    PriceBadgeStyle, PriceCardStyle,
    PriceDiscountBadgeStyle, PricePerGBStyle, PriceSeparatorStyle, PriceTypeStyle
} from "../../styles/storage/Price";

interface StoragePriceItemProps {
    sizeGB: number;
    priceCzk: number;
    discountedPriceCzk?: number;
    percentageDiscount?: number;
    isBestValue?: boolean;
    onSelect: () => void;
}

const StoragePriceItem: React.FC<StoragePriceItemProps> = ({
   sizeGB,
   priceCzk,
   discountedPriceCzk,
   percentageDiscount,
   isBestValue = false,
   onSelect,
}) => {
    const pricePerGB = discountedPriceCzk ? discountedPriceCzk / sizeGB : priceCzk / sizeGB;

    return (
        <PriceCardStyle>
            {isBestValue && <PriceBadgeStyle>Nejvýhodnější</PriceBadgeStyle>}
            {percentageDiscount !== 0 && (
                <PriceDiscountBadgeStyle>-{percentageDiscount}%</PriceDiscountBadgeStyle>
            )}
            <Typography sx={{ fontWeight: 'bold' }} variant="h6">{sizeGB} GB</Typography>
            {percentageDiscount == 0 ? (
                <NormalPriceStyle variant="h6">{priceCzk} Kč</NormalPriceStyle>
            ) : (
                <DiscountedPriceContainerStyle>
                    <DiscountedPriceStyle variant="h6">
                        {discountedPriceCzk} Kč
                    </DiscountedPriceStyle>
                    <OriginalPriceStyle variant="body2">{priceCzk} Kč</OriginalPriceStyle>
                </DiscountedPriceContainerStyle>
            )}
            <PriceSeparatorStyle />
            <PricePerGBStyle>{pricePerGB.toFixed(2)} Kč / GB</PricePerGBStyle>
            <PriceSeparatorStyle />
            <PriceTypeStyle>Jednorázová platba</PriceTypeStyle>
            <Button
                onClick={onSelect}
                variant="contained"
                fullWidth
            >
                Zvolit
            </Button>
        </PriceCardStyle>
    );
};

export default StoragePriceItem;
