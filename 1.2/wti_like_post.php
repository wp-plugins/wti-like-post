<?php
/*
Plugin Name: WTI Like Post
Plugin URI: http://www.webtechideas.com/wti-like-post-plugin/
Description: WTI Like Post is a plugin for adding like (thumbs up) and unlike (thumbs down) functionality for wordpress posts/pages. On admin end alongwith handful of configuration settings, it will show maximum of 10 most liked posts/pages. If you have already liked a post/page and now you dislike it, then the old voting will be cancelled and vice-versa. It also has the option to reset the settings to default if needed. It comes with a widget to display the most liked posts/pages. It has live updation of like count on the widget if you like or dislike any post/page. It also comes with a language file for en-US(english- United States).
Version: 1.2
Author: webtechideas
Author URI: http://www.webtechideas.com/
License: GPLv2 or later

Copyright 2011  Webtechideas  (email : webtechideas@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
*/

#### INSTALLATION PROCESS ####
/*
1. Download the plugin and extract it
2. Upload the directory '/wti_like_post/' to the '/wp-content/plugins/' directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Click on 'WTI Like Post' link under Settings menu to access the admin section
5. On widgets section, there is a widget called 'Most Liked Posts' available which can be used to show most liked posts
*/

$wti_like_post_db_version = "1.0";

add_action( 'init', 'WtiLoadPluginTextdomain' );

function WtiLoadPluginTextdomain() {
     load_plugin_textdomain( 'wti-like-post', false, 'wti-like-post/lang' );
}

function SetOptionsWtiLikePost() {
	global $wpdb, $wti_like_post_db_version;

     //creating the like post table on activating the plugin
	$wti_like_post_table_name = $wpdb->prefix . "wti_like_post";
	if($wpdb->get_var("show tables like '$wti_like_post_table_name'") != $wti_like_post_table_name) {
		$sql = "CREATE TABLE " . $wti_like_post_table_name . " (
			`id` bigint(11) NOT NULL AUTO_INCREMENT,
			`post_id` int(11) NOT NULL,
			`value` int(2) NOT NULL,
			`date_time` datetime NOT NULL,
			`ip` varchar(20) NOT NULL,
			PRIMARY KEY (`id`)
		)";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		add_option("wti_like_post_db_version", $wti_like_post_db_version);
	}
	
     //adding options for the like post plugin
	add_option('wti_like_post_jquery', '1', '', 'yes');
	add_option('wti_like_post_voting_period', '0', '', 'yes');
	add_option('wti_like_post_voting_style', 'style1', '', 'yes');
	add_option('wti_like_post_alignment', 'left', '', 'yes');
	add_option('wti_like_post_position', 'bottom', '', 'yes');
	add_option('wti_like_post_login_required', '0', '', 'yes');
	add_option('wti_like_post_login_message', 'Please login to vote.', '', 'yes');
	add_option('wti_like_post_thank_message', 'Thanks for your vote.', '', 'yes');
	add_option('wti_like_post_voted_message', 'You have already voted.', '', 'yes');
	add_option('wti_like_post_excluded_posts', '0', '', 'yes');
	add_option('wti_like_post_show_on_pages', '0', '', 'yes');
	add_option('wti_like_post_show_on_widget', '1', '', 'yes');
	add_option('post_category', '', '', 'yes');	
	add_option('wti_like_post_db_version', $wti_like_post_db_version, '', 'yes');	
}

register_activation_hook(__FILE__, 'SetOptionsWtiLikePost');

function UnsetOptionsWtiLikePost() {
	global $wpdb;
     
     //dropping the table on plugin uninstall
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wti_like_post");

     //deleting the added options on plugin uninstall
	delete_option('wti_like_post_jquery');
	delete_option('wti_like_post_voting_period');
	delete_option('wti_like_post_voting_style');
	delete_option('wti_like_post_alignment');
	delete_option('wti_like_post_position');
	delete_option('wti_like_post_login_required');
	delete_option('wti_like_post_login_message');
	delete_option('wti_like_post_thank_message');
	delete_option('wti_like_post_voted_message');
	delete_option('wti_like_post_db_version');
	delete_option('wti_like_post_excluded_posts');
	delete_option('wti_like_post_show_on_pages');
	delete_option('wti_like_post_show_on_widget');
	delete_option('post_category');
}

