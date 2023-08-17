</div>
<div style='background-color: #3151762e;'>
    <div style='font-size: 10px !important; padding: 15px; text-align: center;'>
        <!--  CMS::fetch('full_contact');  -->

        <div class="socials" style="text-align: center;">

            <img src="<?= $logo; ?>" height="35">
            <h3>Follow us</h3>
            <?php foreach ($socials as $key => $social) : ?>
                <a href="<?= $social['link']; ?>"><img height="25px" src="<?= $logo; ?>/../socials/<?= $social['name']; ?>.png"> </a>
            <?php endforeach; ?>
        </div>
        <p>This email was sent to you by <a href="<?= domain; ?>"><?= project_name; ?></a> &copy; <?= date("Y"); ?></p>
    </div>
</div>
</div>
</div>
<style>
    .socials a {
        text-decoration: none;
        padding: 2px;

    }
</style>
<div style="display: none;">