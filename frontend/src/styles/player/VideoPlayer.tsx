import {styled} from "styled-components";

export const VideoPlayerContainerStyle = styled.div`
    .video-js {
        .vjs-control-bar {
            background-color: ${(props) => props.theme.primaryDarker};
        }

        .vjs-big-play-button {
            background-color: ${(props) => props.theme.primaryDarker};
        }
        
        .vjs-current-time {
            display: none !important;
        }

        .vjs-time-divider {
            display: none !important;
        }

        .vjs-duration {
            display: none !important;
        }

        .vjs-remaining-time {
            display: none !important;
        }

        .vjs-picture-in-picture-control {
            display: none !important;
        }

        @media print, screen and (min-width: 63.9375em), (orientation: landscape) {
            .vjs-current-time {
                display: block !important;
            }

            .vjs-time-divider {
                display: block !important;
            }

            .vjs-duration {
                display: block !important;
            }

            .vjs-remaining-time {
                display: block !important;
            }

            .vjs-picture-in-picture-control {
                display: block !important;
            }
        }
    }
`;