register_uninstall_hook(__FILE__, 'UnsetOptionsWtiLikePost');

#### ADMIN OPTIONS ####
function WtiLikePostAdminMenu() {
	add_options_page('WTI Like Post', 'WTI Like Post', 'activate_plugins', 'WtiLikePostAdminMenu', 'WtiLikePostAdminContent');
}
add_action('admin_menu', 'WtiLikePostAdminMenu');

function WtiLikePostAdminRegisterSettings() {
     //registering the settings
	register_setting( 'wti_like_post_options', 'wti_like_post_jquery' );
	register_setting( 'wti_like_post_options', 'wti_like_post_voting_period' );
	register_setting( 'wti_like_post_options', 'wti_like_post_voting_style' );
	register_setting( 'wti_like_post_options', 'wti_like_post_alignment' );
	register_setting( 'wti_like_post_options', 'wti_like_post_position' );
	register_setting( 'wti_like_post_options', 'wti_like_post_login_required' );
	register_setting( 'wti_like_post_options', 'wti_like_post_login_message' );
	register_setting( 'wti_like_post_options', 'wti_like_post_thank_message' );
	register_setting( 'wti_like_post_options', 'wti_like_post_voted_message' );
	register_setting( 'wti_like_post_options', 'wti_like_post_excluded_posts' );
	register_setting( 'wti_like_post_options', 'wti_like_post_show_on_pages' );
	register_setting( 'wti_like_post_options', 'wti_like_post_show_on_widget' );
	register_setting( 'wti_like_post_options', 'wti_like_post_db_version' );	
	register_setting( 'wti_like_post_options', 'post_category' );	
}
add_action('admin_init', 'WtiLikePostAdminRegisterSettings');

