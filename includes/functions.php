<?php

function create_movie_post_type() {
    register_post_type('youtube_movie',
        array(
            'labels' => array(
                'name' => __('Youtube katalog'),
                'singular_name' => __('Youtube katalog'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title'),
        )
    );
}
add_action('init', 'create_movie_post_type');


function add_movie_fields() {
    add_meta_box('youtube_movie_fields', 'Informacje o filmie', 'display_movie_fields', 'youtube_movie', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_movie_fields');

function display_movie_fields($post) {
    wp_nonce_field(basename(__FILE__), 'youtube_movie_nonce');

    $movie_title = get_post_meta($post->ID, '_movie_title', true);
    $movie_description = get_post_meta($post->ID, '_movie_description', true);
    $youtube_url = get_post_meta($post->ID, '_youtube_url', true);
    $publication_date = get_post_meta($post->ID, '_publication_date', true);
    $author_name = get_post_meta($post->ID, '_author_name', true);

    ?>
	<p>
		<label for="movie_title">Nazwa filmu: *</label>
		<input type="text" id="movie_title" name="movie_title" value="<?php echo esc_attr($movie_title); ?>" required>
	</p>

	<p>
		<label for="movie_description">Opis filmu: *</label>
		<textarea id="movie_description" name="movie_description" pattern=".*\S.*" required><?php echo esc_textarea($movie_description); ?></textarea>
	</p>

	<p>
		<label for="youtube_url">URL do filmu na YouTube: *</label>
		<input type="url" id="youtube_url" name="youtube_url" value="<?php echo esc_url($youtube_url); ?>" required>
	</p>

	<p>
		<label for="publication_date">Data publikacji: *</label>
		<input type="date" id="publication_date" name="publication_date" value="<?php echo esc_attr($publication_date); ?>" required>
	</p>
	
	<p>
		<label for="author_name">Autor filmu: *</label>
		<input type="text" id="author_name" name="author_name" value="<?php echo esc_attr($author_name); ?>" required>
	</p>
    <?php
}


function save_movie_fields($post_id) {
    if (!isset($_POST['youtube_movie_nonce']) || !wp_verify_nonce($_POST['youtube_movie_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    $fields = array('movie_title', 'movie_description', 'youtube_url', 'publication_date', 'author_name');

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'save_movie_fields');