import {styled} from "styled-components";
import {Box, Typography} from "@mui/material";

export const PriceCardStyle = styled.div`
    background-color: ${p => p.theme.secondary};
    border-radius: 5px;
    padding: 20px;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
`;

export const PriceBadgeStyle = styled.div`
    position: absolute;
    top: -15px;
    left: 50%;
    transform: translateX(-50%);
    background-color: yellow;
    color: black;
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 0.8rem;
    text-transform: uppercase;
`;

export const OriginalPriceStyle = styled(Typography)`
    text-decoration: line-through;
    color: gray;
    font-size: 1rem;
    align-self: flex-end;
`;

export const DiscountedPriceContainerStyle = styled.div`
    display: flex;
    align-items: flex-end;
    justify-content: center;
    margin-bottom: 5px;
`;

export const DiscountedPriceStyle = styled(Typography)`
    color: ${p => p.theme.primary};
    font-size: 1.6rem;
    font-weight: bold;
`;

export const NormalPriceStyle = styled(Typography)`
    font-size: 1.6rem;
    font-weight: bold;
    margin-bottom: 5px;
`;

export const PriceDiscountBadgeStyle = styled.div`
    position: absolute;
    top: -5px;
    left: -5px;
    background-color: red;
    color: white;
    font-weight: bold;
    padding: 2px 8px;
    border-radius: 5px;
    font-size: 0.8rem;
    transform: rotate(-20deg);
`;

export const PriceSeparatorStyle = styled(Box)`
    width: 100%;
    height: 1px;
    background-color: ${p => p.theme.background};
`;

export const PricePerGBStyle = styled(Typography)`
    font-size: 1.1rem;
    color: lightgray;
    margin-bottom: 10px;
    margin-top: 10px;
`;

export const PriceTypeStyle = styled(Typography)`
    font-size: 0.9rem;
    color: lightgray;
    margin-bottom: 10px;
    margin-top: 10px;
`;