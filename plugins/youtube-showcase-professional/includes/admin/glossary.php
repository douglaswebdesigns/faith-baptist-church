<?php
/**
 * Settings Glossary Functions
 *
 * @package YT_SCASE_PRO
 * @version 1.1.0
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
add_action('yt_scase_pro_settings_glossary', 'yt_scase_pro_settings_glossary');
/**
 * Display glossary information
 * @since WPAS 4.0
 *
 * @return html
 */
function yt_scase_pro_settings_glossary() {
	global $title;
?>
<div class="wrap">
<h2><?php echo $title; ?></h2>
<p><?php _e('Displays a video gallery on a page with paged navigation. Videos adjust to the screen size of the device.', 'yt-scase-pro'); ?></p>
<p><?php _e('The below are the definitions of entities, attributes, and terms included in YouTube Showcase Professional.', 'yt-scase-pro'); ?></p>
<div id="glossary" class="accordion-container">
<ul class="outer-border">
<li id="emd_video" class="control-section accordion-section">
<h3 class="accordion-section-title hndle" tabindex="1"><?php _e('Videos', 'yt-scase-pro'); ?></h3>
<div class="accordion-section-content">
<div class="inside">
<table class="form-table"><p class"lead"><?php _e('YouTube video which may be displayed as single video, a collection of videos based on a video channel , custom playlist, or videos based on user defined search terms.', 'yt-scase-pro'); ?></p><tr>
<th><?php _e('Featured', 'yt-scase-pro'); ?></th>
<td><?php _e('Adds the video to featured video list. Featured is filterable in the admin area. Featured does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Video Type', 'yt-scase-pro'); ?></th>
<td><?php _e('Identifies the content that will load in the player. Video Type is a required field. Video Type is filterable in the admin area. Video Type does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Video ID', 'yt-scase-pro'); ?></th>
<td><?php _e('<p>The unique 11 digit alphanumeric video id found on the YouTube video. For example; in https://www.youtube.com/watch?v=uVgWZd7oGOk. uVgWZd7oGOk is the video id.</p> Video ID is a required field. Video ID is filterable in the admin area. Video ID does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Custom Playlist', 'yt-scase-pro'); ?></th>
<td><?php _e('Enter a comma-separated list of video IDs to play. The first video that plays will be the video specified in the video id field and the videos specified in here will play thereafter. Custom Playlist is a required field. Custom Playlist is filterable in the admin area. Custom Playlist does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('PlayList ID', 'yt-scase-pro'); ?></th>
<td><?php _e('Enter a YouTube playlist ID. Make sure the parameter value begins with the letters <code>PL</code>. PlayList ID is a required field. PlayList ID is filterable in the admin area. PlayList ID does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Query Terms', 'yt-scase-pro'); ?></th>
<td><?php _e('Enter the search terms without space. You can use <b>+</b> operator to force the search results to include or <b>-</b> operator to force the search results to omit the term. For example; Movies+2015-2010 Query Terms is a required field. Query Terms is filterable in the admin area. Query Terms does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('User Uploads', 'yt-scase-pro'); ?></th>
<td><?php _e('Enter the name of the YouTube channel retrieve a list of videos uploaded to the channel. User Uploads is a required field. User Uploads is filterable in the admin area. User Uploads does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Video Thumbnail Image', 'yt-scase-pro'); ?></th>
<td><?php _e('Sets the video thumbnail image. Displayed best at 16:9 ratio. For small images 320x180px looks good. Video Thumbnail Image does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Display Order', 'yt-scase-pro'); ?></th>
<td><?php _e('Sets the order of video display in gallery views. Exp. the video with display order of 1 precedes another video with 2 Display Order has a default value of <b>1</b>.', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Duration', 'yt-scase-pro'); ?></th>
<td><?php _e('Displays video duration stat when checked. Duration does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Like Count', 'yt-scase-pro'); ?></th>
<td><?php _e('Displays video like count stat when checked. Like Count does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Favorite Count', 'yt-scase-pro'); ?></th>
<td><?php _e('Displays video_favorite_count when checked. Favorite Count does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Comment Count', 'yt-scase-pro'); ?></th>
<td><?php _e('Displays video comment count when checked. Comment Count does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('View Count', 'yt-scase-pro'); ?></th>
<td><?php _e('Displays video view count when checked. View Count does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('View Count', 'yt-scase-pro'); ?></th>
<td><?php _e('Displays channel view count when checked. View Count does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Comment Count', 'yt-scase-pro'); ?></th>
<td><?php _e('Displays channel comment count when checked. Comment Count does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Subscriber Count', 'yt-scase-pro'); ?></th>
<td><?php _e('Displays channel subscriber count when checked. Subscriber Count does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Video Count', 'yt-scase-pro'); ?></th>
<td><?php _e('Displays channel video count when checked. Video Count does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Published At', 'yt-scase-pro'); ?></th>
<td><?php _e('Displays the date that the video was published at when checked. Published At is filterable in the admin area. Published At does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Channel Published At', 'yt-scase-pro'); ?></th>
<td><?php _e('Displays the date that the channel published at when checked. Channel Published At is filterable in the admin area. Channel Published At does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Autohide', 'yt-scase-pro'); ?></th>
<td><?php _e('<p>Indicates whether the video controls will automatically hide after a video begins playing. <strong>Fade out:</strong> The default behavior. The video progress bar will fade out while the player controls remain visible. <strong>Slide out:</strong> The video progress bar and the player controls will slide out of view a couple of seconds after the "video starts" playing. They will only \'reappear\' if the user moves her mouse over the video player or presses a key on her keyboard. <strong>Visible:</strong> The video progress bar and the video player controls will be visible throughout the video and in fullscreen.</p> Autohide is filterable in the admin area. Autohide has a default value of <b>\'2\'</b>.Autohide is displayed as a dropdown and has predefined values of: 2, 1, 0.', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Autoplay', 'yt-scase-pro'); ?></th>
<td><?php _e('<p>The video will autoplay when checked.</p> Autoplay is filterable in the admin area. Autoplay has a default value of <b>1</b>.', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('CC Load Policy', 'yt-scase-pro'); ?></th>
<td><?php _e('If checked closed captions will be shown by default, even if the user has turned captions off. CC Load Policy is filterable in the admin area. CC Load Policy does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Control Bar Theme', 'yt-scase-pro'); ?></th>
<td><?php _e('<p>Sets a dark or light control bar.</p> Control Bar Theme is filterable in the admin area. Control Bar Theme has a default value of <b>\'dark\'</b>.', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Display Controls', 'yt-scase-pro'); ?></th>
<td><?php _e('Sets whether the video player controls will display. Display Controls is filterable in the admin area. Display Controls does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Disable Keyboard Controls', 'yt-scase-pro'); ?></th>
<td><?php _e('<p>When checked, it disables the player keyboard controls. Keyboard controls are as follows: <strong>Spacebar:</strong> Play / Pause. <strong>Arrow Left:</strong> Jump back 10% in the current video. <strong>Arrow Right:</strong> Jump ahead 10% in the current video. <strong>Arrow Up:</strong> Volume up. <strong>Arrow Down:</strong> Volume Down.</p> Disable Keyboard Controls is filterable in the admin area. Disable Keyboard Controls does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Display Fullscreen', 'yt-scase-pro'); ?></th>
<td><?php _e('<p>When unchecked, the player does not display the fullscreen button.</p> Display Fullscreen is filterable in the admin area. Display Fullscreen has a default value of <b>1</b>.', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Display Annotations', 'yt-scase-pro'); ?></th>
<td><?php _e('<p>Sets if video annotations will be displayed by default or not.</p> Display Annotations is filterable in the admin area. Display Annotations does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Display Related Videos', 'yt-scase-pro'); ?></th>
<td><?php _e('<p>When unchecked, the player does not show related videos when playback of the initial video ends.</p> Display Related Videos is filterable in the admin area. Display Related Videos has a default value of <b>1</b>.', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Interface Language', 'yt-scase-pro'); ?></th>
<td><?php _e('<p>Sets the player\'s interface language.</p> Interface Language is filterable in the admin area. Interface Language has a default value of <b>\'en\'</b>.Interface Language is displayed as a dropdown and has predefined values of: aa, ab, af, am, ar, as, ay, az, ba, be, bg, bh, bi, bn, bo, br, ca, co, cs, cy, da, de, dz, el, en, eo, es, et, eu, fa, fi, fj, fo, fr, fy, ga, gd, gl, gn, gu, ha, he, hi, hr, hu, hy, ia, id, ie, ik, is, it, iu, ja, jw, ka, kk, kl, km, kn, ko, ks, ku, ky, la, ln, lo, lt, lv, mg, mi, mk, ml, mn, mo, mr, ms, mt, my, na, ne, nl, no, oc, om, or, pa, pl, ps, pt, qu, rm, rn, ro, ru, rw, sa, sd, sg, sh, si, sk, sl, sm, sn, so, sq, sr, ss, st, su, sv, sw, ta, te, tg, th, ti, tk, tl, tn, to, tr, ts, tt, tw, ug, uk, ur, uz, vi, vo, wo, xh, yi, yo, za, zh, zu.', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Loop', 'yt-scase-pro'); ?></th>
<td><?php _e('<p>When checked the player plays the initial video again and again.</p> Loop does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Modesbranding', 'yt-scase-pro'); ?></th>
<td><?php _e('<p>When checked, the player does not show a YouTube logo. Note that a small YouTube text label will still display in the upper-right corner of a paused video when the user\'s mouse pointer hovers over the player.</p> Modesbranding is filterable in the admin area. Modesbranding does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Start Playing', 'yt-scase-pro'); ?></th>
<td><?php _e('<p>When set, the player begins playing the video at the given number of seconds from the start of the video.</p> Start Playing does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Stop Playing After', 'yt-scase-pro'); ?></th>
<td><?php _e('<p>Sets the time the player stops playing the video in seconds from the start of the video.</p> Stop Playing After is filterable in the admin area. Stop Playing After has a default value of <b>3600</b>.', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Show info', 'yt-scase-pro'); ?></th>
<td><?php _e('<p>When unchecked, the player will not display information like the video title and uploader before the video starts playing.</p> Show info is filterable in the admin area. Show info has a default value of <b>1</b>.', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Theme', 'yt-scase-pro'); ?></th>
<td><?php _e('<p>Sets the color of the player\'s video progress bar to highlight the amount of the video that the viewer has already seen.</p> Theme is filterable in the admin area. Theme has a default value of <b>\'red\'</b>.', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Plays inline', 'yt-scase-pro'); ?></th>
<td><?php _e('Sets whether videos play inline or fullscreen in an HTML5 player on iOS. When checked, the player plays the video inline for UIWebViews created with the allowsInlineMediaPlayback property set to TRUE otherwise the video is played fullscreen. Plays inline is filterable in the admin area. Plays inline does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Title', 'yt-scase-pro'); ?></th>
<td><?php _e(' Title is a required field. Being a unique identifier, it uniquely distinguishes each instance of Video entity. Title is filterable in the admin area. Title does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Excerpt', 'yt-scase-pro'); ?></th>
<td><?php _e(' Excerpt does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Content', 'yt-scase-pro'); ?></th>
<td><?php _e(' Content does not have a default value. ', 'yt-scase-pro'); ?></td>
</tr><tr>
<th><?php _e('Category', 'yt-scase-pro'); ?></th>

<td><?php _e(' Category supports parent-child relationships like categories', 'yt-scase-pro'); ?>. <?php _e('Category has a default value of:', 'yt-scase-pro'); ?> <?php _e(' all-videos', 'yt-scase-pro'); ?>. <div class="taxdef-block"><p><?php _e('The following are the preset values and value descriptions for <b>Category:</b>', 'yt-scase-pro'); ?></p>
<table class="table tax-table form-table"><tr><td><?php _e('All videos', 'yt-scase-pro'); ?></td>
<td><?php _e('Parent category for all videos', 'yt-scase-pro'); ?></td>
</tr>
</table>
</div></td>
</tr>
<tr>
<th><?php _e('Tag', 'yt-scase-pro'); ?></th>

<td><?php _e(' Tag accepts multiple values like tags', 'yt-scase-pro'); ?>. <?php _e('Tag does not have a default value', 'yt-scase-pro'); ?>.<div class="taxdef-block"><p><?php _e('There are no preset values for <b>Tag:</b>', 'yt-scase-pro'); ?></p></div></td>
</tr>
<tr>
<th><?php _e('Related Videos', 'yt-scase-pro'); ?></th>
<td><?php _e('Allows to display and create connections with Videos', 'yt-scase-pro'); ?>. <?php _e('One instance of Videos can associated with many instances of Videos, and vice versa', 'yt-scase-pro'); ?>.  <?php _e('The relationship can be set up in the edit area of Videos using Related Videos relationship box', 'yt-scase-pro'); ?>. </td>
</tr></table>
</div>
</div>
</li>
</ul>
</div>
</div>
<?php
}
