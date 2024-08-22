<style>
    body {
        font-family: arial;
        font-size: 10pt;
    }

    a {
        color: #000066;
        text-decoration: none;
    }

    table {
        border-collapse: collapse;
        border: 1px solid black;
        width: 100%;
    }

    thead {
        vertical-align: bottom;
        text-align: center;
        font-weight: bold;
    }

    tfoot {
        text-align: center;
        font-weight: bold;
    }

    th {
        text-align: right;
        padding-left: 0.35em;
        padding-right: 0.35em;
        padding-top: 0.35em;
        padding-bottom: 0.35em;
        vertical-align: top;
        border: 1px solid black;
    }

    td {
        padding-left: 0.35em;
        padding-right: 0.35em;
        padding-top: 0.35em;
        padding-bottom: 0.35em;
        vertical-align: top;
        border: 1px solid black;
    }

    img {
        margin: 0.2em;
        vertical-align: middle;
    }

    .heading-table {
        border: none;
        margin-bottom: 0px;
        margin-top: 0px;
    }

    .heading-table td {
        padding-left: 0px;
        padding-right: 0px;
        padding-top: 0px;
        padding-bottom: 0px;
        vertical-align: middle;
        border: none;
    }

    .heading-img {
        width: 50px;
    }

    .heading-left {
        width: 10%;
        text-align: center;
    }

    .heading-center {
        width: 70%;
        text-align: center;
    }

    .heading-right {
        width: 15%;
    }

    .heading-company-name {
        font-size: 13pt;
        text-align: center;
        font-family: "arial";
        font-weight: bold;
    }

    .heading-company-address {
        font-size: 12pt;
        font-weight: bold;
        text-align: center;
        font-family: "sans";
    }

    .title {
        text-align: center;
        line-height: 12pt;
        margin: 0px;
        font-size: 12pt !important;
        text-transform: uppercase;
    }

    .description {
        text-align: center;
        margin-bottom: 0px;
        font-size: 12pt !important;
        font-weight: bold;
        margin: 1px;
    }

    .title-container {
        margin-bottom: 0px;
    }

    caption {
        text-align: left;
        vertical-align: bottom;
    }
</style>

<?php if ($records) { ?>
    <div>
        <div>
            <table style="width: 20rem;">
                <thead>
                    <tr>
                        <?php foreach ($getOnlyThesDates as $date) { ?>
                            <th style="width: 150px; text-align:center">
                                <?php echo date('m/d/Y', strtotime($date->dateCreated)) ?> <br>
                                <?php echo date('l', strtotime($date->dateCreated)) ?>
                            </th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php foreach ($getOnlyThesDates as $date) { ?>
                            <td>
                                <?php
                                $total_balance = 0;
                                $current_date  = '';
                                foreach ($records as $key => $rec) {
                                    $recDate      = date('m/d/Y', strtotime($rec->dateCreated));
                                    $dateDate     = date('m/d/Y', strtotime($date->dateCreated));
                                    $current_date = date('Y-m-d', strtotime($date->dateCreated));
                                ?>
                                    <?php if ($recDate == $dateDate) { ?>
                                        <p><?php echo $rec->customer ?></p>
                                        <p>Balance: <strong><?php echo  number_format($rec->balance, 2) ?></strong> </p>
                                        <p>JO # <?php echo $rec->transID  ?> </a></p>
                                        <hr style="color: black; margin-top: 1px; margin-bottom: 1px">
                                    <?php
                                        $total_balance +=  $rec->balance;
                                    } ?>
                                <?php } ?>
                                <div>
                                    <p>Total: <?php echo number_format($total_balance, 2) ?></p>
                                </div>
                                <div>
                                    <?php if ($variance_result) { ?>
                                        <?php
                                        $ctr = 1;
                                        foreach ($variance_result as $key => $var) { ?>
                                            <?php
                                            if (date('Y-m-d', strtotime($key)) == date('Y-m-d', strtotime($date->dateCreated))) { ?>
                                                <p>Variance: <?php echo $ctr ?> : <?php echo  number_format($var, 2) ?></p>
                                            <?php
                                                $ctr++;
                                            } ?>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </td>
                        <?php } ?>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <div>
            <div>
                <div>
                    No data found
                </div>
            </div>
        </div>
    <?php } ?>