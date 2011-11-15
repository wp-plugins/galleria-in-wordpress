<?php

class Galleria_in_WordPress_Admin {

	function __construct() {
        $this->init();
    }

    function init() {
        add_action('add_meta_boxes', array(&$this, 'register_gallery_photos_meta_box'));
        add_action('admin_head', array( &$this, 'jQuery' ) );
        add_filter('attachment_fields_to_edit', array(&$this, 'add_link_to_add_to_gallery') );
        add_action('wp_ajax_Galleria_for_WordPress_set_post_thumbnail', array(&$this, 'set_post_thumbnail') );
        add_action('wp_ajax_Galleria_for_WordPress_delete_image_from_gallery', array(&$this, 'delete_image_from_gallery') );
        add_action('wp_ajax_Galleria_for_WordPress_get_new_image_row_html', array(&$this, 'new_image_row_html') );
        
    }

	function register_gallery_photos_meta_box() {
	    add_meta_box( 
	        'gallery_photos',
	        __( 'Gallery Photos', 'galleria_for_wordpress' ),
	        array(&$this, 'output_gallery_photos_meta_box'),
	        'gallery' 
	    );
	}

	function output_gallery_photos_meta_box() {
		global $post;
		// var_dump($post);
		$children = get_post_meta($post->ID, '_galleria_for_wp_image_id');
		if ( $children ) {
			$args = array( 'post_type' => 'attachment', 'post__in' => $children, 'posts_per_page' => '-1', 'order' => 'ASC' );
			$attachments = get_posts($args);
		}
		else
			$attachments = NULL;
		// var_dump($attachments);
	    ?>
		<table class="widefat">
		<thead>
		    <tr>
		        <th>Thumb</th>
		        <th>Caption</th>
		        <th>Action</th>
		    </tr>
		</thead>
		<tfoot>
		    <tr>
			    <th>Thumb</th>
		        <th>Caption</th>
		        <th>Action</th>
		    </tr>
		</tfoot>
		<tbody id="galleria-table-body">
			<?php
				if ( ! empty( $attachments ) ) :
					foreach ( $attachments as $attachment ) :
						?>
						<tr id="attached-image-<?php echo $attachment->ID; ?>">
							<td><?php echo wp_get_attachment_image($attachment->ID, 'thumbnail'); ?></td>
							<td style="vertical-align:middle"><?php echo $attachment->post_excerpt; ?></td>
							<td style="vertical-align:middle"><a onclick="Galleria_for_WordPress_deleteImageFromGallery('<?php echo $post->ID; ?>', '<?php echo $attachment->ID; ?>', '<?php echo wp_create_nonce( "delete_image_from_gallery" );?>')">Delete</a></td>
						</tr> <?php 
					endforeach;
				endif;
				 ?>
		   <tr id="add-new-images-tr">
		     <td colspan=3 style="padding: 1em;text-align: center"><a href="http://www.bundletechnologies.com/roxane/wp-admin/media-upload.php?post_id=<?php echo $post->ID?>&type=image&TB_iframe=1" class="thickbox">Add images to the gallery</a></td>
		   </tr>
		</tbody>
		</table>
	    <?php
	}

