<?php

use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

?>

    <div data-role="page" id="page-about" data-unload="false">
        <?php PageHeaderBuilder::create('Rules & Help')->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <p>
            <h4>Game concept</h4>
            The goal of the OV Rally game is to win with your team by scoring the most points.
            There are several teams, all trying to gain as much points as possible by claiming stations.<br />
            Each team can gain points by claiming railway stations, spread throughout The Netherlands.
            A station can be claimed by taking a picture with your group at the station.
            <br /><br />

            <h4>Claiming and occupying stations</h4>
            Stations can be claimed and occupied by taking a picture with your group at the station.<br />
            Press the <i>Claim Station</i> button on the front page and follow the wizard to claim a station.<br />
            The submitted picture must be taken at the time the claim is made.
            <br /><br />

            <h4>Overtaking and reclaiming stations</h4>
            Occupied stations can be overtaken or reclaimed, half an hour after the current occupying picture of the station is accepted by a leader.<br />
            Each station will remain of the occupying team, until it has been taken over by a different team.<br />
            The occupying team may reclaim the station when possible to secure it again, for half an hour.
            <br /><br />

            <h4>Picture approval</h4>
            All pictures must be approved by a leader before a station is claimed and occupied.
            The leader must accept the picture or the claim will be ignored.
            <br /><br />

            <h4>Earning points</h4>
            Each station is worth a number of points, depending on the size of the station.
            The points of a station will be increased if the station is being occupied more than once.<br />
            Your team score equals the accumulated number of points of your occupied stations.
            <br /><br />

            <h4>Strategy; information is key</h4>
            The OV Rally app provides several pages with information about stations, occupations, teams and more.
            This allows you to easily develop your own strategy, for example, by taking over high value stations from
            other teams.
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>

<?php

// Include the page bottom
require_once('bottom.php');