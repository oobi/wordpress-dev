<?php

/**
 * Content of the "Campaign Statistics" tab
 *
 * Included in admin/list-table.php
 *
 * @link       http://www.fireflyinteractive.net
 * @since      1.0.0
 *
 * @package    Buzz_Mailchimp
 * @subpackage Buzz_Mailchimp/admin/partials
 */

// get default campaign ID if passed via URL
$cid = isset( $_GET['cid'] ) ? $_GET['cid'] : NULL;
?>

<h3>Campaign Statistics</h3>

<?php
    // Get the campaign report
    $report = $ffm->get_campaign_report($cid);
    $ffm->show_error( $report );
    if(!$ffm->is_error($report)) :
?>

<table id="campaign-summary" class="widefat">
    <thead>
        <tr>
            <th class="row-title">Campaign Statistics</th>
            <th></th>
        </tr>
    </thead>
    <tr>
        <td class="row-title">
            <label for="tablecell">Campaign Title</label>
        </td>
        <td><?php echo $report['campaign_title']; ?></td>
    </tr>
    <tr class="alternate">
        <td class="row-title">
            <label for="tablecell">Subject</label>
        </td>
        <td><?php echo $report['subject_line']; ?></td>
    </tr>
    <tr>
        <td class="row-title">
            <label for="tablecell">Send Time</label>
        </td>
        <td><?php echo $report['send_time']; ?></td>
    </tr>
    <tr class="alternate">
        <td class="row-title">
            <label for="tablecell">List Name</label>
        </td>
        <td><?php echo $report['list_name']; ?></td>
    </tr>
    <tr>
        <td class="row-title">
            <label for="tablecell">Emails Sent</label>
        </td>
        <td><?php echo $report['emails_sent']; ?></td>
    </tr>
    <tr class="alternate">
        <td class="row-title">
            <label for="tablecell">Unsubscribed</label>
        </td>
        <td><?php echo $report['unsubscribed']; ?></td>
    </tr>
    <tr>
        <td class="row-title">
            <label for="tablecell">Abuse Reports</label>
        </td>
        <td><?php echo $report['abuse_reports']; ?></td>
    </tr>
    <tr class="alternate">
        <td class="row-title">
            <label for="tablecell">Bounces</label>
        </td>
        <td>
            Hard Bounces: <?php echo $report['bounces']['hard_bounces']; ?><br>
            Soft Bounces: <?php echo $report['bounces']['soft_bounces']; ?><br>
            Syntax Errors: <?php echo $report['bounces']['syntax_errors']; ?>
        </td>
    </tr>
    <tr>
        <td class="row-title">
            <label for="tablecell">Forwards</label>
        </td>
        <td>
            Forwards Count: <?php echo $report['forwards']['forwards_count']; ?><br>
            Forwards Opens: <?php echo $report['forwards']['forwards_opens']; ?>
        </td>
    </tr>
    <tr class="alternate">
        <td class="row-title">
            <label for="tablecell">Opens</label>
        </td>
        <td>
            Opens Total: <?php echo $report['opens']['opens_total']; ?><br>
            Unique Opens: <?php echo $report['opens']['unique_opens']; ?><br>
            Open Rate: <?php echo intval($report['opens']['open_rate'] * 100); ?>%<br>
            Last Open: <?php echo $report['opens']['last_open']; ?>
        </td>
    </tr>
    <tr>
        <td class="row-title">
            <label for="tablecell">Clicks</label>
        </td>
        <td>
            Clicks Total: <?php echo $report['clicks']['clicks_total']; ?><br>
            Unique Clicks: <?php echo $report['clicks']['unique_clicks']; ?><br>
            Unique Subscriber Clicks: <?php echo $report['clicks']['unique_subscriber_clicks']; ?><br>
            Clicks Rate: <?php echo intval($report['clicks']['click_rate'] * 100); ?>%<br>
            Last Clicks: <?php echo $report['clicks']['last_click']; ?>
        </td>
    </tr>
    <tr class="alternate">
        <td class="row-title">
            <label for="tablecell">Facebook Likes</label>
        </td>
        <td>
            Recipient Likes: <?php echo $report['facebook_likes']['recipient_likes']; ?><br>
            Unique Likes: <?php echo $report['facebook_likes']['unique_likes']; ?><br>
            Facebook Likes: <?php echo $report['facebook_likes']['facebook_likes']; ?>
        </td>
    </tr>
</table>

<?php else : ?>

Unable to retrieve campaign stats for ID <?php echo $cid; ?>. Campaign not found.

<?php endif; ?>