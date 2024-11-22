<?php
function dt_admin_menu() {
    add_menu_page(
        'OCP Events',
        'OCP Events',
        'manage_options',
        'dt-timeline-events',
        'dt_timeline_events_page',
        'dashicons-calendar-alt',
        6
    );
}
add_action('admin_menu', 'dt_admin_menu');

function dt_timeline_events_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'timeline_events';

    // Handle form submissions
    if (isset($_POST['dt_add_event'])) {
        $name = sanitize_text_field($_POST['name']);
        $year = sanitize_text_field($_POST['year']);
        $description = wp_kses_post($_POST['description']);
        $location = sanitize_text_field($_POST['location']); // Added Location

        // Check if name, year, description, and location are not empty
        if (!empty($name) && !empty($year) && !empty($description) && !empty($location)) {
            $wpdb->insert($table_name, [
                'name' => $name,
                'year' => $year,
                'description' => $description,
                'location' => $location, // Added Location
            ]);
            echo '<div class="updated"><p>Event added successfully.</p></div>';
        } else {
            echo '<div class="error"><p>Name, year, description, and location are required fields.</p></div>';
        }
    } elseif (isset($_POST['dt_update_event'])) {
        $id = intval($_POST['id']);
        $name = sanitize_text_field($_POST['name']);
        $year = sanitize_text_field($_POST['year']);
        $description = wp_kses_post($_POST['description']);
        $location = sanitize_text_field($_POST['location']); // Added Location

        $wpdb->update($table_name, [
            'name' => $name,
            'year' => $year,
            'description' => $description,
            'location' => $location, // Added Location
        ], ['id' => $id]);
    } elseif (isset($_POST['dt_delete_event'])) {
        $id = intval($_POST['id']);
        $wpdb->delete($table_name, ['id' => $id]);
    }

    // Fetch all events
    $events = $wpdb->get_results("SELECT * FROM $table_name");
    ?>
    <div class="wrap">
        <h1>Timeline</h1>
        <hr>
        
        <div class="d-flex justify-content-end">

        <button id="add-event-button" class="btn btn-primary btn-sm"> <i class="fas fa-plus-circle"></i> Add New Event</button>
        </div>
      
        <table class="wp-list-table widefat fixed striped mt-4">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location</th> <!-- Added Location -->
                    <th>Year</th>
                    <!-- <th>Description</th> -->
                    
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event) : ?>
                    <tr>
                        <td><?php echo esc_html($event->name); ?></td>
                        <td><?php echo esc_html($event->location); ?></td> <!-- Added Location -->
                        <td><?php echo esc_html($event->year); ?></td>
                        <!-- <td><?php echo wp_kses_post($event->description); ?></td> -->
                        <td>
                            <button class="btn btn-primary update-event-button btn-sm" data-id="<?php echo $event->id; ?>" data-name="<?php echo esc_html($event->name); ?>" data-year="<?php echo esc_html($event->year); ?>" data-description="<?php echo esc_html($event->description); ?>" data-location="<?php echo esc_html($event->location); ?>"><i class="fas fa-eye"></i></button>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="id" value="<?php echo $event->id; ?>">
                                <button type="submit" name="dt_delete_event" value="Delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this event?');"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Structure -->
    <div class="modal fade" id="event-modal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="modal-title">Add New Event</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" id="event-form">
                        <input type="hidden" name="id" id="event-id">
                        <div class="mb-3">
                            <label for="event-name" class="form-label">Name:</label>
                            <input type="text" class="form-control" name="name" id="event-name" required>
                        </div>
                        <div class="mb-3">
                            <label for="event-location" class="form-label">Location:</label>
                            <input type="text" class="form-control" name="location" id="event-location" required> <!-- Added Location -->
                        </div>
                        <div class="mb-3">
                            <label for="event-year" class="form-label">Year:</label>
                            <input type="text" class="form-control" name="year" id="event-year" required>
                        </div>
                        <div class="mb-3">
                            <label for="event-description" class="form-label">Description:</label>
                            <div id="quill-editor" style="height: 150px;"></div>
                            <textarea name="description" id="event-description" style="display: none;"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-sm" style="float:right;" id="save-event-button">Save Event</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
