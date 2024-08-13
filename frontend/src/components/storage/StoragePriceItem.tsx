import React from "react";
import {Button, Typography} from "@mui/material";
import {
    DiscountedPriceContainerStyle, DiscountedPriceStyle,
    NormalPriceStyle, OriginalPriceStyle,
    PriceBadgeStyle, PriceCardStyle,
    PriceDiscountBadgeStyle, PricePerGBStyle, PriceSeparatorStyle, PriceTypeStyle
} from "../../styles/storage/Price";
import {fetchCheckoutSession} from "../../service/storageService";
import {loadStripe} from "@stripe/stripe-js";

interface StoragePriceItemProps {
    id: number;
    sizeGB: number;
    priceCzk: number;
    discountedPriceCzk?: number;
    percentageDiscount?: number;
    isBestValue?: boolean;
}

const StoragePriceItem: React.FC<StoragePriceItemProps> = ({
   id,
   sizeGB,
   priceCzk,
   discountedPriceCzk,
   percentageDiscount,
   isBestValue = false,
}) => {
    const pricePerGB = discountedPriceCzk ? discountedPriceCzk / sizeGB : priceCzk / sizeGB;

    const handleProcessPayment = async () => {

        try {
            const stripe = await loadStripe(import.meta.env.VITE_STRIPE_PUBLIC_KEY);

            if (!stripe) {
                console.error("Stripe.js has not loaded yet.");
                return;
            }

            const data = await fetchCheckoutSession(id);
            const sessionId = data.checkoutSessionId;

            const { error } = await stripe.redirectToCheckout({
                sessionId: sessionId,
            });

            if (error) {
                console.error("Error redirecting to checkout:", error);
            }
        } catch (error) {
            console.error("Error loading checkout session:", error);
        }
    };

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
                onClick={handleProcessPayment}
                variant="contained"
                fullWidth
            >
                Zvolit
            </Button>
        </PriceCardStyle>
    );
};

export default StoragePriceItem;