import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { Helmet, HelmetProvider } from "react-helmet-async";
import Loading from "../loading/Loading";
import NotFoundPage from "./NotFoundPage";
import VideoDetail from "../video/VideoDetail";
import axiosPrivate from "../../api/axiosPrivate";

const VideoDetailPage: React.FC = () => {
    const { hash } = useParams<{ hash: string }>();
    const [video, setVideo] = useState<any | null>(null);
    const [loading, setLoading] = useState<boolean>(true);
    const [notFound, setNotFound] = useState<boolean>(false);

    useEffect(() => {
        axiosPrivate.get(`/v1/personal/videos/${hash}`)
            .then(response => {
                setVideo(response.data.payload.data);
            })
            .catch(() => {
                setNotFound(true);
            })
            .finally(() => {
                setLoading(false);
            });
    }, [hash]);

    if (loading) {
        return <Loading />;
    }

    if (notFound) {
        return <NotFoundPage />;
    }

    return (
        <HelmetProvider>
            <Helmet>
                <title>{video ? video.name + " | SoukromeKino" : "..."}</title>
            </Helmet>
            <VideoDetail video={video} />
        </HelmetProvider>
    );
}

export default VideoDetailPage;
