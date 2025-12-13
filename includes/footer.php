<?php
// includes/footer.php
?>

  <!-- Global Action Dialog -->
  <div id="ncDialog" style="display:none;">
    <div class="nc-dialog-backdrop"></div>
    <div class="nc-dialog-card" role="status" aria-live="polite">
      <div class="nc-dialog-icon" id="ncDialogIcon">
        <div class="nc-spinner"></div>
      </div>
      <div class="nc-dialog-text">
        <div class="nc-dialog-title" id="ncDialogTitle">Working...</div>
        <div class="nc-dialog-sub" id="ncDialogSub">Please wait</div>
      </div>
      <div class="nc-dialog-bar">
        <div class="nc-dialog-bar-fill" id="ncDialogBar"></div>
      </div>
    </div>
  </div>

  <!-- Global Confirm Modal -->
  <div id="ncConfirm" style="display:none;">
    <div class="nc-confirm-backdrop"></div>
    <div class="nc-confirm-card" role="dialog" aria-modal="true" aria-labelledby="ncConfirmTitle">
      <div class="nc-confirm-title" id="ncConfirmTitle">Confirm</div>
      <div class="nc-confirm-msg" id="ncConfirmMsg">Are you sure?</div>
      <div class="nc-confirm-actions">
        <button class="btn secondary" id="ncConfirmCancel">Cancel</button>
        <button class="btn danger" id="ncConfirmOk">Delete</button>
      </div>
    </div>
  </div>

</main>

<script src="../assets/js/app.js"></script>
</body>
</html>
