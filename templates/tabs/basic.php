<!-- basic / settings tab -->
<div class="tab-pane fade"
     id="samplesettings"
     role="tabpanel"
     aria-labelledby="samplesettingstab">

  <h4 class="mt-3 mb-3"><?php echo _("Basic settings"); ?></h4>

  <div class="row">
    <div class="mb-3 col-md-8 mt-2">
      <label for="txtapikey" class="form-label">
        <?php echo _("Sample API Key"); ?>
      </label>
      <div class="input-group">
        <input type="text"
               class="form-control"
               id="txtapikey"
               name="txtapikey"
               value="<?php echo htmlspecialchars($apiKey, ENT_QUOTES); ?>">
        <button type="button"
                class="btn btn-outline-secondary"
                id="btnGenerateKey"
                title="<?php echo _("Generate random API key"); ?>">
          <i class="fas fa-magic"></i>
        </button>
      </div>
    </div>
  </div>
</div><!-- /.tab-pane -->

<script>
  // simple random key generator, like the original SamplePlugin
  (function () {
    const btn = document.getElementById('btnGenerateKey');
    const inp = document.getElementById('txtapikey');
    if (!btn || !inp) return;

    btn.addEventListener('click', function () {
      const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
      let key = '';
      for (let i = 0; i < 32; i++) {
        key += chars.charAt(Math.floor(Math.random() * chars.length));
      }
      inp.value = key;
    });
  })();
</script>
