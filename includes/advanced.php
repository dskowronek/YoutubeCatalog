<?php

function create_youtube_videos_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'youtube_videos';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        description text,
        youtube_url varchar(255) NOT NULL,
        publication_date date NOT NULL,
        author varchar(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}


function youtube_videos_menu() {
    add_menu_page('YouTube Videos', 'YouTube Videos', 'manage_options', 'youtube_videos', 'youtube_videos_page');
    add_submenu_page('youtube_videos', 'Dodaj nowy film', 'Dodaj nowy', 'manage_options', 'youtube_videos_add', 'youtube_videos_add_page');
}
add_action('admin_menu', 'youtube_videos_menu');


function youtube_videos_page() {
    ?>
    <div class="wrap">
        <h1>YouTube Videos</h1>
        <?php
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            delete_youtube_video(intval($_GET['id']));
            echo '<div class="updated"><p>Film został usunięty.</p></div>';
        }
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nazwa</th>
                    <th>Opis</th>
                    <th>URL YouTube</th>
                    <th>Data publikacji</th>
                    <th>Autor</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $videos = get_all_youtube_videos();
                foreach ($videos as $video) {
                    ?>
                    <tr>
                        <td><?php echo esc_html($video->id); ?></td>
                        <td><?php echo esc_html($video->name); ?></td>
                        <td><?php echo esc_html($video->description); ?></td>
                        <td><?php echo esc_html($video->youtube_url); ?></td>
                        <td><?php echo esc_html($video->publication_date); ?></td>
                        <td><?php echo esc_html($video->author); ?></td>
                        <td>
                            <a href="?page=youtube_videos&action=delete&id=<?php echo esc_attr($video->id); ?>">Usuń</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

function youtube_videos_add_page() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errors = validate_youtube_video_form($_POST);
        if (empty($errors)) {
            add_youtube_video($_POST);
            echo '<div class="updated"><p>Film został dodany.</p></div>';
        } else {
            echo '<div class="error"><p>' . esc_html(implode(', ', $errors)) . '</p></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1>Dodaj nowy film</h1>
        <form method="post">
            <label for="name">Nazwa:</label>
            <input type="text" id="name" name="name" required><br>

            <label for="description">Opis:</label>
            <textarea id="description" name="description" pattern=".*\S.*" required></textarea><br>

            <label for="youtube_url">URL YouTube:</label>
            <input type="url" id="youtube_url" name="youtube_url" required><br>

            <label for="publication_date">Data publikacji:</label>
            <input type="date" id="publication_date" name="publication_date" required><br>

            <label for="author">Autor:</label>
            <input type="text" id="author" name="author" required><br>

            <input type="submit" value="Dodaj film">
        </form>
    </div>
    <?php
}


function add_youtube_video($data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'youtube_videos';

    $wpdb->insert(
        $table_name,
        array(
            'name' => $data['name'],
            'description' => $data['description'],
            'youtube_url' => $data['youtube_url'],
            'publication_date' => $data['publication_date'],
            'author' => $data['author'],
        ),
        array('%s', '%s', '%s', '%s', '%s')
    );
}

function get_all_youtube_videos() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'youtube_videos';
    return $wpdb->get_results("SELECT * FROM $table_name");
}

function delete_youtube_video($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'youtube_videos';
    $wpdb->delete($table_name, array('id' => $id), array('%d'));
}

function validate_youtube_video_form($data) {
    $errors = array();

    if (empty($data['name'])) {
        $errors[] = 'Nazwa filmu jest wymagana.';
    }

    if (empty($data['youtube_url']) || !filter_var($data['youtube_url'], FILTER_VALIDATE_URL)) {
        $errors[] = 'Podaj poprawny URL do filmu na YouTube.';
    }

    if (empty($data['publication_date'])) {
        $errors[] = 'Data publikacji jest wymagana.';
    }

    if (empty($data['author'])) {
        $errors[] = 'Autor filmu jest wymagany.';
    }

    return $errors;
}