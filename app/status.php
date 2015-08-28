<?php

use app\occupation\OccupationManager;
use app\picture\PictureManager;
use app\session\SessionManager;
use app\station\StationManager;
use app\team\TeamManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

?>

    <div data-role="page" id="page-status">
        <?php PageHeaderBuilder::create('Application Status')->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content" align="center">
            <p>The current application status is shown below.</p><br />

            <table class="ui-responsive">
                <tr>
                    <td>Database</td>
                    <td><span style="color: green;">Connected!</span></td>
                </tr>
                <tr>
                    <td>Sessions</td>
                    <td><?=SessionManager::getSessionCount(); ?></td>
                </tr>
                <tr>
                    <td>Stations</td>
                    <td><?=StationManager::getStationCount(); ?></td>
                </tr>
                <tr>
                    <td>Teams</td>
                    <td><?=TeamManager::getTeamCount(); ?></td>
                </tr>
                <tr>
                    <td>Pictures</td>
                    <td><?=PictureManager::getPictureCount(); ?></td>
                </tr>
                <tr>
                    <td>Occupations</td>
                    <td><?=OccupationManager::getOccupationCount(); ?></td>
                </tr>
                <tr>
                    <td>Uptime</td>
                    <td><?php
                        try {
                            $display = '';
                            system("uptime", $display);
                            preg_match('/[^,]+,[^,]+/i', $display, $matches);
                            echo $matches[0];
                        } catch(Exception $e) {
                            echo '<i>Unknown</i>';
                        }
                        ?></td>
                </tr>
                <tr>
                    <td>CPU usage</td>
                    <td><?php
                        if(function_exists('sys_getloadavg')) {
                            // Get the CPU status
                            $cpu = sys_getloadavg();

                            echo $cpu[0] . ' (1 min avg)<br />';
                            echo $cpu[1] . ' (5 min avg)<br />';
                            echo $cpu[2] . ' (15 min avg)<br />';
                        }
                        else
                            echo '<i>Unknown</i>';
                        ?></td>
                </tr>
            </table>
            <br />

            <p><i>This page refreshes automatically every 10 seconds...</i></p>

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

        <?php PageFooterBuilder::create()->build(); ?>
    </div>

<?php

// Include the page bottom
require_once('bottom.php');