	function jQuery() {
		global $post;
		?>
		<script>
		function Galleria_for_WordPress_deleteImageFromGallery(post_id, id, nonce){
			jQuery.post(ajaxurl, {
				action:"Galleria_for_WordPress_delete_image_from_gallery", post_id: post_id, thumbnail_id: id, _ajax_nonce: nonce, cookie: encodeURIComponent(document.cookie)
			}, function(str){
				jQuery('tr#attached-image-'+id).hide(500);
				// alert(str);
				// var win = window.dialogArguments || opener || parent || top;
				// $link.text( setPostThumbnailL10n.setThumbnail );
				if ( str == '0' ) {
					
					// alert( setPostThumbnailL10n.error );
				} else {

					// jQuery('a.wp-post-thumbnail').show();
					// $link.text( setPostThumbnailL10n.done );
					// $link.fadeOut( 2000 );
					// win.WPSetThumbnailID(id);
					// win.WPSetThumbnailHTML(str);
				}
			}
			);
		}
		</script>
		<?php 
		if( ! $post ) 
			$post = get_post($_GET['post_id'] );
		else
			return;

		if ( isset( $post ) && $post->post_type != "gallery" )
			return;

		?>
		<script>
		(function($) {
			$(document).ready( function() { 
				$('a.describe-toggle-on').each(function( i, el) { 
					id = $(el).parent().attr('id').split('-');
					id = id[2];

					$(el).next().after('<a style="float:right; margin-right: 5px; line-height: display: block;line-height: 36px; text-decoration: underline; cursor: pointer;" onclick="Galleria_for_WordPress_WPSetAsThumbnail(\''+id+'\', \'<?php echo wp_create_nonce( "set_post_thumbnail" );?>\')" id="add-to-gallery-link-'+id+'")">Add to Gallery</a>');
					
					
				});

			});
		})(jQuery);
		function Galleria_for_WordPress_WPSetAsThumbnail(id, nonce){
			// var $link = jQuery('a#wp-post-thumbnail-' + id);
			// $link.text( setPostThumbnailL10n.saving );
			jQuery.post(ajaxurl, {
				action:"Galleria_for_WordPress_set_post_thumbnail", post_id: post_id, thumbnail_id: id, _ajax_nonce: nonce, cookie: encodeURIComponent(document.cookie)
			}, function(str){
				// alert(str);
				// jQuery('tr#attached-image-'+id).hide(500);
				jQuery("add-to-gallery-link-"+id).html("Added to Gallery");
				jQuery.post(ajaxurl, {action:"Galleria_for_WordPress_get_new_image_row_html", post_id: id, _ajax_nonce: <?php echo 1 ?>, cookie: encodeURIComponent(document.cookie) }, function(str){ 

						jQuery('#add-new-images-tr', window.parent.parent.document).before( str );
						var win = window.dialogArguments || opener || parent || top;
						win.tb_remove();
				} );
				
				// $link.text( setPostThumbnailL10n.setThumbnail );
				if ( str == '0' ) {
					// alert( setPostThumbnailL10n.error );
				} else {
					// jQuery('a.wp-post-thumbnail').show();
					// $link.text( setPostThumbnailL10n.done );
					// $link.fadeOut( 2000 );
					// win.WPSetThumbnailID(id);
					// win.WPSetThumbnailHTML(str);
				}
			}
			);
		}

		</script>
		<?php

	}

	function add_link_to_add_to_gallery($fields) {
		if ( $_POST['fetch'] != 1 )
			return $fields;

		$query_args = explode("?", $_SERVER['HTTP_REFERER']);
		$post_id_query_arg = explode("&", $query_args[1]);
		$parent_post_id = str_replace("post_id=", "", $post_id_query_arg[0]);

		if( ! $post ) 
			$post = get_post( $parent_post_id );
		else
			return;

		if ( isset( $post ) && $post->post_type != "gallery" )
			return;
		
		?><script>
		(function($) {
			$(document).ready( function() { 
				$('a.wp-post-thumbnail').each(function( i, el) { 
					id = $(el).attr('id');
					id = id.replace( "wp-post-thumbnail-", "" );
					$(el).before('<a style="margin: 0 20px; line-height: display: block;line-height: 1.4em; text-decoration: underline; cursor: pointer;" onclick="Galleria_for_WordPress_WPSetAsThumbnail(\''+id+'\', \'<?php echo wp_create_nonce( "set_post_thumbnail" );?>\')">Add to Gallery</a>');
				});
			});
		})(jQuery);
		</script>
		<?php
		return $fields;
	}

	function set_post_thumbnail() {
		$post_ID = intval( $_POST['post_id'] );

		if ( !current_user_can( 'edit_post', $post_ID ) )
			die( '-1' );
		$thumbnail_id = intval( $_POST['thumbnail_id'] );

		check_ajax_referer( "set_post_thumbnail" );

		$current_children = get_post_meta($post_ID, '_galleria_for_wp_image_id');
		
		if ( in_array( $thumbnail_id, $current_children) )
			die( "That image is already in the gallery.");
		else
			add_post_meta($post_ID, '_galleria_for_wp_image_id', $thumbnail_id);

		die( "Image added to gallery." );

	}

	function delete_image_from_gallery() {
		$post_ID = intval( $_POST['post_id'] );

		if ( !current_user_can( 'edit_post', $post_ID ) )
			die( '-1' );
		$thumbnail_id = intval( $_POST['thumbnail_id'] );

		check_ajax_referer( "delete_image_from_gallery" );

		delete_post_meta($post_ID, '_galleria_for_wp_image_id', $thumbnail_id );
	}

	function new_image_row_html() {
		$id = $_POST['post_id'];
		$attachment = get_post($id);
		?><tr id="attached-image-<?php echo $attachment->ID; ?>">
			<td><?php echo wp_get_attachment_image($attachment->ID, 'thumbnail'); ?></td>
			<td style="vertical-align:middle"><?php echo $attachment->post_excerpt; ?></td>
			<td style="vertical-align:middle"><a onclick="Galleria_for_WordPress_deleteImageFromGallery('<?php echo $post->ID; ?>', '<?php echo $attachment->ID; ?>', '<?php echo wp_create_nonce( "delete_image_from_gallery" );?>')">Delete</a></td>
		</tr><?php
		die;
	}
}
?>