function WtiLikePostAdminContent() {
     //creating the admin configuration interface
     global $wpdb, $wti_like_post_db_version;
     
?>
<div class="wrap">
	<h2><?php _e('WTI Like Post Options', 'wti-like-post');?></h2>
	<br class="clear" />
	
	<div id="poststuff" class="ui-sortable meta-box-sortables">
		<div id="WtiLikePostOptions" class="postbox">
			<h3><?php _e('Configuration', 'wti-like-post'); ?></h3>
			<div class="inside">
			     <form method="post" action="options.php">
				  <?php settings_fields('wti_like_post_options'); ?>
				  <table class="form-table">
				       <tr valign="top">
					    <th scope="row"><label for="wti_like_post_jquery"><?php _e('jQuery Framework', 'wti-like-post'); ?></label></th>
					    <td>
						 <select name="wti_like_post_jquery" id="wti_like_post_jquery">
						      <option value="1" <?php if(get_option('wti_like_post_jquery') == '1') { echo 'selected'; }?>><?php _e('Enabled', 'wti-like-post') ?></option>
						      <option value="0" <?php if(get_option('wti_like_post_jquery') == '0') { echo 'selected'; }?>><?php _e('Disabled', 'wti-like-post') ?></option>
						 </select>
						 <span class="description"><?php _e('Disable it if you already have the jQuery framework enabled in your theme.', 'wti-like-post'); ?></span>
					    </td>
				       </tr>
				       <tr valign="top">
					    <th scope="row"><label><?php _e('Voting Period', 'wti-like-post'); ?></label></th>
					    <td>
						<?php
						$voting_period = get_option('wti_like_post_voting_period');
						?>
						<select name="wti_like_post_voting_period" id="wti_like_post_voting_period">
							<option value="0">Always can vote</option>
							<option value="once" <?php if("once" == $voting_period) echo "selected='selected'"; ?>>Only once</option>
							<option value="1" <?php if("1" == $voting_period) echo "selected='selected'"; ?>>One day</option>
							<option value="2" <?php if("2" == $voting_period) echo "selected='selected'"; ?>>Two days</option>
							<option value="3" <?php if("3" == $voting_period) echo "selected='selected'"; ?>>Three days</option>
							<option value="7" <?php if("7" == $voting_period) echo "selected='selected'"; ?>>One week</option>
							<option value="14" <?php if("14" == $voting_period) echo "selected='selected'"; ?>>Two weeks</option>
							<option value="21" <?php if("21" == $voting_period) echo "selected='selected'"; ?>>Three weeks</option>
							<option value="1m" <?php if("1m" == $voting_period) echo "selected='selected'"; ?>>One month</option>
							<option value="2m" <?php if("2m" == $voting_period) echo "selected='selected'"; ?>>Two months</option>
							<option value="3m" <?php if("3m" == $voting_period) echo "selected='selected'"; ?>>Three months</option>
							<option value="6m" <?php if("6m" == $voting_period) echo "selected='selected'"; ?>>Six Months</option>
							<option value="1y" <?php if("1y" == $voting_period) echo "selected='selected'"; ?>>One Year</option>
						</select>
						 <span class="description"><?php _e('Select the voting period after which user can vote again.');?></span>
					    </td>
				       </tr>
				       <tr valign="top">
					    <th scope="row"><label><?php _e('Voting Style', 'wti-like-post'); ?></label></th>
					    <td>
						<?php
						$voting_style = get_option('wti_like_post_voting_style');
						?>
						<select name="wti_like_post_voting_style" id="wti_like_post_voting_style">
							<option value="style1" <?php if("style1" == $voting_style) echo "selected='selected'"; ?>>Style1</option>
							<option value="style2" <?php if("style2" == $voting_style) echo "selected='selected'"; ?>>Style2</option>
							<option value="style3" <?php if("style3" == $voting_style) echo "selected='selected'"; ?>>Style3</option>
						</select>
						 <span class="description"><?php _e('Select the voting style from 3 available options with 3 different sets of images.');?></span>
					    </td>
				       </tr>			
				       <tr valign="top">
					    <th scope="row"><label><?php _e('Login required to vote', 'wti-like-post'); ?></label></th>
					    <td>	
						 <input type="radio" name="wti_like_post_login_required" id="login_yes" value="1" <?php if(1 == get_option('wti_like_post_login_required')) { echo 'checked'; } ?> /> Yes
						 <input type="radio" name="wti_like_post_login_required" id="login_no" value="0" <?php if((0 == get_option('wti_like_post_login_required')) || ('' == get_option('wti_like_post_login_required'))) { echo 'checked'; } ?> /> No
						 <span class="description"><?php _e('Select whether only logged in users can vote or not.');?></span>
					    </td>
				       </tr>			
				       <tr valign="top">
					    <th scope="row"><label><?php _e('Login required message', 'wti-like-post'); ?></label></th>
					    <td>	
						 <input type="text" size="40" name="wti_like_post_login_message" id="wti_like_post_login_message" value="<?php echo get_option('wti_like_post_login_message'); ?>" />
						 <span class="description"><?php _e('Message to show in case login required and user is not logged in.', 'wti-like-post');?></span>
					    </td>
				       </tr>			
				       <tr valign="top">
					    <th scope="row"><label><?php _e('Thank you message', 'wti-like-post'); ?></label></th>
					    <td>	
						 <input type="text" size="40" name="wti_like_post_thank_message" id="wti_like_post_thank_message" value="<?php _e(get_option('wti_like_post_thank_message')); ?>" />
						 <span class="description"><?php _e('Message to show after successful voting.', 'wti-like-post');?></span>
					    </td>
				       </tr>			
				       <tr valign="top">
					    <th scope="row"><label><?php _e('Already voted message', 'wti-like-post'); ?></label></th>
					    <td>	
						 <input type="text" size="40" name="wti_like_post_voted_message" id="wti_like_post_voted_message" value="<?php _e(get_option('wti_like_post_voted_message')); ?>" />
						 <span class="description"><?php _e('Message to show if user has already voted.', 'wti-like-post');?></span>
					    </td>
				       </tr>
				       <tr valign="top">
					    <th scope="row"><label><?php _e('Show on pages', 'wti-like-post'); ?></label></th>
					    <td>	
						 <input type="radio" name="wti_like_post_show_on_pages" id="show_pages_yes" value="1" <?php if(('1' == get_option('wti_like_post_show_on_pages'))) { echo 'checked'; } ?> /> Yes
						 <input type="radio" name="wti_like_post_show_on_pages" id="show_pages_no" value="0" <?php if('0' == get_option('wti_like_post_show_on_pages') || ('' == get_option('wti_like_post_show_on_pages'))) { echo 'checked'; } ?> /> No
						 <span class="description"><?php _e('Select yes if you want to show the like option on pages as well.', 'wti-like-post')?></span>
					    </td>
				       </tr>	
				       <tr valign="top">
					    <th scope="row"><label><?php _e('Exclude post/page IDs', 'wti-like-post'); ?></label></th>
					    <td>	
						 <input type="text" size="40" name="wti_like_post_excluded_posts" id="wti_like_post_excluded_posts" value="<?php _e(get_option('wti_like_post_excluded_posts')); ?>" />
						 <span class="description"><?php _e('Enter comma separated post/page ids where you do not want to show the like option. If Show on pages setting is set to Yes but you have added the page id here, then like option will not be shown for the same page.', 'wti-like-post');?></span>
					    </td>
				       </tr>
				       <tr valign="top">
					    <th scope="row"><label><?php _e('Show excluded posts/pages on widget', 'wti-like-post'); ?></label></th>
					    <td>	
						 <input type="radio" name="wti_like_post_show_on_widget" id="show_widget_yes" value="1" <?php if(('1' == get_option('wti_like_post_show_on_widget')) || ('' == get_option('wti_like_post_show_on_widget'))) { echo 'checked'; } ?> /> Yes
						 <input type="radio" name="wti_like_post_show_on_widget" id="show_widget_no" value="0" <?php if('0' == get_option('wti_like_post_show_on_widget')) { echo 'checked'; } ?> /> No
						 <span class="description"><?php _e('Select yes if you want to show the excluded posts/pages on widget.', 'wti-like-post')?></span>
					    </td>
				       </tr>
				       <tr valign="top">
					    <th scope="row"><label><?php _e('Position Setting', 'wti-like-post'); ?></label></th>
					    <td>	
						 <input type="radio" name="wti_like_post_position" id="position_top" value="top" <?php if(('top' == get_option('wti_like_post_position')) || ('' == get_option('wti_like_post_position'))) { echo 'checked'; } ?> /> Top of Content
						 <input type="radio" name="wti_like_post_position" id="position_bottom" value="bottom" <?php if('bottom' == get_option('wti_like_post_position')) { echo 'checked'; } ?> /> Bottom of Content
						 <span class="description"><?php _e('Select the position where you want to show the like options.', 'wti-like-post')?></span>
					    </td>
				       </tr>			
				       <tr valign="top">
					    <th scope="row"><label><?php _e('Alignment Setting', 'wti-like-post'); ?></label></th>
					    <td>	
						 <input type="radio" name="wti_like_post_alignment" id="alignment_left" value="left" <?php if(('left' == get_option('wti_like_post_alignment')) || ('' == get_option('wti_like_post_alignment'))) { echo 'checked'; } ?> /> Left
						 <input type="radio" name="wti_like_post_alignment" id="alignment_right" value="right" <?php if('right' == get_option('wti_like_post_alignment')) { echo 'checked'; } ?> /> Right
						 <span class="description"><?php _e('Select the alignment whether to show on left or on right.', 'wti-like-post')?></span>
					    </td>
				       </tr>
				       <tr valign="top">
					    <th scope="row">
						 <input class="button-primary" type="submit" name="Save" value="<?php _e('Save Options', 'wti-like-post'); ?>" />
						 <input type="submit" name="Reset" value="<?php _e('Reset Options', 'wti-like-post'); ?>" onclick="return confirmReset()" />
					    </th>
					    <td></td>
				       </tr>
				  </table>
			     </form>
			</div>
		</div>
	</div>	
	<script>
	function confirmReset()
	{
		//check whether user agrees to reset the settings to default or not
		var check = confirm("<?php _e('Are you sure to reset the options to default settings?', 'wti-like-post')?>");
		
		if(check)
		{
			//reset the settings
			document.getElementById('wti_like_post_jquery').value = 1;
			document.getElementById('wti_like_post_voting_period').value = 0;
			document.getElementById('wti_like_post_voting_style').value = 'style1';
			document.getElementById('login_yes').checked = false;
			document.getElementById('login_no').checked = true;
			document.getElementById('wti_like_post_login_message').value = 'Please login to vote.';
			document.getElementById('wti_like_post_thank_message').value = 'Thanks for your vote.';
			document.getElementById('wti_like_post_voted_message').value = 'You have already voted.';
			document.getElementById('show_pages_yes').checked = false;
			document.getElementById('show_pages_no').checked = true;
			document.getElementById('wti_like_post_excluded_posts').value = 0;
			document.getElementById('show_widget_yes').checked = true;
			document.getElementById('show_widget_no').checked = false;
			document.getElementById('position_top').checked = false;
			document.getElementById('position_bottom').checked = true;
			document.getElementById('alignment_left').checked = true;
			document.getElementById('alignment_right').checked = false;
			
			return true;
		}
		
		return false;
	}
	</script>
	
	<div id="poststuff" class="ui-sortable meta-box-sortables">
		<h3><?php _e('Most Liked Posts', 'wti-like-post');?></h3>
		<?php
		//getting the most liked posts
		$query = "SELECT post_id, SUM(value) AS like_count, post_title FROM `wp_wti_like_post` L JOIN wp_posts P ";
		$query .= "ON L.post_id = P.ID WHERE value > 0 GROUP BY post_id ORDER BY like_count DESC, post_title LIMIT 10";
		
		$posts = $wpdb->get_results($query);
		$post_count = count($posts);		
	    
		if(count($posts) > 0) {
			echo '<table cellspacing="0" class="wp-list-table widefat fixed likes">';
			echo '<thead><tr><th>';
			_e('Post Title', 'wti-like-post');
			echo '</th><th>';
			_e('Like Count', 'wti-like-post');
			echo '</th><tr></thead>';
			echo '<tbody class="list:likes" id="the-list">';
			
			foreach ($posts as $post) {
				$post_title = stripslashes($post->post_title);
				$permalink = get_permalink($post->post_id);
				$like_count = $post->like_count;
				
				echo '<tr>';
				echo '<td><a href="' . $permalink . '" title="' . $post_title.'" rel="nofollow" target="_blank">' . $post_title . '</a></td>';
				echo '<td>'.$like_count.'</td>';
				echo '</tr>';
			}
			
			echo '</tbody></table>';
		} else {
			echo '<p>';
			_e('No posts liked yet', 'wti-like-post');
			echo '</p>';
		}
		?>
	</div>
</div>
<?php
}

