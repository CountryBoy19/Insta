<?php defined('ROOT') || die() ?>

<?php $data = $database->query("SELECT SUM(`amount`) AS `earnings`, `currency`, COUNT(`id`) AS `count` FROM `payments` GROUP BY `currency`"); ?>

<div class="card card-shadow mb-5">
    <div class="card-body">
        <h4 class="card-title">Your website's generated sales</h4>

        <?php if(!$data->num_rows): ?>
            You don't have any sales yet.. :(
        <?php else: ?>

            <ul>
                <?php while($sales = $data->fetch_object()): ?>
                    <h6><span class="text-info"><?= $sales->count ?></span> sales and generated a revenue of <span class="text-success"><?= number_format($sales->earnings, 2) ?></span> <?= $sales->currency; ?></h6>
                <?php endwhile; ?>
            </ul>

        <?php endif; ?>
    </div>
</div>



<?php
$logs = [];
$data = $database->query("SELECT COUNT(*) AS `total_sales`, DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`, TRUNCATE(SUM(`amount`), 2) AS `total_earned` FROM `payments` WHERE `date` BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND DATE_ADD(NOW(), INTERVAL 1 DAY) GROUP BY `formatted_date`");
while($log = $data->fetch_assoc()) { $logs[] = $log; }

$chart_labels_array = [];
$chart_total_earned_array = $chart_total_sales_array = [];

for($i = 0; $i < count($logs); $i++) {
    $chart_labels_array[] = (new \DateTime($logs[$i]['formatted_date']))->format('Y-m-d');
    $chart_total_earned_array[] = $logs[$i]['total_earned'];
    $chart_total_sales_array[] = $logs[$i]['total_sales'];
}

/* Defining the chart data */
$chart_labels = '["' . implode('", "', $chart_labels_array) . '"]';
$chart_total_earned = '[' . implode(', ', $chart_total_earned_array) . ']';
$chart_total_sales = '[' . implode(', ', $chart_total_sales_array) . ']';

$test = 1;
?>

<div class="card card-shadow mb-5">
    <div class="card-body">
        <h4 class="card-title">Sales in the last 30 days</h4>

        <div class="chart-container">
            <canvas id="thirty_days_sales"></canvas>
        </div>

    </div>
</div>


