<?php
use Vendor\AiClient\Message;

/**
 * @param Message $message A message.
 * @return Message|WP_Error The reply.
 */
function send_message( Message $message, $mode = WP_Thing::MODE ): WP_Thing {}
