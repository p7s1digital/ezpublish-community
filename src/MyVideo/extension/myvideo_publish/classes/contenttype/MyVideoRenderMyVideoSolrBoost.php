<?php
/**
 * Description of MyVideoRenderMyVideoSearch
 *
 */
class MyVideoRenderMyVideoSolrBoost extends MyVideoRendererBase implements MyVideoRendererInterface
{
    /**
     * render content object
     */
    public function render()
    {
        $data = array();
        $data['type'] = 'solrBoost';
        // $this->copyFieldContent('object_type_movie', $data);
        // $this->copyFieldContent('object_type_channel', $data);
        // $this->copyFieldContent('object_type_user', $data);
        // $this->copyFieldContent('object_type_playlist', $data);
        $this->copyFieldContent('creation_date', $data);
        $this->copyFieldContent('movie_title', $data);
        $this->copyFieldContent('movie_description', $data);
        $this->copyFieldContent('movie_tags', $data);
        // $this->copyFieldContent('movie_playing_time0_to1', $data);
        // $this->copyFieldContent('movie_playing_time1_to3', $data);
        // $this->copyFieldContent('movie_playing_time3_to6', $data);
        // $this->copyFieldContent('movie_playing_time6_to10', $data);
        // $this->copyFieldContent('movie_playing_time10', $data);
        $this->copyFieldContent('movie_total_view_count', $data);
        $this->copyFieldContent('movie_last_day_view_count', $data);
        $this->copyFieldContent('movie_last_week_view_count', $data);
        $this->copyFieldContent('movie_last_month_view_count', $data);
        $this->copyFieldContent('movie_rating', $data);
        $this->copyFieldContent('movie_series_title', $data);
        $this->copyFieldContent('movie_season_title', $data);
        $this->copyFieldContent('movie_episode_title', $data);
        $this->copyFieldContent('movie_clip_title', $data);
        $this->copyFieldContent('channel_user_nickname', $data);
        $this->copyFieldContent('channel_user_title', $data);
        $this->copyFieldContent('channel_user_description', $data);
        $this->copyFieldContent('channel_user_tags', $data);
        $this->copyFieldContent('channel_widget_title', $data);
        $this->copyFieldContent('channel_widget_description', $data);
        $this->copyFieldContent('channel_widget_tags', $data);
        $this->copyFieldContent('playlist_name', $data);
        $this->copyFieldContent('playlist_description', $data);
        $this->copyFieldContent('playlist_tags', $data);
        $this->copyFieldContent('user_nickname', $data);
        $this->copyFieldContent('user_title', $data);
        $this->copyFieldContent('user_description', $data);
        $this->copyFieldContent('user_tags', $data);

        return $data;
    }
}
