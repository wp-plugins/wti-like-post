<?php
class MostLikedPostsWidget extends WP_Widget
{
     function MostLikedPostsWidget()
     {
          $widget_ops = array('description' => __('Most Liked Posts', 'new-users') );
          parent::WP_Widget(false, $name = __('Most Liked Posts'), $widget_ops);
     }

     /** @see WP_Widget::widget */
     function widget($args, $instance) {
          global $MostLikedPosts;
          $MostLikedPosts->widget($args, $instance); 
     }
    
     function update($new_instance, $old_instance) {         
          if($new_instance['title'] == ''){
               $new_instance['title'] = __('Most Liked Posts', 'wti-like-post');
          }
         
          if($new_instance['number'] == ''){
               $new_instance['number'] = 10;
          }
         
          return $new_instance;
     }
    
     function form($instance)
     {
          global $MostLikedPosts;
          ?>
		<p>
               <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wti-like-post'); ?>:<br />
               <input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title'];?>" /></label>
          </p>		
		<p>
               <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show', 'wti-like-post'); ?>:<br />
               <input type="text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" style="width: 25px;" value="<?php echo $instance['number'];?>" /> <small>(Default. 10)</small></label>
          </p>
		<p>
               <label for="<?php echo $this->get_field_id('show_count'); ?>"><input type="checkbox" id="<?php echo $this->get_field_id('show_count'); ?>" name="<?php echo $this->get_field_name('show_count'); ?>" value="1" <?php if($instance['show_count'] == '1') echo 'checked="checked"'; ?> /> <?php _e('Show like count', 'wti-like-post'); ?></label>
          </p>
		<input type="hidden" id="wti-submit" name="wti-submit" value="1" />
	   
          <?php
     }
}

class MostLikedPosts
{
     function MostLikedPosts(){
          add_action( 'widgets_init', array(&$this, 'init') );
     }
    
     function init(){
          register_widget("MostLikedPostsWidget");
     }
     
     function widget($args, $instance = array() ){
		global $wpdb;
		extract($args);
	    
		$title = $instance['title'];
		$number = $instance['number'];
		$show_count = $instance['show_count'];
		
		$widget_data  = $before_widget;
		$widget_data .= $before_title . $title . $after_title;
		$widget_data .= '<ul class="wti-most-liked-posts">';
		
		$show_excluded_posts = get_option('wti_like_post_show_on_widget');
		$excluded_post_ids = explode(',', get_option('wti_like_post_excluded_posts'));
		
		if(!$show_excluded_posts && count($excluded_post_ids) > 0) {
			$where = "AND post_id NOT IN (" . get_option('wti_like_post_excluded_posts') . ")";
		}
		
		//getting the most liked posts
		$query = "SELECT post_id, SUM(value) AS like_count, post_title FROM `{$wpdb->prefix}wti_like_post` L JOIN {$wpdb->prefix}posts P ";
		$query .= "ON L.post_id = P.ID WHERE value > 0 $where GROUP BY post_id ORDER BY like_count DESC, post_title ASC LIMIT $number";
	
		$posts = $wpdb->get_results($query);
	 
		if(count($posts) > 0) {
			foreach ($posts as $post) {
				$post_title = stripslashes($post->post_title);
				$permalink = get_permalink($post->post_id);
				$like_count = $post->like_count;
				
				$widget_data .= '<li><a href="' . $permalink . '" title="' . $post_title.'" rel="nofollow">' . $post_title . '</a>';
				$widget_data .= $show_count == '1' ? ' ('.$like_count.')' : '';
				$widget_data .= '</li>';
			}
		} else {
			$widget_data .= '<li>';
			$widget_data .= __('No posts liked yet', 'wti-like-post');
			$widget_data .= '</li>';
		}
		
		$widget_data .= '</ul>';
          $widget_data .= $after_widget;
		
		echo $widget_data;
	}
}

$MostLikedPosts = new MostLikedPosts();
?>