#### WIDGET ####
function WtiMostLikedPosts($number = 10, $before, $after, $show_count = 0, $return = false) {
	global $wpdb;
	$widget_data = "";
	
	$show_excluded_posts = get_option('wti_like_post_show_on_widget');
	$excluded_post_ids = explode(',', get_option('wti_like_post_excluded_posts'));
	
	if(!$show_excluded_posts && count($excluded_post_ids) > 0) {
		$where = "AND post_id NOT IN (" . get_option('wti_like_post_excluded_posts') . ")";
	}
	
     //getting the most liked posts
     $query = "SELECT post_id, SUM(value) AS like_count, post_title FROM `wp_wti_like_post` L JOIN wp_posts P ";
     $query .= "ON L.post_id = P.ID WHERE value > 0 $where GROUP BY post_id ORDER BY like_count DESC, post_title ASC LIMIT $number";

     $posts = $wpdb->get_results($query);
 
     if(count($posts) > 0) {
          foreach ($posts as $post) {
               $post_title = stripslashes($post->post_title);
               $permalink = get_permalink($post->post_id);
               $like_count = $post->like_count;
               
               $widget_data .= $before.'<a href="' . $permalink . '" title="' . $post_title.'" rel="nofollow">' . $post_title . '</a>';
               $widget_data .= $show_count == '1' ? ' ('.$like_count.')' : '';
               $widget_data .= $after;
          }
     } else {
          $widget_data .= $before;
	  $widget_data .= __('No posts liked yet', 'wti-like-post');
	  $widget_data .= $after;
     }
     
     if($return) {
	return $widget_data;
     } else {
	echo $widget_data;
     }
}

