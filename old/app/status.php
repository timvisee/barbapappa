<?php

use app\balance\BalanceManager;
use app\mail\MailManager;
use app\session\SessionManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use app\template\PageSidebarBuilder;
use app\transaction\TransactionManager;
use app\user\UserManager;

// Include the page top
require_once('top.php');

?>
<div data-role="page" id="page-status">
    <?php PageHeaderBuilder::create(__('pageAbout', 'applicationStatus'))->setBackButton('index.php')->build(); ?>

    <div data-role="main" class="ui-content" align="center">
        <p><?=__('pageStatus', 'currentStatusShownBelow'); ?></p><br />

        <table class="ui-responsive">
            <tr>
                <td><?=__('general', 'database'); ?></td>
                <td><span style="color: green;">Connected!</span></td>
            </tr>
            <tr>
                <td><?=__('general', 'users'); ?></td>
                <td><?=UserManager::getUserCount(); ?></td>
            </tr>
            <tr>
                <td><?=__('general', 'sessions'); ?></td>
                <td><?=SessionManager::getSessionCount(); ?></td>
            </tr>
            <tr>
                <td><?=__('mail', 'verifiedMails'); ?></td>
                <td><?=MailManager::getMailCount(); ?></td>
            </tr>
            <tr>
                <td><?=__('general', 'balances'); ?></td>
                <td><?=BalanceManager::getBalanceCount(); ?></td>
            </tr>
            <tr>
                <td><?=__('general', 'transactions'); ?></td>
                <td><?=TransactionManager::getTransactionCount(); ?></td>
            </tr>
            <tr>
                <td><?=__('pageStatus', 'uptime'); ?></td>
                <td><?php
                    if(function_exists('sys_getloadavg')) {
                        try {
                            $display = '';
                            system("uptime", $display);
                            preg_match('/[^,]+,[^,]+/i', $display, $matches);
                            echo $matches[0];
                        } catch(Exception $e) {
                            echo '<i>Unknown</i>';
                        }
                    } else
                        echo '<i>Unknown</i>';
                    ?></td>
            </tr>
            <tr>
                <td><?=__('pageStatus', 'cpuUsage'); ?></td>
                <td><?php
                    if(function_exists('sys_getloadavg')) {
                        // Get the CPU status
                        $cpu = sys_getloadavg();

                        echo $cpu[0] . ' (1 min avg)<br />';
                        echo $cpu[1] . ' (5 min avg)<br />';
                        echo $cpu[2] . ' (15 min avg)<br />';
                    } else
                        echo '<i>Unknown</i>';
                    ?></td>
            </tr>
        </table>
        <br />

        <p><i><?=__('pageStatus', 'pageRefreshesEveryTenSec'); ?>..</i></p>

        <script>
            var staticsRefreshTimer;
            $(document).on('pageshow', function() {
                if(staticsRefreshTimer == null)
                    staticsRefreshTimer = setInterval(function() {
                        if(getActivePageId() == 'page-status') {
                            showLoader('Refreshing page...');
                            refreshPage();
                            hideLoader();
                        }
                    }, 10000);
            });
        </script>
    </div>

    <?php
    // Build the footer and sidebar
    PageFooterBuilder::create()->build();
    PageSidebarBuilder::create()->build();
    ?>
</div>
<?php

// Include the page bottom
require_once('bottom.php');