import { styled } from 'styled-components';
import { Box, Typography } from "@mui/material";
import { Theme } from "../../components/providers/ThemeProvider";
import PlayArrowIcon from "@mui/icons-material/PlayArrow";
import { Link } from 'react-router-dom';
import NotAvailableIcon from '@mui/icons-material/ReportProblemOutlined';

export const VideoLink = styled(Link)<{ theme: Theme; disabled?: boolean }>(({ theme, disabled }) => ({
    textDecoration: 'none',
    color: 'inherit',
    pointerEvents: disabled ? 'none' : 'auto',
    cursor: disabled ? 'default' : 'pointer',
    '&:hover .play-icon, &:hover .video-title': {
        color: disabled ? 'inherit' : theme.primary,
    },
    '&:hover .video-overlay': {
        backgroundColor: disabled ? 'rgba(0, 0, 0, 0.3)' : 'rgba(0, 0, 0, 0.5)',
    },
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    marginBottom: '15px',
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

export const DefaultThumbnail = styled(Box)({
    position: 'absolute',
    top: 0,
    left: 0,
    width: '100%',
    height: '100%',
    backgroundColor: '#555',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    color: '#fff',
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
    left: '8px',
    padding: '2px 6px',
    borderRadius: '4px',
});

export const PlayIcon = styled(PlayArrowIcon)({
    fontSize: '3rem',
    color: '#fff',
    transition: 'color 0.3s ease',
});

export const ProcessingText = styled(Typography)({
    position: 'absolute',
    top: '8px',
    right: '8px',
    backgroundColor: 'rgba(0, 0, 0, 0.6)',
    padding: '2px 6px',
    borderRadius: '4px',
});

export const NotAvailableIconStyled = styled(NotAvailableIcon)({
    fontSize: '3rem',
});
