<?php

use v2\Classes\EventFetcher;

$sports = EventFetcher::getAllSports();; ?>

<style>
    .dropdown-menu.show {
        z-index: 2;
    }

    .select2-container {
        z-index: 3;
    }

    .bookie .select2-container {
        width: 100% !important;
    }
</style>

<div class="dropdown dropleft">
    <button type="button" class="btn btn-blck btn-white btn-sm dropdown-toggle" data-toggle="dropdown">
        Search <i class="fa fa-filter"></i>
    </button>
    <div class="dropdown-menu" style="padding: 0px;width:300px;">

        <form action="<?= $action ?? ''; ?>" method="get" id="filter_form" style="margin: 10px;">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group bookie">
                        <label>Team</label><br>
                        <input type="text" class="form-control" value="<?= $sieve['team'] ?? ''; ?>" name="team" id="team">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group bookie ">
                        <label>Sports</label><br>
                        <select data-placeholder="select sports" class="form-control select2_single" multiple name="sports[]">
                            <?php foreach ($sports as $sport) : ?>
                                <option <?= (isset($sieve['sports']) && !empty($sieve['sports']) && in_array($sport, $sieve['sports'])) ? 'selected' : ''; ?> value="<?= $sport; ?>"><?= ucwords($sport); ?></option>

                            <?php endforeach; ?>

                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group bookie ">
                        <label>Markets</label><br>

                        <select data-placeholder="select markets" class="form-control select2_single" multiple name="markets[]">
                            <?php foreach ($markets as $market) : ?>
                                <option <?= (isset($sieve['markets']) && !empty($sieve['markets']) && in_array($market['key'], $sieve['markets'])) ? 'selected' : ''; ?> value="<?= $market['key']; ?>"><?= ucwords($market['value']); ?></option>
                            <?php endforeach; ?>

                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <label>Odds (Range)</label><br>
                    <div class="form-group d-flex">
                        <input type="number" min="0" step=".01" name="odds_range[start]" placeholder="Start" class="form-control col-md-6 " value="<?= $sieve['odds_range']['start'] ?? ''; ?>">
                        <input type="number" min="0" step=".01" name="odds_range[end]" placeholder="End" class="form-control col-md-6" value="<?= $sieve['odds_range']['end'] ?? ''; ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group bookie">
                        <label>Playing on</label><br>
                        <select class="form-control select2_single" name="playing" data-placeholder="Select event date">
                            <?php
                            $playing = [
                                date('Y-m-d') => "Today",
                                date("Y-m-d", strtotime(" +1 day")) => "Tomorrow",
                                date("Y-m-d", strtotime(" +2 day")) => date("D M d, Y", strtotime(" +2 day")),
                                date("Y-m-d", strtotime(" +3 day")) => date("D M d, Y", strtotime(" +3 day")),
                                date("Y-m-d", strtotime(" +4 day")) => date("D M d, Y", strtotime(" +4 day")),
                                date("Y-m-d", strtotime(" +5 day")) => date("D M d, Y", strtotime(" +5 day")),
                                date("Y-m-d", strtotime(" +6 day")) => date("D M d, Y", strtotime(" +6 day")),
                            ];

                            foreach ($playing as $date => $human_date) : ?>
                                <option <?= (isset($sieve['playing']) && $sieve['playing'] == $date) ? 'selected' : ''; ?> value='<?= $date; ?>'><?= $human_date; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group ">
                        <label for="popularity">
                            <input id="popularity" type="checkbox" name="popularity" value="1" <?= (isset($sieve['popularity']) && $sieve['popularity'] == 1) ? 'checked' : ''; ?>>
                            Order By Popularity
                        </label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <button type="Submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>