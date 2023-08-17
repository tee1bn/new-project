<?php

use v2\Classes\Bookies;
use v2\Models\ConversionLinting;

$bookies = (new Bookies)->getAvailabilityOfFrom();


?>
<div class="dropdown" style="display: inline;">
    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-filter"></i>
    </button>

    <div class="dropdown-menu" style="padding: 20px; ">
        <form action="<?= $action ?? ""; ?>" method="get" id="filter_form" style="width:400px;">
            <div class="row">
                <div class="form-group col-md-6 col-xs-12">
                    <label>Ref</label><br>
                    <input type="" name="ref" class="form-control" value="<?= $sieve['ref'] ?? ''; ?>">
                </div>

                <div class="form-group col-md-6 col-xs-12">
                    <label>Bookie</label><br>
                    <select class="form-control select2_single" data-placeholder="Select bookies" name="bookies">
                        <option value="">Select bookie</option>
                        <?php
                        ksort($bookies);
                        $bookies_with_issue = [];
                        $bookies = collect($bookies)->keyBy('brand')->toArray();


                        foreach ($bookies as $key => $bookie) :
                        ?>
                            <option <?= (isset($sieve['bookies'])) && ($key == $sieve['bookies']) ? 'selected' : ''; ?> value="<?= $key; ?>"><?= ucwords($bookie['brand']); ?></option>

                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="form-group col-md-6 col-xs-12">
                    <label>Admin</label><br>
                    <input type="" name="user" placeholder="First or Last Name or email, phone ,username" class="form-control" value="<?= $sieve['user'] ?? ''; ?>">
                </div>

                <div class="form-group col-md-6 col-xs-12">
                    <label>Market ID</label><br>
                    <input type="" name="market_id" placeholder="Market Id" class="form-control" value="<?= $sieve['market_id'] ?? ''; ?>">
                </div>

                <div class="form-group col-md-6 col-xs-12">
                    <label>Gravity</label><br>
                    <input type="" name="gravity" placeholder="Gravity" class="form-control" value="<?= $sieve['gravity'] ?? ''; ?>">
                </div>

                <div class=" form-group col-md-6 col-xs-12">
                    <?= ConversionLinting::getStatusFilter($sieve); ?>
                </div>

            </div>


            <div class="row">

                <div class=" form-group col-md-6 col-xs-12">
                    <label>* created at </label>
                    <input placeholder="Start" type="date" value="<?= $sieve['created_at']['start_date'] ?? ''; ?>" class="form-control" name="created_at[start_date]">
                </div>


                <div class=" form-group col-md-6 col-xs-12">
                    <label>* created at </label>
                    <input type="date" placeholder="End " value="<?= $sieve['created_at']['end_date'] ?? ''; ?>" class="form-control" name="created_at[end_date]">
                </div>

            </div>


            <div class="form-group">
                <button type="Submit" class="btn btn-primary">Submit</button>
                <!-- <a  onclick="$('#filter_form').reset()">Reset</a> -->
            </div>
        </form>

    </div>
</div>