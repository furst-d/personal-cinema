import {styled} from "styled-components";
import {Typography} from "@mui/material";

export const MeterContainer = styled.div`
    background-color: ${p => p.theme.secondary};
    border-radius: 10px;
    width: 100%;
    height: 30px;
    overflow: hidden;
    position: relative;
`;

export const MeterBar = styled.div<{ $percentage: number }>`
    height: 100%;
    width: ${({ $percentage }) => `${$percentage}%`};
    background-color: ${({ $percentage }) =>
            $percentage >= 66 ? 'green' :
                    $percentage >= 33 ? 'orange' :
                            'red'};
    transition: width 0.5s ease-in-out;
`;

export const MeterText = styled(Typography)`
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: ${p => p.theme.textLight};
    font-weight: bold;
`;