<script>
    /* Display chart */
    let thirty_days_sales = new Chart(document.getElementById('thirty_days_sales').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= $chart_labels ?>,
            datasets: [{
                label: 'Total Sales',
                data: <?= $chart_total_sales ?>,
                backgroundColor: '#237f52',
                borderColor: '#237f52',
                fill: false
            },
            {
                label: 'Total Earned',
                data: <?= $chart_total_earned ?>,
                backgroundColor: '#37D28D',
                borderColor: '#37D28D',
                fill: false
            }]
        },
        options: {
            tooltips: {
                mode: 'index',
                intersect: false
            },
            title: {
                text: '',
                display: true
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>


<?php
$logs = [];
$data = $database->query("SELECT COUNT(*) AS `total`, DATE_FORMAT(`last_check_date`, '%Y-%m-%d') AS `date` FROM `instagram_users` WHERE `last_check_date` BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND DATE_ADD(NOW(), INTERVAL 1 DAY) GROUP BY `date`");
while($log = $data->fetch_assoc()) { $logs[] = $log; }

$chart_labels_array = [];
$chart_total_array = $chart_total_sales_array = [];

for($i = 0; $i < count($logs); $i++) {
    $chart_labels_array[] = (new \DateTime($logs[$i]['date']))->format('Y-m-d');
    $chart_total_array[] = $logs[$i]['total'];
}

/* Defining the chart data */
$chart_labels = '["' . implode('", "', $chart_labels_array) . '"]';
$chart_total = '[' . implode(', ', $chart_total_array) . ']';

$test = 1;
?>

<div class="card card-shadow mb-5">
    <div class="card-body">
        <h4 class="card-title">Checked accounts in the last 30 days</h4>

        <div class="chart-container">
            <canvas id="thirty_days_checked_ig_accounts"></canvas>
        </div>

    </div>
</div>


<script>
    /* Display chart */
    let thirty_days_checked_ig_accounts = new Chart(document.getElementById('thirty_days_checked_ig_accounts').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= $chart_labels ?>,
            datasets: [{
                label: 'Total Checked IG Accounts',
                data: <?= $chart_total ?>,
                backgroundColor: '#F75581',
                borderColor: '#F75581',
                fill: false
            }]
        },
        options: {
            tooltips: {
                mode: 'index',
                intersect: false
            },
            title: {
                text: '',
                display: true
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>

<?php
$data = $database->query("
		SELECT
			(SELECT COUNT(*) FROM `users`) AS `users_count`,
			(SELECT COUNT(*) FROM `payments`) AS `payments_count`,
            (SELECT COUNT(*) FROM `instagram_users`) AS `instagram_users_count`,
            (SELECT COUNT(*) FROM `unlocked_reports`) AS `unlocked_reports_count`
		")->fetch_object();
?>

<div class="card card-shadow mb-5">
    <div class="card-body">
        <h4 class="card-title">Here are some total numbers</h4>

        <div class="chart-container">
            <canvas id="total_chart"></canvas>
        </div>

    </div>
</div>


<script>
    /* Display chart */
    let total_chart_id = document.getElementById('total_chart').getContext('2d');

    let total_chart = new Chart(total_chart_id, {
        type: 'bar',
        data: {
            labels: ['Users', 'Payments', 'Instagram Users', 'Unlocked Reports'],
            datasets: [{
                label: 'Totals',
                data: [<?= $data->users_count ?>, <?= $data->payments_count ?>, <?= $data->instagram_users_count ?>, <?= $data->unlocked_reports_count ?>],
                backgroundColor: ['#007bff', '#37d28d', '#f75581', '#2caff7'],
                borderWidth: 1
            }]
        },
        options: {
            title: {
                text: '',
                display: false
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>





<?php
$data = $database->query("
		SELECT
			(SELECT COUNT(*) FROM `users` WHERE YEAR(`date`) = YEAR(CURDATE()) AND MONTH(`date`) = MONTH(CURDATE())) AS `users_count`,
			(SELECT COUNT(*) FROM `payments` WHERE YEAR(`date`) = YEAR(CURDATE()) AND MONTH(`date`) = MONTH(CURDATE())) AS `payments_count`,
            (SELECT COUNT(*) FROM `instagram_users` WHERE YEAR(`added_date`) = YEAR(CURDATE()) AND MONTH(`added_date`) = MONTH(CURDATE())) AS `instagram_users_count`,
            (SELECT COUNT(*) FROM `unlocked_reports` WHERE YEAR(`date`) = YEAR(CURDATE()) AND MONTH(`date`) = MONTH(CURDATE())) AS `unlocked_reports_count`
		")->fetch_object();
?>

<div class="card card-shadow mb-5">
    <div class="card-body">
        <h4 class="card-title">This is what happened this month</h4>

        <div class="chart-container">
            <canvas id="month_chart"></canvas>
        </div>

    </div>
</div>


<script>
    /* Display chart */
    let month_chart_id = document.getElementById('month_chart').getContext('2d');

    let month_chart = new Chart(month_chart_id, {
        type: 'bar',
        data: {
            labels: ['Users', 'Payments', 'Instagram Users', 'Unlocked Reports'],
            datasets: [{
                label: 'New stats',
                data: [<?= $data->users_count ?>, <?= $data->payments_count ?>, <?= $data->instagram_users_count ?>, <?= $data->unlocked_reports_count ?>],
                backgroundColor: ['#007bff', '#37d28d', '#f75581', '#2caff7'],
                borderWidth: 1
            }]
        },
        options: {
            title: {
                text: '',
                display: false
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>



<?php
$data = $database->query("
SELECT
(SELECT COUNT(`user_id`) FROM `users` WHERE YEAR(`date`) = YEAR(CURDATE()) AND MONTH(`date`) = MONTH(CURDATE()) AND DAY(`date`) = DAY(CURDATE())) AS `new_users_today`,
(SELECT COUNT(`user_id`) FROM `users` WHERE `active` = '1') AS `confirmed_users`,
(SELECT COUNT(`user_id`) FROM `users` WHERE `active` = '0') AS `unconfirmed_users`,
(SELECT COUNT(`user_id`) FROM `users` WHERE `last_activity` > UNIX_TIMESTAMP() - 2592000) AS `active_users`
")->fetch_object();
?>


<div class="card card-shadow mb-5">
    <div class="card-body">
        <h4 class="card-title">Some details about registered accounts..</h4>

        <div class="chart-container">
            <canvas id="users_chart"></canvas>
        </div>

    </div>
</div>


<script>
    /* Display chart */
    let users_chart_id = document.getElementById('users_chart').getContext('2d');

    let users_chart = new Chart(users_chart_id, {
        type: 'bar',
        data: {
            labels: ['New users today', 'Total Confirmed Users', 'Total Unconfirmed Users', 'Total active users in last month'],
            datasets: [{
                label: 'Account stats',
                data: [<?= $data->new_users_today ?>, <?= $data->confirmed_users ?>, <?= $data->unconfirmed_users ?>, <?= $data->active_users ?>],
                backgroundColor: ['#714eb7', '#cd476b', '#0064ce', '#2284ba'],
                borderWidth: 1
            }]
        },
        options: {
            title: {
                text: '',
                display: false
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>