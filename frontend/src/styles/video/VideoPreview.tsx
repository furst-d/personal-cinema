import { styled } from 'styled-components';
import { Box, Typography } from "@mui/material";
import { Theme } from "../../components/providers/ThemeProvider";
import PlayArrowIcon from "@mui/icons-material/PlayArrow";
import { Link } from 'react-router-dom';

export const VideoLink = styled(Link)<{ theme: Theme }>(({ theme }) => ({
    textDecoration: 'none',
    color: 'inherit',
    '&:hover .play-icon, &:hover .video-title': {
        color: theme.primary,
    },
    '&:hover .video-overlay': {
        backgroundColor: 'rgba(0, 0, 0, 0.5)',
    },
    display: 'flex',
    flexDirection: 'column',
    marginBottom: '15px',
    alignItems: 'center',
    '@media (min-width: 600px)': {
        alignItems: 'flex-start',
    },
}));

export const PreviewContainer = styled(Box)({
    position: 'relative',
    width: '272px',
    height: '153px',
    overflow: 'hidden',
    borderRadius: '8px',
    backgroundColor: '#000',
    marginBottom: '15px',
});

export const Thumbnail = styled('img')({
    position: 'absolute',
    top: 0,
    left: 0,
    width: '100%',
    height: '100%',
    objectFit: 'cover',
});

export const Overlay = styled(Box)({
    position: 'absolute',
    top: 0,
    left: 0,
    width: '100%',
    height: '100%',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    color: '#fff',
    backgroundColor: 'rgba(0, 0, 0, 0.3)',
});

export const Duration = styled(Typography)({
    position: 'absolute',
    bottom: '8px',
    right: '8px',
    backgroundColor: 'rgba(0, 0, 0, 0.6)',
    padding: '2px 6px',
    borderRadius: '4px',
    transition: 'none',
});

export const Size = styled(Typography)({
    position: 'absolute',
    bottom: '8px',
    left: '8px',
    backgroundColor: 'rgba(0, 0, 0, 0.6)',
    padding: '2px 6px',
    borderRadius: '4px',
    transition: 'none',
});

export const HdBadge = styled(Box)({
    position: 'absolute',
    top: '8px',
    right: '8px',
    backgroundColor: 'rgba(0, 0, 0, 0.6)',
    padding: '2px 6px',
    borderRadius: '4px',
});

export const PlayIcon = styled(PlayArrowIcon)({
    fontSize: '3rem',
    color: '#fff',
    transition: 'color 0.3s ease',
});