function AddWidgetWtiMostLikedPosts() {
	function WidgetWtiMostLikedPosts($args) {
		extract($args);
		$options = get_option("wti_most_liked_posts");
          
		if (!is_array( $options )) {
			$options = array(
				'title' => __('Most Liked Posts', 'wti-like-post'),
				'number' => '10',
				'show_count' => '0'
			);
		}
          
		$title = $options['title'];
		$number = $options['number'];
		$show_count = $options['show_count'];
		
		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo '<ul class="wti-most-liked-posts">';

		WtiMostLikedPosts($number, '<li>', '</li>', $show_count);
		
		echo '</ul>';
		echo $after_widget;
	}
     
	wp_register_sidebar_widget('WtiMostLikedPosts', 'Most Liked Posts', 'WidgetWtiMostLikedPosts');
	
	function OptionsWidgetWtiMostLikedPosts() {
		$options = get_option("wti_most_liked_posts");
		
		if (!is_array( $options )) {
			$options = array(
                    'title' => __('Most Liked Posts', 'wti-like-post'),
                    'number' => '10',
                    'show_count' => '0'
			);
		}
		
          //processing the option settings for the widget
		if (isset($_POST['wti-submit'])) {
			$options['title'] = htmlspecialchars($_POST['wti-title']);
               
			if (ctype_digit($_POST['wti-number'])) {
				$options['number'] = ($_POST['wti-number']);
			} else {
				$options['number'] = '0';
			}
               
			if (isset($_POST['wti-show-count'])) {
				$options['show_count'] = '1';
			} else {
				$options['show_count'] = '0';
			}

			if ( $options['number'] > 10 || (int)$options['number'] == 0) {
				$options['number'] = 10;
			}
			
			update_option("wti_most_liked_posts", $options);
		}
          
          //widget option setting fields
		?>
		<p>
               <label for="wti-title"><?php _e('Title', 'wti-like-post'); ?>:<br />
               <input class="widefat" type="text" id="wti-title" name="wti-title" value="<?php echo $options['title'];?>" /></label>
          </p>
		
		<p>
               <label for="wti-number"><?php _e('Number of posts to show', 'wti-like-post'); ?>:<br />
               <input type="text" id="wti-number" name="wti-number" style="width: 25px;" value="<?php echo $options['number'];?>" /> <small>(max. 10)</small></label>
          </p>
		
		<p>
               <label for="wti-show-count"><input type="checkbox" id="wti-show-count" name="wti-show-count" value="1" <?php if($options['show_count'] == '1') echo 'checked="checked"'; ?> /> <?php _e('Show like count', 'wti-like-post'); ?></label>
          </p>
		
		<input type="hidden" id="wti-submit" name="wti-submit" value="1" />
		<?php
	}
     
	wp_register_widget_control('WtiMostLikedPosts', 'Most Liked Posts', 'OptionsWidgetWtiMostLikedPosts');

} 

