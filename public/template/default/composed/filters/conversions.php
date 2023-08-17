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
        Search <i class="fa fa-filter blink_me tx-primary"></i>
    </button>
    <div class="dropdown-menu" style="padding: 0px;width:300px;">

        <?php if (!($auth && $auth->canSeeConvertedCodesRealTime())) : ?>

            <div class="alert alert-primary mg-b-0 " role="alert">
                <p class="mb-0">
                    <span class="badge badge-sm badge-danger">new</span>
                    <br>Search your preferred betting site, odds, matches, sports etc. <br> <b>Only for premium users</b>
                    <a href="<?= $domain ?>/pg/pricing"><button type="button" class="btn btn-sm btn-block btn-primary">Subscribe now</button></a>
                </p>
            </div>
        <?php else : ?>


            <div class="alert alert-primary mg-b-0 " role="alert">
                <p class="mb-0">
                    <span class="badge badge-sm badge-primary">new</span>
                    Search your preferred betting site, odds, matches, sports etc.
                </p>
            </div>


            <form action="<?= $action ?? ''; ?>" method="get" id="filter_form" style="margin: 10px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group bookie">
                            <label>Bookie</label><br>
                            <select class="form-control select2_single" data-placeholder="Select bookies" multiple name="bookies[]">
                                <?php
                                ksort($bookies);
                                $bookies_with_issue = [];

                                foreach ($bookies as $key => $bookie) :
                                ?>
                                    <option <?= (isset($sieve['bookies']) && !empty($sieve['bookies']) && in_array($key, $sieve['bookies'])) ? 'selected' : ''; ?> value="<?= $key; ?>"><?= ucwords($bookie['name']); ?></option>

                                <?php endforeach; ?>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label>Odds (Range)</label><br>
                        <div class="form-group d-flex">
                            <input type="number" min="0" step=".01" name="odds_range[start]" placeholder="Start" class="form-control col-md-5 mr-2" value="<?= $sieve['odds_range']['start'] ?? ''; ?>">
                            <input type="number" min="0" step=".01" name="odds_range[end]" placeholder="End" class="form-control col-md-6" value="<?= $sieve['odds_range']['end'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label>Number of events (Range)</label><br>
                        <div class="form-group d-flex">
                            <input type="number" min="0" step="1" name="events_range[start]" placeholder="Start" class="form-control col-md-5 mr-2" value="<?= $sieve['events_range']['start'] ?? ''; ?>">
                            <input type="number" min="0" step="1" name="events_range[end]" placeholder="End" class="form-control col-md-6" value="<?= $sieve['events_range']['end'] ?? ''; ?>">
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

        <?php endif; ?>


    </div>
</div>