    <div id="main-position">
      <div id="main" class="float-break">
        <div class="overflow-fix">
          <!-- Main area content: START -->
            <div id="mxd_wrapper">
              {if is_set($errorMessage)}
                <div class="flash"><span class="error">{$errorMessage}</span></div>
              {/if}
              {if is_set($infoMessage)}
                <div class="flash"><span class="notice">{$infoMessage}</span></div>
              {/if}
              <br/>
              {$module_result.content}
            </div>
          <!-- Main area content: END -->
        </div>
      </div>
    </div>