add_action('init', 'AddWidgetWtiMostLikedPosts');

#### FRONT-END VIEW ####
function GetWtiLikePost($arg = null) {
	global $wpdb;
	$post_id = get_the_ID();	
	$wti_like_post = "";
	
	//get the posts ids where we do not need to show like functionality
	$excluded_posts = explode(",", get_option('wti_like_post_excluded_posts'));
	
	if(!in_array($post_id, $excluded_posts)) {		
		$like_count = GetWtiLikeCount($post_id);
		$unlike_count = GetWtiUnlikeCount($post_id);
		$msg = GetWtiVotedMessage($post_id);
		$alignment = ("left" == get_option('wti_like_post_alignment')) ? 'left' : 'right';
		$style = (get_option('wti_like_post_voting_style') == "") ? 'style1' : get_option('wti_like_post_voting_style');
	     
		$wti_like_post .= "<div id='watch_action'>".
                         "<div id='watch_position' style='float:".$alignment."; '>".
                              "<div id='action_like' >".
                              "<span class='like-".$post_id." like'><img id='like-".$post_id."' rel='like' class='lbg-$style jlk' src='".WP_PLUGIN_URL."/wti-like-post/images/pixel.gif'></span>".
                              "<span id='lc-".$post_id."' class='lc'>".$like_count."</span>".
                         "</div>".
                         "<div id='action_unlike' >".
                              "<span class='unlike-".$post_id." unlike'><img id='unlike-".$post_id."' rel='unlike' class='unlbg-$style jlk' src='".WP_PLUGIN_URL."/wti-like-post/images/pixel.gif'></span>".
                              "<span id='unlc-".$post_id."' class='unlc'>".$unlike_count."</span>".
                         "</div> ".		                				
                    "</div> ".
                    "<div id='status-".$post_id."' class='status' style='float:".$alignment."; '>&nbsp;&nbsp;" . $msg . "</div>".
               "</div>".
               "<div id='clear'></div>";
	}
     
	if ($arg == 'put') {
		return $wti_like_post;
	} else {
		echo $wti_like_post;
	}
}

