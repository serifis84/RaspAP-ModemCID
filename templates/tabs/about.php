<!-- about tab -->
<div class="tab-pane fade"
     id="sampleabout"
     role="tabpanel"
     aria-labelledby="sampleabouttab">

  <h4 class="mt-3 mb-3"><?php echo _("About this plugin"); ?></h4>

  <p>
    <?php echo htmlspecialchars($description); ?>
  </p>

  <p>
    <strong><?php echo _("Author"); ?>:</strong>
    <?php echo htmlspecialchars($author); ?>
  </p>

  <p>
    <strong><?php echo _("Project URL"); ?>:</strong>
    <a href="<?php echo htmlspecialchars($uri); ?>" target="_blank" rel="noopener">
      <?php echo htmlspecialchars($uri); ?>
    </a>
  </p>
</div><!-- /.tab-pane -->
