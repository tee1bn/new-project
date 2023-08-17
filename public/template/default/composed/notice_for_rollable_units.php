<?php

$auth = $user ?? $auth;

if ($auth && ($rollable = $auth->hasRollableUnits())) : ?>
    <?php if ($rollable->units > 0) : ?>
        <div class="alert alert-primary">
            <strong>Roll over 🚨 </strong> <span>Top up your subscription before <b><?= $rollable->RollableBy; ?></b> to rollover
                <b><?= $rollable->units ?? '0'; ?> units</b>.</span>
        </div>
    <?php endif; ?>
<?php else : ?>
    <!--    <div class="alert alert-warning">
        <span>Save time, make more, secure odds, stay flexible, every seconds count.</span>
    </div> -->
<?php endif; ?>