function PutWtiLikePost($content) {
	$show_on_pages = false;
	
	if((is_page() && get_option('wti_like_post_show_on_pages')) || (!is_page())) {
		$show_on_pages = true;
	}
     
	if (!is_feed() && $show_on_pages) {     
		$wti_like_post_content = GetWtiLikePost('put');
		$wti_like_post_position = get_option('wti_like_post_position');
		
		if ($wti_like_post_position == 'top') {
		     $content = $wti_like_post_content . $content;
		} elseif ($wti_like_post_position == 'bottom') {
		     $content = $content . $wti_like_post_content;
		} else {
		     $content = $wti_like_post_content . $content . $wti_like_post_content;
		}
	}
     
	return $content;
}

add_filter('the_content', 'PutWtiLikePost');

function GetWtiLikeCount($post_id) {
	global $wpdb;
	$wti_like_count = $wpdb->get_var("SELECT SUM(value) FROM {$wpdb->prefix}wti_like_post WHERE post_id = '$post_id' AND value >= 0");
	
	if(!$wti_like_count) {
		$wti_like_count = 0;
	} else {
		$wti_like_count = "+" . $wti_like_count;
	}
	
	return $wti_like_count;
}

function GetWtiUnlikeCount($post_id) {
	global $wpdb;
	$wti_unlike_count = $wpdb->get_var("SELECT SUM(value) FROM {$wpdb->prefix}wti_like_post WHERE post_id = '$post_id' AND value <= 0");
	
	if(!$wti_unlike_count) {
		$wti_unlike_count = 0;
	}
	
	return $wti_unlike_count;
}

