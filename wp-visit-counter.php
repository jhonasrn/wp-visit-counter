<?php
/*
Plugin Name: WP Visit Counter
Description: A simple plugin to count website visits per day.
Version: 2.0
Author: Jhonas
*/

if (!session_id()) {
    session_start();
}

// === UTILITIES ===

function wpvc_get_daily_file() {
    $upload_dir = wp_upload_dir();
    $file = $upload_dir['basedir'] . '/wpvc_daily_visits.json';

    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }

    return $file;
}

// === VISIT COUNTER ===

add_action('init', 'wpvc_count_visits');

function wpvc_count_visits() {
    if (is_admin()) {
        return;
    }

    if (isset($_SESSION['wpvc_counted'])) {
        return;
    }

    $file = wpvc_get_daily_file();
    $date = date('Y-m-d');

    $fp = fopen($file, 'c+');
    if ($fp && flock($fp, LOCK_EX)) {
        $size = filesize($file);
        $data = $size > 0 ? json_decode(fread($fp, $size), true) : [];
        $data[$date] = isset($data[$date]) ? $data[$date] + 1 : 1;

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($data));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    $_SESSION['wpvc_counted'] = true;
}

// === SHORTCODE TO DISPLAY TOTAL VISITS ===

add_shortcode('visit_counter', 'wpvc_display_counter');

function wpvc_display_counter() {
    $file = wpvc_get_daily_file();
    $data = json_decode(file_get_contents($file), true);
    $total = array_sum($data);

    return '<div class="wpvc-counter">Visits: ' . esc_html($total) . '</div>';
}

// === ADMIN PAGE ===

add_action('admin_menu', 'wpvc_admin_menu');

function wpvc_admin_menu() {
    add_menu_page(
        'Visit Counter',
        'Visit Counter',
        'manage_options',
        'wpvc-visit-counter',
        'wpvc_admin_page',
        'dashicons-chart-line',
        100
    );
}

function wpvc_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $file = wpvc_get_daily_file();
    $data = json_decode(file_get_contents($file), true);

    $today = date('Y-m-d');
    $today_visits = isset($data[$today]) ? $data[$today] : 0;

    // Handle date filter
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
    $end_date   = isset($_GET['end_date']) ? $_GET['end_date'] : '';

    // Filter data by date range
    $filtered_data = [];
    foreach ($data as $date => $visits) {
        if (
            ($start_date === '' || $date >= $start_date) &&
            ($end_date === '' || $date <= $end_date)
        ) {
            $filtered_data[$date] = $visits;
        }
    }

    ?>
    <div class="wrap">
        <h1>Visit Counter</h1>
        <p><strong>Visitors Today:</strong> <?php echo esc_html($today_visits); ?></p>

        <form method="get" style="margin-bottom: 20px;">
            <input type="hidden" name="page" value="wpvc-visit-counter">
            <table>
                <tr>
                    <td><label for="start_date">Start Date:</label></td>
                    <td><input type="date" name="start_date" id="start_date" value="<?php echo esc_attr($start_date); ?>"></td>
                </tr>
                <tr>
                    <td><label for="end_date">End Date:</label></td>
                    <td><input type="date" name="end_date" id="end_date" value="<?php echo esc_attr($end_date); ?>"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" class="button button-primary" value="Filter">
                        <a href="<?php echo admin_url('admin.php?page=wpvc-visit-counter'); ?>" class="button">Reset Filters</a>
                    </td>
                </tr>
            </table>
        </form>

        <h2>Daily Visits</h2>
        <table class="widefat">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Visits</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($filtered_data)) {
                    foreach ($filtered_data as $date => $visits) {
                        echo '<tr><td>' . esc_html($date) . '</td><td>' . esc_html($visits) . '</td></tr>';
                    }
                } else {
                    echo '<tr><td colspan="2">No data available for selected range.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}
