<!-- status tab -->
<div class="tab-pane fade show active"
     id="samplestatus"
     role="tabpanel"
     aria-labelledby="samplestatustab">

  <h4 class="mt-3 mb-3">
    <?= sprintf(_("Status of %s"), htmlspecialchars($serviceName)); ?>
  </h4>

  <p>
    <?= sprintf(_("Current <code>%s</code> status is displayed below."), htmlspecialchars($serviceName)); ?>
  </p>

  <div class="row">
    <div class="mb-3 col-md-8 mt-2">
      <textarea id="logoutput"
                class="logoutput text-secondary"
                readonly><?=
        // serviceLog is already HTML-escaped in PHP, convert back to plain text
        html_entity_decode($serviceLog, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
      ?></textarea>
    </div>
  </div>
</div><!-- /.tab-pane -->

<script>
  const logArea = document.getElementById('logoutput');

  async function refreshLog() {
    try {
      const response = await fetch(`/?ajax=<?= strtolower($pluginName) ?>&t=${Date.now()}`, {
        cache: 'no-store'
      });
      if (response.ok) {
        const text = await response.text();
        logArea.value = text;
        logArea.scrollTop = logArea.scrollHeight; // auto-scroll to bottom
      }
    } catch (e) {
      console.error('Failed to update log:', e);
    }
  }

  // refresh every 3 seconds
  setInterval(refreshLog, 3000);
</script>

<style>
  .logoutput {
    width: 100%;
    height: 400px;
    resize: none;
    overflow: auto;
    font-family: monospace;
    white-space: pre-wrap;
  }
</style>