function GetWtiVotedMessage($post_id, $ip = null) {
	global $wpdb;
	
	if(null == $ip)
	{
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	$wti_has_voted = $wpdb->get_var("SELECT COUNT(id) AS has_voted FROM {$wpdb->prefix}wti_like_post WHERE post_id = '$post_id' AND ip = '$ip'");
	
	if($wti_has_voted > 0) {
		$wti_voted_message = get_option('wti_like_post_voted_message');
	}
	
	return $wti_voted_message;
}

function HasWtiAlreadyVoted($post_id, $ip = null) {
	global $wpdb;
	
	if(null == $ip)
	{
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	$wti_has_voted = $wpdb->get_var("SELECT COUNT(id) AS has_voted FROM {$wpdb->prefix}wti_like_post WHERE post_id = '$post_id' AND ip = '$ip'");
	
	return $wti_has_voted;
}

function GetWtiLastVotedDate($post_id, $ip = null) {
	global $wpdb;
	
	if(null == $ip)
	{
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	$wti_has_voted = $wpdb->get_var("SELECT date_time FROM {$wpdb->prefix}wti_like_post WHERE post_id = '$post_id' AND ip = '$ip'");

	return $wti_has_voted;
}

function GetWtiNextVoteDate($last_voted_date, $voting_period) {
	switch($voting_period) {
		case "1":
			$day = 1;
			break;
		case "2":
			$day = 2;
			break;
		case "3":
			$day = 3;
			break;
		case "7":
			$day = 7;
			break;
		case "14":
			$day = 14;
			break;
		case "21":
			$day = 21;
			break;
		case "1m":
			$month = 1;
			break;
		case "2m":
			$month = 2;
			break;
		case "3m":
			$month = 3;
			break;
		case "6m":
			$month = 6;
			break;
		case "1y":
			$year = 1;
			break;
	}
	
	$last_strtotime = strtotime($last_voted_date);
	$next_strtotime = mktime(date('H', $last_strtotime), date('i', $last_strtotime), date('s', $last_strtotime),
				date('m', $last_strtotime) + $month, date('d', $last_strtotime) + $day, date('Y', $last_strtotime) + $year);
	
	$next_voting_date = date('Y-m-d H:i:s', $next_strtotime);
	
	return $next_voting_date;
}

add_shortcode('most_liked_posts', 'WtiMostLikedPostsShortcode');

function WtiMostLikedPostsShortcode($args) {
     global $wpdb;
     
     if($args['limit']) {
	  $limit = $args['limit'];
     } else {
	  $limit = 10;
     }
     
     //getting the most liked posts
     $query = "SELECT post_id, SUM(value) AS like_count, post_title FROM `wp_wti_like_post` L JOIN wp_posts P ";
     $query .= "ON L.post_id = P.ID WHERE value > 0 $where GROUP BY post_id ORDER BY like_count DESC, post_title ASC LIMIT $limit";

     $posts = $wpdb->get_results($query);
 
     if(count($posts) > 0) {
	  echo '<table>';
	  echo '<tr>';
	  echo '<td>' . __('Title', 'wti-like-post') .'</td>';
	  echo '<td>' . __('Like Count', 'wti-like-post') .'</td>';
	  echo '</tr>';
	  
          foreach ($posts as $post) {
               $post_title = stripslashes($post->post_title);
               $permalink = get_permalink($post->post_id);
               $like_count = $post->like_count;
               
               echo '<tr>';
	       echo '<td><a href="' . $permalink . '" title="' . $post_title.'" rel="nofollow">' . $post_title . '</a></td>';
               echo '<td>' . $like_count . '</td>';
               echo '</tr>';
          }
	  
	  echo '</table>';
     } else {
	  echo '<p>' . __('No posts liked yet', 'wti-like-post') . '</p>';
     }
}

function WtiLikePostEnqueueScripts() {
	if (get_option('wti_like_post_jquery') == '1') {
	    wp_enqueue_script('WtiLikePost', WP_PLUGIN_URL.'/wti-like-post/js/wti_like_post.js', array('jquery'));	
	}
	else {
	    wp_enqueue_script('WtiLikePost', WP_PLUGIN_URL.'/wti-like-post/js/wti_like_post.js');	
	}
}

function WtiLikePostAddHeaderLinks() {
	echo '<link rel="stylesheet" type="text/css" href="'.WP_PLUGIN_URL.'/wti-like-post/css/wti_like_post.css" media="screen" />'."\n";
	echo '<script type="text/javascript">';
	echo 'var blog_url = \''.get_bloginfo('wpurl').'\'';
	echo '</script>'."\n";
}

add_action('init', 'WtiLikePostEnqueueScripts');
add_action('wp_head', 'WtiLikePostAddHeaderLinks');
?>