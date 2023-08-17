<footer class="foot row">
  <div class="col-md-12 text-center">
    <span>&copy;<?= date("Y"); ?> <?= project_name; ?> </span>
  </div>
  <div class="col-md-12 text-center  mg-t-5">
    <?php foreach ($socials as $key => $social) : ?>
      <a href="<?= $social['link']; ?>" target="_blank" class="pd-4 text-secondary"><i class="fab fa-2x fa-<?= $social['name']; ?>"></i></a>
    <?php endforeach; ?>
  </div>

</footer>

</body>

</html>