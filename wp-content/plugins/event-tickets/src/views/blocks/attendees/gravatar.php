<?php
/**
 * Block: Attendees List
 * Gravatar
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/attendees/gravatar.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link {INSERT_ARTICLE_LINK_HERE}
 *
 * @since 4.9
 * @version 4.9.4
 *
 */

echo get_avatar( $attendee['purchaser_email'], 60, '', $attendee['purchaser_name'] );
