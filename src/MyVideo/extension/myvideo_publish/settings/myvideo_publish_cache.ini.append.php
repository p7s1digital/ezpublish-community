<?php /* #?ini charset="utf-8"? */
/*
# expire dependent objects
#
# EXAMPLE
# [content_class_identifier]
# ClearRelationAttribute[]
# ClearRelationAttributeReverse[]
# ClearRelationCommon[]
# ClearRelationCommonReverse[]
# ClearChildren[]
# ClearParents[]

[myvideo_video]
ClearRelationAttributeReverse[]=myvideo_videolist

[myvideo_image_teaser]
ClearRelationAttributeReverse[]=myvideo_player_page
ClearRelationAttributeReverse[]=myvideo_videolist

[myvideo_text_teaser]
ClearRelationAttributeReverse[]=myvideo_videolist

[myvideo_socialmedia_comments]
ClearRelationAttributeReverse[]=myvideo_format_page
ClearRelationAttributeReverse[]=myvideo_player_page
ClearRelationAttributeReverse[]=myvideo_sub_homepage

[myvideo_videolist]
ClearRelated[]
ClearRelationAttributeReverse[]=myvideo_player_page
ClearRelationAttributeReverse[]=myvideo_homepage
ClearRelationAttributeReverse[]=myvideo_sub_homepage
ClearRelationAttributeReverse[]=myvideo_sushibar
ClearRelationAttributeReverse[]=myvideo_format_page

[myvideo_sushibar]
ClearRelated[]
ClearRelationAttributeReverse[]=myvideo_player_page
ClearRelationAttributeReverse[]=myvideo_homepage
ClearRelationAttributeReverse[]=myvideo_sub_homepage
ClearRelationAttributeReverse[]=myvideo_tab_module
ClearRelationAttributeReverse[]=myvideo_format_page
ClearRelationAttributeReverse[]=myvideo_contest_page

[myvideo_tab_module]
ClearRelated[]
ClearRelationAttributeReverse[]=myvideo_player_page
ClearRelationAttributeReverse[]=myvideo_sub_homepage
ClearRelationAttributeReverse[]=myvideo_homepage
ClearRelationAttributeReverse[]=myvideo_format_page

[myvideo_menu_flyout]
ClearParents[]=myvideo_menu

[myvideo_menu_item]
ClearParents[]=myvideo_menu

[myvideo_article]
ClearParents[]=myvideo_footer

[myvideo_solr_suggestions]
ClearRelationAttributeReverse[]=myvideo_search

[myvideo_solr_boost]
ClearRelated[]
ClearRelationAttributeReverse[]=myvideo_search
ClearRelationAttributeReverse[]=myvideo_tagpage

*/
