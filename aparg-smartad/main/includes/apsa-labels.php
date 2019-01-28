<?php

defined('ABSPATH') or die('No script kiddies please!');

global $apsa_admin_labels;
$apsa_admin_labels = array();

/* Labels for child */
$apsa_admin_labels['element_plural_name'] = __('Ads', 'apsa-aparg');
$apsa_admin_labels['update_failed_msg'] = __('Update failed.', 'apsa-aparg');
$apsa_admin_labels['unsaved_reload'] = __('The changes you made will be lost if you navigate away from this page.', 'apsa-aparg');
$apsa_admin_labels['plugin_name'] = 'smartad';
$apsa_admin_labels["element_type_image"] = __('Image', 'apsa-aparg');
$apsa_admin_labels["element_type_video"] = __('Video', 'apsa-aparg');
$apsa_admin_labels["element_type_flash"] = __('Flash', 'apsa-aparg');
$apsa_admin_labels["element_type_code"] = __('Code', 'apsa-aparg');
$apsa_admin_labels["element_type_iframe"] = __('Iframe', 'apsa-aparg');
$apsa_admin_labels["element_type_custom"] = __('Custom', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["element_name_image"] = __('Image', 'apsa-aparg');
$apsa_admin_labels["element_name_video"] = __('Video', 'apsa-aparg');
$apsa_admin_labels["element_name_flash"] = __('Flash', 'apsa-aparg');
$apsa_admin_labels["element_name_code"] = __('Code', 'apsa-aparg');
$apsa_admin_labels["element_name_custom"] = __('Custom', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["custom_ad"] = __('Custom Ad', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["element_name_iframe"] = __('Iframe', 'apsa-aparg');
$apsa_admin_labels['broken_link_message'] = __('Broken Link', 'apsa-aparg');
$apsa_admin_labels['max_visits_def'] = __('no restrict', 'apsa-aparg');
$apsa_admin_labels['visits_warning_message'] = __('Limit Expired', 'apsa-aparg');
$apsa_admin_labels["camp_event_count_child"] = __('Clicks', 'apsa-aparg');
$apsa_admin_labels["element_event_count_child"] = __('Clicks', 'apsa-aparg');
$apsa_admin_labels["element_rate"] = __('CTR', 'apsa-aparg');
$apsa_admin_labels["link_to"] = __('Link To', 'apsa-aparg');
$apsa_admin_labels["link_to_desc"] = __('URL to customer’s website.', 'apsa-aparg');
$apsa_admin_labels["max_clicks"] = __('Max Clicks', 'apsa-aparg');
$apsa_admin_labels["max_clicks_desc"] = __('Number of clicks after which the ad will be automatically suspended.', 'apsa-aparg');
$apsa_admin_labels["image_file"] = __('Image file', 'apsa-aparg');
$apsa_admin_labels["image_file_desc"] = __('Path to image.', 'apsa-aparg');
$apsa_admin_labels["choose_img_file"] = __('Choose', 'apsa-aparg');
$apsa_admin_labels["swf_file"] = __('SWF File', 'apsa-aparg');
$apsa_admin_labels["swf_file_desc"] = __('Path to SWF file.', 'apsa-aparg');
$apsa_admin_labels["choose_swf_file"] = __('Choose', 'apsa-aparg');
$apsa_admin_labels["video_url"] = __('Youtube/Vimeo URL', 'apsa-aparg');
$apsa_admin_labels["video_url_desc"] = __('URL of Youtube or Vimeo video.', 'apsa-aparg');
$apsa_admin_labels["source_url"] = __('Source URL', 'apsa-aparg');
$apsa_admin_labels["source_url_desc"] = __('URL of website to load.', 'apsa-aparg');
$apsa_admin_labels["html_code"] = __('HTML code', 'apsa-aparg');
$apsa_admin_labels["html_code_desc"] = __('Custom code.', 'apsa-aparg');
$apsa_admin_labels["type_code"] = __('Enter', 'apsa-aparg');
$apsa_admin_labels["bg_img_type"] = __('Background Image Type', 'apsa-aparg');
$apsa_admin_labels["bg_img_type_desc"] = __('Type of the background image.', 'apsa-aparg');
$apsa_admin_labels["link_color"] = __('Link Color', 'apsa-aparg');
$apsa_admin_labels["link_color_desc"] = __('Choose the link color in HTML Hex format.', 'apsa-aparg');
$apsa_admin_labels["contain"] = __('Contain', 'apsa-aparg');
$apsa_admin_labels["cover"] = __('Cover', 'apsa-aparg');
$apsa_admin_labels["repeat"] = __('Repeat', 'apsa-aparg');
$apsa_admin_labels["valid_url_error"] = __('Enter valid url', 'apsa-aparg');
$apsa_admin_labels["open_link_type"] = __('Link Open Type', 'apsa-aparg');
$apsa_admin_labels["open_link_type_desc"] = __('Choose how to open the ad link.', 'apsa-aparg');
$apsa_admin_labels["open_link_type_blank"] = __('New Tab', 'apsa-aparg');
$apsa_admin_labels["open_link_type_self"] = __('Self', 'apsa-aparg');
$apsa_admin_labels["open_link_type_window"] = __('New Window', 'apsa-aparg');
$apsa_admin_labels["show_link"] = __('Show Link', 'apsa-aparg');
$apsa_admin_labels["show_link_desc"] = __('Choose whether to show or not the ad link.', 'apsa-aparg');
$apsa_admin_labels["stat_event_child"] = __('Clicks', 'apsa-aparg');
$apsa_admin_labels["auto_play_video"] = __('Auto Play Video', 'apsa-aparg');
$apsa_admin_labels["auto_play_video_desc"] = __('Choose whether to automatically play ad video or not.', 'apsa-aparg');
$apsa_admin_labels["choose_custom_ads_desc"]=__('Choose Custom Ads', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["custom_ads"]=__('Custom Ads', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["edit_ad"]=__('Edit Ad', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["add_new_ad"]=__('Add New Ad', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["save"]=__('Save', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["published_on"]=__('Published On', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["add_new"]=__('Add New', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["update"]=__('Update', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["ad_saved"]=__('Ad Saved', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["ad_updated"]=__('Ad Updated', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["ad_page_placeholder"]=__('Enter title here', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["build_your_ad"]=__('Build Your Ad', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["preview"]=__('Preview', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["general_opt"]=__('General Options', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["move_to_trash"]=__('Move To Trash', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["ad_options"]=__('Ad Options', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["ad_template"]=__('Template', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["choose_template"]=__('Choose Template', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels["search_res_for"] = __('Search results for ', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['ad_moved_trash'] = __('ad moved to the Trash. ', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['ads_moved_trash'] = __('ads moved to the Trash. ', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['ad_restored_trash'] = __('ad restored from the Trash. ', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['ads_restored_trash'] = __('ads restored from the Trash. ', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['ad_permanently_deleted'] = __('ad permanently deleted. ', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['ads_permanently_deleted'] = __('ads permanently deleted. ', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['available'] = __('Available', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['trash'] = __('Trash', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['delete_permanently'] = __('Delete Permanently', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['restore'] = __('Restore', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['bulk_action'] = __('Bulk Action', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['apply'] = __('Apply', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['all_dates'] = __('All Dates', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['filter'] = __('Filter', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['title'] = __('Title', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['no_ads_trash'] = __('No ads found in trash.', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['no_ads'] = __('No ads found.', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['undo'] = __('Undo', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['search_ads'] = __('Search Ads', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['items'] = __('Items', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['item'] = __('item', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['add_new_ad'] = __('Add New Ad', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['publish'] = __('Publish', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['immediately'] = __('Immediately', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['edit'] = __('Edit', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['date'] = __('Date', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['published'] = __('Published', 'apsa-aparg');//===========Custom=====
$apsa_admin_labels['no_title'] = __('no title', 'apsa-aparg');//===========Custom=====


/* Labels for framework */
$apsa_admin_labels["camp_elements_count"] = $apsa_admin_labels['element_plural_name'];
$apsa_admin_labels["camp_event_count"] = __('Views', 'apsa-aparg');
$apsa_admin_labels["camp_elements"] = $apsa_admin_labels['element_plural_name'];
$apsa_admin_labels["filter_element_all"] = __('All', 'apsa-aparg');
$apsa_admin_labels["filter_element_active"] = __('Active', 'apsa-aparg');
$apsa_admin_labels["filter_element_suspended"] = __('Suspended', 'apsa-aparg');
$apsa_admin_labels["add_new_element"] = __('Add New', 'apsa-aparg');
$apsa_admin_labels["activate_element"] = __('Activate', 'apsa-aparg');
$apsa_admin_labels["suspend_element"] = __('Suspend', 'apsa-aparg');
$apsa_admin_labels["delete_element"] = __('Delete', 'apsa-aparg');
$apsa_admin_labels["element_event_count"] = __('Views', 'apsa-aparg');
$apsa_admin_labels["detailed_description"] = __('Detailed description.', 'apsa-aparg');
$apsa_admin_labels["deadline"] = __('Deadline', 'apsa-aparg');
$apsa_admin_labels["deadline_desc"] = __('Date after which the ad will be automatically suspended.', 'apsa-aparg');
$apsa_admin_labels["deadline_def"] = __('no deadline', 'apsa-aparg');
$apsa_admin_labels['deadline_warning_message'] = __('Deadline Reached', 'apsa-aparg');
$apsa_admin_labels["schedule"] = __('Schedule', 'apsa-aparg');
$apsa_admin_labels["schedule_desc"] = __('Date from which the ad will automatically start showing.', 'apsa-aparg');
$apsa_admin_labels["schedule_def"] = __('no schedule', 'apsa-aparg');
$apsa_admin_labels['schedule_warning_message'] = __('Not Viewing Yet', 'apsa-aparg');
$apsa_admin_labels["max_views"] = __('Max Views', 'apsa-aparg');
$apsa_admin_labels["max_views_desc"] = __('Number of views after which the ad will be automatically suspended.', 'apsa-aparg');
$apsa_admin_labels["max_views_def"] = __('no restrict', 'apsa-aparg');
$apsa_admin_labels['views_warning_message'] = __('Limit Expired', 'apsa-aparg');
$apsa_admin_labels["include"] = __('Include', 'apsa-aparg');
$apsa_admin_labels["include_element_desc"] = __('Specify posts, pages, tags or categories where ad must be shown.', 'apsa-aparg');
$apsa_admin_labels["apsa_all_pages"]['not_loc'] = 'All Pages'; //not localaized need to be kept in DB
$apsa_admin_labels["apsa_all_pages"]['loc'] = __('All Pages', 'apsa-aparg');
$apsa_admin_labels["apsa_all_posts"]['not_loc'] = 'All Posts'; //not localaized need to be kept in DB
$apsa_admin_labels["apsa_all_posts"]['loc'] = __('All Posts', 'apsa-aparg');
$apsa_admin_labels["tagsinput_post_tag"] = __('Tag', 'apsa-aparg');
$apsa_admin_labels["tagsinput_category"] = __('Category', 'apsa-aparg');
$apsa_admin_labels["tagsinput_page"] = __('Page', 'apsa-aparg');
$apsa_admin_labels["tagsinput_post"] = __('Post', 'apsa-aparg');
$apsa_admin_labels["tagsinput_language"] = __('Language', 'apsa-aparg');
$apsa_admin_labels["tagsinput_device"] = __('Device', 'apsa-aparg');
$apsa_admin_labels["tagsinput_desktop"] = __('Desktop', 'apsa-aparg');
$apsa_admin_labels["tagsinput_tablet"] = __('Tablet', 'apsa-aparg');
$apsa_admin_labels["tagsinput_mobile"] = __('Mobile', 'apsa-aparg');
$apsa_admin_labels["specify_tag"] = __('Tag name', 'apsa-aparg');
$apsa_admin_labels["specify_category"] = __('Category name', 'apsa-aparg');
$apsa_admin_labels["specify_post"] = str_replace('%all_posts%', $apsa_admin_labels["apsa_all_posts"]['loc'], __('Post name or "%all_posts%"', 'apsa-aparg'));
$apsa_admin_labels["specify_page"] = str_replace('%all_pages%', $apsa_admin_labels["apsa_all_pages"]['loc'], __('Page name or "%all_pages%"', 'apsa-aparg'));
$apsa_admin_labels["specify_language"] = str_replace('%language%', $apsa_admin_labels["tagsinput_language"], __('Language name or "%language%"', 'apsa-aparg'));
$apsa_admin_labels["specify_device"] = str_replace('%device%', $apsa_admin_labels["tagsinput_device"], __('Device name or "%device%"', 'apsa-aparg'));
$apsa_admin_labels["yellow"] = __('Yellow', 'apsa-aparg');
$apsa_admin_labels["blue"] = __('Blue', 'apsa-aparg');
$apsa_admin_labels["red"] = __('Red', 'apsa-aparg');
$apsa_admin_labels["green"] = __('Green', 'apsa-aparg');
$apsa_admin_labels["light_blue"] = __('Light Blue', 'apsa-aparg');
$apsa_admin_labels["grey"] = __('Grey', 'apsa-aparg');
$apsa_admin_labels["exclude"] = __('Exclude', 'apsa-aparg');
$apsa_admin_labels["exclude_element_desc"] = __('Specify posts, pages, tags or categories where ad must be hidden.', 'apsa-aparg');
$apsa_admin_labels["Statistics_element"] = __('Statistics', 'apsa-aparg');
$apsa_admin_labels["stat_from_place"] = __('From', 'apsa-aparg');
$apsa_admin_labels["stat_to_place"] = __('To', 'apsa-aparg');
$apsa_admin_labels["stat_show"] = __('Show', 'apsa-aparg');
$apsa_admin_labels["export_stats"] = __('Export stats', 'apsa-aparg');
$apsa_admin_labels["general"] = __('General', 'apsa-aparg');
$apsa_admin_labels["shortcode"] = __('Shortcode', 'apsa-aparg');
$apsa_admin_labels["activate_camp"] = __('Activate', 'apsa-aparg');
$apsa_admin_labels["suspend_camp"] = __('Suspend', 'apsa-aparg');
$apsa_admin_labels["export_campaign_Stats"] = __('Export Campaign Stats', 'apsa-aparg');
$apsa_admin_labels["delete_camp"] = __('Delete', 'apsa-aparg');
$apsa_admin_labels["include_camp_desc"] = __('Specify posts, pages, tags or categories where campaign must be shown.', 'apsa-aparg');
$apsa_admin_labels["exclude_camp_desc"] = __('Specify posts, pages, tags or categories where campaign must be hidden.', 'apsa-aparg');
$apsa_admin_labels["options"] = __('Options', 'apsa-aparg');
$apsa_admin_labels["sticky_position"] = __('Sticky Position', 'apsa-aparg');
$apsa_admin_labels["sticky_position_desc"] = __('Place on the screen where the sticky will be displayed.', 'apsa-aparg');
$apsa_admin_labels["sticky_position_def"] = __('Bottom-Right', 'apsa-aparg');
$apsa_admin_labels["sticky_top_left"] = __('Top-Left', 'apsa-aparg');
$apsa_admin_labels["sticky_top_center"] = __('Top-Center', 'apsa-aparg');
$apsa_admin_labels["sticky_top_right"] = __('Top-Right', 'apsa-aparg');
$apsa_admin_labels["sticky_middle_left"] = __('Middle-Left', 'apsa-aparg');
$apsa_admin_labels["sticky_middle_right"] = __('Middle-Right', 'apsa-aparg');
$apsa_admin_labels["sticky_bottom_left"] = __('Bottom-Left', 'apsa-aparg');
$apsa_admin_labels["sticky_bottom_center"] = __('Bottom-Center', 'apsa-aparg');
$apsa_admin_labels["sticky_bottom_right"] = __('Bottom-Right', 'apsa-aparg');
$apsa_admin_labels["change_interval"] = __('Change Interval', 'apsa-aparg');
$apsa_admin_labels["change_interval_desc"] = __('Interval between changing ads of the current campaign measured in seconds.', 'apsa-aparg');
$apsa_admin_labels["change_interval_def"] = __('each reload', 'apsa-aparg');
$apsa_admin_labels["bg_selector"] = __('Background Selector', 'apsa-aparg');
$apsa_admin_labels["bg_selector_desc"] = __('CSS selector of the element to which background ad will be applied to.', 'apsa-aparg');
$apsa_admin_labels["show_interval"] = __('Show Interval', 'apsa-aparg');
$apsa_admin_labels["show_interval_desc"] = __('Interval by the end of which the next ad will be shown measured in seconds.', 'apsa-aparg');
$apsa_admin_labels["show_interval_def"] = __('each reload', 'apsa-aparg');
$apsa_admin_labels["popup_direction"] = __('Animation Type', 'apsa-aparg');
$apsa_admin_labels["popup_direction_desc"] = __('Animation type of the ad.', 'apsa-aparg');
$apsa_admin_labels["embed_direction"] = __('Animation Type', 'apsa-aparg');
$apsa_admin_labels["embed_direction_desc"] = __('Animation type of the ad.', 'apsa-aparg');
$apsa_admin_labels["sticky_direction"] = __('Animation Type', 'apsa-aparg');
$apsa_admin_labels["sticky_direction_desc"] = __('Animation type of the ad.', 'apsa-aparg');
$apsa_admin_labels["width"] = __('Width', 'apsa-aparg');
$apsa_admin_labels["width_desc"] = __('Width of the ad container element measured in both px and %.', 'apsa-aparg');
$apsa_admin_labels["default"] = __('Default:', 'apsa-aparg');
$apsa_admin_labels["height"] = __('Height', 'apsa-aparg');
$apsa_admin_labels["height_desc"] = __('Height of the ad container element measured in both px and %.', 'apsa-aparg');
$apsa_admin_labels["height_desc_px"] = __('Height of the ad container element measured in px.', 'apsa-aparg');
$apsa_admin_labels["popup_show_delay"] = __('Popup Show Delay', 'apsa-aparg');
$apsa_admin_labels["popup_show_delay_desc"] = __('Delay of showing ad popup after page loaded measured in seconds.', 'apsa-aparg');
$apsa_admin_labels["popup_autoclose"] = __('Popup Autoclose', 'apsa-aparg');
$apsa_admin_labels["popup_autoclose_desc"] = __('Automatically close popup after given period of time measured in seconds.', 'apsa-aparg');
$apsa_admin_labels["popup_autoclose_def"] = __('no autoclose', 'apsa-aparg');
$apsa_admin_labels["sticky_show_delay"] = __('Sticky Show Delay', 'apsa-aparg');
$apsa_admin_labels["sticky_show_delay_desc"] = __('Delay of showing ad sticky after page loaded measured in seconds.', 'apsa-aparg');
$apsa_admin_labels["sticky_autoclose"] = __('Sticky Autoclose', 'apsa-aparg');
$apsa_admin_labels["sticky_autoclose_desc"] = __('Automatically close sticky after given period of time measured in seconds.', 'apsa-aparg');
$apsa_admin_labels["sticky_autoclose_def"] = __('no autoclose', 'apsa-aparg');
$apsa_admin_labels["put_in_frame"] = __('Put In Frame', 'apsa-aparg');
$apsa_admin_labels["put_in_frame_desc"] = __('Choose whether to wrap or not the ad popup into frame.', 'apsa-aparg');
$apsa_admin_labels["close_button_delay"] = __('Close Button Delay', 'apsa-aparg');
$apsa_admin_labels["close_button_delay_desc"] = __('Delay of showing close button after showing popup measured in seconds.', 'apsa-aparg');
$apsa_admin_labels["background_pattern"] = __('Background Pattern', 'apsa-aparg');
$apsa_admin_labels["background_pattern_desc"] = __('Pattern of background ad.', 'apsa-aparg');
$apsa_admin_labels["overlay_pattern"] = __('Overlay Pattern', 'apsa-aparg');
$apsa_admin_labels["overlay_pattern_desc"] = __('Pattern of popup ad overlay.', 'apsa-aparg');
$apsa_admin_labels["statistics_camp"] = __('Statistics', 'apsa-aparg');
$apsa_admin_labels["element_stat_title"] = __('Ad Statistics', 'apsa-aparg');
$apsa_admin_labels["camp_stat_title"] = __('Full Statistics', 'apsa-aparg');
$apsa_admin_labels["stat_event"] = __('Views', 'apsa-aparg');
$apsa_admin_labels["camp_type_bg"] = __('Background', 'apsa-aparg');
$apsa_admin_labels["camp_type_popup"] = __('Popup', 'apsa-aparg');
$apsa_admin_labels["camp_type_embed"] = __('Embed', 'apsa-aparg');
$apsa_admin_labels["camp_type_sticky"] = __('Sticky', 'apsa-aparg');
$apsa_admin_labels["sort_mover_tile"] = __('Drag & Drop', 'apsa-aparg');
$apsa_admin_labels["second"] = __('s', 'apsa-aparg');
$apsa_admin_labels["required_filed_error"] = __('Fill required field in', 'apsa-aparg');
$apsa_admin_labels["positive_int_error"] = __('Enter positive integer', 'apsa-aparg');
$apsa_admin_labels["cant_get_stat_msg"] = __('Failed to get statistics.', 'apsa-aparg');
$apsa_admin_labels["unc_type_of_file_msg"] = __('Incorrect type of file, choose correct type.', 'apsa-aparg');
$apsa_admin_labels["camp_choose_type_msg"] = __('Failed to create, you must choose type for new campaign.', 'apsa-aparg');
$apsa_admin_labels["created_new_camp_msg"] = __('Created new campaign.', 'apsa-aparg');
$apsa_admin_labels["cant_add_camp_msg"] = __('Can not add campaign.', 'apsa-aparg');
$apsa_admin_labels["check_form_req_msg"] = __('Check forms’ requirements.', 'apsa-aparg');
$apsa_admin_labels["camp_saved_msg"] = __('Campaign saved.', 'apsa-aparg');
$apsa_admin_labels["camp_not_saved_msg"] = __('Campaign not saved.', 'apsa-aparg');
$apsa_admin_labels["element_choose_type_msg"] = __('Failed to create, you must choose type for new ad.', 'apsa-aparg');
$apsa_admin_labels["created_new_element_msg"] = __('Created new ad.', 'apsa-aparg');
$apsa_admin_labels["cant_add_element_msg"] = __('Can not create the ad.', 'apsa-aparg');
$apsa_admin_labels["camp_deleted_msg"] = __('Campaign deleted.', 'apsa-aparg');
$apsa_admin_labels["camp_active_msg"] = __('Campaign activated.', 'apsa-aparg');
$apsa_admin_labels["camp_suspend_msg"] = __('Campaign suspended.', 'apsa-aparg');
$apsa_admin_labels["camp_action_err_msg"] = __('Can not perform the action.', 'apsa-aparg');
$apsa_admin_labels["general_err_msg"] = __('Can not perform the action.', 'apsa-aparg');
$apsa_admin_labels["element_del_msg"] = __('Ad deleted.', 'apsa-aparg');
$apsa_admin_labels["element_del_err_msg"] = __('Can not delete the ad.', 'apsa-aparg');
$apsa_admin_labels["element_active_msg"] = __('Ad activated.', 'apsa-aparg');
$apsa_admin_labels["element_suspend_msg"] = __('Ad suspended.', 'apsa-aparg');
$apsa_admin_labels["element_action_err_msg"] = __('Can not perform the action.', 'apsa-aparg');
$apsa_admin_labels["no_camp_stat_msg"] = __('There are no campaigns to get statistics for.', 'apsa-aparg');
$apsa_admin_labels["camps_updated_msg"] = __('Campaigns updated.', 'apsa-aparg');
$apsa_admin_labels["confirm_del_camp"] = __('Do you want to delete the campaign?', 'apsa-aparg');
$apsa_admin_labels["confirm_del_camp_title"] = __('Delete Campaign', 'apsa-aparg');
$apsa_admin_labels["confirm_del_element"] = __('Do you want to delete the ad?', 'apsa-aparg');
$apsa_admin_labels["confirm_del_element_title"] = __('Delete The Ad', 'apsa-aparg');
$apsa_admin_labels["status_active"] = __('Active', 'apsa-aparg');
$apsa_admin_labels["status_suspended"] = __('Suspended', 'apsa-aparg');
$apsa_admin_labels["creation_date"] = __('Date:', 'apsa-aparg');
$apsa_admin_labels["stat_count"] = __('Count', 'apsa-aparg');
$apsa_admin_labels["no_result"] = __('No result', 'apsa-aparg');
$apsa_admin_labels["confirm_yes"] = __('Yes', 'apsa-aparg');
$apsa_admin_labels["confirm_cancel"] = __('Cancel', 'apsa-aparg');
$apsa_admin_labels["update_camp"] = __('Update', 'apsa-aparg');
$apsa_admin_labels["popup_dir_center"] = __('Center', 'apsa-aparg');
$apsa_admin_labels["popup_dir_top"] = __('From top', 'apsa-aparg');
$apsa_admin_labels["popup_dir_right"] = __('From right', 'apsa-aparg');
$apsa_admin_labels["popup_dir_bottom"] = __('From bottom', 'apsa-aparg');
$apsa_admin_labels["popup_dir_left"] = __('From left', 'apsa-aparg');
$apsa_admin_labels["sticky_dir_center"] = __('Center', 'apsa-aparg');
$apsa_admin_labels["sticky_dir_top"] = __('From top', 'apsa-aparg');
$apsa_admin_labels["sticky_dir_right"] = __('From right', 'apsa-aparg');
$apsa_admin_labels["sticky_dir_bottom"] = __('From bottom', 'apsa-aparg');
$apsa_admin_labels["sticky_dir_left"] = __('From left', 'apsa-aparg');
$apsa_admin_labels["camp_name_background"] = __('Background campaign', 'apsa-aparg');
$apsa_admin_labels["camp_name_popup"] = __('Popup campaign', 'apsa-aparg');
$apsa_admin_labels["camp_name_embed"] = __('Embed campaign', 'apsa-aparg');
$apsa_admin_labels["camp_name_sticky"] = __('Sticky campaign', 'apsa-aparg');
$apsa_admin_labels["success_update_msg"] = __('Successfully updated.', 'apsa-aparg');
$apsa_admin_labels["update_browser"] = __('Please update your browser', 'apsa-aparg');
$apsa_admin_labels["popup_color"] = __('Popup Color', 'apsa-aparg');
$apsa_admin_labels["popup_header_color"] = __('Popup Header Color', 'apsa-aparg');
$apsa_admin_labels["popup_color_desc"] = __('Choose the popup frame and the background color in HTML Hex format.', 'apsa-aparg');
$apsa_admin_labels["popup_header_color_desc"] = __('Choose the popup header color in HTML Hex format.', 'apsa-aparg');
$apsa_admin_labels["sticky_color"] = __('Sticky Color', 'apsa-aparg');
$apsa_admin_labels["sticky_header_color"] = __('Sticky Header Color', 'apsa-aparg');
$apsa_admin_labels["sticky_color_desc"] = __('Choose the sticky frame and the background color in HTML Hex format.', 'apsa-aparg');
$apsa_admin_labels["sticky_header_color_desc"] = __('Choose the sticky header color in HTML Hex format.', 'apsa-aparg');
$apsa_admin_labels["alignment"] = __('Alignment', 'apsa-aparg');
$apsa_admin_labels["alignment_left"] = __('Left', 'apsa-aparg');
$apsa_admin_labels["alignment_center"] = __('Center', 'apsa-aparg');
$apsa_admin_labels["alignment_right"] = __('Right', 'apsa-aparg');
$apsa_admin_labels["alignment_none"] = __('None', 'apsa-aparg');
$apsa_admin_labels["anticache_desc"] = __('If enabled ads will be rotating in the campaign even with a cache plugin installed <br> Note: After enabling/disabling Anti-Cache clear cache files', 'apsa-aparg');
$apsa_admin_labels["anticache"] = __('Anti-Cache', 'apsa-aparg');
$apsa_admin_labels["choose_element_type"] = __('Choose Ad Type', 'apsa-aparg');
$apsa_admin_labels["widget_desc"] = __('A widget that displays ads of embed campaign.', 'apsa-aparg');
$apsa_admin_labels["general_settings"] = __('General Settings', 'apsa-aparg');
$apsa_admin_labels["anticache_notice"] = __('We have detected cache plugin and recommend to activate Anti-Cache option from "%gen_settings%".', 'apsa-aparg');
$apsa_admin_labels["update_all"] = __('Update All', 'apsa-aparg');
$apsa_admin_labels["developed_by"] = __('Developed by', 'apsa-aparg');
$apsa_admin_labels["custom_css"] = __('Custom CSS', 'apsa-aparg');
$apsa_admin_labels["choose_campaign_type"] = __('Choose Campaign Type', 'apsa-aparg');
$apsa_admin_labels["add"] = __('Add', 'apsa-aparg');
$apsa_admin_labels["enter_code"] = __('Enter Code', 'apsa-aparg');
$apsa_admin_labels["ok"] = __('Ok', 'apsa-aparg');
$apsa_admin_labels["choose_period"] = __('Choose Period', 'apsa-aparg');
$apsa_admin_labels["export"] = __('Export', 'apsa-aparg');
$apsa_admin_labels["campaigns"] = __('Campaigns', 'apsa-aparg');
$apsa_admin_labels["add_new_campaign"] = __('Add New', 'apsa-aparg');
$apsa_admin_labels["export_full_stats"] = __('Export Full Stats', 'apsa-aparg');
$apsa_admin_labels["filter_campaign_all"] = __('All', 'apsa-aparg');
$apsa_admin_labels["filter_campaign_active"] = __('Active', 'apsa-aparg');
$apsa_admin_labels["filter_campaign_suspended"] = __('Suspended', 'apsa-aparg');
$apsa_admin_labels["add_new_campaign_full"] = __('Add New Campaign', 'apsa-aparg');
$apsa_admin_labels["select_campaign"] = __('Select campaign', 'apsa-aparg');
$apsa_admin_labels["no_campaign_to_select"] = __('There is no campaign to select', 'apsa-aparg');
$apsa_admin_labels["settings"] = __('Settings', 'apsa-aparg');
$apsa_admin_labels["adblock_warning"] = __('AdBlock Warning', 'apsa-aparg');
$apsa_admin_labels["adblock_warning_activate"] = __('Activate', 'apsa-aparg');
$apsa_admin_labels["adblock_warning_desc"] = __('This warning will appear if AdBlock plugin installed in a client browser', 'apsa-aparg');
$apsa_admin_labels["adblock_warning_default"] = __('To get the best experience of using our site please disable ad blocker.', 'apsa-aparg');
$apsa_admin_labels["select_embed_campaign"] = __('Select embed campaign.', 'apsa-aparg');
$apsa_admin_labels["embed_alignment"] = __('Embed alignment', 'apsa-aparg');
$apsa_admin_labels["select_embed_alignment"] = __('Select embed alignment.', 'apsa-aparg');
$apsa_admin_labels["campaigns_page"] = __('Campaigns page', 'apsa-aparg');
$apsa_admin_labels["general_settings_page"] = __('General Settings page', 'apsa-aparg');
$apsa_admin_labels["settings_page"] = __('Settings page', 'apsa-aparg');
$apsa_admin_labels["embed_shortcode_desc"] = __('Paste generated shortcode to a post or a page. For alignement generate new shortcode and paste it.', 'apsa-aparg');
$apsa_admin_labels["plugin_settings"] = __('Settings', 'apsa-aparg');
$apsa_admin_labels["media_title_choose_file"] = __('Choose file', 'apsa-aparg');
$apsa_admin_labels["media_button_choose_file"] = $apsa_admin_labels["add"];
$apsa_admin_labels["auto_placement"] = __('Auto Placement', 'apsa-aparg');
$apsa_admin_labels["auto_placement_desc"] = __('Specify where shortcode must be automatically placed and select alignment.', 'apsa-aparg');
$apsa_admin_labels["embed_placement_before"] = __('Before Content', 'apsa-aparg');
$apsa_admin_labels["embed_placement_after"] = __('After Content', 'apsa-aparg');